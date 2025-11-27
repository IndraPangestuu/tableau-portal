<?php

use Illuminate\Support\Facades\Route;

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
// redirect root ke login/dashboard
Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : redirect('/login');
});

// halaman sebelum login
Route::get('/login', [App\Http\Controllers\AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

// halaman embed tableau
Route::get('/embed', [App\Http\Controllers\EmbedController::class, 'show'])->middleware('auth');

// halaman setelah login
Route::get('/dashboard', [App\Http\Controllers\EmbedController::class, 'dashboard'])->middleware('auth');

// View dashboard by menu
Route::get('/view/{menu}', [App\Http\Controllers\EmbedController::class, 'viewMenu'])->middleware('auth')->name('view.menu');

// Admin - User & Menu Management
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    // Users
    Route::get('/users', [App\Http\Controllers\UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [App\Http\Controllers\UserController::class, 'create'])->name('users.create');
    Route::post('/users', [App\Http\Controllers\UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [App\Http\Controllers\UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [App\Http\Controllers\UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');

    // Menus
    Route::get('/menus', [App\Http\Controllers\MenuController::class, 'index'])->name('menus.index');
    Route::get('/menus/create', [App\Http\Controllers\MenuController::class, 'create'])->name('menus.create');
    Route::post('/menus', [App\Http\Controllers\MenuController::class, 'store'])->name('menus.store');
    Route::get('/menus/{menu}/edit', [App\Http\Controllers\MenuController::class, 'edit'])->name('menus.edit');
    Route::put('/menus/{menu}', [App\Http\Controllers\MenuController::class, 'update'])->name('menus.update');
    Route::delete('/menus/{menu}', [App\Http\Controllers\MenuController::class, 'destroy'])->name('menus.destroy');
    Route::post('/menus/reorder', [App\Http\Controllers\MenuController::class, 'reorder'])->name('menus.reorder');
    Route::get('/menus/fetch-views', [App\Http\Controllers\MenuController::class, 'fetchTableauViews'])->name('menus.fetch-views');
    Route::get('/menus/fetch-sites', [App\Http\Controllers\MenuController::class, 'fetchTableauSites'])->name('menus.fetch-sites');
});