<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Super Admin
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator',
                'email' => 'admin@inventory.com',
                'password' => Hash::make('admin123'),
                'role' => 'super_admin',
                'phone' => '081234567890',
                'is_active' => true,
            ]
        );

        // Warehouse Staff
        User::firstOrCreate(
            ['username' => 'staff'],
            [
                'name' => 'Staff Gudang',
                'email' => 'staff@inventory.com',
                'password' => Hash::make('staff123'),
                'role' => 'warehouse_staff',
                'phone' => '081234567891',
                'is_active' => true,
            ]
        );

        // Cashier
        User::firstOrCreate(
            ['username' => 'kasir'],
            [
                'name' => 'Kasir',
                'email' => 'kasir@inventory.com',
                'password' => Hash::make('kasir123'),
                'role' => 'cashier',
                'phone' => '081234567892',
                'is_active' => true,
            ]
        );
    }
}

