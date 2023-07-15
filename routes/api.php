<?php


use App\Http\Controllers\Pharmacy\PharmacyController;
use App\Http\Controllers\Repository\RepositoryController;
use App\Http\Controllers\User\Auth\AuthUserController;
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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});
Route::controller(AuthUserController::class)
    ->prefix('auth')->as('auth.')
    ->group(function () {
        Route::post('register', 'register');
        Route::post('login', 'login');
        Route::get('login_with_token', 'loginWithToken');
        Route::post('send-verify-code', 'sendVerifyCode');
        Route::post('verify-code', 'verifyCode');
        Route::middleware(['auth:sanctum','abilities:frontuser'])
            ->group(function () {
            Route::get('logout', 'logout');
            Route::post('add_info', 'addInfo');
        });
    });
Route::middleware(['auth:sanctum','abilities:frontuser'])->group(function (){
    Route::controller(RequestsUserController::class)
        ->prefix('requests')->middleware(['auth:sanctum','abilities:frontuser'])
        ->group(function () {
            Route::get('get-requests','getRequests');
            Route::get('get-archived-requests','getArchivedRequests');
            Route::post('create-request','createRequestRegistration');
            Route::post('reject-request','rejectRequest');
            Route::post('accept-request','acceptRequest');
            Route::post('delete-request','deleteRequest');
        });

    Route::controller(RequestsUserController::class)
        ->prefix('requests')
        ->group(function () {
            Route::get('get-requests','getRequests');
            Route::get('get-archived-requests','getArchivedRequests');
            Route::post('create-request','createRequestRegistration');
            Route::post('reject-request','rejectRequest');
            Route::post('accept-request','acceptRequest');
            Route::post('delete-request','deleteRequest');
        });


    Route::controller(RepositoryController::class)
        ->prefix('repository')->group(function () {
            Route::post('create-request','createRequest');
            Route::post('create-drug-storage','createDrugStorage');
            Route::post('create-batch','createBatchDrug');
        });

    Route::controller(PharmacyController::class)
        ->prefix('pharmacy')->group(function () {
            Route::post('create-drug-storage','createDrugStorage');
            Route::post('create-batch','createBatchDrug');
            Route::post('search-drug','searchDrug');
        });

});


