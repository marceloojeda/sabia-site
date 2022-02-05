<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;


Auth::routes();
Route::resource('sales', 'SalesController');
Route::post('sales/filtered', 'SalesController@index');

Route::resource('calendars', 'CalendarsController');
Route::resource('teams', 'TeamsController');

Route::get('/', function() {
    return response()->redirectToIntended('/home');
});
Route::get('/home', 'HomeController@index')->name('home');

Route::get('/myzap/start', 'MyzapController@start');
Route::get('/myzap/qrcode/{session}', 'MyzapController@getQrCode');
Route::get('/myzap/send-ticket/{sale}', 'MyzapController@sendTicket');
Route::post('/myzap/store-billet', 'MyzapController@storeBillet');

Route::get('/adm/teams', 'TeamsController@indexToAdm');