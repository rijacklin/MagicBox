<?php

use App\Http\Controllers\CardController;
use App\Http\Controllers\CollectionCardController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\CollectionShareController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/cards', [CardController::class, 'index'])->name('cards.index');
    Route::get('/cards/{card}', [CardController::class, 'show'])->name('cards.show');
    Route::get('/api/cards/search', [CardController::class, 'search'])->name('cards.search');
    Route::get('/api/cards/autocomplete', [CardController::class, 'autocomplete'])->name('cards.autocomplete');

    Route::resource('collections', CollectionController::class);

    Route::post('/collections/{collection}/cards', [CollectionCardController::class, 'store'])->name('collections.cards.store');
    Route::patch('/collections/{collection}/cards/{card}', [CollectionCardController::class, 'update'])->name('collections.cards.update');
    Route::delete('/collections/{collection}/cards/{card}', [CollectionCardController::class, 'destroy'])->name('collections.cards.destroy');

    Route::prefix('collections/{collection}/shares')->name('collections.shares.')->group(function () {
        Route::post('/', [CollectionShareController::class, 'store'])->name('store');
        Route::delete('/{share}', [CollectionShareController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('shares')->name('shares.')->group(function () {
        Route::post('/{share}/accept', [CollectionShareController::class, 'accept'])->name('accept');
        Route::post('/{share}/reject', [CollectionShareController::class, 'reject'])->name('reject');
    });
});

require __DIR__.'/auth.php';
