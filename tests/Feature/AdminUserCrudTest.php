<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $cashier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Super Admin Test',
            'username' => 'admin_test',
            'email' => 'admintest@inventory.com',
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
            'phone' => '081234567890',
            'is_active' => true,
        ]);

        $this->cashier = User::create([
            'name' => 'Cashier Test',
            'username' => 'cashier_test',
            'email' => 'cashiertest@inventory.com',
            'password' => Hash::make('password123'),
            'role' => 'cashier',
            'phone' => '089876543210',
            'is_active' => true,
        ]);
    }

    public function test_guest_is_redirected_from_admin_users(): void
    {
        $response = $this->get(route('admin.users'));
        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_access_admin_users(): void
    {
        $response = $this->actingAs($this->cashier)->get(route('admin.users'));
        $response->assertRedirect(route('dashboard'));
    }

    public function test_admin_can_view_users_list_excluding_super_admin(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users'));

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.Admin.User');

        // Super admin harus tidak ada di data users yang dikirim ke view
        $users = $response->viewData('users');
        $this->assertFalse($users->contains('role', 'super_admin'));
        $this->assertFalse($users->contains('username', 'admin_test'));
        $this->assertTrue($users->contains('username', 'cashier_test'));

        // Super admin tidak boleh ada di dalam tabel
        $response->assertDontSee('admin_test');
        $response->assertDontSee('admintest@inventory.com');
        // Kasir / Staff Gudang harus muncul
        $response->assertSee('Cashier Test');
        $response->assertSee('cashier_test');
        $response->assertSee('cashiertest@inventory.com');
    }

    public function test_admin_can_create_new_user(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.users.store'), [
            'name' => 'New Warehouse Staff',
            'username' => 'warehouse_staff1',
            'email' => 'warehouse@inventory.com',
            'password' => 'secret123',
            'role' => 'warehouse_staff',
            'phone' => '081122334455',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.users'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name' => 'New Warehouse Staff',
            'username' => 'warehouse_staff1',
            'email' => 'warehouse@inventory.com',
            'role' => 'warehouse_staff',
            'phone' => '081122334455',
            'is_active' => true,
        ]);

        $created = User::where('username', 'warehouse_staff1')->first();
        $this->assertNotNull($created);
        $this->assertTrue(Hash::check('secret123', $created->password));
    }

    public function test_cannot_create_user_with_super_admin_role(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.users.store'), [
            'name' => 'Attempt Super Admin',
            'username' => 'super_attempt',
            'email' => 'super@inventory.com',
            'password' => 'secret123',
            'role' => 'super_admin',
        ]);

        $response->assertSessionHasErrors(['role']);
    }

    public function test_create_user_validates_required_fields(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.users.store'), [
            'name' => '',
            'username' => '',
            'email' => '',
            'password' => '',
            'role' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'username', 'email', 'password', 'role']);
    }

    public function test_create_user_prevents_duplicate_username_and_email(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.users.store'), [
            'name' => 'Duplicate User',
            'username' => 'cashier_test',
            'email' => 'cashiertest@inventory.com',
            'password' => 'secret123',
            'role' => 'cashier',
        ]);

        $response->assertSessionHasErrors(['username', 'email']);
    }

    public function test_admin_can_update_user_without_changing_password(): void
    {
        $response = $this->actingAs($this->admin)->put(route('admin.users.update', $this->cashier), [
            'name' => 'Updated Cashier Name',
            'username' => 'cashier_test',
            'email' => 'cashiertest@inventory.com',
            'password' => '', // blank to keep existing password
            'role' => 'cashier',
            'phone' => '081999999999',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.users'));
        $response->assertSessionHas('success');

        $this->cashier->refresh();
        $this->assertSame('Updated Cashier Name', $this->cashier->name);
        $this->assertSame('081999999999', $this->cashier->phone);
        $this->assertTrue(Hash::check('password123', $this->cashier->password));
    }

    public function test_admin_can_update_user_password(): void
    {
        $response = $this->actingAs($this->admin)->put(route('admin.users.update', $this->cashier), [
            'name' => 'Cashier Test',
            'username' => 'cashier_test',
            'email' => 'cashiertest@inventory.com',
            'password' => 'newpassword456',
            'role' => 'cashier',
            'phone' => '089876543210',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.users'));
        $this->cashier->refresh();
        $this->assertTrue(Hash::check('newpassword456', $this->cashier->password));
    }

    public function test_admin_can_deactivate_and_activate_user_via_status_dropdown(): void
    {
        // Deactivate user (is_active = '0')
        $response = $this->actingAs($this->admin)->put(route('admin.users.update', $this->cashier), [
            'name' => 'Cashier Test',
            'username' => 'cashier_test',
            'email' => 'cashiertest@inventory.com',
            'role' => 'cashier',
            'is_active' => '0',
        ]);

        $response->assertRedirect(route('admin.users'));
        $this->cashier->refresh();
        $this->assertFalse($this->cashier->is_active);

        // Reactivate user (is_active = '1')
        $response = $this->actingAs($this->admin)->put(route('admin.users.update', $this->cashier), [
            'name' => 'Cashier Test',
            'username' => 'cashier_test',
            'email' => 'cashiertest@inventory.com',
            'role' => 'cashier',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.users'));
        $this->cashier->refresh();
        $this->assertTrue($this->cashier->is_active);
    }

    public function test_admin_can_delete_other_user(): void
    {
        $target = User::create([
            'name' => 'To Be Deleted',
            'username' => 'delete_me',
            'email' => 'deleteme@inventory.com',
            'password' => Hash::make('password123'),
            'role' => 'cashier',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.users.destroy', $target));

        $response->assertRedirect(route('admin.users'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('users', ['id' => $target->id]);
    }

    public function test_admin_cannot_delete_super_admin(): void
    {
        $response = $this->actingAs($this->admin)->delete(route('admin.users.destroy', $this->admin));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }
}
