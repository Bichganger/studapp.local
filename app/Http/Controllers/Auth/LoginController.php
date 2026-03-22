<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * Показать форму входа.
     */
    public function showLoginForm()
    {
        return view('login');
    }

    /**
     * Обработать вход пользователя.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            // Аутентификация успешна
            Auth::login($user, $request->filled('remember'));
            
            // Определяем URL для перенаправления в зависимости от роли
            $redirectUrl = $this->redirectTo($user);
            
            return response()->json([
                'success' => true,
                'redirect' => $redirectUrl,
                'message' => 'Добро пожаловать, ' . $user->first_name . '!',
                'role' => $user->role
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Неверные учетные данные.'
        ], 401);
    }

    /**
     * Выход пользователя.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login.html');
    }

    /**
     * Получить URL для перенаправления после входа.
     */
    protected function redirectTo($user)
    {
        switch ($user->role) {
            case 'admin':
                return '/admin.html';
            case 'teacher':
                return '/teacher.html';
            case 'student':
                return '/student.html';
            default:
                return '/index.html';
        }
    }
}