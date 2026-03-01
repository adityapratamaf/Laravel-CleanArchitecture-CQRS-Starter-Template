<?php

use Illuminate\Support\Facades\Route;
use App\Presentation\Http\Controllers\Web\UserPageController;
use App\Presentation\Http\Controllers\Web\AuthController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->prefix('users')->group(function () {
    Route::get('/', [UserPageController::class, 'index']);
    Route::get('/create', [UserPageController::class, 'create']);
    Route::post('/', [UserPageController::class, 'store']);
    Route::get('/{id}/edit', [UserPageController::class, 'edit'])->whereNumber('id');
    Route::put('/{id}', [UserPageController::class, 'update'])->whereNumber('id');
    Route::get('/{id}', [UserPageController::class, 'show'])->whereNumber('id');
    Route::delete('/{id}', [UserPageController::class, 'destroy'])->whereNumber('id');
});

