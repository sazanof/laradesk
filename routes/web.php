<?php

use App\Helpers\ConfigHelper;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('main', [
        'name' => ConfigHelper::getValue('app.name'),
        'bg' => ConfigHelper::getValue('app.bg')
    ]);
})->name('root');
Route::get('/user', [UserController::class, 'getUser']);
Route::post('/login', [UserController::class, 'authUser']);
Route::middleware('auth')->group(function () {
    Route::get('/logout', [UserController::class, 'logout']);
});
