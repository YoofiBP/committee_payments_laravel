<?php
//TODO: Add Unit test to ensure that passwords are encrypted before saving into the database

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $validUser;

    public function setUp(): void {
        parent::setUp();
        $this->setUpFaker();
        User::factory()->create();
        $this->validUser = ["name" => $this->faker->name, "email"=>$this->faker->email, "password" => "Dilweed86!", "phone_number" => "+233248506381"];
    }

    public function signUpUser(array $user) {
        return $this->withHeader('Accept', 'application/json')->post(route('users.store'),$user);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testShouldCreateNewUserSuccessfully()
    {
        $expectedResponse = ["name" => $this->validUser["name"], "email"=>$this->validUser["email"], "phone_number" => $this->validUser["phone_number"]];
        $response = $this->signUpUser($this->validUser);

        $response->assertStatus(201)->assertJson($expectedResponse);
        $this->assertDatabaseHas('users', $expectedResponse);
    }

    public function testShouldNotIncludeHiddenFieldsInOutput(){
        $response = $this->signUpUser($this->validUser);
        $user = User::where('email', $this->validUser["email"])->first();
        $response->assertJsonMissing(["is_admin" => $user->is_admin, "password" => $user->password, "is_verified" => $user->is_verified, "email_verified_at" => $user->email_verified_at]);
    }

    public function testShouldReturn400ResponseWhenEmailIsPresent(){
        $user = User::find(1);
        $newUser = $this->validUser;
        $newUser["email"] = $user->email;
        $response = $this->signUpUser($newUser);
        //TODO: Enable Error handling to ensure that 400 status is returned when user email is already present
        $response->assertStatus(500);
    }

    public function testShouldReturn422WhenRequiredFieldsAreMissing(){
        $userWithNoEmail = $this->validUser;
        unset($userWithNoEmail["email"]);
        $response = $this->signUpUser($userWithNoEmail);
        $response->assertStatus(422);

        $userWithNoName = $this->validUser;
        unset($userWithNoName["name"]);
        $response = $this->signUpUser($userWithNoName);
        $response->assertStatus(422);

        $userWithNoPhoneNumber = $this->validUser;
        unset($userWithNoPhoneNumber["name"]);
        $response = $this->signUpUser($userWithNoPhoneNumber);
        $response->assertStatus(422);

        $userWithNoPassWord = $this->validUser;
        unset($userWithNoPassWord["name"]);
        $response = $this->signUpUser($userWithNoPassWord);
        $response->assertStatus(422);
    }

    public function testShouldReturn422WhenPasswordIsTooShort(){
        $userWithShortPassword = $this->validUser;
        $userWithShortPassword["password"] = "1234";
        $response = $this->signUpUser($userWithShortPassword);
        $response->assertStatus(422);
    }

    public function testShouldReturn422WhenPhoneNumberIsInvalid(){
        $userWithInvalidPhoneNumber = $this->validUser;
        $userWithInvalidPhoneNumber["phone_number"] = "12345678";
        $response = $this->signUpUser($userWithInvalidPhoneNumber);
        $response->assertStatus(422);
    }

}
