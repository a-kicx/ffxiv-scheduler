<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ParticipantController;
use Illuminate\Support\Facades\Route;

// Top: create event
Route::get('/', [EventController::class, 'create'])->name('events.create');
Route::post('/', [EventController::class, 'store'])->name('events.store');

// Public: view event & participate
Route::prefix('{event:ulid}')->name('events.')->group(function () {
    Route::get('/', [EventController::class, 'show'])->name('show');
    Route::post('/join', [ParticipantController::class, 'store'])->name('participants.store');
    Route::get('/edit', [ParticipantController::class, 'edit'])->name('participants.edit');
    Route::put('/edit', [ParticipantController::class, 'update'])->name('participants.update');
});

// Admin: manage event
Route::prefix('{event:ulid}/admin/{adminToken}')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'show'])->name('show');
    Route::get('/edit', [AdminController::class, 'edit'])->name('edit');
    Route::put('/edit', [AdminController::class, 'update'])->name('update');
    Route::post('/clear/soft', [AdminController::class, 'clearSoft'])->name('clear.soft');
    Route::post('/clear/hard', [AdminController::class, 'clearHard'])->name('clear.hard');
});
