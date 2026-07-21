<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of system users.
     */
    public function index(Request $request)
    {
        $query = User::with(['roles', 'profile']);

        if ($request->filled('txt_search')) {
            $search = $request->input('txt_search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('role_id')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('roles.id', $request->input('role_id'));
            });
        }

        $total = (clone $query)->count();
        $list = $query->orderBy('id', 'desc')->get();

        return response()->json([
            'list' => $list,
            'total' => $total,
            'roles' => Role::where('status', 1)->get(),
        ]);
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:roles,id',
            'status' => 'nullable|integer',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'status' => $request->input('status', 1),
            ]);

            // Assign Role
            $user->roles()->sync([$request->role_id]);

            // Create Profile
            Profile::create([
                'user_id' => $user->id,
                'phone' => $request->phone,
                'address' => $request->address,
                'type' => 'STAFF',
            ]);

            return response()->json([
                'data' => $user->load(['roles', 'profile']),
                'message' => 'បានបង្កើតអ្នកប្រើប្រាស់ថ្មីដោយជោគជ័យ!',
            ], 201);
        });
    }

    /**
     * Display the specified user.
     */
    public function show(string $id)
    {
        $user = User::with(['roles', 'profile'])->find($id);

        if (!$user) {
            return response()->json(['message' => 'រកមិនឃើញអ្នកប្រើប្រាស់ឡើយ!'], 404);
        }

        return response()->json(['data' => $user]);
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'រកមិនឃើញអ្នកប្រើប្រាស់ឡើយ!'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
            'role_id' => 'required|exists:roles,id',
            'status' => 'nullable|integer',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request, $user) {
            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'status' => $request->input('status', $user->status),
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            // Update Role
            $user->roles()->sync([$request->role_id]);

            // Update Profile
            if ($user->profile) {
                $user->profile->update([
                    'phone' => $request->phone,
                    'address' => $request->address,
                ]);
            } else {
                Profile::create([
                    'user_id' => $user->id,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'type' => 'STAFF',
                ]);
            }

            return response()->json([
                'data' => $user->load(['roles', 'profile']),
                'message' => 'បានកែប្រែទិន្នន័យអ្នកប្រើប្រាស់ដោយជោគជ័យ!',
            ]);
        });
    }

    /**
     * Remove the specified user.
     */
    public function destroy(string $id)
    {
        if ($id == 1) {
            return response()->json(['message' => 'មិនអាចលុប Super Admin Account បានឡើយ!'], 403);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'រកមិនឃើញអ្នកប្រើប្រាស់ឡើយ!'], 404);
        }

        return DB::transaction(function () use ($user) {
            $user->roles()->detach();
            if ($user->profile) {
                $user->profile->delete();
            }
            $user->delete();

            return response()->json(['message' => 'បានលុបអ្នកប្រើប្រាស់ដោយជោគជ័យ!']);
        });
    }
}
