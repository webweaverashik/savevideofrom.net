<?php

declare (strict_types = 1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@savevideofrom.net'],
            [
                'name'              => 'Administrator',
                'password'          => Hash::make('ChangeMe123!'),
                'is_admin'          => true,
                'email_verified_at' => now(),
            ],
        );
    }
}
