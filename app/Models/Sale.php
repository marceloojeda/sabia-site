<?php

namespace App\Models;

use App\Models\Traits\DateFormat;
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

    public function getSquadSales(Request $request)
    {
        $head = $request->user();

        $results = DB::table('sales')
            ->join('users', 'sales.user_id', 'users.id')
            ->where('users.head_id', $head->id)
            ->orWhere('user_id', $head->id);

        if ($request->has('trashed')) {
            $results->withTrashed();
        }

        if(!empty($request->buyer)) {
            $results->where('buyer', 'like', '%'.$request->buyer.'%');
        }
        if(!empty($request->seller)) {
            $results->where('seller', 'like', '%'.$request->seller.'%');
        }
        $sales = $results
            ->select(['sales.*'])
            ->orderByDesc('sales.id')
            ->paginate();

        return $sales;
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
        select count(s.id) as vendas, u.name as seller
        from sales s join users u on s.user_id = u.id 
        where s.user_id is not null
        and s.amount_paid is not null
        and s.payment_status = 'Pago'
        and s.user_id = $headId
        group by u.name;
EOF;

        return DB::select($sql);
    }
}
