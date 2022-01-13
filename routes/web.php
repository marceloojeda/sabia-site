<?php

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

Route::resource('sales', 'SalesController');
Route::post('/wix/callback', 'SalesController@callbackWix');

Route::get('/coordenador', function (Request $request) {
    return view('coordenador.index');
});
