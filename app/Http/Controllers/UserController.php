<?php

namespace App\Http\Controllers;

use App\Models\User;

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

    // GET USER + LOANS (SIMULASI)
    public function getUserWithLoans($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'user' => $user,
            'loans' => [
                [
                    'book_id' => 1,
                    'title' => 'Laravel for Beginners',
                    'status' => 'borrowed',
                    'loan_date' => '2026-04-25'
                ],
                [
                    'book_id' => 2,
                    'title' => 'Clean Code',
                    'status' => 'returned',
                    'loan_date' => '2026-04-20'
                ]
            ]
        ]);
    }
}
