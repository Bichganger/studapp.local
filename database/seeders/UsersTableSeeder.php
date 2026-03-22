<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Создание обычного пользователя
        User::create([
            'name' => 'Василий Петров',
            'email' => 'student@studapp.local',
            'password' => Hash::make('stud1234'),
            'is_admin' => false,
        ]);

        // Создание администратора
        User::create([
            'name' => 'Ольга Иванова',
            'email' => 'admin@studapp.local',
            'password' => Hash::make('admin1234'),
            'is_admin' => true,
        ]);
    }
}
