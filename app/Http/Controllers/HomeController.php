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

        $dashInfo = $this->getHeadDashInfo($request);
        $headAlert = $dashInfo;

        if ($request->user()->type == 'Administrador') {
            $headAlert = $this->getInfoSales();
            return view('adm.home', ['headAlert' => $headAlert]);
        }

        return view('home', ['headAlert' => $headAlert]);
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
        $confirmedSales = Sale::whereNotNull('amount_paid')
            ->whereNotNull('user_id')
            ->select("id")
            ->get();

        $teamSales = Sale::getSalesPerTeam($request->user()->id);
        $headSales = Sale::getSalesOfHead($request->user()->id);
        $totalEquipe = 0;
        if (!empty($teamSales[0]->vendas)) {
            $totalEquipe = $teamSales[0]->vendas;
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
        $retorno['metas'] = [
            'adm' => $calendarAdm->toArray(),
            'team' => $calendarTeam->toArray(),
        ];

        $retorno['metas']['team']['billets_actual'] = $this->teamPerformanceCalculate($request->user());

        return $retorno;
    }

    private function getInfoSales()
    {

        $salesModel = new Sale();
        $retorno['sales'] = $salesModel->getInfoSalesToAdm();

        $calendarIds = [env('SEMANA_ATUAL_SELLER'), env('SEMANA_ATUAL_HEAD'), env('SEMANA_ATUAL_ADM')];
        $calendar = Calendar::where('is_active', true)
            ->whereIn('id', $calendarIds)
            ->get();

        $metas = $calendar->toArray();

        $retorno['metas'] = [
            'seller' => $metas[0],
            'head' => $metas[1],
            'adm' => $metas[2]
        ];

        $teamsSales = $salesModel->getSalesPerTeam(2);
        $arrChatInfo = [];
        foreach ($teamsSales as $key => $team) {
            $arrChatInfo[] = [
                'team' => $team->head,
                'goal' => $metas[1]['billets_goal'],
                'sales' => $team->vendas
            ];
        }

        $retorno['desempenho'] = $arrChatInfo;

        return $retorno;
    }

    private function teamPerformanceCalculate($head)
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
            ->where('id', $head->id)
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
        
        $totalTeam += $this->getSalesHeadPerPeriod($head->toArray(), $calendarTeam);

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
}
