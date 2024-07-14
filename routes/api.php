<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\PostController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [UserController::class, 'login'])->middleware('throttle:login');;

Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::get('/users', [AdminController::class, 'listUsers'])->name('admin.users.list');
    Route::post('/users', [AdminController::class, 'createUser'])->name('admin.users.create');
    Route::get('/users/{id}', [AdminController::class, 'getUser'])->name('admin.users.get');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    
    Route::post('/post-list', [AdminController::class, 'postList'])->name('admin.post-list');
});


Route::middleware(['auth:sanctum', 'check.notadmin'])->group(function () {
    Route::prefix('posts')->group(function () {
        //posts
        Route::get('/', [PostController::class, 'index'])->name('posts.index');
        Route::post('/', [PostController::class, 'store'])->name('posts.store');
        Route::get('/{id}', [PostController::class, 'show'])->name('posts.show');
        Route::put('/{id}', [PostController::class, 'update'])->name('posts.update');
        Route::delete('/{id}', [PostController::class, 'destroy'])->name('posts.destroy');
    });

    Route::post('/report', [PostController::class, 'report']);
});





Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
