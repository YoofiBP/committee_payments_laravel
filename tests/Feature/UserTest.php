<?php
//TODO: Add Unit test to ensure that passwords are encrypted before saving into the database

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $user, $validUser;

    public function setUp(): void {
        parent::setUp();
        $this->setUpFaker();
        $this->user = User::factory()->create();
        $this->validUser = ["name" => $this->faker->name, "email"=>$this->faker->email, "password" => "Dilweed86!", "phone_number" => "+233248506381"];
    }

    public function signUpUser(array $user) {
        return $this->withHeader('Accept', 'application/json')->post(route('user.signup'),$user);
    }

    public function loginUser(array $user){
        return $this->withHeader('Accept', 'application/json')->post(route('user.login'), $user);
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

    public function testShouldReturn_400ResponseWhenEmailIsPresent(){
        $user = User::find(1);
        $newUser = $this->validUser;
        $newUser["email"] = $user->email;
        $response = $this->signUpUser($newUser);
        $response->assertStatus(400);
        $this->assertDatabaseCount('users', 1);
    }

    public function testShouldReturn_422WhenRequiredFieldsAreMissing(){
        $userWithNoEmail = $this->validUser;
        unset($userWithNoEmail["email"]);
        $response = $this->signUpUser($userWithNoEmail);
        $response->assertStatus(422);
        $this->assertDatabaseMissing('users', ["phone_number" => $userWithNoEmail]);

        $userWithNoName = $this->validUser;
        unset($userWithNoName["name"]);
        $response = $this->signUpUser($userWithNoName);
        $response->assertStatus(422);
        $this->assertDatabaseMissing('users', ["email" => $userWithNoName]);

        $userWithNoPhoneNumber = $this->validUser;
        unset($userWithNoPhoneNumber["name"]);
        $response = $this->signUpUser($userWithNoPhoneNumber);
        $response->assertStatus(422);
        $this->assertDatabaseMissing('users', ["email" => $userWithNoName]);

        $userWithNoPassWord = $this->validUser;
        unset($userWithNoPassWord["name"]);
        $response = $this->signUpUser($userWithNoPassWord);
        $response->assertStatus(422);
        $this->assertDatabaseMissing('users', ["email" => $userWithNoName]);
    }

    public function testShouldReturn_422WhenPasswordIsTooShort(){
        $userWithShortPassword = $this->validUser;
        $userWithShortPassword["password"] = "1234";
        $response = $this->signUpUser($userWithShortPassword);
        $response->assertStatus(422);
        $this->assertDatabaseMissing('users', ["email" => $userWithShortPassword]);
    }

    public function testShouldReturn_422WhenPhoneNumberIsInvalid(){
        $userWithInvalidPhoneNumber = $this->validUser;
        $userWithInvalidPhoneNumber["phone_number"] = "12345678";
        $response = $this->signUpUser($userWithInvalidPhoneNumber);
        $response->assertStatus(422);
        $this->assertDatabaseMissing('users', ["email" => $userWithInvalidPhoneNumber]);
    }

    public function testShouldLoginUserSuccessfully(){
        $response = $this->loginUser(["email" => $this->user->email, "password" => 'testPassword']);
        $response->assertStatus(200)->assertJson(["email" => $this->user->email, "name" => $this->user->name, "phone_number" => $this->user->phone_number])->assertJsonMissing(["is_admin" => $this->user->is_admin, "password" => $this->user->password, "is_verified" => $this->user->is_verified, "email_verified_at" => $this->user->email_verified_at]);;
    }

    public function testShouldReturn_401WhenLoginFails(){
        $response = $this->loginUser(["email" => $this->user->email, "password" => 'wrongPassword']);
        $response->assertStatus(401)->assertJsonMissing(["email" => $this->user->email, "name" => $this->user->name, "phone_number" => $this->user->phone_number]);

        $response = $this->loginUser(["email" => "random@email.com", "password" => 'testPassword']);
        $response->assertStatus(401)->assertJsonMissing(["email" => $this->user->email, "name" => $this->user->name, "phone_number" => $this->user->phone_number]);
    }

}
