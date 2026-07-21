<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * Display a listing of permissions.
     */
    public function index(Request $request)
    {
        $query = Permission::query();

        if ($request->filled('txt_search')) {
            $search = $request->input('txt_search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('group', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('group')) {
            $query->where('group', $request->input('group'));
        }

        $list = $query->orderBy('group', 'asc')->orderBy('id', 'asc')->get();
        $total = $query->count();
        $groups = Permission::distinct()->pluck('group');

        return response()->json([
            'list' => $list,
            'total' => $total,
            'groups' => $groups,
        ]);
    }

    /**
     * Store a newly created permission.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:permissions,name',
            'group' => 'required|string',
            'is_menu_web' => 'nullable|boolean',
            'web_route_key' => 'nullable|string',
        ]);

        $permission = Permission::create($validated);

        return response()->json([
            'data' => $permission,
            'message' => 'បានបង្កើតសិទ្ធិថ្មីដោយជោគជ័យ!',
        ], 201);
    }

    /**
     * Display the specified permission.
     */
    public function show(string $id)
    {
        $permission = Permission::find($id);

        if (!$permission) {
            return response()->json(['message' => 'រកមិនឃើញសិទ្ធិឡើយ!'], 404);
        }

        return response()->json(['data' => $permission]);
    }

    /**
     * Update the specified permission.
     */
    public function update(Request $request, string $id)
    {
        $permission = Permission::find($id);

        if (!$permission) {
            return response()->json(['message' => 'រកមិនឃើញសិទ្ធិឡើយ!'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|unique:permissions,name,' . $id,
            'group' => 'required|string',
            'is_menu_web' => 'nullable|boolean',
            'web_route_key' => 'nullable|string',
        ]);

        $permission->update($validated);

        return response()->json([
            'data' => $permission,
            'message' => 'បានកែប្រែសិទ្ធិដោយជោគជ័យ!',
        ]);
    }

    /**
     * Remove the specified permission.
     */
    public function destroy(string $id)
    {
        $permission = Permission::find($id);

        if (!$permission) {
            return response()->json(['message' => 'រកមិនឃើញសិទ្ធិឡើយ!'], 404);
        }

        $permission->roles()->detach();
        $permission->delete();

        return response()->json(['message' => 'បានលុបសិទ្ធិដោយជោគជ័យ!']);
    }
}
