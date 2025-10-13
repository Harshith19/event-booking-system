<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Event $event): JsonResponse
    {
        $this->authorize('create', Ticket::class);

        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
        ]);

        $ticket = $event->tickets()->create($validated);

        return $this->successResponse($ticket, 'Ticket created successfully', 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ticket $ticket): JsonResponse
    {
        $this->authorize('update', $ticket);

        $validated = $request->validate([
            'type' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric|min:0',
            'quantity' => 'sometimes|integer|min:1',
        ]);

        $ticket->update($validated);

        return $this->successResponse($ticket, 'Ticket updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket): JsonResponse
    {
        $this->authorize('delete', $ticket);

        // Check if there are any confirmed bookings
        if($ticket->bookings()->where('status', 'confirmed')->exists()) {
            return $this->errorResponse('Cannot delete ticket with confirmed bookings', 422);
        }
        
        $ticket->delete();

        return $this->successResponse(null, 'Ticket deleted successfully');
    }
}
