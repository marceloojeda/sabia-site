<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;


Auth::routes();
Route::resource('sales', 'SalesController');
Route::post('sales/filtered', 'SalesController@indexFiltered');

Route::resource('calendars', 'CalendarsController');
Route::resource('teams', 'TeamsController');
Route::get('/teams/{user}/remove', 'TeamsController@removeSeller');
Route::get('/teams/sales-seller/{user}', 'TeamsController@salesOfMember');

Route::get('/', function() {
    return response()->redirectToIntended('/home');
});
Route::get('/home', 'HomeController@index')->name('home');

Route::get('/myzap/start', 'MyzapController@start');
Route::get('/myzap/close/{session}', 'MyzapController@close');
Route::get('/myzap/check-state/{session}', 'MyzapController@checkState');
Route::get('/myzap/send-ticket/{sale}', 'MyzapController@sendTicket');
Route::post('/myzap/store-billet', 'MyzapController@storeBillet');

Route::post('/myzap/webhook', 'MyzapController@webhook');

Route::get('/adm/teams', 'TeamsController@indexToAdm');
Route::post('/adm/buyers', 'TeamsController@buyerList')->name('adm.buyers');
Route::get('/adm/teams/sales-seller/{user}', 'TeamsController@salesOfSeller');
Route::get('/team/get-performance', 'TeamsController@getPerformance');
Route::get('/team/send-ticket-batch', 'TeamsController@sendTicketsBatch');
Route::get('/adm/get-teams-performance', 'TeamsController@getTeamsPerformance');
Route::get('/adm/get-teams-ranking', 'TeamsController@getWeeksRanking');

Route::get('/adm/check-bilhetes', 'SalesController@checkBilhetesInutilizados');
Route::get('/adm/duplicate-sales', 'TeamsController@checkDuplicatesSales');

Route::get('/obrigado', 'HomeController@obrigado');
Route::post('/adm/raffle', 'SalesController@checkWinner')->name('adm.raffle');