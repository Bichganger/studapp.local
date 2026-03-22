<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /**
     * Показать форму регистрации.
     */
    public function showRegistrationForm()
    {
        return view('register');
    }

    /**
     * Обработать регистрацию пользователя.
     */
    public function register(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $this->create($request->all());

        // В реальном приложении здесь должна быть аутентификация
        // Auth::login($user);
        
        return response()->json([
            'success' => true,
            'message' => 'Регистрация прошла успешно!'
        ]);
    }

    /**
     * Получить валидатор для входящих данных.
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'group' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Создать нового пользователя.
     */
    protected function create(array $data)
    {
        return User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'group' => $data['group'],
            'role' => 'student', // По умолчанию все новые пользователи - студенты
            'password' => Hash::make($data['password']),
        ]);
    }
}