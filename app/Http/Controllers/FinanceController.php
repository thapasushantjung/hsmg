<?php

namespace App\Http\Controllers;

use App\Models\Payment;

class FinanceController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['tenant', 'booking.currentBedAssignment.bed.room'])->latest()->get();

        $payments->each(function (Payment $payment) {
            $payment->tenant?->append('full_name');
        });

        $totalRevenue = Payment::where('status', 'paid')->sum('amount');
        $pendingDues = Payment::where('status', 'pending')->sum('amount');
        $overdueDues = Payment::where('status', 'overdue')->sum('amount');

        return inertia('finance', [
            'payments' => $payments,
            'stats' => [
                'total_revenue' => $totalRevenue,
                'pending_dues' => $pendingDues,
                'overdue_dues' => $overdueDues,
            ],
        ]);
    }
}
