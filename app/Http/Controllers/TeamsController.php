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

        $team = User::with('sales')
            ->where('head_id', $request->user()->id)
            ->where('type', 'Vendedor')
            ->orderBy('name')
            ->get()
            ->toArray();

        foreach ($team as $key => $member) {
            $team[$key]['phone'] = str_replace('_', '', $member['phone']);
        }

        return view('coordenador.team.index', compact('team'));
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

        $sql = <<<EOF
        select count(s.id) as billets, u.id, u.name, u.phone, h.name as head
        from users u 
        join sales s on u.id = s.user_id
        join users h on u.head_id = h.id 
        group by u.id, u.name, u.phone, h.name
        order by h.name, u.name;
EOF;

        $teams = DB::select($sql);

        return view('adm.teams.index', compact('teams'));
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
            if(!$user) {
                $hasUnique = true;
            } else {
                $phone .= '_';
                $email = $phone . '_head_' . $headId . '@sabia.in';
            }
        }
        
        return $email;
    }
}
