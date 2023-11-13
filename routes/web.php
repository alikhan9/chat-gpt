<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\ChatRoomsController;
use App\Http\Controllers\ChatSettingsController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
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

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    Route::get('/', [ChatController::class, 'home']);
    Route::post('/chat-rooms', [ChatRoomsController::class, 'store']);
    Route::post('/message', [MessageController::class,'store']);
    Route::delete('/chat-rooms/{chatRoom}', [ChatRoomsController::class,'destroy']);
    Route::put('/chat-rooms/{chatRoom}', [ChatRoomsController::class,'update']);
    Route::put('chat-settings', [ChatSettingsController::class, 'update']);
});

require __DIR__.'/auth.php';
