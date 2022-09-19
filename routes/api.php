<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\TransactionController;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post("register", [AuthController::class, "register"]);
        Route::post("login", [AuthController::class, "login"])->name('login');
    });
    Route::group(['middleware' => ["auth:sanctum"]], function () {
        Route::prefix('product')->group(function () {
            Route::get("/", [ProductController::class, "index"]);
            Route::post("create", [ProductController::class, "store"]);
            Route::post("update/{id}", [ProductController::class, "update"]);
        });
            Route::prefix('transaction')->group(function () {
                Route::get("/", [TransactionController::class, "index"]);
                Route::post("callback", [TransactionController::class, "transaction"]);
            });

    });
});


