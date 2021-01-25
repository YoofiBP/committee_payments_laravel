<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    public function setUp(): void {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testShouldCreateNewUserSuccessfully()
    {
        $userInfo = ["name" => "Yoofi", "email"=>"yoofi@gmail.com", "password" => "Dilweed86!", "phone_number" => "+233248506381"];
        $expectedResponse = ["name" => "Yoofi", "email"=>"yoofi@gmail.com", "phone_number" => "+233248506381"];
        $response = $this->withHeader('Accept', 'application/json')->post(route('users.store'),$userInfo);

        $response->assertStatus(201)->assertJson($expectedResponse)->assertJsonMissing(["is_admin" => false]);
        $this->assertDatabaseHas('users', $expectedResponse);
    }
}
