<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarsController extends BaseController
{
    public function __construct()
    {
        parent::__construct(true);

        $this->middleware('auth');
    }

    private function checkPerfilUsuario(Request $request)
    {
        if (!$this->validatePerfil($request->user(), true)) {
            Auth::logout();
            return redirect('/home');
        }
    }

    private function rules()
    {
        return [
            'title' => 'required|max:255',
            'description' => 'required|max:255',
            'type' => 'required|in:Meta,Ação',
            'audience' => 'required|in:Administradores,Vendedores,Coordenadores,Geral',
            'begin_at' => 'required|min:10',
            'finish_at' => 'required|min:10',
            'billets_goal' => 'required|numeric'
        ];
    }

    public function index(Request $request)
    {
        $this->checkPerfilUsuario($request);

        // $this->updateStatus($request->user()->id);

        $query = Calendar::where('is_active', true);
        if ($request->has('trashed')) {
            $query->withTrashed();
        }
        $query->orderByDesc('begin_at');

        $calendars = $query->get()->toArray();

        return view('adm.calendar.index', compact('calendars'));
    }

    public function create(Request $request)
    {
        $this->checkPerfilUsuario($request);
        return view('adm.calendar.create');
    }

    public function store(Request $request)
    {
        $this->checkPerfilUsuario($request);

        $this->validate($request, $this->rules());

        $calendarData = $request->except('_token');
        $calendarData['begin_at'] = $this->toDate($calendarData['begin_at']);
        $calendarData['finish_at'] = $this->toDate($calendarData['finish_at']);
        $calendarData['status'] = 'Pendente';
        if (strtotime($calendarData['begin_at']) >= strtotime(date('Y-m-d'))) {
            $calendarData['status'] = 'Executando';
        }
        if (strtotime($calendarData['finish_at']) < strtotime(date('Y-m-d'))) {
            $calendarData['status'] = 'Terminado';
        }
        $calendarData['user_id'] = $request->user()->id;
        $calendarData['is_active'] = true;

        Calendar::create($calendarData);

        return redirect('/calendars');
    }

    public function show(Request $request, Calendar $calendar)
    {
        $calendarData = $calendar->toArray();
        $calendarData['begin_at'] = $this->toDateBr($calendar->begin_at);
        $calendarData['finish_at'] = $this->toDateBr($calendar->finish_at);

        return view('adm.calendar.show', ['event' => $calendarData]);
    }

    public function edit(Calendar $calendar)
    {
        $calendarData = $calendar->toArray();
        $calendarData['begin_at'] = $this->toDateBr($calendar->begin_at);
        $calendarData['finish_at'] = $this->toDateBr($calendar->finish_at);

        return view('adm.calendar.edit', ['event' => $calendarData]);
    }

    public function update(Request $request, Calendar $calendar)
    {
        $this->checkPerfilUsuario($request);

        $this->validate($request, $this->rules());

        $calendarData = $request->except('_token');
        $calendarData['begin_at'] = $this->toDate($calendarData['begin_at']);
        $calendarData['finish_at'] = $this->toDate($calendarData['finish_at']);
        $calendarData['status'] = 'Pendente';
        if (strtotime($calendarData['begin_at']) >= strtotime(date('Y-m-d'))) {
            $calendarData['status'] = 'Executando';
        }
        if (strtotime($calendarData['finish_at']) < strtotime(date('Y-m-d'))) {
            $calendarData['status'] = 'Terminado';
        }
        $calendarData['user_id'] = $request->user()->id;
        $calendarData['is_active'] = true;

        $calendar->update($calendarData);

        return redirect('/calendars');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Calendar  $calendar
     * @return \Illuminate\Http\Response
     */
    public function destroy(Calendar $calendar)
    {
        //
    }

    private function updateStatus($headId)
    {
        $actualDate = date('Y-m-d');

        $this->setFinishedEvents($actualDate, $headId);
        $this->setCurrentEvents($actualDate, $headId);
    }

    private function setFinishedEvents($actualDate, $headId)
    {
        $saleModel = new Sale();

        $events = Calendar::where('is_active', true)
            ->where('finish_at', '<', $actualDate)
            ->get();

        foreach ($events as $event) {
            $data = ['status' => 'Terminado'];
            
            if($event->audience == 'Vendedores') {
                $data['billets_actual'] = $saleModel->getSellerSalesPerPeriod(
                    $event->begin_at,
                    $event->finish_at,
                    $event->user_id);
            }
            if($event->audience == 'Coordenadores') {
                $data['billets_actual'] = $saleModel->getTeamSalesPerPeriod(
                    $event->begin_at,
                    $event->finish_at,
                    $headId);
            }

            $event->update($data);
            $event->refresh();
        }
    }

    private function setCurrentEvents($actualDate, $headId)
    {
        $saleModel = new Sale();

        $events = Calendar::where('is_active', true)
            ->where('begin_at', '>=', $actualDate)
            ->where('finish_at', '<=', $actualDate)
            ->get();

        foreach ($events as $event) {
            $data = ['status' => 'Executando'];
            
            if($event->audience == 'Vendedores') {
                $data['billets_actual'] = $saleModel->getSellerSalesPerPeriod(
                    $event->begin_at,
                    $event->finish_at,
                    $event->user_id);
            }
            if($event->audience == 'Coordenadores') {
                $data['billets_actual'] = $saleModel->getTeamSalesPerPeriod(
                    $event->begin_at,
                    $event->finish_at,
                    $headId);
            }
            $event->update(['status' => 'Executando']);
            $event->refresh();
        }
    }
}
