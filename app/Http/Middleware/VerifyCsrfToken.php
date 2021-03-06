<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'myzap/start',
        'myzap/check-state/*',
        'myzap/send-ticket/*',
        'myzap/store-billet',
        'myzap/webhook',
        'myzap/close',
        'team/get-performance',
        'team/send-ticket-batch'
    ];
}
