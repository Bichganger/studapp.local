<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

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

Route::redirect('/', '/login');

// Маршруты аутентификации (должны быть доступны всем)
Auth::routes();

// Группа маршрутов для авторизованных пользователей (админ и студент)
Route::middleware(['auth'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');

    // Перенаправление после аутентификации в зависимости от роли
    Route::get('/home', function () {
        $user = auth()->user();
        if ($user->isAdmin()) {
            return redirect('/frontend/admin.html');
        }
        return redirect('/index.html');
    })->name('home');
});


    // Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home'); // Закомментировано, так как логика перенесена в другой маршрут
