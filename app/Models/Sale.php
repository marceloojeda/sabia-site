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

    public function getTeamSalesPerPeriod($beginAt, $finishAt, $headId)
    {
        $sales = Sale::join('users', 'sales.user_id', 'users.id')
            ->where('users.head_id', $headId)
            ->where('sales.created_at', '>=', $beginAt)
            ->where('sales.created_at', '<=', $finishAt)
            ->select('sales.id')
            ->get();

        return !$sales ? 0 : sizeof($sales->toArray());
    }
}
