<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\ContentPageController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/halaman/{slug}', [PageController::class, 'show'])->name('pages.show');

Route::get('/e-resources', [ContentPageController::class, 'resources'])->name('resources.index');
Route::get('/e-resources/{resource}', [ContentPageController::class, 'resource'])->name('resources.show');
Route::get('/layanan', [ContentPageController::class, 'services'])->name('services.index');
Route::get('/layanan/{service}', [ContentPageController::class, 'service'])->name('services.show');
Route::get('/fasilitas', [ContentPageController::class, 'facilities'])->name('facilities.index');
Route::get('/fasilitas/{facility}', [ContentPageController::class, 'facility'])->name('facilities.show');
Route::get('/pengumuman', [ContentPageController::class, 'announcements'])->name('announcements.index');
Route::get('/pengumuman/{announcement}', [ContentPageController::class, 'announcement'])->name('announcements.show');
Route::get('/agenda', [ContentPageController::class, 'agenda'])->name('agenda.index');
Route::get('/agenda/{agendaItem}', [ContentPageController::class, 'agendaItem'])->name('agenda.show');
Route::get('/berita', [ContentPageController::class, 'news'])->name('news.index');
Route::get('/berita/{newsPost}', [ContentPageController::class, 'newsPost'])->name('news.show');
Route::get('/galeri', [ContentPageController::class, 'galleries'])->name('galleries.index');
Route::get('/galeri/{gallery}', [ContentPageController::class, 'gallery'])->name('galleries.show');
Route::get('/staff', [ContentPageController::class, 'staff'])->name('staff.index');
Route::get('/staff/{staffMember}', [ContentPageController::class, 'staffMember'])->name('staff.show');

Route::middleware('guest')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::get('/settings', [SiteSettingController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [SiteSettingController::class, 'update'])->name('settings.update');

    Route::get('/content/{type}', [ContentController::class, 'index'])->name('content.index');
    Route::get('/content/{type}/create', [ContentController::class, 'create'])->name('content.create');
    Route::post('/content/{type}', [ContentController::class, 'store'])->name('content.store');
    Route::get('/content/{type}/{id}/edit', [ContentController::class, 'edit'])->name('content.edit');
    Route::put('/content/{type}/{id}', [ContentController::class, 'update'])->name('content.update');
    Route::delete('/content/{type}/{id}', [ContentController::class, 'destroy'])->name('content.destroy');
});
