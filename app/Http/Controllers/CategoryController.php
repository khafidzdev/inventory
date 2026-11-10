<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index(): View
    {
        $categories = Category::withCount('products')
            ->orderBy('name')
            ->get();

        return view('dashboard.Product.Category', compact('categories'));
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:categories,code'],
            'name' => ['required', 'string', 'max:100'],
        ], [
            'code.required' => 'Kode kategori wajib diisi.',
            'code.alpha_dash' => 'Kode kategori hanya boleh berisi huruf, angka, strip, dan garis bawah.',
            'code.unique' => 'Kode kategori sudah digunakan.',
            'name.required' => 'Nama kategori wajib diisi.',
        ]);

        Category::create([
            'code' => strtoupper($validated['code']),
            'name' => $validated['name'],
        ]);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('categories', 'code')->ignore($category->id)],
            'name' => ['required', 'string', 'max:100'],
        ], [
            'code.required' => 'Kode kategori wajib diisi.',
            'code.alpha_dash' => 'Kode kategori hanya boleh berisi huruf, angka, strip, dan garis bawah.',
            'code.unique' => 'Kode kategori sudah digunakan.',
            'name.required' => 'Nama kategori wajib diisi.',
        ]);

        $category->update([
            'code' => strtoupper($validated['code']),
            'name' => $validated['name'],
        ]);

        return redirect()->route('categories.index')->with('success', 'Data kategori berhasil diperbarui.');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->count() > 0) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh beberapa produk.');
        }

        $categoryName = $category->name;
        $category->delete();

        return redirect()->route('categories.index')->with('success', "Kategori {$categoryName} berhasil dihapus.");
    }
}
