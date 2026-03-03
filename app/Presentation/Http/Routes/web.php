<?php

use Illuminate\Support\Facades\Route;
use App\Presentation\Http\Controllers\Web\UserWebController;
use App\Presentation\Http\Controllers\Web\AuthWebController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthWebController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthWebController::class, 'login']);
Route::post('/logout', [AuthWebController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->prefix('users')->group(function () {
    Route::get('/', [UserWebController::class, 'index']);
    Route::get('/create', [UserWebController::class, 'create']);
    Route::post('/', [UserWebController::class, 'store']);
    Route::get('/{id}/edit', [UserWebController::class, 'edit'])->whereNumber('id');
    Route::put('/{id}', [UserWebController::class, 'update'])->whereNumber('id');
    Route::get('/{id}', [UserWebController::class, 'show'])->whereNumber('id');
    Route::delete('/{id}', [UserWebController::class, 'destroy'])->whereNumber('id');
});

