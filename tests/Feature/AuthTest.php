<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertViewIs('auth.Login');
        $response->assertSee('name="email"', false);
        $response->assertSee('name="password"', false);
        $response->assertSee('togglePassword', false);
    }

    public function test_admin_can_login_with_email_and_is_redirected_to_admin_dashboard(): void
    {
        $admin = User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@inventory.com',
            'password' => Hash::make('admin123'),
            'role' => 'super_admin',
            'phone' => '081234567890',
            'is_active' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@inventory.com',
            'password' => 'admin123',
        ]);

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect(route('admin.dashboard'));

        $dashboardResponse = $this->actingAs($admin)->get('/admin/dashboard');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertViewIs('dashboard.Admin.Dashboard');
    }

    public function test_non_admin_is_redirected_to_general_dashboard(): void
    {
        $staff = User::create([
            'name' => 'Staff Gudang',
            'username' => 'staff',
            'email' => 'staff@inventory.com',
            'password' => Hash::make('staff123'),
            'role' => 'warehouse_staff',
            'phone' => '081234567891',
            'is_active' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'staff@inventory.com',
            'password' => 'staff123',
        ]);

        $this->assertAuthenticatedAs($staff);
        $response->assertRedirect(route('dashboard'));
    }

    public function test_non_admin_cannot_access_admin_dashboard(): void
    {
        $cashier = User::create([
            'name' => 'Kasir',
            'username' => 'kasir',
            'email' => 'kasir@inventory.com',
            'password' => Hash::make('kasir123'),
            'role' => 'cashier',
            'phone' => '081234567892',
            'is_active' => true,
        ]);

        $response = $this->actingAs($cashier)->get('/admin/dashboard');

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error');
    }

    public function test_inactive_user_cannot_login(): void
    {
        User::create([
            'name' => 'Nonaktif',
            'username' => 'nonaktif',
            'email' => 'nonaktif@inventory.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'phone' => '081234567899',
            'is_active' => false,
        ]);

        $response = $this->post('/login', [
            'email' => 'nonaktif@inventory.com',
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_user_cannot_login_with_invalid_password(): void
    {
        User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@inventory.com',
            'password' => Hash::make('admin123'),
            'role' => 'super_admin',
            'phone' => '081234567890',
            'is_active' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@inventory.com',
            'password' => 'wrongpassword',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_user_can_logout(): void
    {
        $admin = User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@inventory.com',
            'password' => Hash::make('admin123'),
            'role' => 'super_admin',
            'phone' => '081234567890',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_access_admin_users_view(): void
    {
        $admin = User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@inventory.com',
            'password' => Hash::make('admin123'),
            'role' => 'super_admin',
            'phone' => '081234567890',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get('/admin/users');

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.Admin.User');
        $response->assertSee('dataTable-1');
        $response->assertSee('No');
        $response->assertSee('Nama');
        $response->assertSee('Username');
        $response->assertSee('Email');
        $response->assertSee('Role');
        $response->assertSee('Status');
        $response->assertSee('Action');
        $response->assertSee('Edit');
        $response->assertSee('Delete');
    }
}
