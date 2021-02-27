<?php

namespace Tests\Feature;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Tests\TestCase;

class EventTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase, WithFaker;

    /**
     * @var \Illuminate\Database\Eloquent\Collection|Model|mixed
     */
    private $nonAdminUser, $adminUser, $setUpDBCount, $validEvent, $setUpEvent;

    public function setUp(): void
    {
        parent::setUp();
        $this->setUpEvent = Event::factory()->create();
        $this->setUpFaker();
        $this->adminUser = User::factory()->create(["is_admin" => true]);
        $this->nonAdminUser = User::factory()->create();
        $this->validEvent = ["name" => "Ubora", "venue" => "Unique Floral", "event_date" => "2021-02-28 12:50 PM", "flyer" => "flyerLink"];
        $this->setUpDBCount = Event::all()->count();
    }

    public function createEvent(array $event, Authenticatable $actingUser){
        return $this->actingAs($actingUser)->withHeader('Accept', 'application/json')->post(route('events.store'), $event);
    }

    public function getAllEvents(Authenticatable $actingUser){
        return $this->actingAs($actingUser)->withHeader('Accept', 'application/json')->get(route('events.index'));
    }

    public function getEventById(Authenticatable $actingUser, int $eventId){
        return $this->actingAs($actingUser)->withHeader('Accept', 'application/json')->get(route('events.show', ['event' => $eventId]));
    }

    public function  testShouldCreateEventSuccessfully(){
        $event  = $this->validEvent;
        $response = $this->createEvent($event, $this->adminUser);
        $response->assertStatus(201);
        $this->assertDatabaseHas('events', ["name" => $event['name'], "venue" => $event["venue"], "event_date" =>  $event['event_date']]);
    }

    public function testShouldReturn_422WhenRequiredFieldsAreMissing(){
        $eventWithoutName = $this->validEvent;
        unset($eventWithoutName['name']);
        $response = $this->createEvent($eventWithoutName, $this->adminUser);
        $response->assertStatus(422);
        $this->assertDatabaseCount('events', $this->setUpDBCount);

        $eventWithoutVenue = $this->validEvent;
        unset($eventWithoutVenue['venue']);
        $response = $this->createEvent($eventWithoutVenue, $this->adminUser);
        $response->assertStatus(422);
        $this->assertDatabaseCount('events', $this->setUpDBCount);

        $eventWithoutDate = $this->validEvent;
        unset($eventWithoutDate['event_date']);
        $response = $this->createEvent($eventWithoutDate, $this->adminUser);
        $response->assertStatus(422);
        $this->assertDatabaseCount('events', $this->setUpDBCount);

        $eventWithoutFlyer = $this->validEvent;
        unset($eventWithoutFlyer['flyer']);
        $response = $this->createEvent($eventWithoutFlyer, $this->adminUser);
        $response->assertStatus(422);
        $this->assertDatabaseCount('events', $this->setUpDBCount);
    }

    public function testShouldNotAllowNonAdminUserToCreateEvent(){
        $event  = $this->validEvent;
        $response = $this->createEvent($event, $this->nonAdminUser);
        $response->assertStatus(403);
        $this->assertDatabaseMissing('events', ["name" => $event['name'], "venue" => $event["venue"], "event_date" => date_create_from_format(config('constants.date_format'), $event['event_date'])]);
    }

    public function testShouldNotAllowUnauthenticatedUserToCreateEvent(){
        $event  = $this->validEvent;
        $response = $this->withHeader('Accept', 'application/json')->post(route('events.store'), $event);
        $response->assertStatus(401);
        $this->assertDatabaseMissing('events', ["name" => $event['name'], "venue" => $event["venue"], "event_date" => date_create_from_format(config('constants.date_format'), $event['event_date'])]);
    }

    public function testShouldGetAllEventsSuccessfully(){
        $response = $this->getAllEvents($this->adminUser);
        $response->assertStatus(200);
        $response->assertJsonCount($this->setUpDBCount);
    }

    public function testShouldNotAllowUnauthenticatedUserToGetAllEvents(){
        $response = $this->withHeader('Accept', 'application/json')->get(route('events.index'));
        $response->assertStatus(401);
    }

    public function testShouldGetEventByIdSuccessfully(){
        $response = $this->getEventById($this->adminUser, $this->setUpEvent->id);
        $response->assertStatus(200);
        $response->assertJson(["name" => $this->setUpEvent['name'], "venue" => $this->setUpEvent['venue'], "event_date" => $this->setUpEvent["event_date"]]);
    }

    public function testShouldNotAllowUnauthenticatedUserToGetSpecificEvent(){
        $response = $this->withHeader('Accept', 'application/json')->get(route('events.show', ['event' => $this->setUpEvent->id]));
        $response->assertStatus(401);
    }

    public function testShouldNotIncludeTotalContributionInResponseIfNotAdmin(){
        $response = $this->getEventById($this->nonAdminUser, $this->setUpEvent->id);
        $response->assertJsonMissing(["total_contribution" => "0"]);
    }


}
