<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TeamsController extends BaseController
{
    public function __construct()
    {
        parent::__construct(true);

        $this->middleware('auth');
    }

    private function checkPerfilUsuario(Request $request)
    {
        if (!$this->validatePerfil($request->user())) {
            Auth::logout();
            return redirect('/home');
        }
    }

    public function index(Request $request)
    {
        $this->checkPerfilUsuario($request);

        $userModel = new User();
        $team = $userModel->getTeam($request->user());

        foreach ($team as $key => $member) {
            $team[$key]['phone'] = str_replace('_', '', $member['phone']);
            $team[$key]['sales'] = $this->getTotalVendas($member['id']);
        }

        return view('coordenador.team.index', compact('team'));
    }

    private function getTotalVendas($userId)
    {
        $sales = Sale::where('user_id', $userId)
            ->whereNotNull('amount_paid')
            ->where('payment_status', 'Pago')
            ->select('id')
            ->get();

        if(!$sales) {
            return 0;
        }

        return sizeof($sales->toArray());
    }

    public function create(Request $request)
    {
        $this->checkPerfilUsuario($request);

        return view('coordenador.team.create');
    }

    public function store(Request $request)
    {
        $this->checkPerfilUsuario($request);

        $this->validate($request, [
            'name' => 'required|max:255',
            'phone' => 'required|max:15'
        ]);

        $phone = $this->somenteNumeros($request->phone);
        $userData = [
            'head_id' => $request->user()->id,
            'type' => 'Vendedor',
            'name' => $request->name,
            'phone' => $this->checkTelefone($request->phone),
            'email' => $this->checkSellerEmail($phone, $request->user()->id),
            'password' => Hash::make('password'),
            'is_active' => true,
            'email_verified_at' => date('Y-m-d H:i:s')
        ];

        User::create($userData);

        return redirect('/teams');
    }

    public function edit(Request $request, $user)
    {
        $this->checkPerfilUsuario($request);

        $user = User::where('id', $user)->firstOrFail();

        return view('coordenador.team.edit', compact('user'));
    }

    public function update(Request $request, $user)
    {
        $this->checkPerfilUsuario($request);

        $this->validate($request, [
            'name' => 'required|max:255',
            'phone' => 'required|max:15'
        ]);

        $user = User::where('id', $user)->firstOrFail();
        $userData = [
            'name' => $request->name,
            'phone' => $this->checkTelefone($request->phone)
        ];

        $user->update($userData);

        return redirect('/teams');
    }

    public function indexToAdm(Request $request)
    {
        $this->checkPerfilUsuario($request);

        $heads = User::where('type', 'Coordenador')
            ->where('is_active', true)
            ->get();

        $teams = [];
        foreach ($heads as $k => $head) {
            $arrName = explode(" ", $head->name);
            $squad = [
                'head' => 'Coordenador: ' . strtoupper($arrName[0]),
                'billets' => 0,
                'team' => []
            ];
            
            $arrHead = [
                'id' => $head->id,
                'name' => $head->name,
                'phone' => $head->phone,
                'billets' => 0,
            ];
            if ($head->sales) {
                $arrHead['billets'] = sizeof($head->sales);
                $squad['billets'] = sizeof($head->sales);
            }
            $squad['team'][] = $arrHead;

            $squad['billets'] += $this->getTeamFromHead($head->id, $squad['team']);
            
            array_push($teams, $squad);
        }

        return view('adm.teams.index', compact('teams'));
    }

    private function getTeamFromHead($headId, &$teams)
    {
        $sellers = User::where('type', 'Vendedor')
            ->where('is_active', true)
            ->where('head_id', $headId)
            ->get();

        $billets = 0;
        foreach ($sellers as $k => $seller) {
            $arrSeller = [
                'id' => $seller->id,
                'name' => $seller->name,
                'phone' => $seller->phone,
                'billets' => 0,
            ];
            if ($seller->sales) {
                $arrSeller['billets'] = sizeof($seller->sales);
            }

            array_push($teams, $arrSeller);
            $billets += intval($arrSeller['billets']);
        }

        return $billets;
    }

    private function checkTelefone($numeroComMascara)
    {
        $novoNumero = $numeroComMascara;
        while ($user = User::where('phone', $novoNumero)->first()) {
            $novoNumero .= "_";
        }

        return $novoNumero;
    }

    private function checkSellerEmail($phone, $headId)
    {
        $hasUnique = false;
        $email = $phone . '_head_' . $headId . '@sabia.in';
        while (!$hasUnique) {
            $user = User::where('email', $email)->first();
            if (!$user) {
                $hasUnique = true;
            } else {
                $phone .= '_';
                $email = $phone . '_head_' . $headId . '@sabia.in';
            }
        }

        return $email;
    }

    public function removeSeller(User $user)
    {
        $user->update(['is_active' => false]);
        return redirect('/teams');
    }

    public function getPerformance(Request $request)
    {
        $this->checkPerfilUsuario($request);

        $semanaId = env('SEMANA_ATUAL_SELLER');
        $calendarTeam = Calendar::where('is_active', true)
            ->where('id', $semanaId)
            ->first();
        
        $meta = 0;
        if($calendarTeam) {
            $meta = $calendarTeam->billets_goal;
        }

        $retorno = [];
        $retorno[] = [
            'seller' => $request->user()->name,
            'vendas' => $this->getTotalSalesFromSeller($request->user()->id),
            'meta' => $meta
        ];
        
        $team = User::where('is_active', true)
            ->where('head_id', $request->user()->id)
            ->get();

        foreach ($team as $member) {
            $retorno[] = [
                'seller' => $member->name,
                'vendas' => $this->getTotalSalesFromSeller($member->id),
                'meta' => $meta
            ];
        }

        return response()->json($retorno);
    }

    private function getTotalSalesFromSeller($userId)
    {
        $sales = Sale::where('user_id', $userId)
            ->whereNotNull('amount_paid')
            ->where('payment_status', 'Pago')
            ->select('id')
            ->get();

        return !$sales ? 0 : sizeof($sales->toArray());
    }

    public function sendTicketsBatch(Request $request)
    {
        $this->checkPerfilUsuario($request);

        $arrSalesId = explode('|', $request->sales);

        $arrSales = [];
        foreach ($arrSalesId as $saleId) {
            $arrSale = Sale::where('id', $saleId)->first()->toArray();
            $arrSale['ticket_number'] = str_pad(strval($arrSale['ticket_number']), 4, '0', STR_PAD_LEFT);

            array_push($arrSales, $arrSale);
        }

        $head = $request->user();
        $headPhone = $head->phone;
        $headPhone = preg_replace("/[^0-9]/", "", $headPhone);

        return view('coordenador.sale_ticket', ['sales' => $arrSales, 'session' => $headPhone]);
    }

    public function salesOfSeller(Request $request, User $user)
    {
        $saleModel = new Sale();

        $sales = $saleModel->getSellerSales($user->id);
        foreach ($sales as $k => $sale) {
            $sales[$k]['created_at'] = date('d/m/Y H:i', strtotime($sale['created_at']));
            $sales[$k]['ticket_number'] = str_pad($sale['ticket_number'], 4, '0', STR_PAD_LEFT); 
        }

        return view('adm.teams.sales_seller', compact('sales'));
    }

    public function salesOfMember(Request $request, User $user)
    {
        $saleModel = new Sale();

        $sales = $saleModel->getSellerSales($user->id);
        foreach ($sales as $k => $sale) {
            $sales[$k]['created_at'] = date('d/m/Y H:i', strtotime($sale['created_at']));
            $sales[$k]['ticket_number'] = str_pad($sale['ticket_number'], 4, '0', STR_PAD_LEFT); 
        }

        return view('coordenador.team.sales_member', compact('sales'));
    }
}
