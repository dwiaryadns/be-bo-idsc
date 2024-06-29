<?php

use App\Http\Controllers\AccessFasyankesController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\BoInfoController;
use App\Http\Controllers\FasyankesController;
use App\Http\Controllers\LegalDocController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
Route::post('/get-otp', [AuthController::class, 'getOtp']);
Route::post('/store-otp', [AuthController::class, 'storeOtp']);

Route::post('/midtrans/callback', [PaymentController::class, 'handleNotification']);

Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail']);
Route::get('/check/token/{token}', [ForgotPasswordController::class, 'checkToken']);
Route::post('/password/reset', [ResetPasswordController::class, 'resetPassword']);

Route::post('/access-fasyankes', [AccessFasyankesController::class, 'checkAccessFasyankes']);
Route::post('/access-fasyankes/store', [AccessFasyankesController::class, 'storeAccessFasyankes']);

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::prefix('bo-info')->group(function () {
        Route::get('/', [BoInfoController::class, 'getBoInfo']);
        Route::post('/store', [BoInfoController::class, 'storeBoIfo']);
    });
    Route::prefix('warehouses')->group(function () {
        Route::get('/', [WarehouseController::class, 'getWarehouses']);
        Route::post('/store', [WarehouseController::class, 'storeWarehouse']);
    });
    Route::prefix('fasyankes')->group(function () {
        Route::get('/', [FasyankesController::class, 'getFasyankes']);
        Route::post('/store', [FasyankesController::class, 'storeFasyankes']);
    });
    Route::post('/create-transaction', [PaymentController::class, 'createTransaction']);
    Route::get('/legal-document-bo', [LegalDocController::class, 'getLegalDoc']);
    Route::post('/legal-document-bo/upload', [LegalDocController::class, 'upload']);
    Route::post('/legal-document-fasyankes/upload', [LegalDocController::class, 'uploadLegalFasyankes']);
});
