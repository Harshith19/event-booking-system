<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    public function test_organizer_can_create_event(): void
    {
        $organizer = User::factory()->organizer()->create();
        $token = $organizer->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/events', [
                'title' => 'Test Event',
                'description' => 'Test Description',
                'date' => now()->addWeek()->toDateTimeString(),
                'location' => 'Test Location',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => ['id', 'title', 'description', 'date', 'location'],
                'message',
            ]);
    }

    public function test_customer_cannot_create_event(): void
    {
        $customer = User::factory()->customer()->create();
        $token = $customer->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/events', [
                'title' => 'Test Event',
                'description' => 'Test Description',
                'date' => now()->addWeek()->toDateTimeString(),
                'location' => 'Test Location',
            ]);

        $response->assertStatus(403);
    }

    public function test_can_list_events_with_pagination(): void
    {
        Event::factory()->count(15)->create();

        $response = $this->getJson('/api/events');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'data' => [
                        '*' => ['id', 'title', 'description', 'date', 'location', 'user', 'tickets']
                    ],
                    'links',
                    'meta',
                ],
            ]);
    }
}
