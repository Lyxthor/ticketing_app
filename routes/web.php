<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;
use App\Models\Kategori;
use App\Models\Event;
use App\Http\Controllers\KategoriController;

Route::get('/', function () {
    return view('home', [
        'categories' => Kategori::all(),
        'events' => Event::all()
    ]);
})->name('home');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    Route::resource('kategori', KategoriController::class);
});
// Category routes (admin)
Route::prefix('admin')->name('categories.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/categories', [DashboardController::class, 'index'])->name('index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('store');
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('update');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('destroy');
});

require __DIR__.'/auth.php';
