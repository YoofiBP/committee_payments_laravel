<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\EventRequest;
use App\Http\Resources\EventCollection;
use App\Http\Resources\EventResource;
use App\Models\Event;
use App\Services\Interfaces\EventServiceInterface;
use Illuminate\Http\Request;

class EventController extends Controller
{
    private EventServiceInterface $eventService;

    public function __construct(EventServiceInterface $eventService){
        $this->middleware(['web', 'auth:sanctum', 'verified']);
        $this->eventService = $eventService;
        $this->authorizeResource(Event::class, 'event');
    }

    public function store(EventRequest $request){
        $data = $request->validated();
        $event = $this->eventService->add($data);
        return (new EventResource($event))->response()->setStatusCode(201);
    }

    public function index() {
        $events = $this->eventService->all();
        return new EventCollection($events);
    }

    public function show(Event $event){
        return new EventResource($event);
    }

    public function destroy(Event $event){
        $this->eventService->delete($event);
        return response()->json(["message"=>"Delete Successful"],200);
    }

    public function update(EventRequest $request, Event $event){
        $data = $request->validated();
        $event = $this->eventService->update($event, $data);
        return new EventResource($event);
    }
}
