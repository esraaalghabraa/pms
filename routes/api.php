<?php


use App\Http\Controllers\User\Auth\AuthUserController;
use App\Http\Controllers\User\Auth\RequestsUserController;
use App\Http\Controllers\User\MedicinesController;
use App\Http\Controllers\User\Pharmacy\CustomerController;
use App\Http\Controllers\User\Pharmacy\EmployeeController;
use App\Http\Controllers\User\Pharmacy\MedicinesBuyOrderController;
use App\Http\Controllers\User\Pharmacy\PharmacyMedicinesController;
use App\Http\Controllers\User\Pharmacy\SaleBillsController;
use App\Http\Controllers\User\Repository\MedicinesSaleOrderController;
use App\Http\Controllers\User\Repository\RepositoryMedicinesController;
use App\Http\Controllers\User\Repository\RequestsMedicinesController;
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
        Route::middleware(['auth:sanctum', 'abilities:user'])
            ->group(function () {
                Route::get('logout', 'logout');
                Route::post('edit-profile', 'addInfo');
                Route::post('get-profile', 'getInfo');
            });
    });

Route::middleware(['auth:sanctum', 'abilities:user'])->group(function () {

    Route::prefix('pharmacy')->group(function () {
        Route::middleware('check_permission:drugs-pharma')->prefix('stored-medicines')->controller(PharmacyMedicinesController::class)
            ->group(function () {
                Route::post('get', 'getStoredMedicines');
                Route::post('search', 'searchStoredMedicines');
                Route::post('get-stored-medicine', 'getStoredMedicine');
                Route::post('create', 'createMedicineStorage');
                Route::post('update', 'updateMedicine');
                Route::post('create-batch', 'createBatchMedicine');
                Route::post('update-batch', 'updateBatchMedicine');
            });

        Route::controller(MedicinesBuyOrderController::class)->group(function () {
            Route::prefix('repositories')->group(function () {
                Route::get('get', 'getRepositories');
                Route::post('search', 'searchRepository');
                Route::post('get-repository-with-requests', 'getRepositoryWithRequests');
                Route::post('get-repository-with-medicines', 'getRepositoryWithMedicines');
            });
            Route::middleware('check_permission:orders-pharma')->prefix('buy-orders')->group(function () {
                Route::post('get', 'get');
                Route::post('get-medicines-order', 'getMedicinesOrder');
                Route::post('send', 'sendOrder');
                Route::post('receive', 'receive');
            });
        });
        Route::middleware('check_permission:customers-pharma')->prefix('customers')->controller(CustomerController::class)
            ->group(function (){
                Route::post('get-all','getAll');
                Route::post('create','create');
                Route::post('saleBile','saleBile');
                Route::post('update','update');
                Route::post('delete','delete');
                Route::post('get','getCustomer');
            });
        Route::middleware('check_permission:employee-pharma')->prefix('employees')->controller(EmployeeController::class)
            ->group(function (){
                Route::post('get-roles','getRoles');
                Route::post('create-roles','createRole');
                Route::get('get-permissions','getPermissions');
                Route::post('get-employees','getEmployees');
                Route::post('get-employee','getEmployee');
                Route::post('create-employee','createEmployee');
                Route::post('update-employee','updateEmployee');
                Route::post('delete-employee','deleteEmployee');
            });
        Route::middleware('check_permission:bills-pharma')->prefix('sale-bills')->controller(SaleBillsController::class)
            ->group(function (){
                Route::post('get-medicine-by-barcode','getMedicineByBarcode');
                Route::post('get-all-daily','getDailyBills');
                Route::post('get-all-customers','getCustomerBills');
                Route::post('get-daily','getDailyBill');
                Route::post('get-customer','getCustomerBill');
                Route::post('create-customer-bill','createCustomerBill');
                Route::post('add-sale-to-daily-bill','addSaleToDailyBill');
                Route::post('delete-bill','deleteBill');
            });
    });

    Route::middleware('check_permission:drugs-repo,orders-repo')->prefix('repository')->group(function () {
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

        Route::controller(RequestsMedicinesController::class)->prefix('requests-medicines')->group(function () {
            Route::post('get', 'get');
            Route::post('create', 'create');
        });
    });

    Route::prefix('medicines')->controller(MedicinesController::class)
        ->group(function () {
            Route::get('get', 'getMedicines');
            Route::post('search', 'searchMedicines');
            Route::post('get-medicine', 'getMedicine');
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


