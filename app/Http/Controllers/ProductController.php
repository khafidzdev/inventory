<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index(): View
    {
        $products = Product::with(['category', 'unit'])
            ->orderBy('created_at', 'desc')
            ->get();

        $categories = Category::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();

        return view('dashboard.Product.Product', compact('products', 'categories', 'units'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'unit_id' => ['required', 'exists:units,id'],
            'sku' => ['required', 'string', 'max:64', 'unique:products,sku'],
            'barcode' => ['nullable', 'string', 'max:64', 'unique:products,barcode'],
            'name' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'is_active' => ['required', 'in:0,1'],
        ], [
            'category_id.required' => 'Kategori produk wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'unit_id.required' => 'Satuan produk wajib dipilih.',
            'unit_id.exists' => 'Satuan yang dipilih tidak valid.',
            'sku.required' => 'SKU produk wajib diisi.',
            'sku.unique' => 'SKU produk sudah digunakan oleh produk lain.',
            'barcode.unique' => 'Barcode sudah digunakan oleh produk lain.',
            'name.required' => 'Nama produk wajib diisi.',
            'purchase_price.required' => 'Harga beli wajib diisi.',
            'purchase_price.numeric' => 'Harga beli harus berupa angka.',
            'selling_price.required' => 'Harga jual wajib diisi.',
            'selling_price.numeric' => 'Harga jual harus berupa angka.',
            'stock.required' => 'Stok produk wajib diisi.',
            'stock.integer' => 'Stok produk harus berupa angka bulat.',
            'minimum_stock.required' => 'Stok minimum wajib diisi.',
            'minimum_stock.integer' => 'Stok minimum harus berupa angka bulat.',
            'is_active.required' => 'Status produk wajib dipilih.',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        Product::create([
            'category_id' => $validated['category_id'],
            'unit_id' => $validated['unit_id'],
            'sku' => $validated['sku'],
            'barcode' => $validated['barcode'] ?? null,
            'name' => $validated['name'],
            'image' => $imagePath,
            'purchase_price' => $validated['purchase_price'],
            'selling_price' => $validated['selling_price'],
            'stock' => $validated['stock'],
            'minimum_stock' => $validated['minimum_stock'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'unit_id' => ['required', 'exists:units,id'],
            'sku' => ['required', 'string', 'max:64', Rule::unique('products', 'sku')->ignore($product->id)],
            'barcode' => ['nullable', 'string', 'max:64', Rule::unique('products', 'barcode')->ignore($product->id)],
            'name' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'is_active' => ['required', 'in:0,1'],
        ], [
            'category_id.required' => 'Kategori produk wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'unit_id.required' => 'Satuan produk wajib dipilih.',
            'unit_id.exists' => 'Satuan yang dipilih tidak valid.',
            'sku.required' => 'SKU produk wajib diisi.',
            'sku.unique' => 'SKU produk sudah digunakan oleh produk lain.',
            'barcode.unique' => 'Barcode sudah digunakan oleh produk lain.',
            'name.required' => 'Nama produk wajib diisi.',
            'purchase_price.required' => 'Harga beli wajib diisi.',
            'purchase_price.numeric' => 'Harga beli harus berupa angka.',
            'selling_price.required' => 'Harga jual wajib diisi.',
            'selling_price.numeric' => 'Harga jual harus berupa angka.',
            'stock.required' => 'Stok produk wajib diisi.',
            'stock.integer' => 'Stok produk harus berupa angka bulat.',
            'minimum_stock.required' => 'Stok minimum wajib diisi.',
            'minimum_stock.integer' => 'Stok minimum harus berupa angka bulat.',
            'is_active.required' => 'Status produk wajib dipilih.',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $product->image = $request->file('image')->store('products', 'public');
        }

        $product->category_id = $validated['category_id'];
        $product->unit_id = $validated['unit_id'];
        $product->sku = $validated['sku'];
        $product->barcode = $validated['barcode'] ?? null;
        $product->name = $validated['name'];
        $product->purchase_price = $validated['purchase_price'];
        $product->selling_price = $validated['selling_price'];
        $product->stock = $validated['stock'];
        $product->minimum_stock = $validated['minimum_stock'];
        $product->is_active = $request->boolean('is_active');
        $product->save();

        return redirect()->route('products.index')->with('success', 'Data produk berhasil diperbarui.');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $productName = $product->name;
        $product->delete();

        return redirect()->route('products.index')->with('success', "Produk {$productName} berhasil dihapus.");
    }
}

