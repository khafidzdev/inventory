<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Default Categories
        $electronics = Category::firstOrCreate(
            ['code' => 'ELC'],
            ['name' => 'Elektronik']
        );

        $stationery = Category::firstOrCreate(
            ['code' => 'ATK'],
            ['name' => 'Alat Tulis Kantor']
        );

        $food = Category::firstOrCreate(
            ['code' => 'FNB'],
            ['name' => 'Makanan & Minuman']
        );

        // Default Units
        $pcs = Unit::firstOrCreate(
            ['code' => 'PCS'],
            ['name' => 'Pieces']
        );

        $box = Unit::firstOrCreate(
            ['code' => 'BOX'],
            ['name' => 'Box']
        );

        $pack = Unit::firstOrCreate(
            ['code' => 'PCK'],
            ['name' => 'Pack']
        );

        // Sample Products
        Product::firstOrCreate(
            ['sku' => 'PRD-001'],
            [
                'category_id' => $electronics->id,
                'unit_id' => $pcs->id,
                'barcode' => '899123456001',
                'name' => 'Mouse Wireless Logitech M170',
                'purchase_price' => 120000,
                'selling_price' => 165000,
                'stock' => 25,
                'minimum_stock' => 5,
                'is_active' => true,
            ]
        );

        Product::firstOrCreate(
            ['sku' => 'PRD-002'],
            [
                'category_id' => $stationery->id,
                'unit_id' => $box->id,
                'barcode' => '899123456002',
                'name' => 'Kertas HVS A4 75gsm PaperOne',
                'purchase_price' => 45000,
                'selling_price' => 55000,
                'stock' => 50,
                'minimum_stock' => 10,
                'is_active' => true,
            ]
        );

        Product::firstOrCreate(
            ['sku' => 'PRD-003'],
            [
                'category_id' => $food->id,
                'unit_id' => $pack->id,
                'barcode' => '899123456003',
                'name' => 'Kopi Arabika Gayo 250gr',
                'purchase_price' => 60000,
                'selling_price' => 85000,
                'stock' => 15,
                'minimum_stock' => 3,
                'is_active' => true,
            ]
        );
    }
}

