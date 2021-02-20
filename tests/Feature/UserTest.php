<?php
//TODO: Add Unit test to ensure that passwords are encrypted before saving into the database

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $adminUser, $nonAdminUser, $validUser;

    public function setUp(): void
    {
        parent::setUp();
        $this->setUpFaker();
        $this->adminUser = User::factory()->create(["is_admin" => true]);
        $this->nonAdminUser = User::factory()->create();
        $this->validUser = ["name" => $this->faker->name, "email" => $this->faker->email, "password" => "Dilweed86!", "phone_number" => "+233248506381"];
    }

    public function signUpUser(array $user)
    {
        return $this->withHeader('Accept', 'application/json')->post(route('user.signup'), $user);
    }

    public function loginUser(array $user)
    {
        return $this->withHeader('Accept', 'application/json')->post(route('user.login'), $user);
    }

    public function updateUser(int $userID, array $updateAttributes, Authenticatable $authUser)
    {
        return $this->actingAs($authUser)->withHeader('Accept', 'application/json')->patch(route('users.update', ["user" => $userID]), $updateAttributes);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testShouldCreateNewUserSuccessfully()
    {
        $expectedResponse = ["name" => $this->validUser["name"], "email" => $this->validUser["email"], "phone_number" => $this->validUser["phone_number"]];
        $response = $this->signUpUser($this->validUser);

        $response->assertStatus(201)->assertJson($expectedResponse);
        $this->assertDatabaseHas('users', $expectedResponse);
    }

    public function testShouldNotIncludeHiddenFieldsInOutput()
    {
        $response = $this->signUpUser($this->validUser);
        $user = User::where('email', $this->validUser["email"])->first();
        $response->assertJsonMissing(["is_admin" => $user->is_admin, "password" => $user->password, "is_verified" => $user->is_verified, "email_verified_at" => $user->email_verified_at]);
    }

    public function testShouldReturn_400ResponseWhenEmailIsPresent()
    {
        $newUser = $this->validUser;
        $newUser["email"] = $this->adminUser->email;
        $response = $this->signUpUser($newUser);
        $response->assertStatus(400);
        $this->assertDatabaseCount('users', 2);
    }

    public function testShouldReturn_422WhenRequiredFieldsAreMissing()
    {
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

    public function testShouldReturn_422WhenPasswordIsTooShort()
    {
        $userWithShortPassword = $this->validUser;
        $userWithShortPassword["password"] = "1234";
        $response = $this->signUpUser($userWithShortPassword);
        $response->assertStatus(422);
        $this->assertDatabaseMissing('users', ["email" => $userWithShortPassword]);
    }

    public function testShouldReturn_422WhenPhoneNumberIsInvalid()
    {
        $userWithInvalidPhoneNumber = $this->validUser;
        $userWithInvalidPhoneNumber["phone_number"] = "12345678";
        $response = $this->signUpUser($userWithInvalidPhoneNumber);
        $response->assertStatus(422);
        $this->assertDatabaseMissing('users', ["email" => $userWithInvalidPhoneNumber]);
    }

    public function testShouldLoginUserSuccessfully()
    {
        $response = $this->loginUser(["email" => $this->adminUser->email, "password" => 'testPassword']);
        $response->assertStatus(200)->assertJson(["email" => $this->adminUser->email, "name" => $this->adminUser->name, "phone_number" => $this->adminUser->phone_number])->assertJsonMissing(["is_admin" => $this->adminUser->is_admin, "password" => $this->adminUser->password, "is_verified" => $this->adminUser->is_verified, "email_verified_at" => $this->adminUser->email_verified_at]);;
    }

    public function testShouldReturn_401WhenLoginFails()
    {
        $response = $this->loginUser(["email" => $this->adminUser->email, "password" => 'wrongPassword']);
        $response->assertStatus(401)->assertJsonMissing(["email" => $this->adminUser->email, "name" => $this->adminUser->name, "phone_number" => $this->adminUser->phone_number]);

        $response = $this->loginUser(["email" => "random@email.com", "password" => 'testPassword']);
        $response->assertStatus(401)->assertJsonMissing(["email" => $this->adminUser->email, "name" => $this->adminUser->name, "phone_number" => $this->adminUser->phone_number]);
    }

    public function testShouldUpdateUserSuccessfully()
    {
        $newEmail = "newemail@gmail.com";
        $response = $this->updateUser($this->nonAdminUser->id, ['email' => $newEmail], $this->nonAdminUser);
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', ["id" => $this->nonAdminUser->id, "email" => $newEmail]);
    }

    public function testShouldNotUpdateAndReturn_401WhenUnauthenticated()
    {
        $updateAttributes = ['email' => "newemail@gmail.com"];
        $response = $this->withHeader('Accept', 'application/json')->patch(route('users.update', ["user" => $this->adminUser->id]), $updateAttributes);
        $response->assertStatus(401);
    }

    public function testShouldNotAllowUserToUpdateProfileThatIsNotTheirsIfNotAdmin()
    {
        $newEmail = "newemail@gmail.com";
        $updateAttributes = ['email' => $newEmail];
        $response = $this->updateUser($this->adminUser->id, $updateAttributes, $this->nonAdminUser);
        $response->assertStatus(403);
        $this->assertDatabaseMissing("users", ["id" => $this->adminUser->id, "email" => $newEmail]);
    }

    public function testShouldAllowUserToUpdateProfileThatIsNotTheirsIfAdmin()
    {
        $newEmail = "newemail@gmail.com";
        $updateAttributes = ['email' => $newEmail];
        $response = $this->updateUser($this->nonAdminUser->id, $updateAttributes, $this->adminUser);
        $response->assertStatus(200);
        $this->assertDatabaseHas("users", ["id" => $this->nonAdminUser->id, "email" => $newEmail]);
    }

    public function testShouldDeleteUserSuccessfully()
    {
        $response = $this->actingAs($this->nonAdminUser)->withHeader('Accept', 'application/json')->delete(route('users.destroy', ["user" => $this->nonAdminUser->id]));
        $response->assertStatus(200);
        $this->assertDatabaseMissing("users", ["id" => $this->nonAdminUser->id]);
    }

    public function testShouldNotDeleteAndReturn_401WhenUnauthenticated()
    {
        $response = $this->withHeader('Accept', 'application/json')->delete(route('users.destroy', ["user" => $this->adminUser->id]));
        $response->assertStatus(401);
        $this->assertDatabaseHas("users", ["id" => $this->adminUser->id]);
    }

    public function testShouldNotAllowUserToDeleteProfileThatIsNotTheirsIfNotAdmin()
    {
        $response = $this->actingAs($this->nonAdminUser)->withHeader('Accept', 'application/json')->delete(route('users.destroy', ["user" => $this->adminUser->id]));
        $response->assertStatus(403);
        $this->assertDatabaseHas("users", ["id" => $this->adminUser->id]);
    }

    public function testShouldAllowUserToDeleteProfileThatIsNotTheirsIfAdmin()
    {
        $response = $this->actingAs($this->adminUser)->withHeader('Accept', 'application/json')->delete(route('users.destroy', ["user" => $this->nonAdminUser->id]));
        $response->assertStatus(200);
        $this->assertDatabaseMissing("users", ["id" => $this->nonAdminUser->id]);
    }

    public function testShouldNotAllowUserToViewAllUsersIfNotAdmin()
    {
        $response = $this->actingAs($this->nonAdminUser)->withHeader('Accept', 'application/json')->get(route('users.index'));
        $response->assertStatus(403);
        $response->assertJsonStructure(['message']);
    }

    public function testShouldAllowUserToViewAllUsersIfAdmin()
    {
        $response = $this->actingAs($this->adminUser)->withHeader('Accept', 'application/json')->get(route('users.index'));
        $usersLength = User::all()->count();
        $response->assertStatus(200);
        $response->assertJsonCount($usersLength);
    }

    public function testShouldGetSingleUserSuccessfully()
    {
        $response = $this->actingAs($this->nonAdminUser)->withHeader('Accept', 'application/json')->get(route('users.show', ["user" => $this->nonAdminUser->id]));
        $response->assertStatus(200);
        $response->assertJson(["email" => $this->nonAdminUser->email, "name" => $this->nonAdminUser->name]);
    }

    public function testShouldNotAllowUserToGetSpecificProfileThatIsNotTheirsIfNotAdmin(){
        $response = $this->actingAs($this->nonAdminUser)->withHeader('Accept', 'application/json')->get(route('users.show', ["user" => $this->adminUser->id]));
        $response->assertStatus(403);
        $response->assertJsonStructure(['message']);
        $response->assertJsonMissing(["email" => $this->nonAdminUser->email, "name" => $this->nonAdminUser->name]);
    }

    public function testShouldAllowUserToGetSpecificProfileThatIsNotTheirsIfAdmin(){
        $response = $this->actingAs($this->adminUser)->withHeader('Accept', 'application/json')->get(route('users.show', ["user" => $this->nonAdminUser->id]));
        $response->assertStatus(200);
        $response->assertJson(["email" => $this->nonAdminUser->email, "name" => $this->nonAdminUser->name]);
    }

    //TODO: Add test for verified and unverified users

}
