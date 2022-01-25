<?php

namespace App\Http\Controllers;

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

        $pendingSales = $this->getPendingSales($request->user()->id);
        $headAlert = [
            'pending-sales' => [
                'total' => sizeof($pendingSales),
                'sales' => $pendingSales
            ]
        ];

        return view('home', ['headAlert' => $headAlert]);
    }

    private function getPendingSales($headId)
    {
        $sales = Sale::whereNull('user_id')
            ->whereNotNull('amount_paid')
            ->where('payment_status', 'Pago')
            ->select(['id', 'payment_method', 'buyer', 'buyer_phone', 'created_at'])
            ->orderByDesc('created_at')
            ->get();
        
        return $sales->toArray();
    }
}
