<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CategoryAndUnitCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $staff;
    private User $cashier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Cat Test',
            'username' => 'admin_cat',
            'email' => 'admincat@inventory.com',
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->staff = User::create([
            'name' => 'Staff Cat Test',
            'username' => 'staff_cat',
            'email' => 'staffcat@inventory.com',
            'password' => Hash::make('password123'),
            'role' => 'warehouse_staff',
            'is_active' => true,
        ]);

        $this->cashier = User::create([
            'name' => 'Cashier Cat Test',
            'username' => 'cashier_cat',
            'email' => 'cashiercat@inventory.com',
            'password' => Hash::make('password123'),
            'role' => 'cashier',
            'is_active' => true,
        ]);
    }

    // --- CATEGORY TESTS ---

    public function test_cashier_cannot_access_categories(): void
    {
        $response = $this->actingAs($this->cashier)->get(route('categories.index'));
        $response->assertRedirect(route('dashboard'));
    }

    public function test_staff_and_admin_can_view_categories(): void
    {
        $cat = Category::create(['code' => 'TEST', 'name' => 'Test Category']);

        $responseAdmin = $this->actingAs($this->admin)->get(route('categories.index'));
        $responseAdmin->assertStatus(200);
        $responseAdmin->assertViewIs('dashboard.Product.Category');
        $responseAdmin->assertSee('Test Category');

        $responseStaff = $this->actingAs($this->staff)->get(route('categories.index'));
        $responseStaff->assertStatus(200);
        $responseStaff->assertSee('Test Category');
    }

    public function test_staff_can_create_category(): void
    {
        $response = $this->actingAs($this->staff)->post(route('categories.store'), [
            'code' => 'otm',
            'name' => 'Otomotif',
        ]);

        $response->assertRedirect(route('categories.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('categories', [
            'code' => 'OTM',
            'name' => 'Otomotif',
        ]);
    }

    public function test_admin_can_update_category(): void
    {
        $cat = Category::create(['code' => 'FSH', 'name' => 'Fashion']);

        $response = $this->actingAs($this->admin)->put(route('categories.update', $cat), [
            'code' => 'FSH-MOD',
            'name' => 'Fashion & Pakaian',
        ]);

        $response->assertRedirect(route('categories.index'));
        $response->assertSessionHas('success');

        $cat->refresh();
        $this->assertSame('FSH-MOD', $cat->code);
        $this->assertSame('Fashion & Pakaian', $cat->name);
    }

    public function test_cannot_delete_category_in_use_by_products(): void
    {
        $cat = Category::create(['code' => 'IN_USE', 'name' => 'Category In Use']);
        $unit = Unit::create(['code' => 'PC1', 'name' => 'Pieces']);

        Product::create([
            'category_id' => $cat->id,
            'unit_id' => $unit->id,
            'sku' => 'SKU-IN-USE',
            'name' => 'Product Connected',
            'purchase_price' => 1000,
            'selling_price' => 2000,
            'stock' => 10,
            'minimum_stock' => 1,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('categories.destroy', $cat));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $cat->id]);
    }

    public function test_can_delete_unused_category(): void
    {
        $cat = Category::create(['code' => 'UNUSED', 'name' => 'Unused Category']);

        $response = $this->actingAs($this->admin)->delete(route('categories.destroy', $cat));

        $response->assertRedirect(route('categories.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('categories', ['id' => $cat->id]);
    }

    // --- UNIT TESTS ---

    public function test_cashier_cannot_access_units(): void
    {
        $response = $this->actingAs($this->cashier)->get(route('units.index'));
        $response->assertRedirect(route('dashboard'));
    }

    public function test_staff_and_admin_can_view_units(): void
    {
        $unit = Unit::create(['code' => 'ROLL', 'name' => 'Roll']);

        $responseAdmin = $this->actingAs($this->admin)->get(route('units.index'));
        $responseAdmin->assertStatus(200);
        $responseAdmin->assertViewIs('dashboard.Product.Unit');
        $responseAdmin->assertSee('Roll');

        $responseStaff = $this->actingAs($this->staff)->get(route('units.index'));
        $responseStaff->assertStatus(200);
        $responseStaff->assertSee('Roll');
    }

    public function test_staff_can_create_unit(): void
    {
        $response = $this->actingAs($this->staff)->post(route('units.store'), [
            'code' => 'btl',
            'name' => 'Botol',
        ]);

        $response->assertRedirect(route('units.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('units', [
            'code' => 'BTL',
            'name' => 'Botol',
        ]);
    }

    public function test_admin_can_update_unit(): void
    {
        $unit = Unit::create(['code' => 'CAN', 'name' => 'Can']);

        $response = $this->actingAs($this->admin)->put(route('units.update', $unit), [
            'code' => 'KLG',
            'name' => 'Kaleng',
        ]);

        $response->assertRedirect(route('units.index'));
        $response->assertSessionHas('success');

        $unit->refresh();
        $this->assertSame('KLG', $unit->code);
        $this->assertSame('Kaleng', $unit->name);
    }

    public function test_cannot_delete_unit_in_use_by_products(): void
    {
        $cat = Category::create(['code' => 'CAT1', 'name' => 'Cat 1']);
        $unit = Unit::create(['code' => 'IN_USE_U', 'name' => 'Unit In Use']);

        Product::create([
            'category_id' => $cat->id,
            'unit_id' => $unit->id,
            'sku' => 'SKU-UNIT-IN-USE',
            'name' => 'Product With Unit',
            'purchase_price' => 1000,
            'selling_price' => 2000,
            'stock' => 10,
            'minimum_stock' => 1,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('units.destroy', $unit));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('units', ['id' => $unit->id]);
    }

    public function test_can_delete_unused_unit(): void
    {
        $unit = Unit::create(['code' => 'UNUSED_U', 'name' => 'Unused Unit']);

        $response = $this->actingAs($this->admin)->delete(route('units.destroy', $unit));

        $response->assertRedirect(route('units.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('units', ['id' => $unit->id]);
    }
}
