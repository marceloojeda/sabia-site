<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use App\Models\Sale;
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

        if($request->user()->type == 'Administrador') {
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
        $retorno['totais'] = [
            'geral' => sizeof($sales),
            'confirmados' => sizeof($confirmedSales),
            'equipe' => $teamSales[0]->vendas ?? 0
        ];

        if($request->user()->type == 'Coordenador') {
            return $retorno;
        }

        $semanaId = env('SEMANA_ATUAL_HEAD');
        $calendar = Calendar::where('is_active', true)
            ->where('id', $semanaId)
            ->first();
        $retorno['metas'] = [
            'equipe' => $calendar->toArray()
        ];
        
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

    public function getDesempenho()
    {
        $salesModel = new Sale();
        $teamsSales = $salesModel->getSalesPerTeam();
        $arrChatInfo = [];
        foreach ($teamsSales as $key => $team) {
            $arrChatInfo[] = [
                'team' => $team->head,
                'goal' => 10,
                'sales' => $team->vendas
            ];
        }

        return response()->json($arrChatInfo);
    }
}
