<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //
    public function index()
    {
        $users = User::all();
        if($users == null){
            return response()->json([
                'status' => false,
                'message' => 'No users found'
            ], 404);
        }
        else{
            return response()->json([
                'status' => true,
                'data' => $users
            ], 200);
        }
    }
    public function list(Request $request)
{
    $query = User::query();

    // 🔎 SEARCH by name or email
    if ($request->search) {
        $query->where(function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->search . '%')
              ->orWhere('email', 'like', '%' . $request->search . '%');
        });
    }

    // 📄 PAGINATION
    $users = $query->paginate(10);

    return response()->json([
        'status' => true,
        'message' => 'User list',
        'data' => $users
    ], 200);
}
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,teacher',
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
        ]);
        return response()->json([
            'status' => true,
            'message' => 'User created successfully',
            'data' => $user
        ], 201);
    }
    public function show($id)
    {
        $user = User::find($id);
        if($user == null){
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }
        else{
            return response()->json([
                'status' => true,
                'data' => $user
            ], 200);
        }
    }
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        // $request->validate([
        //     'name' => 'required|string|max:255',
        //     'email' => 'required|email|max:255|unique:users,email,' . $id,
        //     'role' => 'required|in:admin,teacher,student',
        // ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        // only update password if provided
        if ($request->password) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'User updated successfully',
            'data' => $user
        ], 200);
    }
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        $user->delete();

        return response()->json([
            'status' => true,
            'message' => 'User deleted successfully'
        ], 200);
    }
}
