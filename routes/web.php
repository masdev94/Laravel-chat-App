<?php

use App\Http\Controllers\AIController;
use App\Http\Controllers\AIMessageController;
use App\Http\Controllers\AIRoomController;
use App\Http\Controllers\ChatRoomController;
use App\Http\Controllers\SendMessageController;
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

require __DIR__.'/auth.php';

Route::middleware(['auth', 'verified'])->group(function () {
    // Regular chat rooms
    Route::get('/', function () {
        return redirect()->route('ai.rooms.index');
    });
    Route::get('/chat/{room?}', ChatRoomController::class)->name('chat.room');
    Route::post('/message', SendMessageController::class)->name('send.message');

    // AI chat rooms
    Route::prefix('ai')->name('ai.')->group(function () {
        Route::get('/rooms', [AIRoomController::class, 'index'])->name('rooms.index');
        Route::post('/rooms', [AIRoomController::class, 'store'])->name('rooms.store');
        Route::get('/room/{roomId}', [AIRoomController::class, 'show'])->name('room');
        Route::put('/room/{roomId}', [AIRoomController::class, 'update'])->name('room.update');
        Route::delete('/room/{roomId}', [AIRoomController::class, 'destroy'])->name('room.destroy');

        // AI messaging
        Route::post('/message', [AIMessageController::class, 'sendMessage'])->name('message.send');
        Route::get('/history/{roomId}', [AIMessageController::class, 'getHistory'])->name('history');
        Route::delete('/history/{roomId}', [AIMessageController::class, 'clearHistory'])->name('history.clear');

        // Legacy AI routes for regular chat rooms
        Route::post('/toggle', [AIController::class, 'toggleAI'])->name('toggle');
        Route::get('/status', [AIController::class, 'getAIStatus'])->name('status');
    });

    // Backward compatibility - commented out as it conflicts with other routes
    // Route::get('/{room}', function ($room) {
    //     return redirect()->route('chat.room', ['room' => $room]);
    // })->name('dashboard');
});
