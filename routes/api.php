<?php


use App\Http\Controllers\Controller;
use App\Http\Controllers\User\Auth\AuthUserController;
use App\Http\Controllers\User\Pharmacy\MedicinesBuyOrderController;
use App\Http\Controllers\User\Pharmacy\PharmacyMedicinesController;
use App\Http\Controllers\User\Repository\MedicinesSaleOrderController;
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
                Route::post('update-batch', 'updateBatchMedicine');
            });

        Route::controller(MedicinesBuyOrderController::class)->group(function () {
            Route::prefix('repositories')->group(function () {
                Route::get('get', 'getRepositories');
                Route::post('search', 'searchRepository');
                Route::post('get-repository', 'getRepository');
            });
            Route::prefix('buy-orders')->group(function () {
                Route::post('get', 'get');
                Route::post('get-medicines-order', 'getMedicinesOrder');
                Route::post('send', 'sendOrder');
                Route::post('receive', 'receive');
            });
        });
    });

    Route::prefix('repository')->group(function () {
        Route::controller(RepositoryMedicinesController::class)->group(function () {
            Route::prefix('stored-medicines')->group(function () {
                Route::post('get', 'getStoredMedicines');
                Route::post('search', 'searchStoredMedicines');
                Route::post('get-stored-medicine', 'getStoredMedicine');
                Route::post('create', 'createMedicineStorage');
                Route::post('update', 'updateMedicine');
                Route::post('create-batch', 'createBatchMedicine');
                Route::post('update-batch', 'updateBatchMedicine');
            });
        });

        Route::controller(MedicinesSaleOrderController::class)->group(function () {
            Route::prefix('pharmacies')->group(function () {
                Route::get('get', 'getPharmacies');
                Route::post('get-pharmacy', 'getPharmacy');
            });
            Route::prefix('sale-orders')->group(function () {
                Route::post('get', 'getMedicinesOrders');
                Route::post('get-medicines-order', 'getMedicinesOrder');
                Route::post('accept', 'acceptOrder');
                Route::post('reject', 'rejectOrder');
            });
        });
    });

    Route::prefix('medicines')->controller(MedicinesController::class)
        ->group(function () {
            Route::get('get', 'getMedicines');
            Route::post('search', 'searchMedicines');
            Route::post('get-medicine', 'getMedicine');
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


