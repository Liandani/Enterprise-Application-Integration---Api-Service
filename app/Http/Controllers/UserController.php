<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // GET ALL USERS
    public function index()
    {
        return response()->json(User::all());
    }

    // GET USER BY ID
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        return response()->json($user);
    }

    // CREATE USER
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:255'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone
        ]);

        return response()->json([
            'message' => 'User berhasil dibuat',
            'user' => $user
        ], 201);
    }

    // GET USER + LOANS
    public function getUserWithLoans($id)
    {
        $user = User::with(['loans.book'])->find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        return response()->json($user);
    }
}
