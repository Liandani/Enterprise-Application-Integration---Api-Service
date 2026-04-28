<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\User;
use App\Models\Book;
use App\Models\LoanHistory;
use Illuminate\Http\Request;
use Carbon\Carbon;

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
            return response()->json(['message' => 'User atau Book tidak ditemukan'], 404);
        }

        $activeLoan = Loan::where('book_id', $book->id)
            ->where('status', 'borrowed')
            ->first();

        if ($activeLoan) {
            return response()->json(['message' => 'Book sedang dipinjam dan tidak tersedia'], 400);
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

    // GET LOAN HISTORY
    public function history()
    {
        $histories = LoanHistory::with(['user', 'book'])->get();
        return response()->json($histories);
    }

    // PENGEMBALIAN BUKU & HITUNG DENDA
    public function returnBook(Request $request)
    {
        $request->validate([
            'loan_id' => 'required'
        ]);

        $loan = Loan::find($request->loan_id);

        if (!$loan || $loan->status === 'returned') {
            return response()->json(['message' => 'Data peminjaman tidak ditemukan atau sudah dikembalikan'], 404);
        }

        // 1. Logika Hitung Denda (Rp 2.000 per hari terlambat)
        $today = now();
        $dueDate = Carbon::parse($loan->due_date);
        $fine = 0;

        if ($today->gt($dueDate)) {
            $daysLate = $today->diffInDays($dueDate);
            $fine = $daysLate * 2000;
        }

        // 2. Update Status Peminjaman di Database
        $loan->update([
            'status' => 'returned',
            'return_date' => $today,
            'fine_amount' => $fine
        ]);

        // 3. Update Status Buku agar bisa dipinjam lagi
        $book = Book::find($loan->book_id);
        $book->update(['available' => true]);

        return response()->json([
            'message' => 'Buku berhasil dikembalikan',
            'detail_denda' => [
                'hari_terlambat' => $today->gt($dueDate) ? $today->diffInDays($dueDate) : 0,
                'total_denda' => "Rp " . number_format($fine, 0, ',', '.')
            ],
            'data' => $loan
        ]);
    }
}
