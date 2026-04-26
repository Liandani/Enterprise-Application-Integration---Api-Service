<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\User;
use App\Models\Book;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    // GET ALL LOANS
    public function index()
    {
        return response()->json(
            Loan::with(['user', 'book'])->get()
        );
    }

    // CREATE LOAN
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'book_id' => 'required'
        ]);

        $user = User::find($request->user_id);
        $book = Book::find($request->book_id);

        if (!$user || !$book) {
            return response()->json([
                'message' => 'User atau Book tidak ditemukan'
            ], 404);
        }

        if (!$book->available) {
            return response()->json([
                'message' => 'Book tidak tersedia'
            ], 400);
        }

        $loan = Loan::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'loan_date' => now(),
            'due_date' => now()->addDays(7),
            'status' => 'borrowed'
        ]);

        $book->update(['available' => false]);

        return response()->json([
            'message' => 'Loan berhasil dibuat',
            'loan' => $loan->load(['user', 'book'])
        ]);
    }
}
