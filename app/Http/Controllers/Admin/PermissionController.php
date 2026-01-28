<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::orderBy('module')->paginate(20);
        
        return view('admin.permissions.index', compact('permissions'));
    }

    public function create()
    {
        $modules = Permission::select('module')->distinct()->pluck('module');
        
        return view('admin.permissions.create', compact('modules'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:permissions,name',
            'display_name' => 'required|string|max:255',
            'module' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        $permission = Permission::create([
            'name' => strtolower(str_replace(' ', '_', $validated['name'])),
            'display_name' => $validated['display_name'],
            'module' => $validated['module'],
            'description' => $validated['description'] ?? null,
        ]);

        ActivityLog::log('create', 'permissions', "Created permission: {$permission->name}");

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission berhasil ditambahkan.');
    }

    public function edit(Permission $permission)
    {
        $modules = Permission::select('module')->distinct()->pluck('module');
        
        return view('admin.permissions.edit', compact('permission', 'modules'));
    }

    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'display_name' => 'required|string|max:255',
            'module' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        $oldData = $permission->toArray();

        $permission->update($validated);

        ActivityLog::log('update', 'permissions', "Updated permission: {$permission->name}", $oldData, $permission->toArray());

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission berhasil diperbarui.');
    }

    public function destroy(Permission $permission)
    {
        $name = $permission->name;
        $permission->delete();

        ActivityLog::log('delete', 'permissions', "Deleted permission: {$name}");

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission berhasil dihapus.');
    }
}
