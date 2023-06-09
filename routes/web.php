<?php

use App\Helpers\ConfigHelper;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\FieldsController;
use App\Http\Controllers\OfficesController;
use App\Http\Controllers\TicketsController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\UserIsAdmin;
use App\Http\Middleware\UserIsSuperAdmin;
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
    Route::get('/offices', [OfficesController::class, 'getOffices']);

    /** USER PROFILE EDIT **/
    Route::put('/profile', [UserController::class, 'editProfile']);
    Route::middleware(UserIsAdmin::class)->prefix('/admin/tickets')->group(function () {
        Route::post('', [TicketsController::class, 'getTickets']);
    });
    Route::middleware(UserIsSuperAdmin::class)->prefix('/admin/management')->group(function () {
        /** CATEGORIES **/
        Route::get('', [CategoriesController::class, 'getCategoriesTree']);
        Route::post('/categories', [CategoriesController::class, 'createCategory']);
        Route::get('/categories/{id}', [CategoriesController::class, 'getCategoryAndCreateFields']);
        Route::put('/categories/{id}', [CategoriesController::class, 'saveCategory']);
        Route::delete('/categories/{id}', [CategoriesController::class, 'deleteCategory']);

        /** FIELDS **/
        Route::get('/fields', [FieldsController::class, 'getFields']);
        Route::post('/fields', [FieldsController::class, 'createField']);
        Route::put('/fields/{id}', [FieldsController::class, 'editField'])->where('id', '[0-9]+');
        Route::delete('/fields/{id}', [FieldsController::class, 'deleteField']);
        Route::post('/fields/link', [FieldsController::class, 'linkField']);
        Route::post('/fields/unlink', [FieldsController::class, 'unlinkField']);
        Route::put('/fields/order', [FieldsController::class, 'changeFieldOrder']);
        Route::put('/fields/required', [FieldsController::class, 'makeFieldRequired']);
    });

    /** Ticket creation */
    Route::prefix('/user')->group(function () {
        Route::prefix('/tickets')->group(function () {
            Route::post('', [TicketsController::class, 'getSentTickets']);

            Route::get('categories', [CategoriesController::class, 'getCategoriesTree']);
            Route::get('categories/{id}/fields', [CategoriesController::class, 'getCategoryFields'])->where('id', '[0-9]+');;
            Route::post('create', [TicketsController::class, 'createTicket']);
            Route::get('/search/users/{term}', [UserController::class, 'searchUsers']);
        });

    });
});
