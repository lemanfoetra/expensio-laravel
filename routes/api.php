<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\IncomesController;
use App\Http\Controllers\Login;
use App\Http\Controllers\TipeExpenseController;
use Illuminate\Http\Request;
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

Route::post('login', [LoginController::class, 'login']);
Route::post('register', [LoginController::class, 'register']);


Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('expense')->group(function () {
        Route::get('/', [ExpenseController::class, 'index']);
        Route::post('/', [ExpenseController::class, 'store']);
        Route::get('/{id}', [ExpenseController::class, 'show']);
        Route::put('/{id}', [ExpenseController::class, 'update']);
        Route::delete('/{id}', [ExpenseController::class, 'destroy']);
    });

    Route::prefix('dashboard')->group(function () {
        Route::get('jumlah_pengeluaran_hari_ini', [DashboardController::class, 'pengeluaranHariIni']);
        Route::get('jumlah_pengeluaran_minggu_ini', [DashboardController::class, 'pengeluaranMingguIni']);
        Route::get('jumlah_pengeluaran_bulan_ini', [DashboardController::class, 'pengeluaranBulanIni']);

        Route::get('detail_pengeluaran_hari_ini', [DashboardController::class, 'detailPengeluaranHariIni']);
        Route::get('detail_pengeluaran_minggu_ini', [DashboardController::class, 'detailPengeluaranMingguIni']);
        Route::get('detail_pengeluaran_bulan_ini', [DashboardController::class, 'detailPengeluaranBulanIni']);
    });

    Route::prefix('tipe_expense')->group(function () {
        Route::get('/', [TipeExpenseController::class, 'index']);
    });

    Route::prefix('income')->group(function () {
        Route::get('/', [IncomesController::class, 'index']);
        Route::post('/', [IncomesController::class, 'store']);
        Route::get('/{id}', [IncomesController::class, 'show']);
        Route::put('/{id}', [IncomesController::class, 'update']);
        Route::delete('/{id}', [IncomesController::class, 'destroy']);
    });

    Route::prefix('budget')->group(function () {
        Route::get('/', [BudgetController::class, 'index']);
        Route::post('/', [BudgetController::class, 'store']);
        Route::get('/{id}', [BudgetController::class, 'show']);
        Route::put('/{id}', [BudgetController::class, 'update']);
        Route::delete('/{id}', [BudgetController::class, 'destroy']);
    });
});
