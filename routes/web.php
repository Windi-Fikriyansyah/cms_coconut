<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AboutSectionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Hero Section Routes
    Route::get('/hero-section', [App\Http\Controllers\HeroSectionController::class, 'index'])->name('hero.index');
    Route::get('/hero-section/create', [App\Http\Controllers\HeroSectionController::class, 'create'])->name('hero.create');
    Route::post('/hero-section', [App\Http\Controllers\HeroSectionController::class, 'store'])->name('hero.store');
    Route::get('/hero-section/{id}/edit', [App\Http\Controllers\HeroSectionController::class, 'edit'])->name('hero.edit');
    Route::put('/hero-section/{id}', [App\Http\Controllers\HeroSectionController::class, 'update'])->name('hero.update');
    Route::delete('/hero-section/{id}', [App\Http\Controllers\HeroSectionController::class, 'destroy'])->name('hero.destroy');

    Route::prefix('about')->name('about.')->group(function () {

    Route::get('/', [AboutSectionController::class,'index'])->name('index');
    Route::get('/create', [AboutSectionController::class,'create'])->name('create');
    Route::post('/store', [AboutSectionController::class,'store'])->name('store');
    Route::get('/edit/{id}', [AboutSectionController::class,'edit'])->name('edit');
    Route::put('/update/{id}', [AboutSectionController::class,'update'])->name('update');
    Route::delete('/delete/{id}', [AboutSectionController::class,'destroy'])->name('destroy');

});

});

require __DIR__ . '/auth.php';
