<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. إنشاء الدور أولاً إذا لم يكن موجوداً
        DB::table('roles')->updateOrInsert(
            ['id' => 1],
            [
                'name' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 2. إنشاء حساب المدير
        AdminUser::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'role_id' => 1,
                'full_name' => 'المدير العام',
                'phone' => '770000000',
                'password_hash' => Hash::make('Password1@'),
                'two_factor_enabled' => false,
            ]
        );
    }
}