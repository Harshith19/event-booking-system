<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Ticket;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(
        private PaymentService $paymentService
    ) {
        // 
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Booking::class);

        $bookings = $request->user()->bookings()
            ->with(['ticket.event', 'payment'])
            ->paginate(config('constants.PAGINATION'));

        return $this->successResponse($bookings);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Ticket $ticket): JsonResponse
    {
        $this->authorize('create', Booking::class);

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $ticket->quantity,
        ]);

        // Check for multiple booking using middleware
        if($ticket->bookings()->where('user_id', $request->user()->id)->exists()) {
            return $this->errorResponse('You have already booked this ticket', 422);
        }

        // Calculate total amount
        $totalAmount = $ticket->price * $validated['quantity'];

        // // Process payment
        // $paymentResult = $this->paymentService->processPayment(
        //     $validated['payment_method'],
        //     $validated['payment_details'],
        //     $totalAmount
        // );

        // if (!$paymentResult['success']) {
        //     return $this->errorResponse('Payment failed: ' . $paymentResult['message'], 402);
        // }

        // Create booking
        $booking = Booking::create([
            'user_id' => $request->user()->id,
            'ticket_id' => $ticket->id,
            'quantity' => $validated['quantity'],
            'status' => 'pending', 
        ]);

        return $this->successResponse($booking, 'Booking created successfully', 201);
    }

    public function cancel(Request $request, Booking $booking): JsonResponse
    {
        $this->authorize('cancel', $booking);

        // if ($booking->status !== 'pending') {
        //     return $this->errorResponse('Only pending bookings can be cancelled', 422);
        // }

        $booking->update(['status' => 'cancelled']);

        // Refund payment if exists
        if ($booking->payment) {
            $this->paymentService->refund($booking->payment);
        }

        return $this->successResponse($booking, 'Booking cancelled successfully');
    }
}
