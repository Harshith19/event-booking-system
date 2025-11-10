<?php

namespace App\Http\Middleware;

use App\Models\Ticket;
use App\Models\Booking;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventDoubleBooking
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('POST') && str_contains($request->path(), 'api/tickets/') && str_contains($request->path(), '/bookings')) {
            // if($request->route()->named('tickets.bookings.store')) {
            $ticketId = $request->route('id');
            $userId = $request->user()->id;

            // Check if user already has a booking
            $existingBooking = Booking::where('ticket_id', $ticketId)
                ->where('user_id', $userId)
                ->first();

            if($existingBooking) {
                return response()->json([
                    'success' => false,
                    'message' => 'You already booked this ticket.'
                ], 422);
            }
        }

        return $next($request);
    }
}
