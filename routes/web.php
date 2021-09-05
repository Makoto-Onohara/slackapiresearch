<?php

use Illuminate\Support\Facades\Route;
use Auth0\Login\Auth0Controller;
use App\Http\Controllers\Auth\Auth0IndexController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/login', [Auth0IndexController::class, 'login'])->name('login');
Route::get('/logout', [Auth0IndexController::class, 'logout'])->name('logout');
Route::get('/profile', [Auth0IndexController::class, 'profile'])->name('profile');

Route::get('/auth0/callback', [auth0controller::class, 'callback'])->name('auth0-callback');

Route::get('/archives', function() {
    return '記事一覧';
});

Route::get('/archives/{category}/', function($category) {
    return $category.'の一覧';
});

Route::post('/join/', function() {
    return '入会申込完了';
});

Route::get('/join/', function() {
    return redirect()->to('/');
});

Route::get('/{id}/', function($id) {
    return $id. 'のページ';
});
