<?php

namespace App\Models\Traits;

use Illuminate\Support\Carbon;

trait DateFormat
{
    public function getCreatedAtAttribute($date)
    {
        return (new Carbon($date))->format('Y-m-d H:i:s');
    }

    public function getUpdatedAtAttribute($date)
    {
        return (new Carbon($date))->format('Y-m-d H:i:s');
    }

    public function getDeletedAtAttribute($date)
    {
        if ($date) {
            return (new Carbon($date))->format('Y-m-d H:i:s');
        }
    }
}
