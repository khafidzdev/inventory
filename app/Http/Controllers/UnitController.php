<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UnitController extends Controller
{
    /**
     * Display a listing of the units.
     */
    public function index(): View
    {
        $units = Unit::withCount('products')
            ->orderBy('name')
            ->get();

        return view('dashboard.Product.Unit', compact('units'));
    }

    /**
     * Store a newly created unit in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'code' => ['required', 'string', 'max:10', 'alpha_dash', 'unique:units,code'],
        ], [
            'name.required' => 'Nama satuan wajib diisi.',
            'code.required' => 'Kode / Simbol satuan wajib diisi.',
            'code.alpha_dash' => 'Kode satuan hanya boleh berisi huruf, angka, strip, dan garis bawah.',
            'code.unique' => 'Kode satuan sudah digunakan.',
        ]);

        Unit::create([
            'name' => $validated['name'],
            'code' => strtoupper($validated['code']),
        ]);

        return redirect()->route('units.index')->with('success', 'Satuan berhasil ditambahkan.');
    }

    /**
     * Update the specified unit in storage.
     */
    public function update(Request $request, Unit $unit): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'code' => ['required', 'string', 'max:10', 'alpha_dash', Rule::unique('units', 'code')->ignore($unit->id)],
        ], [
            'name.required' => 'Nama satuan wajib diisi.',
            'code.required' => 'Kode / Simbol satuan wajib diisi.',
            'code.alpha_dash' => 'Kode satuan hanya boleh berisi huruf, angka, strip, dan garis bawah.',
            'code.unique' => 'Kode satuan sudah digunakan.',
        ]);

        $unit->update([
            'name' => $validated['name'],
            'code' => strtoupper($validated['code']),
        ]);

        return redirect()->route('units.index')->with('success', 'Data satuan berhasil diperbarui.');
    }

    /**
     * Remove the specified unit from storage.
     */
    public function destroy(Unit $unit): RedirectResponse
    {
        if ($unit->products()->count() > 0) {
            return back()->with('error', 'Satuan tidak dapat dihapus karena masih digunakan oleh beberapa produk.');
        }

        $unitName = $unit->name;
        $unit->delete();

        return redirect()->route('units.index')->with('success', "Satuan {$unitName} berhasil dihapus.");
    }
}
