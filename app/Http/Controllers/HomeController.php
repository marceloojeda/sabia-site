<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->middleware('auth');

        parent::__construct();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        if (!$this->validatePerfil($request->user())) {
            Auth::logout();
            return redirect('/home');
        }

        if ($request->user()->type == 'Administrador') {
            $dashData = $this->initAdmDashboard($request);
            return view('adm.home', compact('dashData'));
        }

        $dashInfo = $this->getHeadDashInfo($request);
        $headAlert = $dashInfo;

        return view('home', ['headAlert' => $headAlert]);
    }

    private function initAdmDashboard(Request $request)
    {
        $totalSales = Sale::where('payment_status', 'Pago')
            ->whereNotNull('amount_paid')
            ->whereNotNull('user_id')
            ->whereNotNull('ticket_number')
            ->get('id');

        $semanaId = env('SEMANA_ATUAL_ADM');
        $calendarAdm = Calendar::where('is_active', true)
            ->where('id', $semanaId)
            ->first();

        $totalSalesWeek = Sale::getTotalSalesPerPeriod($calendarAdm->begin_at, $calendarAdm->finish_at);
        $percSalesWeek = $totalSalesWeek / $calendarAdm->billets_goal * 100;

        $dashData = [
            'totalSales' => sizeof($totalSales->toArray()),
            'totalSalesWeek' => $totalSalesWeek,
            'percSalesWeek' => $percSalesWeek,
            'calendar' => $calendarAdm->toArray()
        ];

        return $dashData;
    }

    private function getHeadDashInfo(Request $request)
    {
        $pendingSalesQuery = Sale::whereNull('user_id')
            ->whereNotNull('amount_paid')
            ->where('payment_status', 'Pago')
            ->select(['id', 'payment_method', 'buyer', 'buyer_phone', 'created_at'])
            ->orderByDesc('created_at')
            ->get();

        $pendingSales = $pendingSalesQuery->toArray();

        $retorno = [
            'pending-sales' => [
                'total' => sizeof($pendingSales),
                'sales' => $pendingSales
            ]
        ];

        $sales = Sale::select("id")->get();
        $confirmedSales = Sale::where('payment_status', 'Pago')
            ->whereNotNull('amount_paid')
            ->whereNotNull('user_id')
            ->whereNotNull('ticket_number')
            ->select("id")
            ->get();

        $teamSales = Sale::getSalesPerTeam($request->user()->id);
        $headSales = Sale::getSalesOfHead($request->user()->id);
        
        $totalEquipe = 0;
        if (!empty($teamSales[0]->vendas)) {
            $totalEquipe = array_sum(array_column($teamSales, 'vendas'));
        }
        if (!empty($headSales[0]->vendas)) {
            $totalEquipe += $headSales[0]->vendas;
        }

        $retorno['totais'] = [
            'geral' => sizeof($sales),
            'confirmados' => sizeof($confirmedSales),
            'equipe' => $totalEquipe
        ];

        $semanaId = env('SEMANA_ATUAL_ADM');
        $calendarAdm = Calendar::where('is_active', true)
            ->where('id', $semanaId)
            ->first();

        $semanaId = env('SEMANA_ATUAL_HEAD');
        $calendarTeam = Calendar::where('is_active', true)
            ->where('id', $semanaId)
            ->first();

        $semanaId = env('SEMANA_ATUAL_SELLER');
        $calendarSeller = Calendar::where('is_active', true)
            ->where('id', $semanaId)
            ->first();

        $acumulado = $this->getAcumuladoMetas($request);
            
        $retorno['metas'] = [
            'adm' => $calendarAdm->toArray(),
            'team' => $calendarTeam->toArray(),
            'seller' => $calendarSeller->toArray(),
            'accumulated' => $acumulado
        ];

        $retorno['metas']['team']['billets_actual'] = $this->teamPerformanceCalculate($request->user());

        return $retorno;
    }

    private function teamPerformanceCalculate($headAuthenticated)
    {
        $semanaId = env('SEMANA_ATUAL_HEAD');
        $calendarTeam = Calendar::where('is_active', true)
            ->where('id', $semanaId)
            ->first();
        
        if(!$calendarTeam) {
            return;
        }
        
        $heads = User::where('type', 'Coordenador')
            ->where('is_active', true)
            ->where('id', $headAuthenticated->id)
            ->get();

        $arrHeads = $heads->toArray();
        $totalTeam = 0;
        foreach ($arrHeads as $head) {
            $team = User::where('type', 'Vendedor')
                ->where('is_active', true)
                ->where('head_id', $head['id'])
                ->get();

            $totalTeam = $this->getSalesTeamPerPeriod($team->toArray(), $calendarTeam);
        }
        
        $totalTeam += $this->getSalesHeadPerPeriod($headAuthenticated->toArray(), $calendarTeam);

        return $totalTeam;
    }

    private function getSalesTeamPerPeriod(array $team, Calendar $calendar)
    {
        $saleModel = new Sale();
        $total = 0;
        foreach ($team as $seller) {
            $total += $saleModel->getTeamSalesPerPeriod($calendar->begin_at, $calendar->finish_at,  $seller['id']);
        }

        return $total;
    }

    private function getSalesHeadPerPeriod(array $head, Calendar $calendar)
    {
        $saleModel = new Sale();
        $total = $saleModel->getTeamSalesPerPeriod($calendar->begin_at, $calendar->finish_at,  $head['id']);

        return $total;
    }

    private function getAcumuladoMetas(Request $request, $isMetaTeams = true)
    {
        $semanaId = env('SEMANA_ATUAL_HEAD');
        $weeks = Calendar::where('is_active', true)
            ->where('audience', 'Coordenadores')
            ->where('id', '<=', $semanaId)
            ->get();

        $team = $this->getTeam($request);

        $acumulado = [
            'meta' => 0,
            'realizado' => 0
        ];
        foreach ($weeks as $week) {
            $weekSales = Sale::where('payment_status', 'Pago')
                ->where('created_at', '>=', $week->begin_at)
                ->where('created_at', '<=', $week->finish_at)
                ->whereNotNull('amount_paid')
                ->whereNotNull('user_id')
                ->whereNotNull('ticket_number')
                ->select(['id', 'user_id'])
                ->get();
            
            $acumulado['meta'] += intval($week->billets_goal);
            $acumulado['realizado'] += intval($this->getTotalSales($team, $weekSales->toArray()));
        }

        return $acumulado;
    }

    private function getTeam(Request $request)
    {
        $model = new User();
        $arrSellers = $model->getTeam($request->user());

        return $arrSellers;
    }

    private function getTotalSales(array $arrSellers, array $weekSales)
    {
        $arrSellerIds = array_column($arrSellers, 'id');
        $total = 0;
        foreach ($weekSales as $sale) {
            if(!in_array($sale['user_id'], $arrSellerIds)) {
                continue;
            }
            $total++;
        }

        return $total;
    }
}
