<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\Api;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Site\EventController;
use App\Http\Controllers\Site\HomeController;
use App\Http\Controllers\Site\LocaleController;
use App\Http\Controllers\Site\PageController;
use App\Http\Controllers\Site\PostController;
use App\Support\Localization;
use Illuminate\Support\Facades\Route;

/*
| Public website (Blade), in every language: English at the root (/events),
| other languages under their code (/nl/events, /id/events). Route names follow
| the same pattern: "events.index", "nl.events.index", "id.events.index".
*/
$publicRoutes = function () {
    Route::get('/', HomeController::class)->name('home');
    Route::get('/about', [PageController::class, 'about'])->name('about');
    Route::get('/contact', [PageController::class, 'contact'])->name('contact');
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
    Route::get('/blog', [PostController::class, 'index'])->name('blog.index');
    Route::get('/blog/{post}', [PostController::class, 'show'])->name('blog.show');
};

foreach (Localization::locales() as $locale) {
    $locale === Localization::default()
        ? Route::group(['locale' => $locale], $publicRoutes)
        : Route::group(['locale' => $locale, 'prefix' => $locale, 'as' => "{$locale}."], $publicRoutes);
}

// Language choice for pages without a language in the URL (login).
Route::get('/language/{locale}', LocaleController::class)->name('locale');

/*
| Authentication
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

/*
| Admin (Vue 3 SPA + JSON endpoints, session authenticated).
| Every logged-in user manages events and news; only admins manage users.
*/
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/account', [Api\AccountController::class, 'show'])->name('account');
        Route::put('/account/password', [Api\AccountController::class, 'updatePassword'])->name('account.password');
        Route::get('/dashboard', Api\DashboardController::class)->name('dashboard');
        Route::get('/events/options', [Api\EventController::class, 'options'])->name('events.options');
        Route::apiResource('events', Api\EventController::class)->scoped(['event' => 'id']);
        Route::apiResource('posts', Api\PostController::class)->scoped(['post' => 'id']);
        Route::apiResource('users', Api\UserController::class)->middleware('admin');
    });

    Route::get('/{any?}', AdminController::class)->where('any', '^(?!api).*$')->name('app');
});
