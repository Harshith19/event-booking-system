<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Traits\CommonQueryScopes;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    use CommonQueryScopes;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $cacheKey = 'events_' . md5(serialize($request->all()));

        $events = cache()->remember($cacheKey, 3600, function() use ($request) {
            $query = Event::with(['user', 'tickets']);
            
            // Search
            if($request->has('search')) {
                $query->searchByTitle($request->search);
            }
    
            // Filter by date
            if($request->has('date')) {
                $query->filterByDate($request->date);
            }
    
            // Filter by location
            if($request->has('location')) {
                $query->where('location', 'like', '%' . $request->location . '%');
            }
    
            return $query->paginate(config('constants.PAGINATION'));
        });


        return $this->successResponse($events);
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event): JsonResponse
    {
        $event->load(['user', 'tickets']);
        return $this->successResponse($event);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Event::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date|after:now',
            'location' => 'required|string|max:255',
        ]);

        $event = Event::create([
            ...$validated,
            'created_by' => $request->user()->id,
        ]);

        return $this->successResponse($event, 'Event created successfully', 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event): JsonResponse
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'date' => 'sometimes|date|after:now',
            'location' => 'sometimes|string|max:255',
        ]);

        $event->update($validated);

        return $this->successResponse($event, 'Event updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event): JsonResponse
    {
        $this->authorize('delete', $event);

        $event->delete();

        return $this->successResponse(null, 'Event deleted successfully');
    }
}
