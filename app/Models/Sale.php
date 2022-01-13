<?php

namespace App\Models;

use App\Models\Traits\DateFormat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        'payment_date'
    ];

    protected $dates = ['deleted_at', 'payment_date', 'deleted_at'];

    public function seller()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public static function checkTicket($number)
    {
        $sale = Sale::where('ticket_number', $number)->first();

        return empty($sale);
    }

    public static function getGeneratedNumbers()
    {
        $tickets = Sale::whereNotNull('ticket_number')->get(['ticket_number']);

        return $tickets ? $tickets->toArray() : [];
    }
}
