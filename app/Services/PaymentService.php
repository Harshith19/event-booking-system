<?php
namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Str;

class PaymentService
{
    public function process(Booking $booking): Payment
    {
        // Payment processing logic - 80% success rate
        $success = rand(1, 100) <= 80;

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'amount' => $booking->total_amount,
            'status' => $success ? 'success' : 'failed',
            'transaction_id' => 'TXN_' . Str::uuid(),
            'failure_message' => $success ? null : 'Payment gateway error',
        ]);

        // payment processing delay
        sleep(2);

        return $payment;
    }

    public function refund(Payment $payment): bool
    {
        if ($payment && $payment->status !== 'success') {
            return false;
        }

        $payment->update(['status' => 'refunded']);

        // refund processing delay
        sleep(1);

        return true;
    }
}