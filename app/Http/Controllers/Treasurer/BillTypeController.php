<?php

namespace App\Http\Controllers\Treasurer;

use App\Http\Controllers\Controller;
use App\Models\BillType;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class BillTypeController extends Controller
{
    public function index()
    {
        $billTypes = BillType::withCount('bills')->get();
        
        return view('treasurer.bill-types.index', compact('billTypes'));
    }

    public function create()
    {
        return view('treasurer.bill-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'is_flexible' => 'boolean',
            'is_mandatory' => 'boolean',
            'is_recurring' => 'boolean',
            'recurring_period' => 'nullable|in:monthly,quarterly,semester,yearly',
            'is_active' => 'boolean',
        ]);

        $validated['is_flexible'] = $validated['is_flexible'] ?? false;
        $validated['is_mandatory'] = $validated['is_mandatory'] ?? true;
        $validated['is_recurring'] = $validated['is_recurring'] ?? false;
        $validated['is_active'] = $validated['is_active'] ?? true;

        $billType = BillType::create($validated);

        ActivityLog::log('create', 'bill_types', "Created bill type: {$billType->name}");

        return redirect()->route('treasurer.bill-types.index')
            ->with('success', 'Jenis tagihan berhasil ditambahkan.');
    }

    public function edit(BillType $billType)
    {
        return view('treasurer.bill-types.edit', compact('billType'));
    }

    public function update(Request $request, BillType $billType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'is_flexible' => 'boolean',
            'is_mandatory' => 'boolean',
            'is_recurring' => 'boolean',
            'recurring_period' => 'nullable|in:monthly,quarterly,semester,yearly',
            'is_active' => 'boolean',
        ]);

        $oldData = $billType->toArray();

        $validated['is_flexible'] = $validated['is_flexible'] ?? false;
        $validated['is_mandatory'] = $validated['is_mandatory'] ?? true;
        $validated['is_recurring'] = $validated['is_recurring'] ?? false;
        $validated['is_active'] = $validated['is_active'] ?? true;

        $billType->update($validated);

        ActivityLog::log('update', 'bill_types', "Updated bill type: {$billType->name}", $oldData, $billType->toArray());

        return redirect()->route('treasurer.bill-types.index')
            ->with('success', 'Jenis tagihan berhasil diperbarui.');
    }

    public function destroy(BillType $billType)
    {
        if ($billType->bills()->count() > 0) {
            return back()->with('error', 'Jenis tagihan tidak dapat dihapus karena masih digunakan.');
        }

        $name = $billType->name;
        $billType->delete();

        ActivityLog::log('delete', 'bill_types', "Deleted bill type: {$name}");

        return redirect()->route('treasurer.bill-types.index')
            ->with('success', 'Jenis tagihan berhasil dihapus.');
    }

    public function toggleStatus(BillType $billType)
    {
        $billType->update(['is_active' => !$billType->is_active]);

        $status = $billType->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Jenis tagihan berhasil {$status}.");
    }
}
