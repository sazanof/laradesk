<?php

use App\Helpers\ConfigHelper;
use App\Helpers\ConfigKey;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentsController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\FieldsController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\NotificationSettingsController;
use App\Http\Controllers\OfficesController;
use App\Http\Controllers\RoomsController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\SurmApiController;
use App\Http\Controllers\TicketsController;
use App\Http\Controllers\TicketThreadController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserFieldAutocompleteController;
use App\Http\Middleware\SetDefaultDepartmentMiddleware;
use App\Http\Middleware\UserBelongsToDepartment;
use App\Http\Middleware\UserHasAccessToTicketMiddleware;
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
        'name' => ConfigHelper::getValue(ConfigKey::Name->value),
        'bg' => ConfigHelper::getValue('app.bg'),
        'logo' => ConfigHelper::getValue('app.logo') ?? '',
        'favicon' => ConfigHelper::getValue('app.favicon') ?? '',
        'max_file_size' => ConfigHelper::getValue(ConfigKey::MaxFileSize->value) ?? null,
        'allowed_mimes' => ConfigHelper::getValue(ConfigKey::AllowedMimes->value) ?? null
    ]);
})->name('root');

Route
    ::middleware(SetDefaultDepartmentMiddleware::class)
    ->get('/user', [UserController::class, 'getUser']);
Route
    ::middleware(SetDefaultDepartmentMiddleware::class)
    ->post('/login', [UserController::class, 'authUser']);

Route::middleware('auth')->group(function () {

    Route::get('/logout', [UserController::class, 'logout']);
    Route::get('/offices', [OfficesController::class, 'getOffices']);
    Route::get('/counters', [TicketsController::class, 'getCounters']);
    Route::get('/departments', [DepartmentsController::class, 'getDepartments']);
    Route::prefix('/news')->group(function () {
        Route::get('', [NewsController::class, 'getUserNews']);
        Route::put('{id}/read', [NewsController::class, 'readNew']);
    });

    /** USER PROFILE EDIT **/
    Route::put('/profile', [UserController::class, 'editProfile']);
    Route::post('/profile/avatar', [UserController::class, 'updateAvatar']);
    Route::get('/profile/notifications', [NotificationSettingsController::class, 'getUserNotifications']);
    Route::post('/profile/notifications', [NotificationSettingsController::class, 'updateUserNotifications']);
    Route::post('/profile/updates', [UserController::class, 'requestUpdates']);
    Route::get('/avatars/{id}/{size?}', [UserController::class, 'getAvatar']);

    /** ADMIN */
    Route::middleware(UserIsAdmin::class)->prefix('/admin')->group(function () {
        Route::middleware(UserBelongsToDepartment::class)
            ->prefix('tickets')->group(function () {
                Route::post('', [TicketsController::class, 'getTickets']);
                Route::get('{id}', [TicketsController::class, 'getTicket'])->where('id', '[0-9]+');
                Route::get('{id}', [TicketsController::class, 'getTicket'])->where('id', '[0-9]+');
                Route::delete('{id}', [TicketsController::class, 'deleteTicket'])->where('id', '[0-9]+');
                /** ADMIN COMMENTS **/
                Route::post('{id}/solution', [TicketThreadController::class, 'addSolutionComment'])
                    ->where('id', '[0-9]+');
                Route::post('{id}/close', [TicketThreadController::class, 'addCloseComment'])
                    ->where('id', '[0-9]+');
                Route::post('{id}/reopen', [TicketThreadController::class, 'addReopenComment'])
                    ->where('id', '[0-9]+');
                Route::post('{id}/relevant', [TicketsController::class, 'getRelevantTickets'])
                    ->where('id', '[0-9]+');
                /** ADMIN MANAGE PARTICIPANT */
                Route::post('{id}/participants', [TicketsController::class, 'addParticipant'])
                    ->where('id', '[0-9]+');
                Route::put('{id}/participants', [TicketsController::class, 'removeParticipant'])
                    ->where('id', '[0-9]+');
            });

        /**
         * STATISTICS
         */
        Route::prefix('statistics')->group(function () {
            Route::post('', [StatisticsController::class, 'getStatistics']);
        });

        /** CHANGE DEPARTMENT */
        Route::post('department/{id}', [DepartmentsController::class, 'changeDepartment'])
            ->where('id', '[0-9]+');

        /** MANAGEMENT */
        Route::middleware(UserIsSuperAdmin::class)->prefix('management')->group(function () {
            /** CATEGORIES **/
            Route::get('{id?}', [CategoriesController::class, 'getCategoriesByDepartment'])->where('id', '[0-9]+');
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

            /** ADMINISTRATORS */
            Route::get('/administrators', [UserController::class, 'getAdministrators']);
            Route::post('/administrators/access', [UserController::class, 'addAccess']);
            Route::put('/administrators/access', [UserController::class, 'deleteAccess']);

            /** SETTINGS */
            Route::post('settings', [SettingsController::class, 'saveSettings']);

            /** DEPARTMENTS */
            Route::prefix('department')->group(function () {
                Route::post('', [DepartmentsController::class, 'addDepartment']);
                Route::put('{id}', [DepartmentsController::class, 'updateDepartment'])->where('id', '[0-9]+');
                Route::put('{id}/on', [DepartmentsController::class, 'enableDepartment'])->where('id', '[0-9]+');
                Route::put('{id}/off', [DepartmentsController::class, 'disableDepartment'])->where('id', '[0-9]+');
                Route::get('{id}/members', [DepartmentsController::class, 'getDepartmentMembers'])->where('id', '[0-9]+');
                Route::post('{departmentId}/members', [DepartmentsController::class, 'addDepartmentMember'])->where('id', '[0-9]+');
                Route::delete('{departmentId}/members/{memberId}', [DepartmentsController::class, 'deleteDepartmentMember'])->where('id', '[0-9]+');
            });

            /** OFFICES */
            Route::prefix('offices')->group(function () {
                Route::post('', [OfficesController::class, 'createOffice']);
                Route::put('{id}', [OfficesController::class, 'editOffice']);
                Route::delete('{id}', [OfficesController::class, 'deleteOffice']);
            });

            Route::prefix('rooms')->group(function () {
                Route::post('csv', [RoomsController::class, 'onUploadCsv']);
                Route::post('csv/start', [RoomsController::class, 'uploadCsvRoomData']);
            });

            /** NEWS */
            Route::prefix('news')->group(function () {
                Route::get('{page?}', [NewsController::class, 'getNews'])->where('page', '[0-9]+');
                Route::post('', [NewsController::class, 'addNew']);
                Route::put('{id}', [NewsController::class, 'updateNew'])->where('id', '[0-9]+');
                Route::delete('{id}', [NewsController::class, 'deleteNew'])->where('id', '[0-9]+');
                Route::put('{id}/publish', [NewsController::class, 'publishNew'])->where('id', '[0-9]+');
                Route::put('{id}/unpublish', [NewsController::class, 'unPublishNew'])->where('id', '[0-9]+');
            });

        });
        Route::get('dashboard', [DashboardController::class, 'getAdminDashboardData']);
    });

    Route::middleware(UserHasAccessToTicketMiddleware::class)->group(function () {
        Route::get('/ticket-files/{id}', [TicketsController::class, 'getTicketFiles'])
            ->where('id', '[0-9]+');

        Route::get('/ticket-files/{id}/{path}', [TicketsController::class, 'getTicketFile'])
            ->where('id', '[0-9]+')
            ->where('path', '.*');
    });

    /** USER TICKET ROUTES */
    Route::prefix('/user')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'getUserDashboardData']);
        Route::prefix('/tickets')->group(function () {
            Route::post('', [TicketsController::class, 'getUserTickets']);
            Route::post('export/excel', [ExportController::class, 'exportTickets']);
            Route::get('{id}/export/pdf', [ExportController::class, 'exportTicketsPdf']);
            Route::get('export/{filename}', [ExportController::class, 'downloadExportFile']);
            Route::get('{id}', [TicketsController::class, 'getUserTicket'])->where('id', '[0-9]+');
            Route::get('{id}/files', [FieldsController::class, 'downloadFiles'])->where('id', '[0-9]+');
            Route::get('/file/{id}', [FieldsController::class, 'getFile'])->where('id', '[0-9]+');
            Route::post('{id}/participants', [TicketsController::class, 'addParticipant'])->where('id', '[0-9]+');
            Route::put('{id}/participants', [TicketsController::class, 'removeParticipant'])->where('id', '[0-9]+');

            /**
             * AUTOCOMPLETE
             */
            Route::prefix('fields')->group(function () {
                Route::prefix('autocomplete')->group(function () {
                    Route::post('{id}', [UserFieldAutocompleteController::class, 'list']);
                    Route::post('', [UserFieldAutocompleteController::class, 'add']);
                    Route::delete('{id}', [UserFieldAutocompleteController::class, 'delete']);
                });
            });

            /**
             * AUTOCOMPLETE
             */
            Route::prefix('similar')->group(function () {
                Route::post('', [TicketsController::class, 'getSimilar']);
            });

            /** DRAFTS */
            /*Route::get('drafts/{categoryId}', [DraftsController::class, 'getDraft'])->where('categoryId', '[0-9]+');
            Route::post('drafts', [DraftsController::class, 'saveDraft']);
            Route::delete('drafts/{categoryId}', [DraftsController::class, 'deleteDraft'])->where('categoryId', '[0-9]+');*/


            /** USER COMMENTS **/
            Route::middleware(UserHasAccessToTicketMiddleware::class)->group(function () {
                Route::get('{id}/thread', [TicketThreadController::class, 'getTicketThread'])
                    ->where('id', '[0-9]+');
                Route::post('{id}/approve', [TicketThreadController::class, 'addApproveComment'])
                    ->where('id', '[0-9]+');
                Route::post('{id}/decline', [TicketThreadController::class, 'addDeclineComment'])
                    ->where('id', '[0-9]+');
                Route::post('{id}/comment', [TicketThreadController::class, 'addComment'])
                    ->where('id', '[0-9]+');
                /** THREAD (by ID) */
                Route::prefix('thread')->group(function () {
                    Route::get('{commentId}/files', [TicketThreadController::class, 'getTicketThreadFiles'])
                        ->where('commentId', '[0-9]+');
                    Route::get('{commentId}/files/{fileId}', [TicketThreadController::class, 'getTicketThreadFile'])
                        ->where('commentId', '[0-9]+')
                        ->where('fileId', '[0-9]+');
                });

            });

            Route::get('categories/{id}', [CategoriesController::class, 'getCategoriesByDepartment'])
                ->where('id', '[0-9]+');
            Route::get('categories/{id}/fields', [CategoriesController::class, 'getCategoryFields'])
                ->where('id', '[0-9]+');
            Route::post('create', [TicketsController::class, 'createTicket']);
            Route::post('upload-image', [TicketsController::class, 'uploadImageInEditor']);
            Route::post('search/users', [UserController::class, 'searchUsers']);
        });

        /** NOTIFICATIONS */
        Route::prefix('notifications')->group(function () {
            Route::get('last', [NotificationsController::class, 'getLastNotifications']);
            Route::put('{id}', [NotificationsController::class, 'markAsRead']);
            Route::delete('', [NotificationsController::class, 'deleteNotifications']);
            Route::delete('{id}', [NotificationsController::class, 'deleteNotification']);
        });
    });
    /** SURM */
    Route::prefix('surm')->group(function () {
        Route::prefix('workplaces')->group(function () {
            Route::get('', [SurmApiController::class, 'getWorkplacesByUserRoom']);
        });
    });
});
