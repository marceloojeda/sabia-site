<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use stdClass;

class SalesController extends BaseController
{

    public function __construct()
    {
        parent::__construct(true);

        $this->middleware('auth');
    }

    private function rulesStore()
    {
        return [
            'user_id' => 'required|exists:users,id',
            'buyer' => 'required|max:255',
            'buyer_email' => 'nullable|email',
            'amount' => 'required|numeric',
            'amount_paid' => 'required|numeric',
            'is_ecommerce' => 'required|boolean',
            'payment_date' => 'required|date'
        ];
    }

    private function rulesUpdate()
    {
        return [
            'user_id' => 'required|exists:users,id',
            'buyer' => 'required|max:255',
            'buyer_email' => 'nullable|email',
            'amount_paid' => 'required|numeric'
        ];
    }

    private function checkPerfilUsuario(Request $request)
    {
        if (!$this->validatePerfil($request->user())) {
            Auth::logout();
            return redirect('/home');
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->checkPerfilUsuario($request);

        $salesModel = new Sale();
        $sales = $salesModel->getSquadSales($request);

        foreach ($sales as $k => $sale) {
            if (!empty($sale->ticket_number)) {
                $sales[$k]->ticket_number = str_pad(strval($sale->ticket_number), 4, '0', STR_PAD_LEFT);
            }
        }

        $filter = [
            'buyer' => null,
            'seller' => null,
            'hasPages' => true
        ];

        return view('coordenador.sales_index', compact('sales', 'filter'));
    }

    public function indexFiltered(Request $request)
    {
        $salesModel = new Sale();
        $sales = $salesModel->getSquadSalesFiltered($request);

        foreach ($sales as $k => $sale) {
            if (!empty($sale->ticket_number)) {
                $sales[$k]->ticket_number = str_pad(strval($sale->ticket_number), 4, '0', STR_PAD_LEFT);
            }
        }

        $filter = [];
        if (!empty($request->buyer)) {
            $filter['buyer'] = $request->buyer;
        }
        if (!empty($request->seller)) {
            $filter['seller'] = $request->seller;
        }
        $filter['hasPages'] = false;

        return view('coordenador.sales_index', compact('sales', 'filter'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->checkPerfilUsuario($request);

        return redirect('/home');

        $this->arrVendedores = $this->getVendedores($request->user()->id, $request->user()->name);

        $saleObj = new Sale();

        $saleData = $saleObj->getFillable();
        $saleData['is_ecommerce'] = false;
        $saleData['amount'] = 12;
        $saleData['amount_paid'] = 12;
        $saleData['payment_status'] = 'Pago';
        $saleData['payment_date'] = date('Y-m-d H:i:s');

        return view('coordenador.sale_create', [
            'saleData' => $saleData,
            'formasPagamento' => $this->arrFormasPagamento,
            'statusVenda' => $this->arrStatusPagamento,
            'vendedores' => $this->arrVendedores
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->checkPerfilUsuario($request);

        $request['is_ecommerce'] = strval($request['is_ecommerce']) === "true";
        $this->validate($request, $this->rulesStore());

        $saleData = $request->except(['_token']);

        //caso a venda seja do proprio coordenador
        if ($saleData['user_id'] == $request->user()->id) {
            $saleData['seller'] = $request->user()->name;
        } else {
            $saleData['seller'] = $this->getVendedor($saleData['user_id'])->name;
        }

        $bilhetes = $saleData['amount_paid'];
        $sales = [];
        $arrSalesId = [];
        for ($i = 0; $i < $bilhetes; $i++) {
            $ticket = $this->getTicketNumber($saleData);

            $saleData['amount_paid'] = 12;
            $saleData['ticket_number'] = $ticket;

            $obj = Sale::create($saleData);
            $obj->refresh();

            $this->setSaleInNumberAvailable($obj->id, $ticket);

            $arrSale = $obj->toArray();
            $arrSale['ticket_number'] = str_pad(strval($obj->ticket_number), 4, '0', STR_PAD_LEFT);

            array_push($sales, $arrSale);
            array_push($arrSalesId, $arrSale['id']);
        }

        return redirect('/team/send-ticket-batch?sales=' . implode('|', $arrSalesId));
        // $head = $request->user();
        // $headPhone = $head->phone;
        // $headPhone = preg_replace("/[^0-9]/", "", $headPhone);

        // return view('coordenador.sale_ticket', ['sales' => $sales, 'session' => $headPhone]);
    }

    private function getTicketNumber($saleData)
    {
        $number = $this->getNumberAvailable();
        // if (!$number && $saleData['payment_status'] === 'Pago' && empty($saleData['ticket_number'])) {
        if (!$number) {
            // while (!$number) {
            //     $number = Sale::getLastTicket() + 1;
            // }

            $number = Sale::getLastTicket() + 1;
        }

        // $number = Sale::getLastTicket() + 1;
        return $number;
    }

    private function getNumberAvailable()
    {
        $arrNumbers = Sale::getNumberAvailable();

        if (empty($arrNumbers)) {
            return null;
        }

        $index = array_rand($arrNumbers);
        $number = $arrNumbers[$index]->number;
        $id = $arrNumbers[$index]->id;

        DB::update('update billets_control set available = ?, updated_at = ? where id = ? and number = ?', [false, date('Y-m-d H:i:s'), $id, $number]);

        return $number;
    }

    private function setSaleInNumberAvailable($saleId, $number)
    {
        $numberAvailable = DB::table('billets_controls')->where('number', $number)->where('available', false);
        if ($numberAvailable) {
            DB::update('update billets_control set sale_id = ? where number = ?', [$saleId, $number]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Sales  $sales
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Sale $sale)
    {
        $this->checkPerfilUsuario($request);

        $arrSale = $sale->toArray();
        if (!empty($sale->ticket_number)) {
            $arrSale['ticket_number'] = str_pad(strval($sale->ticket_number), 4, '0', STR_PAD_LEFT);
        }

        $sales = [];
        array_push($sales, $arrSale);

        $head = $request->user();
        $headPhone = $head->phone;
        $headPhone = preg_replace("/[^0-9]/", "", $headPhone);

        return view('coordenador.sale_ticket', ['sales' => $sales, 'session' => $headPhone]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Sales  $sales
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Sale $sale)
    {
        $this->checkPerfilUsuario($request);

        return redirect('/home');

        $this->arrVendedores = $this->getVendedores($request->user()->id, $request->user()->name);

        return view('coordenador.sale_edit', [
            'sale' => $sale,
            'formasPagamento' => $this->arrFormasPagamento,
            'statusVenda' => $this->arrStatusPagamento,
            'vendedores' => $this->arrVendedores
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Sales  $sales
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Sale $sale)
    {
        $this->validate($request, $this->rulesUpdate());

        $saleData = $request->except(['_token']);

        //caso a venda seja do proprio coordenador
        if ($saleData['user_id'] == $request->user()->id) {
            $saleData['seller'] = $request->user()->name;
        } else {
            $saleData['seller'] = $this->getVendedor($saleData['user_id'])->name;
        }
        $saleData['payment_status'] = $sale->payment_status;

        $ticket = $sale->ticket_number ?? $this->getTicketNumber($saleData);
        $saleData['amount_paid'] = 12;
        $saleData['ticket_number'] = $ticket;
        $sale->update($saleData);

        $this->setSaleInNumberAvailable($sale->id, $ticket);

        return redirect('/sales');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Sales  $sales
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sale $sale)
    {
        $sale->delete();

        return redirect('/sales');
    }

    public function checkBilhetesInutilizados()
    {
        $numbersAvailable = [];
        for ($billet = 1; $billet <= 4635; $billet++) {
            $sale = Sale::where('payment_status', 'Pago')
                ->whereNotNull('amount_paid')
                ->whereNotNull('user_id')
                ->where('ticket_number', $billet)
                ->select('id')
                ->first();

            if (!$sale) {
                array_push($numbersAvailable, $billet);
            }
        }

        $this->makeBillets($numbersAvailable);

        return response("Numeros disponiveis atualizados");
    }

    private function makeBillets(array $numbersAvailable)
    {
        $dataAtual = date('Y-m-d H:i:s');
        foreach ($numbersAvailable as $number) {

            $numberAvailable = DB::table('billets_control')->where('number', $number)->select('id')->first();
            if ($numberAvailable) {
                continue;
            }

            $sql = sprintf("insert into billets_control (number, created_at, updated_at) values (%d, '%s', '%s')", $number, $dataAtual, $dataAtual);
            $result = DB::insert($sql);
        }
    }

    public function checkWinner(Request $request)
    {
        $params = $request->except('_token');
        $numero = $params['numero'] ?? 0;

        $sale = Sale::whereNotNull('amount_paid')
            ->where('payment_status', 'Pago')
            ->where('ticket_number', $numero)
            ->first();

        if (!$sale) {
            // dd($params);
            return View('adm.teams.raffle', [
                'filter' => $params,
                'winner' => null
            ]);
        }

        $arrSale = $sale->toArray();
        $winner = [
            'billet' => str_pad(strval($arrSale['ticket_number']), 4, '0', STR_PAD_LEFT),
            'buyer' => $arrSale['buyer'],
            'buyerPhone' => $arrSale['buyer_phone'],
            'saleDate' => $this->toDateBr($arrSale['created_at']),
            'seller' => $arrSale['seller']
        ];

        return view('adm.teams.raffle', [
            'filter' => $params,
            'winner' => $winner
        ]);
    }
}
