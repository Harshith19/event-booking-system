<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\PaymentService;
use App\Notifications\BookingConfirmed;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        private PaymentService $paymentService
    ) {}

    public function processPayment(Request $request, Booking $booking): JsonResponse
    {
        $this->authorize('view', $booking);

        if($booking->status !== 'pending') {
            return $this->errorResponse('Booking is not in pending state', 422);
        }

        $payment = $this->paymentService->process($booking);

        if($payment->status === 'success') {
            $booking->update(['status' => 'confirmed']);
            $booking->user->notify(new BookingConfirmed($booking));
            
            return $this->successResponse($payment, 'Payment processed successfully');
        } else {
            $booking->update(['status' => 'cancelled']);
            return $this->errorResponse('Payment failed: ' . $payment->failure_message, 402);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id): JsonResponse
    {
        $payment = Payment::with('booking')->findOrFail($id);

        $this->authorize('view', $payment->booking);

        return $this->successResponse($payment);
    }
}
