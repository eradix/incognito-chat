<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\UserController;

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

//guest middleware -> non authenticated user
Route::middleware(['guest'])->group(function () {
    Route::redirect('/', '/login');
    Route::get("/login", [UserController::class, 'login'])->name('login');
    Route::get("/register", [UserController::class, 'register'])->name('register');
    Route::post("/register", [UserController::class, 'storeUser'])->name('storeUser');
    Route::post("/login", [UserController::class, 'authenticateUser'])->name('authenticateUser');
});

//auth middleware -> authenticated user
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [ChatController::class, 'home'])->name('home');
    Route::get('/chat/{id}/{group?}', [ChatController::class, 'showChat'])->name('showChat');
    Route::post('/storeChat', [ChatController::class, 'storeChat'])->name('storeChat');
    Route::get('/createGroup', [ChatController::class, 'createGroup'])->name('createGroup');
    Route::post('/storeGroup', [ChatController::class, 'storeGroup'])->name('storeGroup');
    Route::put('/updateChatMsg', [ChatController::class, 'updateChat'])->name('updateChatMsg');
    Route::delete('/deleteChatMsg', [ChatController::class, 'deleteChatMsg'])->name('deleteChatMsg');
    Route::post('/addUserInAGroup/{group_id}', [ChatController::class, 'addUserInAGroup'])->name('addUserInAGroup');
    Route::delete('/leaveInAGroup/{group_id}', [ChatController::class, 'leaveInAGroup'])->name('leaveInAGroup');
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::put('/update-info', [UserController::class, 'updateProfile'])->name('updateProfile');
    Route::put('/updateUserPassword', [UserController::class, 'updateUserPassword'])->name('updateUserPassword');
    Route::delete('deleteUser', [UserController::class, 'deleteUser'])->name('deleteUser');
    Route::post('/fetchChat/{id}/{group?}', [ChatController::class, 'fetchChat'])->name('fetchChat');
    Route::post('/fetchChatPerUser', [ChatController::class, 'fetchChatPerUser'])->name('fetchChatPerUser');
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
});
