<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AboutSectionController;
use App\Http\Controllers\ProductController;
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

    Route::prefix('about')->name('about-page.')->group(function () {
        Route::get('/page', [App\Http\Controllers\AboutPageController::class, 'index'])->name('index');
        Route::get('/page/create', [App\Http\Controllers\AboutPageController::class, 'create'])->name('create');
        Route::post('/page/store', [App\Http\Controllers\AboutPageController::class, 'store'])->name('store');
        Route::get('/page/edit/{id}', [App\Http\Controllers\AboutPageController::class, 'edit'])->name('edit');
        Route::put('/page/update/{id}', [App\Http\Controllers\AboutPageController::class, 'update'])->name('update');
        Route::delete('/page/delete/{id}', [App\Http\Controllers\AboutPageController::class, 'destroy'])->name('destroy');
    });

Route::prefix('product')->name('product.')->group(function () {

    Route::get('/', [ProductController::class,'index'])->name('index');
    Route::get('/create', [ProductController::class,'create'])->name('create');
    Route::post('/store', [ProductController::class,'store'])->name('store');
    Route::get('/edit/{id}', [ProductController::class,'edit'])->name('edit');
    Route::put('/update/{id}', [ProductController::class,'update'])->name('update');
    Route::delete('/delete/{id}', [ProductController::class,'destroy'])->name('destroy');

});

Route::prefix('quality')->name('quality.')->group(function () {

    Route::get('/', [App\Http\Controllers\QualityController::class,'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\QualityController::class,'create'])->name('create');
    Route::post('/store', [App\Http\Controllers\QualityController::class,'store'])->name('store');
    Route::get('/edit/{id}', [App\Http\Controllers\QualityController::class,'edit'])->name('edit');
    Route::put('/update/{id}', [App\Http\Controllers\QualityController::class,'update'])->name('update');
    Route::delete('/delete/{id}', [App\Http\Controllers\QualityController::class,'destroy'])->name('destroy');

});

Route::prefix('gallery')->name('gallery.')->group(function () {

    Route::get('/', [App\Http\Controllers\GalleryController::class,'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\GalleryController::class,'create'])->name('create');
    Route::post('/store', [App\Http\Controllers\GalleryController::class,'store'])->name('store');
    Route::get('/edit/{id}', [App\Http\Controllers\GalleryController::class,'edit'])->name('edit');
    Route::put('/update/{id}', [App\Http\Controllers\GalleryController::class,'update'])->name('update');
    Route::delete('/delete/{id}', [App\Http\Controllers\GalleryController::class,'destroy'])->name('destroy');

});

Route::prefix('certificate')->name('certificate.')->group(function () {

    Route::get('/', [App\Http\Controllers\CertificateController::class,'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\CertificateController::class,'create'])->name('create');
    Route::post('/store', [App\Http\Controllers\CertificateController::class,'store'])->name('store');
    Route::get('/edit/{id}', [App\Http\Controllers\CertificateController::class,'edit'])->name('edit');
    Route::put('/update/{id}', [App\Http\Controllers\CertificateController::class,'update'])->name('update');
    Route::delete('/delete/{id}', [App\Http\Controllers\CertificateController::class,'destroy'])->name('destroy');

});

Route::prefix('contact')->name('contact.')->group(function () {

    Route::get('/', [App\Http\Controllers\ContactController::class,'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\ContactController::class,'create'])->name('create');
    Route::post('/store', [App\Http\Controllers\ContactController::class,'store'])->name('store');
    Route::get('/edit/{id}', [App\Http\Controllers\ContactController::class,'edit'])->name('edit');
    Route::put('/update/{id}', [App\Http\Controllers\ContactController::class,'update'])->name('update');
    Route::delete('/delete/{id}', [App\Http\Controllers\ContactController::class,'destroy'])->name('destroy');

});

Route::prefix('footer')->name('footer.')->group(function () {

    Route::get('/', [App\Http\Controllers\FooterController::class,'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\FooterController::class,'create'])->name('create');
    Route::post('/store', [App\Http\Controllers\FooterController::class,'store'])->name('store');
    Route::get('/edit/{id}', [App\Http\Controllers\FooterController::class,'edit'])->name('edit');
    Route::put('/update/{id}', [App\Http\Controllers\FooterController::class,'update'])->name('update');
    Route::delete('/delete/{id}', [App\Http\Controllers\FooterController::class,'destroy'])->name('destroy');

});

Route::prefix('blog')->name('blog.')->group(function () {

    Route::get('/', [App\Http\Controllers\BlogController::class,'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\BlogController::class,'create'])->name('create');
    Route::post('/store', [App\Http\Controllers\BlogController::class,'store'])->name('store');
    Route::get('/edit/{id}', [App\Http\Controllers\BlogController::class,'edit'])->name('edit');
    Route::put('/update/{id}', [App\Http\Controllers\BlogController::class,'update'])->name('update');
    Route::delete('/delete/{id}', [App\Http\Controllers\BlogController::class,'destroy'])->name('destroy');
    // AI Generation Route
    Route::post('/generate', [App\Http\Controllers\BlogController::class,'generate'])->name('generate');

});

Route::prefix('why_choose')->name('why_choose.')->group(function () {

    Route::get('/', [App\Http\Controllers\WhyChooseController::class,'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\WhyChooseController::class,'create'])->name('create');
    Route::post('/store', [App\Http\Controllers\WhyChooseController::class,'store'])->name('store');
    Route::get('/edit/{id}', [App\Http\Controllers\WhyChooseController::class,'edit'])->name('edit');
    Route::put('/update/{id}', [App\Http\Controllers\WhyChooseController::class,'update'])->name('update');
    Route::delete('/delete/{id}', [App\Http\Controllers\WhyChooseController::class,'destroy'])->name('destroy');

});

});

require __DIR__ . '/auth.php';
