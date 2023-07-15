<?php
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Drugs\Classifications\CategoryController;
use App\Http\Controllers\Admin\Drugs\Classifications\DosageFormController;
use App\Http\Controllers\Admin\Drugs\Classifications\IndicationController;
use App\Http\Controllers\Admin\Drugs\Classifications\ManufactureCompanyController;
use App\Http\Controllers\Admin\Drugs\Classifications\ScientificMaterialController;
use App\Http\Controllers\Admin\Drugs\Classifications\TherapeuticEffectController;
use App\Http\Controllers\Admin\Drugs\DrugController;
use App\Http\Controllers\Admin\Pharmacies\PharmacyController;
use App\Http\Controllers\Admin\Repositories\RepositoryController;
use App\Http\Controllers\Admin\Requests\Drugs\RequestDrugController;
use App\Http\Controllers\Admin\Requests\Registrations\RequestRegistrationController;
use Illuminate\Support\Facades\Route;

                /************          Login Admin          ************/
    Route::post('login',[LoginController::class,'login']);

                /************        Abilities Admin        ************/
    Route::middleware(['auth:sanctum','abilities:admin'])->group(function (){
        Route::post('logout',[LoginController::class,'logout']);

             Route::controller(DrugController::class)->prefix('drug')->group(function () {
                 Route::get('get-all','get');
                 Route::post('create','create');
                 Route::post('update','update');
                 Route::post('delete','delete');
                 Route::post('create-drugs','createDrugs');
                });

             Route::controller(CategoryController::class)->prefix('Category')->group(function (){
                 Route::get('get-all','get');
                 Route::post('create','create');
                 Route::post('update','update');
                 Route::post('delete','delete');
                 Route::post('get-drugs','getDrugs');
             });
             Route::controller(ManufactureCompanyController::class)->prefix('ManufactureCompany')->group(function (){
                 Route::get('get-all','get');
                 Route::post('create','create');
                 Route::post('update','update');
                 Route::post('delete','delete');
                 Route::post('get-drugs','getDrugs');
             });
             Route::controller(ScientificMaterialController::class)->prefix('ScientificMaterial')->group(function (){
                 Route::get('get-all','get');
                 Route::post('create','create');
                 Route::post('update','update');
                 Route::post('delete','delete');
                 Route::post('get-drugs','getDrugs');
             });
             Route::controller(IndicationController::class)->prefix('Indication')->group(function (){
                 Route::get('get-all','get');
                 Route::post('create','create');
                 Route::post('update','update');
                 Route::post('delete','delete');
                 Route::post('get-drugs','getDrugs');
             });

             Route::controller(TherapeuticEffectController::class)->prefix('TherapeuticEffect')->group(function (){
                 Route::get('get-all','get');
                 Route::post('create','create');
                 Route::post('update','update');
                 Route::post('delete','delete');
                 Route::post('get-drugs','getDrugs');
             });

             Route::controller(DosageFormController::class)->prefix('DosageForm')->group(function (){
                 Route::get('get-all','get');
                 Route::post('create','create');
                 Route::post('update','update');
                 Route::post('delete','delete');
                 Route::post('get-drugs','getDrugs');
             });
             Route::controller(PharmacyController::class)->prefix('Pharmacies')->group(function () {
                Route::get('get-all','get');
                Route::post('create','create');
             });
             Route::controller(RepositoryController::class)->prefix('Repositories')->group(function () {
                Route::get('get-all','get');
                Route::post('create','create');
             });


    Route::controller(RequestRegistrationController::class)->prefix('RequestRegistration')->group(function (){
        Route::get('get','get');
        Route::get('get-pending','getPending');
        Route::get('get-accepting','getAccepting');
        Route::get('get-rejecting','getRejecting');
        Route::post('accept','accept');
        Route::post('reject','reject');
        Route::post('delete','delete');
        Route::post('delete-all','deleteAll');
        Route::get('get-archived','getArchived');
        Route::post('add-to-archived','addToArchived');
        Route::post('return-from-archived','returnFromArchived');

    });
    Route::controller(RequestDrugController::class)->prefix('AddRequestDrug')->group(function (){
        Route::get('get','get');
        Route::get('get-pending','getPending');
        Route::get('get-accepting','getAccepting');
        Route::get('get-rejecting','getRejecting');
        Route::post('accept','accept');
        Route::post('reject','reject');
        Route::post('delete','delete');
        Route::post('delete-all','deleteAll');
        Route::get('get-archived','getArchived');
        Route::post('add-to-archived','addToArchived');
        Route::post('return-from-archived','returnFromArchived');

    });

});


