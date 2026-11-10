<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardMetricsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_displays_correct_metrics(): void
    {
        $admin = User::factory()->create([
            'role' => 'super_admin',
        ]);

        User::factory()->create([
            'role' => 'warehouse_staff',
        ]);

        $category = Category::create([
            'code' => 'CAT1',
            'name' => 'Elektronik',
        ]);

        $unit = Unit::create([
            'code' => 'PCS',
            'name' => 'Pieces',
        ]);

        Product::create([
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'sku' => 'PRD-001',
            'barcode' => '1234567890',
            'name' => 'Laptop ROG',
            'purchase_price' => 15000000,
            'selling_price' => 18000000,
            'stock' => 10,
            'minimum_stock' => 2,
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'sku' => 'PRD-002',
            'barcode' => '1234567891',
            'name' => 'Mouse Wireless',
            'purchase_price' => 150000,
            'selling_price' => 200000,
            'stock' => 1,
            'minimum_stock' => 5,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Analytics Dashboard');
        $response->assertSee('Total Barang');
        $response->assertSee('Total User');
        $response->assertViewHas('totalProducts', 2);
        $response->assertViewHas('totalUsers', 1);
    }

    public function test_warehouse_dashboard_displays_correct_metrics(): void
    {
        $warehouse = User::factory()->create([
            'role' => 'warehouse_staff',
        ]);

        $category = Category::create([
            'code' => 'CAT1',
            'name' => 'Elektronik',
        ]);

        $unit = Unit::create([
            'code' => 'PCS',
            'name' => 'Pieces',
        ]);

        Product::create([
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'sku' => 'PRD-001',
            'barcode' => '1234567890',
            'name' => 'Keyboard Gaming',
            'purchase_price' => 500000,
            'selling_price' => 700000,
            'stock' => 25,
            'minimum_stock' => 5,
            'is_active' => true,
        ]);

        $response = $this->actingAs($warehouse)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Analytics Dashboard');
        $response->assertSee('Total Barang');
        $response->assertSee('Total User');
        $response->assertViewHas('totalProducts', 1);
        $response->assertViewHas('totalUsers', 1);
    }
}
