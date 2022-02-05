<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

abstract class BaseController extends Controller
{
    protected array $arrFormasPagamento, $arrStatusPagamento, $arrVendedores;
    protected $user;

    public function __construct($withSellers = false)
    {
        $this->arrFormasPagamento = Config::get('constants.PAYMENT_METHOD', ['Cartão Crédito', 'Boleto Bancário', 'Boleto Mensalidade', 'Pix', 'Outro']);
        $this->arrStatusPagamento = Config::get('constants.PAYMENT_STATUS', ['Pago', 'Não Pago', 'Pagamento Rejeitado', 'Pagamento Cancelado', 'Outro']);

        if ($withSellers) {
            $this->arrVendedores = $this->getVendedores();
        }
    }

    protected function validatePerfil($user, $isAdmin = false)
    {
        $perfil = $user->type;

        if($isAdmin) {
            return $perfil == 'Administrador';
        }

        return in_array($perfil, ['Coordenador', 'Administrador']);
    }

    protected function getVendedores($headId = null)
    {
        $query = DB::table('users')
            ->where('is_active', true)
            ->where('type', 'Vendedor');

        if($headId) {
            $query->where('head_id', $headId);
        }
        
        $vendedores = $query
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return $vendedores->toArray();
    }

    protected function getVendedor($vendedorId)
    {
        if (!$this->arrVendedores) {
            $this->arrVendedores = $this->getVendedores();
        }

        $retorno = [];
        foreach ($this->arrVendedores as $vendedor) {
            if ($vendedor->id != $vendedorId) {
                continue;
            }

            $retorno = $vendedor;
            break;
        }

        return $retorno;
    }

    protected function somenteNumeros($str)
    {

        return preg_replace("/[^0-9]/", "", $str);
    }

    protected function toDateBr($data = '')
    {
        if (!empty($data) and $data != '0000-00-00') {

            list($ano, $mes, $dia) = explode('-', substr(@$data, 0, 10));
            return $dia . '/' . $mes . '/' . $ano;
        }
        return 0;
    }

    protected function toDate($data = '')
    {
        list($dia, $mes, $ano) = explode('/', @$data);
        return $ano . '-' . $mes . '-' . $dia;
    }
}
