<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FollowController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/admins-only', function() {
    if (Gate::allows('visitAdminPages')) {
        return 'Only admins can visit this page';
    }
})->middleware('can:visitAdminPages');

// Users
Route::get('/', [UserController::class, "showCorrectHomepage"])->name('login');
Route::post('/register', [UserController::class, 'register'])->middleware('guest');
Route::post('/login', [UserController::class, 'login'])->middleware('guest');
Route::post('/logout', [UserController::class, 'logout'])->middleware('auth');

// User Profile
Route::get('/profile/{user:username}', [UserController::class, "profile"]);
Route::get('/manage-avatar', [UserController::class, "showAvatarForm"])->middleware('login');
Route::post('/manage-avatar', [UserController::class, "storeAvatar"])->middleware('login');

// Follow
Route::post('/create-follow/{user:username}', [FollowController::class, "createFollow"])->middleware('login');
Route::post('/remove-follow/{user:username}', [FollowController::class, "removeFollow"])->middleware('login');

// Posts
Route::get('/create-post', [PostController::class, 'showCreateForm'])->middleware('auth');
Route::post('/create-post', [PostController::class, 'storeNewPost'])->middleware('auth');
Route::delete('/post/{post}', [PostController::class, 'delete'])->middleware('can:delete,post');
Route::get('/post/{post}', [PostController::class, 'showSinglePost']);
Route::get('/post/{post}/edit', [PostController::class, 'showEditForm'])->middleware('can:update,post');
Route::put('/post/{post}', [PostController::class, 'update'])->middleware('can:update,post');