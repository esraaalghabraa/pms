<?php


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\User\Auth\AuthUserController;
use Illuminate\Support\Facades\Route;

Route::post('login',[LoginController::class,'login']);

Route::middleware(['auth:sanctum','abilities:admin'])->group(function (){
    Route::post('logout',[LoginController::class,'logout']);
});
