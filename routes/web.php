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
// Halaman home (landing page)
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : view('home');
})->name('home');

// halaman sebelum login
Route::get('/login', [App\Http\Controllers\AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

// halaman embed tableau
Route::get('/embed', [App\Http\Controllers\EmbedController::class, 'show'])->middleware('auth')->name('embed');

// halaman setelah login
Route::get('/dashboard', [App\Http\Controllers\EmbedController::class, 'dashboard'])->middleware('auth')->name('dashboard');

// View dashboard by menu
Route::get('/view/{menu}', [App\Http\Controllers\EmbedController::class, 'viewMenu'])->middleware('auth')->name('view.menu');

// User Profile & Favorites
Route::middleware('auth')->group(function () {
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/favorites/{menu}/toggle', [App\Http\Controllers\FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::get('/favorites', [App\Http\Controllers\FavoriteController::class, 'index'])->name('favorites.index');
    Route::get('/search/menus', [App\Http\Controllers\SearchController::class, 'menus'])->name('search.menus');
});

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

    // Backups
    Route::get('/backups', [App\Http\Controllers\Admin\BackupController::class, 'index'])->name('backups.index');
    Route::post('/backups', [App\Http\Controllers\Admin\BackupController::class, 'store'])->name('backups.store');
    Route::get('/backups/{filename}/download', [App\Http\Controllers\Admin\BackupController::class, 'download'])->name('backups.download');
    Route::post('/backups/{filename}/restore', [App\Http\Controllers\Admin\BackupController::class, 'restore'])->name('backups.restore');
    Route::delete('/backups/{filename}', [App\Http\Controllers\Admin\BackupController::class, 'destroy'])->name('backups.destroy');

    // Settings
    Route::get('/settings', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');

    // User Menu Access
    Route::get('/users/{user}/menus', [App\Http\Controllers\UserController::class, 'editMenuAccess'])->name('users.menus');
    Route::put('/users/{user}/menus', [App\Http\Controllers\UserController::class, 'updateMenuAccess'])->name('users.menus.update');
});