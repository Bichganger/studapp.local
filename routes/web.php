<?php

use Illuminate\Support\Facades\Route;

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

// Главная страница переадресуется на форму входа
Route::redirect('/', '/login.html');

// Маршруты аутентификации
Route::get('/login.html', function () {
    return view('login');
})->name('login');

Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);

Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

Route::get('/register.html', function () {
    return view('register');
})->name('register');

Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);

// Маршруты для разных ролей
Route::view('/admin.html', 'frontend.admin');
Route::view('/teacher.html', 'frontend.teacher');
Route::view('/student.html', 'frontend.student');
Route::view('/index.html', 'frontend.index');

// Статические страницы
Route::view('/profile.html', 'profile');
Route::view('/settings.html', 'settings');
Route::view('/schedule.html', 'schedule');
Route::view('/grades.html', 'grades');
Route::view('/marketplace.html', 'marketplace');
Route::view('/distance-learning.html', 'distance-learning');