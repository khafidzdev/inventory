<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $staff;
    private User $cashier;
    private Category $category;
    private Unit $unit;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Product Test',
            'username' => 'admin_product',
            'email' => 'adminprod@inventory.com',
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->staff = User::create([
            'name' => 'Staff Product Test',
            'username' => 'staff_product',
            'email' => 'staffprod@inventory.com',
            'password' => Hash::make('password123'),
            'role' => 'warehouse_staff',
            'is_active' => true,
        ]);

        $this->cashier = User::create([
            'name' => 'Cashier Test',
            'username' => 'cashier_product',
            'email' => 'cashierprod@inventory.com',
            'password' => Hash::make('password123'),
            'role' => 'cashier',
            'is_active' => true,
        ]);

        $this->category = Category::create([
            'code' => 'ELC',
            'name' => 'Electronics',
        ]);

        $this->unit = Unit::create([
            'code' => 'PCS',
            'name' => 'Pieces',
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('products.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_cashier_cannot_access_products(): void
    {
        $response = $this->actingAs($this->cashier)->get(route('products.index'));
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error');
    }

    public function test_admin_can_view_products(): void
    {
        Product::create([
            'category_id' => $this->category->id,
            'unit_id' => $this->unit->id,
            'sku' => 'SKU-001',
            'barcode' => '8990001',
            'name' => 'Wireless Keyboard',
            'purchase_price' => 100000,
            'selling_price' => 150000,
            'stock' => 20,
            'minimum_stock' => 5,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('products.index'));
        $response->assertStatus(200);
        $response->assertViewIs('dashboard.Product.Product');
        $response->assertSee('Wireless Keyboard');
        $response->assertSee('SKU-001');
    }

    public function test_petugas_staff_can_view_products(): void
    {
        $response = $this->actingAs($this->staff)->get(route('products.index'));
        $response->assertStatus(200);
        $response->assertViewIs('dashboard.Product.Product');
    }

    public function test_petugas_staff_can_create_product_with_image_and_stock(): void
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->image('product.jpg');

        $response = $this->actingAs($this->staff)->post(route('products.store'), [
            'category_id' => $this->category->id,
            'unit_id' => $this->unit->id,
            'sku' => 'PRD-NEW-01',
            'barcode' => '8990002',
            'name' => 'Gaming Mouse',
            'image' => $file,
            'purchase_price' => 200000,
            'selling_price' => 275000,
            'stock' => 15,
            'minimum_stock' => 5,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('products', [
            'sku' => 'PRD-NEW-01',
            'name' => 'Gaming Mouse',
            'purchase_price' => 200000,
            'selling_price' => 275000,
            'stock' => 15,
            'minimum_stock' => 5,
            'is_active' => true,
        ]);

        $product = Product::where('sku', 'PRD-NEW-01')->first();
        $this->assertNotNull($product->image);
        Storage::disk('public')->assertExists($product->image);
    }

    public function test_admin_can_update_product_stock_and_image(): void
    {
        Storage::fake('public');

        $product = Product::create([
            'category_id' => $this->category->id,
            'unit_id' => $this->unit->id,
            'sku' => 'PRD-UPDATE',
            'name' => 'Old Product Name',
            'purchase_price' => 50000,
            'selling_price' => 70000,
            'stock' => 10,
            'minimum_stock' => 2,
            'is_active' => true,
        ]);

        $newImage = UploadedFile::fake()->image('new-image.png');

        $response = $this->actingAs($this->admin)->put(route('products.update', $product), [
            'category_id' => $this->category->id,
            'unit_id' => $this->unit->id,
            'sku' => 'PRD-UPDATE',
            'name' => 'Updated Product Name',
            'image' => $newImage,
            'purchase_price' => 55000,
            'selling_price' => 80000,
            'stock' => 30,
            'minimum_stock' => 5,
            'is_active' => '0',
        ]);

        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('success');

        $product->refresh();
        $this->assertSame('Updated Product Name', $product->name);
        $this->assertSame('55000.00', (string) $product->purchase_price);
        $this->assertSame('80000.00', (string) $product->selling_price);
        $this->assertSame(30, $product->stock);
        $this->assertFalse($product->is_active);
        $this->assertNotNull($product->image);
        Storage::disk('public')->assertExists($product->image);
    }

    public function test_petugas_staff_can_delete_product(): void
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'unit_id' => $this->unit->id,
            'sku' => 'PRD-DELETE',
            'name' => 'Product to Delete',
            'purchase_price' => 10000,
            'selling_price' => 15000,
            'stock' => 5,
            'minimum_stock' => 1,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->staff)->delete(route('products.destroy', $product));

        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }

    public function test_product_validation_prevents_duplicate_sku(): void
    {
        Product::create([
            'category_id' => $this->category->id,
            'unit_id' => $this->unit->id,
            'sku' => 'SKU-DUPLICATE',
            'name' => 'Existing Product',
            'purchase_price' => 10000,
            'selling_price' => 15000,
            'stock' => 10,
            'minimum_stock' => 1,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->staff)->post(route('products.store'), [
            'category_id' => $this->category->id,
            'unit_id' => $this->unit->id,
            'sku' => 'SKU-DUPLICATE',
            'name' => 'New Product Same SKU',
            'purchase_price' => 20000,
            'selling_price' => 30000,
            'stock' => 5,
            'minimum_stock' => 1,
            'is_active' => '1',
        ]);

        $response->assertSessionHasErrors(['sku']);
    }
}
