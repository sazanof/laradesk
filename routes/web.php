<?php

use App\Events\NewTicket;
use App\Helpers\ConfigHelper;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\FieldsController;
use App\Http\Controllers\NotificationSettingsController;
use App\Http\Controllers\OfficesController;
use App\Http\Controllers\TicketsController;
use App\Http\Controllers\TicketThreadController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\UserIsAdmin;
use App\Http\Middleware\UserIsSuperAdmin;
use App\Models\NotificationSetting;
use App\Models\Ticket;
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
Route::get('/test', function () {
    return View::make('mail.new_ticket', ['ticket' => Ticket::find(10008), 'subject' => 'Новая заявка']);
    NewTicket::dispatch(Ticket::findOrFail(5198));
});
Route::get('/user', [UserController::class, 'getUser']);
Route::post('/login', [UserController::class, 'authUser']);

Route::middleware('auth')->group(function () {

    Route::get('/logout', [UserController::class, 'logout']);
    Route::get('/offices', [OfficesController::class, 'getOffices']);
    Route::get('/counters', [TicketsController::class, 'getCounters']);

    /** USER PROFILE EDIT **/
    Route::put('/profile', [UserController::class, 'editProfile']);
    Route::post('/profile/avatar', [UserController::class, 'updateAvatar']);
    Route::get('/profile/notifications', [NotificationSettingsController::class, 'getUserNotifications']);
    Route::post('/profile/notifications', [NotificationSettingsController::class, 'updateUserNotifications']);
    Route::get('/avatars/{id}/{size?}', [UserController::class, 'getAvatar']);

    /** ADMIN/TICKETS **/
    Route::middleware(UserIsAdmin::class)->prefix('/admin/tickets')->group(function () {
        Route::post('', [TicketsController::class, 'getTickets']);
        Route::get('{id}', [TicketsController::class, 'getTicket'])->where('id', '[0-9]+');
        /** ADMIN COMMENTS **/
        Route::post('{id}/solution', [TicketThreadController::class, 'addSolutionComment'])->where('id', '[0-9]+');
        Route::post('{id}/close', [TicketThreadController::class, 'addCloseComment'])->where('id', '[0-9]+');
        Route::post('{id}/reopen', [TicketThreadController::class, 'addReopenComment'])->where('id', '[0-9]+');
        Route::post('{id}/participants', [TicketsController::class, 'addParticipant'])->where('id', '[0-9]+');
        Route::put('{id}/participants', [TicketsController::class, 'removeParticipant'])->where('id', '[0-9]+');
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

    /** USER TICKET ROUTES */
    Route::prefix('/user')->group(function () {
        Route::prefix('/tickets')->group(function () {
            Route::post('', [TicketsController::class, 'getUserTickets']);
            Route::get('{id}', [TicketsController::class, 'getUserTicket'])->where('id', '[0-9]+');

            /** USER COMMENTS **/
            Route::get('{id}/thread', [TicketThreadController::class, 'getTicketThread'])->where('id', '[0-9]+');
            Route::post('{id}/approve', [TicketThreadController::class, 'addApproveComment'])->where('id', '[0-9]+');
            Route::post('{id}/decline', [TicketThreadController::class, 'addDeclineComment'])->where('id', '[0-9]+');
            Route::post('{id}/comment', [TicketThreadController::class, 'addComment'])->where('id', '[0-9]+');

            Route::get('categories', [CategoriesController::class, 'getCategoriesTree']);
            Route::get('categories/{id}/fields', [CategoriesController::class, 'getCategoryFields'])->where('id', '[0-9]+');;
            Route::post('create', [TicketsController::class, 'createTicket']);
            Route::get('search/users/{term}', [UserController::class, 'searchUsers']);
        });

    });
});
