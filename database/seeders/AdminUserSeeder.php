<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kiểm tra xem đã có admin chưa
        $adminExists = User::where('is_admin', true)->exists();

        if (!$adminExists) {
            User::create([
                'name' => 'Administrator',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'is_admin' => true,
            ]);

            $this->command->info('Admin user created successfully');
        } else {
            $this->command->info('Admin user already exists');
        }

        // Nếu muốn cập nhật tài khoản hiện tại thành admin (ví dụ user ID=1)
        DB::table('users')
            ->where('id', 1)
            ->update(['is_admin' => true]);

        $this->command->info('First user updated to admin');
    }
}
