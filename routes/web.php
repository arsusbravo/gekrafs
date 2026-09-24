<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\Api;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Site\EventController;
use App\Http\Controllers\Site\HomeController;
use App\Http\Controllers\Site\LocaleController;
use App\Http\Controllers\Site\PageController;
use App\Http\Controllers\Site\PostController;
use Illuminate\Support\Facades\Route;

/*
| Public website (Blade)
*/
Route::get('/', HomeController::class)->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/language/{locale}', LocaleController::class)->name('locale');
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
Route::get('/blog', [PostController::class, 'index'])->name('blog.index');
Route::get('/blog/{post}', [PostController::class, 'show'])->name('blog.show');

/*
| Authentication
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

/*
| Admin (Vue 3 SPA + JSON endpoints, session authenticated)
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/me', fn () => request()->user()->only('id', 'name', 'email'))->name('me');
        Route::get('/dashboard', Api\DashboardController::class)->name('dashboard');
        Route::get('/events/options', [Api\EventController::class, 'options'])->name('events.options');
        Route::apiResource('events', Api\EventController::class)->scoped(['event' => 'id']);
        Route::apiResource('posts', Api\PostController::class)->scoped(['post' => 'id']);
        Route::apiResource('users', Api\UserController::class);
    });

    Route::get('/{any?}', AdminController::class)->where('any', '^(?!api).*$')->name('app');
});
