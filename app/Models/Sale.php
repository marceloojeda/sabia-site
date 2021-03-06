<?php

namespace App\Models;

use App\Models\Traits\DateFormat;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Sale extends Model
{
    use SoftDeletes, DateFormat;

    protected $fillable = [
        'user_id',
        'is_ecommerce',
        'seller',
        'payment_method',
        'amount',
        'amount_paid',
        'buyer',
        'buyer_email',
        'buyer_phone',
        'ticket_number',
        'payment_status',
        'payment_date',
        'billet_file'
    ];

    protected $dates = ['deleted_at', 'payment_date', 'deleted_at'];

    public function vendedor()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * @param Request $request
     * @return Paginator
     */
    public function getSquadSales(Request $request)
    {
        
        $arrIds = $this->getSquadIds($request->user());

        $results = DB::table('sales')->where('payment_status', 'Pago')
            ->whereNotNull('amount_paid')
            ->whereIn('user_id', $arrIds);

        if ($request->has('trashed')) {
            $results->withTrashed();
        }

        return $results->orderByDesc('sales.id')->paginate();
    }

    public function getSquadSalesFiltered(Request $request)
    {
        $arrIds = $this->getSquadIds($request->user());

        $sales = Sale::where('payment_status', 'Pago')
            ->whereNotNull('amount_paid')
            ->whereIn('user_id', $arrIds);

        if ($request->has('trashed')) {
            $sales->withTrashed();
        }

        // Aplicação do filtro
        if(!empty($request->buyer)) {
            $sales->where('buyer', 'like', '%'.$request->buyer.'%');
        }
        if(!empty($request->seller)) {
            $sales->where('seller', 'like', '%'.$request->seller.'%');
        }

        return $sales->orderByDesc('sales.id')->get();
    }

    private function getSquadIds(User $head)
    {
        $userModel = new User();
        $squad = $userModel->getTeam($head);
        
        return array_column($squad, "id");
    }

    public static function checkTicket($number)
    {
        $sale = Sale::where('ticket_number', $number)->first();

        return empty($sale);
    }

    public static function getLastTicket()
    {
        $ticket = DB::table('sales')->max('ticket_number');

        if(!$ticket) {
            return 0;
        }

        return $ticket;

    }

    public function getSellerSalesPerPeriod($beginAt, $finishAt, $userId)
    {
        $sales = Sale::where('user_id', $userId)
            ->where('created_at', '>=', $beginAt)
            ->where('created_at', '<=', $finishAt)
            ->select('id')
            ->toSql();

        return !$sales ? 0 : sizeof($sales->toArray());
    }

    public function getTeamSalesPerPeriod($beginAt, $finishAt, $userId)
    {
        $sales = Sale::where('user_id', $userId)
            ->where('created_at', '>=', $beginAt)
            ->where('created_at', '<=', $finishAt)
            ->whereNotNull('amount_paid')
            ->where('payment_status', 'Pago')
            ->select('sales.id')
            ->get();

        return !$sales ? 0 : sizeof($sales->toArray());
    }

    public static function getTotalSalesPerPeriod($beginAt, $finishAt)
    {
        $sales = Sale::where('created_at', '>=', $beginAt)
            ->where('created_at', '<=', $finishAt)
            ->whereNotNull('user_id')
            ->whereNotNull('amount_paid')
            ->whereNotNull('ticket_number')
            ->where('payment_status', 'Pago')
            ->select('sales.id')
            ->get('id');

        return !$sales ? 0 : sizeof($sales->toArray());
    }

    public function getInfoSalesToAdm()
    {
        $result = DB::table('sales')
            ->select(DB::raw("COUNT(*) as total"))
            ->get('total');

        $totalGeral = $result->toArray()[0]->total ?? 0;

        $result = DB::table('sales')
            ->select(DB::raw("COUNT(*) as total"))
            ->whereNotNull('amount_paid')
            ->whereNotNull('user_id')
            ->get('total');

        $totalConfirmados = $result->toArray()[0]->total ?? 0;

        $result = DB::table('sales')
            ->select(DB::raw("COUNT(*) as total"))
            ->whereNull('amount_paid')
            ->whereNull('user_id')
            ->get('total');

        $totalPendentes = $result->toArray()[0]->total ?? 0;

        return [
            'geral' => $totalGeral,
            'confirmados' => $totalConfirmados,
            'pendentes' => $totalPendentes
        ];
    }

    public static function getSalesPerTeam($headId)
    {
        $sql = <<<EOF
        select count(s.id) as vendas, u.name as seller
        from sales s join users u on s.user_id = u.id 
        join users h on u.head_id = h.id 
        where s.user_id is not null
        and s.ticket_number is not null
        and s.amount_paid is not null
        and s.payment_status = 'Pago'
        and u.head_id = $headId
        group by u.name;
EOF;

        return DB::select($sql);
    }

    public static function getSalesOfHead($headId)
    {
        $sql = <<<EOF
        select count(s.id) as vendas, s.seller as seller
        from sales s
        where s.amount_paid is not null
        and s.ticket_number is not null
        and s.payment_status = 'Pago'
        and s.user_id = $headId
        group by s.seller;
EOF;

        return DB::select($sql);
    }

    public function getSellerSales($userId)
    {
        $sales = Sale::where('user_id', $userId)
            ->whereNotNull('amount_paid')
            ->where('payment_status', 'Pago')
            ->orderBy('seller')
            ->orderBy('id')
            ->get();

        return $sales->toArray();
    }

    public function teamRanking(Calendar $calendar)
    {
        $heads = User::where('is_active', true)
            ->where('type', 'Coordenador')
            ->whereNotIn('id', [12,40])
            ->get();

        $userModel = new User();
        $retorno = [];
        foreach ($heads as $head) {
            $sellers = $userModel->getTeam($head);
            $arrSellerIds = array_column($sellers, 'id');
            
            $sales = Sale::where('payment_status', 'Pago')
                ->whereNotNull('amount_paid')
                ->whereNotNull('ticket_number')
                ->whereIn('user_id', $arrSellerIds)
                ->where('created_at', '>=', $calendar->begin_at)
                ->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($calendar->finish_at)))
                ->get('id');

            $headName = ucfirst(strtolower(explode(" ", $head->name)[0]));
            $retorno[] = [
                'head' => $headName,
                'sales' => sizeof($sales->toArray())
            ];
        }

        return $retorno;
    }

    public static function getNumberAvailable()
    {
        $numbers = DB::table('billets_control')
            ->where('available', true)
            ->select(['id', 'number'])
            ->get();

        return $numbers->toArray();
    }

    public function getDuplicateSales($headId)
    {
        $sql = <<<EOF
        select 
        s.id as sale_id, s.seller, s.buyer,
        s.ticket_number, s.created_at,
        DATE_FORMAT(s.created_at, '%d/%m/%Y') as sale_date,
        DATE_FORMAT(s.created_at, '%H:%i:%s') as sale_hour
        from sales s join users u on s.user_id = u.id 
        join users h on u.head_id = h.id 
        where s.user_id is not null
        and s.ticket_number is not null
        and s.amount_paid is not null
        and s.payment_status = 'Pago'
        and u.head_id = $headId
        order by s.id
EOF;

        return DB::select($sql);
    }
}
