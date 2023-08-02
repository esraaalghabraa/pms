<?php


use App\Http\Controllers\User\Auth\AuthUserController;
use App\Http\Controllers\User\Pharmacy\CustomerController;
use App\Http\Controllers\User\Pharmacy\EmployeeController;
use App\Http\Controllers\User\Pharmacy\MedicinesBuyOrderController;
use App\Http\Controllers\User\Pharmacy\PharmacyMedicinesController;
use App\Http\Controllers\User\Pharmacy\RepositoriesController;
use App\Http\Controllers\User\Pharmacy\RoleController;
use App\Http\Controllers\User\Pharmacy\SaleBillsController;
use App\Http\Controllers\User\Repository\PharmaciesController;
use App\Http\Controllers\User\Repository\RepositoryMedicinesController;
use App\Http\Controllers\User\Requests\MedicinesController;
use App\Http\Controllers\User\Requests\RequestsUserController;
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

//Route::get('login',[Controller::class,'unAuthenticated'])->name('login');

Route::controller(AuthUserController::class)
    ->prefix('auth')->as('auth.')
    ->group(function () {
        Route::post('register', 'register');
        Route::post('login', 'login');
        Route::get('login_with_token', 'loginWithToken');
        Route::post('send-verify-code', 'sendVerifyCode');
        Route::post('verify-code', 'verifyCode');
        Route::middleware(['auth:sanctum', 'abilities:user'])
            ->group(function () {
                Route::get('logout', 'logout');
                Route::post('add_info', 'addInfo');
            });
    });

Route::middleware(['auth:sanctum', 'abilities:user'])->group(function () {

    Route::prefix('pharmacy')->group(function () {
        Route::prefix('stored-medicines')->controller(PharmacyMedicinesController::class)
            ->group(function () {
                Route::post('get', 'getStoredMedicines');
                Route::post('search', 'searchStoredMedicines');
                Route::post('create', 'createMedicineStorage');
                Route::post('update', 'updateMedicine');
                Route::post('create-batch', 'createBatchMedicine');
            });

        Route::prefix('repositories')->controller(RepositoriesController::class)
            ->group(function () {
                Route::get('get', 'getRepositories');
                Route::post('search', 'searchRepository');
                Route::post('get-repository', 'getRepository');
            });
        Route::prefix('buy-orders')->controller(MedicinesBuyOrderController::class)
            ->group(function () {
                Route::post('get', 'get');
                Route::post('get-medicines-order', 'getMedicinesOrder');
                Route::post('send', 'sendOrder');
                Route::post('get-repository', 'getRepository');
            });
        Route::prefix('customers')->controller(CustomerController::class)
            ->group(function (){
                Route::post('get-all','getAll');
                Route::post('create','create');
                Route::post('saleBile','saleBile');
                Route::post('update','update');
                Route::post('delete','delete');
                Route::post('get','getCustomer');
            });
        Route::prefix('employees')->controller(EmployeeController::class)
            ->group(function (){
                Route::post('get-all','getAll');
                Route::post('delete','delete');
                Route::post('get','get');
                Route::post('create','create');
            });
        Route::prefix('roles')->controller(RoleController::class)
            ->group(function (){
                Route::post('get-all','getAll');
                Route::post('create','createRole');
                Route::post('update','update');
                Route::post('createe','createPermission');
                Route::post('destroy','destroy');
            });
        Route::prefix('sale-bills')->controller(SaleBillsController::class)
            ->group(function (){
                Route::post('get-all-daily','getDailyBills');
                Route::post('get-all-customers','getCustomerBills');
                Route::post('create','create');
                Route::post('update','update');
                Route::post('delete','delete');
                Route::post('get','getBill');
            });
    });

    Route::prefix('repository')->group(function () {
        Route::controller(RepositoryMedicinesController::class)->group(function () {
            Route::prefix('stored-medicines')->group(function () {
                Route::post('get', 'getStoredMedicines');
                Route::post('search', 'searchStoredMedicines');
                Route::post('create', 'createMedicineStorage');
                Route::post('update', 'updateMedicine');
                Route::post('create-batch', 'createBatchMedicine');
            });
        });

        Route::prefix('pharmacies')->controller(PharmaciesController::class)->group(function () {
            Route::get('get', 'getPharmacies');
            Route::post('get-pharmacy', 'getPharmacy');
        });


    });

    Route::prefix('medicines')->controller(MedicinesController::class)
        ->group(function () {
            Route::get('get', 'getMedicines');
            Route::post('search', 'searchMedicines');
        });

    Route::controller(RequestsUserController::class)->prefix('requests')->group(function () {
            Route::get('get-requests', 'getRequests');
            Route::get('get-archived-requests', 'getArchivedRequests');
            Route::post('create-request', 'createRequestRegistration');
            Route::post('reject-request', 'rejectRequest');
            Route::post('accept-request', 'acceptRequest');
            Route::post('delete-request', 'deleteRequest');
        });

});


