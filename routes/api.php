<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AuthMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->name('auth.')->controller(UserController::class)->group(function () {
    Route::post('/register', 'register')->name('register');
    Route::post('/login', 'login')->name('login');
    Route::post('/logout', 'logout')->name('logout')->middleware(AuthMiddleware::class);
});

Route::middleware(AuthMiddleware::class)->prefix('users')->name('users.')->controller(UserController::class)->group(function () {
    Route::get('/me', 'me')->name('me');
    Route::patch('/me', 'updateMe')->name('updateMe');
});

Route::middleware(AuthMiddleware::class)->prefix('contacts')->name('contacts.')->controller(ContactController::class)->group(function () {
    Route::post('/', 'create')->name('create');
    Route::get('/', 'search')->name('search');
    Route::get('/{id}', 'getById')->name('getById');
    Route::put('/{id}', 'update')->name('update');
    Route::delete('/{id}', 'delete')->name('delete');
});
