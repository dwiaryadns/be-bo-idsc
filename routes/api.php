<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Aidiva\ApiController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\BoInfoController;
use App\Http\Controllers\Core\AccessFasyankesController;
use App\Http\Controllers\Core\IcdxController;
use App\Http\Controllers\Core\MasterKfaController;
use App\Http\Controllers\Core\TransaksiController;
use App\Http\Controllers\DelegateAccessController;
use App\Http\Controllers\DistribusiController;
use App\Http\Controllers\FasyankesController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\KeamananAkunController;
use App\Http\Controllers\LegalDocController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PenerimaanController;
use App\Http\Controllers\StokOpnameController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\WarehouseController;
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
Route::post('/get-otp', [AuthController::class, 'getOtp']);
Route::post('/change-email', [AuthController::class, 'changeEmail']);
Route::post('/store-otp', [AuthController::class, 'storeOtp']);
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
Route::post('/midtrans/callback', [PaymentController::class, 'handleNotification']);
Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail']);
Route::get('/check/token/{token}', [ForgotPasswordController::class, 'checkToken']);
Route::post('/password/reset', [ResetPasswordController::class, 'resetPassword']);

Route::middleware('aidiva.token')->group(function () {
    Route::prefix('v1/aidiva')->group(function () {
        Route::get('/list-fasyankes', [ApiController::class, 'listFasyankes']);
        Route::get('/get-barang', [ApiController::class, 'getBarang']);
    });
});

Route::middleware('check.token')->group(function () {
    Route::get('/list-username', [AccessFasyankesController::class, 'listUsername']);
    Route::post('/access-fasyankes', [AccessFasyankesController::class, 'checkAccessFasyankes']);
    Route::post('/access-fasyankes/store', [AccessFasyankesController::class, 'storeAccessFasyankes']);
    Route::post('/access-fasyankes/update', [AccessFasyankesController::class, 'updateAccessFasyankes']);

    Route::get('/icdx', [IcdxController::class, 'icdx']);

    Route::get('/master-kfa', [MasterKfaController::class, 'index']);
    Route::get('/master-kfa/pov', [MasterKfaController::class, 'kfa_pov']);
    Route::get('/master-kfa/pov/poa', [MasterKfaController::class, 'kfa_poa']);

    Route::get('/master-kategori', [TransaksiController::class, 'master_kategori']);
    Route::get('/master-barang', [TransaksiController::class, 'master_barang']);
    Route::post('/decrease-stock', [TransaksiController::class, 'decreaseStock']);
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/change-twofa', [KeamananAkunController::class, 'verify2FA']);

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::prefix('bo-info')->group(function () {
        Route::get('/', [BoInfoController::class, 'getBoInfo']);
        Route::post('/store', [BoInfoController::class, 'storeBoIfo']);
    });
    Route::prefix('warehouses')->group(function () {
        Route::get('/', [WarehouseController::class, 'getWarehouses']);
        Route::post('/store', [WarehouseController::class, 'storeWarehouse']);
        Route::get('/stock-gudang', [WarehouseController::class, 'stockGudang']);
        Route::post('/update-isjual', [WarehouseController::class, 'updateIsJualBarang']);
    });
    Route::prefix('fasyankes')->group(function () {
        Route::get('/', [FasyankesController::class, 'getFasyankes']);
        Route::post('/store', [FasyankesController::class, 'storeFasyankes']);
        Route::post('/send-otp', [FasyankesController::class, 'sendOtp']);
    });
    Route::post('/create-transaction', [PaymentController::class, 'createTransaction']);
    Route::post('/update-payment', [PaymentController::class, 'updateTransactionStatus']);
    Route::get('/legal-document-bo', [LegalDocController::class, 'getLegalDoc']);
    Route::post('/legal-document-bo/upload', [LegalDocController::class, 'upload']);
    Route::post('/legal-document-fasyankes/upload', [LegalDocController::class, 'uploadLegalFasyankes']);

    Route::get('/subscription/{type}', [SubscriptionController::class, 'index']);

    Route::get('/activity-log', [ActivityLogController::class, 'activity_log']);

    Route::post('/security/send-otp', [KeamananAkunController::class, 'sendOtp']);
    Route::post('/change-password', [KeamananAkunController::class, 'changePassword']);

    Route::prefix('/purchase')->group(function () {
        Route::get('/', [PembelianController::class, 'getPurchase']);
        Route::post('/download', [PembelianController::class, 'download_pdf']);
        Route::get('/get-barang-supplier', [PembelianController::class, 'getBarangSupplier']);
        Route::get('/get-fasyankes-warehouse', [PembelianController::class, 'getFasyankesWarehouse']);
        Route::post('/store', [PembelianController::class, 'purchase']);
    });

    Route::prefix('/good-receipt')->group(function () {
        Route::get('/', [PenerimaanController::class, 'penerimaan']);
        Route::get('/search', [PenerimaanController::class, 'showByPoId']);
        Route::post('/save', [PenerimaanController::class, 'save']);
        Route::post('/update', [PenerimaanController::class, 'updateStockPenerimaan']);
    });

    Route::prefix('/supplier')->group(function () {
        Route::get('/', [SupplierController::class, 'getSupplier']);
        Route::get('/{id}', [SupplierController::class, 'showSupplier']);
        Route::delete('/delete/{id}', [SupplierController::class, 'deleteSupplier']);
        Route::post('/store', [SupplierController::class, 'storeSupplier']);
    });
    Route::prefix('/inventory')->group(function () {
        Route::get('/get-kategori', [InventoryController::class, 'getKategori']);
        Route::get('/get-barang', [InventoryController::class, 'getBarang']);
        Route::get('/get-stock-barang', [InventoryController::class, 'getStockBarang']);
        Route::post('/store-barang', [InventoryController::class, 'storeBarang']);
        Route::post('/import-barang', [InventoryController::class, 'importBarang']);
    });

    Route::prefix('/stok-opname')->group(function () {
        Route::get('/barang', [StokOpnameController::class, 'barang']);
        Route::get('/histori', [StokOpnameController::class, 'getStokOpname']);
        Route::post('/store', [StokOpnameController::class, 'storeOpname']);
    });
    Route::prefix('/distribusi')->group(function () {
        Route::get('/', [DistribusiController::class, 'getDistribusi']);
        Route::get('/get-barang', [DistribusiController::class, 'getBarangGudang']);
        Route::post('/store', [DistribusiController::class, 'store']);
    });
    Route::prefix('/delegate-access')->group(function () {
        Route::get('/', [DelegateAccessController::class, 'getDelegateAccess']);
        Route::post('/store', [DelegateAccessController::class, 'storeDelegateAccess']);
        Route::delete('/delete/{id}', [DelegateAccessController::class, 'destroy']);
    });
});
