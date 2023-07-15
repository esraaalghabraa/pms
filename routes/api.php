<?php


use App\Http\Controllers\Repository\RepositoryController;
use App\Http\Controllers\User\Auth\AuthUserController;
use App\Http\Controllers\User\Pharmacy\MedicinesBuyOrderController;
use App\Http\Controllers\User\Pharmacy\PharmacyMedicinesController;
use App\Http\Controllers\User\Pharmacy\RepositoriesController;
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
        Route::middleware(['auth:sanctum', 'abilities:frontuser'])
            ->group(function () {
                Route::get('logout', 'logout');
                Route::post('add_info', 'addInfo');
            });
    });

Route::middleware(['auth:sanctum', 'abilities:frontuser'])->group(function () {

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

        Route::controller(RepositoryController::class)->group(function () {
            Route::post('create-drug-storage', 'createDrugStorage');
            Route::post('create-batch', 'createBatchDrug');
        });
    });

    Route::prefix('medicines')->controller(MedicinesController::class)
        ->group(function () {
            Route::get('get', 'getMedicines');
            Route::post('search', 'searchMedicines');
        });

    Route::controller(RequestsUserController::class)->prefix('requests')
        ->middleware(['auth:sanctum', 'abilities:frontuser'])->group(function () {
            Route::get('get-requests', 'getRequests');
            Route::get('get-archived-requests', 'getArchivedRequests');
            Route::post('create-request', 'createRequestRegistration');
            Route::post('reject-request', 'rejectRequest');
            Route::post('accept-request', 'acceptRequest');
            Route::post('delete-request', 'deleteRequest');
        });

    Route::controller(RequestsUserController::class)
        ->prefix('requests')->group(function () {
            Route::get('get-requests', 'getRequests');
            Route::get('get-archived-requests', 'getArchivedRequests');
            Route::post('create-request', 'createRequestRegistration');
            Route::post('reject-request', 'rejectRequest');
            Route::post('accept-request', 'acceptRequest');
            Route::post('delete-request', 'deleteRequest');
        });

});


