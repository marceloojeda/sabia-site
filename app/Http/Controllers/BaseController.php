<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class BaseController extends Controller
{
    protected array $arrFormasPagamento, $arrStatusPagamento, $arrVendedores;

    public function __construct($withSellers = false)
    {
        $this->arrFormasPagamento = Config::get('constants.PAYMENT_METHOD', ['Cartão Crédito', 'Boleto Bancário', 'Boleto Mensalidade', 'Pix', 'Outro']);
        $this->arrStatusPagamento = Config::get('constants.PAYMENT_STATUS', ['Pago', 'Não Pago', 'Pagamento Rejeitado', 'Pagamento Cancelado', 'Outro']);

        if($withSellers) {
            $this->arrVendedores = $this->getVendedores();
        }
    }

    protected function getVendedores()
    {
        $vendedores = DB::table('users')
            ->where('is_active', true)
            ->where('type', 'Vendedor')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return $vendedores->toArray();
    }

    protected function getVendedor($vendedorId)
    {
        if(!$this->arrVendedores) {
            $this->arrVendedores = $this->getVendedores();
        }

        $retorno = [];
        foreach ($this->arrVendedores as $vendedor) {
            if($vendedor->id != $vendedorId) {
                continue;
            }

            $retorno = $vendedor;
            break;
        }

        return $retorno;
    }
}
