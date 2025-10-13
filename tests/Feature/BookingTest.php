<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_book_ticket(): void
    {
        $customer = User::factory()->customer()->create();
        $event = Event::factory()->create();
        $ticket = Ticket::factory()->create([
            'event_id' => $event->id,
            'quantity' => 10,
        ]);

        $token = $customer->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("/api/tickets/{$ticket->id}/bookings", [
                'quantity' => 2,
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => ['id', 'user_id', 'ticket_id', 'quantity', 'status'],
                'message',
            ]);
    }

    public function test_customer_cannot_book_more_than_available(): void
    {
        $customer = User::factory()->customer()->create();
        $event = Event::factory()->create();
        $ticket = Ticket::factory()->create([
            'event_id' => $event->id,
            'quantity' => 1,
        ]);

        $token = $customer->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("/api/tickets/{$ticket->id}/bookings", [
                'quantity' => 2,
            ]);

        $response->assertStatus(422);
    }
}
