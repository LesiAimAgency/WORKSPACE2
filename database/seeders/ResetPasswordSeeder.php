<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ResetPasswordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $newPassword = '123456789';

        $this->command->info("--- Đang đặt lại mật khẩu về '{$newPassword}' ---");

        // 1. Reset mật khẩu cho tất cả tài khoản hiện có trong hệ thống
        $users = User::all();

        if ($users->isNotEmpty()) {
            foreach ($users as $user) {
                $user->password = Hash::make($newPassword);
                $user->status = true; // Kích hoạt tài khoản nếu đang bị khóa
                $user->save();

                $this->command->info("✓ Đã cập nhật: [ID: {$user->id}] Email: {$user->email} | Username: {$user->username} | Role: {$user->role}");
            }
        }

        // 2. Đảm bảo tài khoản Super Admin (admin@example.com) luôn tồn tại
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin (Root)',
                'username' => 'admin',
                'password' => Hash::make($newPassword),
                'role' => User::ROLE_ADMIN,
                'status' => true,
                'level' => 0,
            ]
        );

        if ($admin->wasRecentlyCreated) {
            $this->command->info("✓ Đã tạo mới tài khoản Super Admin: admin@example.com (Username: admin)");
        } else {
            $admin->password = Hash::make($newPassword);
            $admin->status = true;
            $admin->save();
        }

        $this->command->info("==========================================");
        $this->command->info("Thành công! Mật khẩu cho các tài khoản đã được đổi thành: {$newPassword}");
        $this->command->info("Bạn có thể đăng nhập bằng Email: admin@example.com hoặc Username: admin");
        $this->command->info("==========================================");
    }
}
