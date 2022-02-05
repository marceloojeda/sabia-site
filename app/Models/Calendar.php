<?php

namespace App\Models;

use App\Models\Traits\DateFormat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Calendar extends Model
{
    use SoftDeletes, DateFormat;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'type',
        'audience',
        'begin_at',
        'finish_at',
        'status',
        'billets_old',
        'billets_actual',
        'billets_goal',
        'is_active'
    ];

    protected $dates = ['deleted_at', 'begin_at', 'finish_at'];
    protected $casts = ['is_active' => 'boolean'];
}
