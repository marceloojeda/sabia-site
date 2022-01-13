<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesController extends BaseController
{

    public function __construct()
    {
        parent::__construct(true);
    }

    private function rulesStore()
    {
        return [
            'user_id' => 'required|exists:users,id',
            'buyer' => 'required|max:255',
            'buyer_email' => 'email',
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
            'buyer_email' => 'email',
            'amount_paid' => 'required|numeric'
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $results = DB::table('sales')->where('user_id', env('SELLER_TEST', 3));
        if ($request->has('trashed')) {
            $results->withTrashed();
        }
        $sales = $results
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('coordenador.sales_index', compact('sales'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $saleObj = new Sale();

        $saleData = $saleObj->getFillable();
        $saleData['user_id'] = 5;
        $saleData['is_ecommerce'] = false;
        $saleData['seller'] = 'Prof. Domingo Hilpert';
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
        $request['is_ecommerce'] = strval($request['is_ecommerce']) === "true";
        $this->validate($request, $this->rulesStore());

        $saleData = $request->except(['_token']);
        $saleData['seller'] = $this->getVendedor($saleData['user_id'])->name;
        $this->setTicketNumber($saleData);

        $obj = Sale::create($saleData);
        return redirect('/sales');
    }

    private function setTicketNumber(&$saleData)
    {
        if ($saleData['payment_status'] === 'Pago' && empty($saleData['ticket_number'])) {
            $numberValid = false;
            while (!$numberValid) {
                $number = $this->generateTicketNumber();
                if ($numberValid = Sale::checkTicket($number)) {
                    $saleData['ticket_number'] = $number;
                }
            }
        }
    }

    private function generateTicketNumber()
    {
        $max = env('TICKET_NUMBER_MAX', 2200);
        $numbersGeral = [];
        for ($i = 1; $i <= $max; $i++) {
            array_push($numbersGeral, $i);
        }

        $generatedNumbers = Sale::getGeneratedNumbers();
        if(!$generatedNumbers) {
            $indexRand = array_rand($numbersGeral);
            return $numbersGeral[$indexRand];
        }

        $arrDiff = array_diff($numbersGeral, array_column($generatedNumbers, 'ticket_number'));
        $indexRand = array_rand($arrDiff);
        return $arrDiff[$indexRand];
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Sales  $sales
     * @return \Illuminate\Http\Response
     */
    public function show(Sale $sale)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Sales  $sales
     * @return \Illuminate\Http\Response
     */
    public function edit(Sale $sale)
    {
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
        $this->setTicketNumber($saleData);

        $sale->update($saleData);
        return redirect('/sales');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Sales  $sales
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sales $sales)
    {
        //
    }
}
