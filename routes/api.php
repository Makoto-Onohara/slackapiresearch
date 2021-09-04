<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/get_slack_user', 'SampleController@getUserList');
Route::post('/post_slack_channel', 'sampleController@postChannel');
Route::get('/g_get_slack_user', 'SampleController@with_headers');
Route::get('/g_get_slack_history', 'SampleController@getChannelHistory');