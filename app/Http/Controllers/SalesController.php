<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $filter = [];
        if(!empty($request->buyer)) {
            $filter['buyer'] = $request->buyer;
        }
        if(!empty($request->seller)) {
            $filter['seller'] = $request->seller;
        }

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

        $this->arrVendedores = $this->getVendedores($request->user()->id);

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
        $saleData['seller'] = $this->getVendedor($saleData['user_id'])->name;
        
        $bilhetes = $saleData['amount_paid'];
        $ticket = $this->getTicketNumber($saleData);
        $sales = [];
        for ($i = 0; $i < $bilhetes; $i++) {
            $saleData['amount_paid'] = 12;
            $saleData['ticket_number'] = $ticket;
            
            $obj = Sale::create($saleData);
            $obj->refresh();
            $ticket++;

            array_push($sales, $obj->toArray());
        }

        return view('coordenador.sale_ticket', ['sales' => $sales]);
    }

    private function getTicketNumber($saleData)
    {
        $number = null;
        if ($saleData['payment_status'] === 'Pago' && empty($saleData['ticket_number'])) {
            while (!$number) {
                $number = Sale::getLastTicket() + 1;
            }
        }
        return $number;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Sales  $sales
     * @return \Illuminate\Http\Response
     */
    public function show(Sale $sale)
    {
        $sales = [];
        array_push($sales, $sale->toArray());
        return view('coordenador.sale_ticket', ['sales' => $sales ]);
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

        $this->arrVendedores = $this->getVendedores($request->user()->id);

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
        $saleData['seller'] = $this->getVendedor($saleData['user_id'])->name;
        $saleData['payment_status'] = $sale->payment_status;

        $bilhetes = $saleData['amount_paid'];
        $ticket = $this->getTicketNumber($saleData);
        for ($i = 0; $i < $bilhetes; $i++) {
            $saleData['amount_paid'] = 12;
            $saleData['ticket_number'] = $ticket;

            $sale->update($saleData);
            $ticket++;
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
}
