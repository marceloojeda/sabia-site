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
        // $number = $this->getNumberAvailable();
        // if (!$number && $saleData['payment_status'] === 'Pago' && empty($saleData['ticket_number'])) {
        //     while (!$number) {
        //         $number = Sale::getLastTicket() + 1;
        //     }
        // }

        $number = Sale::getLastTicket() + 1;
        return $number;
    }

    private function getNumberAvailable()
    {
        $arrNumbers = Sale::getNumberAvailable();

        if(empty($arrNumbers)) {
            return null;
        }

        $index = array_rand($arrNumbers);
        $number = $arrNumbers[$index]->number;
        $id = $arrNumbers[$index]->id;

        DB::update('update billets_control set available = ?, updated_at = ? where id = ? and number = ?', [false, date('Y-m-d H:i:s'), $id, $number ]);

        return $number;
    }

    private function setSaleInNumberAvailable($saleId, $number)
    {
        $numberAvailable = DB::table('billets_controls')->where('number', $number)->where('available', false);
        if($numberAvailable) {
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

        $bilhetes = $saleData['amount_paid'];
        $ticket = $sale->ticket_number ?? $this->getTicketNumber($saleData);
        for ($i = 0; $i < $bilhetes; $i++) {
            $saleData['amount_paid'] = 12;
            $saleData['ticket_number'] = $ticket;

            $sale->update($saleData);
            // $ticket++;
            $this->setSaleInNumberAvailable($sale->id, $ticket);
        }

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
        for ($i = 1; $i <= 2816; $i++) {
            $sale = Sale::where('payment_status', 'Pago')
                ->whereNotNull('amount_paid')
                ->whereNotNull('user_id')
                ->where('ticket_number', $i)
                ->select('id')
                ->first();

            if(!$sale) {
                array_push($numbersAvailable, $i);
            }
        }

        $this->makeBillets($numbersAvailable);

        return response("Numeros disponiveis atualizados");
    }

    private function makeBillets(array $numbersAvailable)
    {
        foreach ($numbersAvailable as $number) {
            $result = DB::insert('insert into billets_control (number) values (?)', [$number]);
        }
    }
}
