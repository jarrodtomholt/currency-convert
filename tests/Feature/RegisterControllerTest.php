<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    use WithFaker;

    /** @dataProvider registrationDataProvider */
    public function testItValidatesRequiredRegistrationDetails($field, $input)
    {
        $this->postJson(route('api.register', [
            $field => $input,
        ]))->assertUnprocessable()->assertJsonValidationErrorFor($field);
    }

    private function registrationDataProvider(): array
    {
        return [
            ['name', ''],
            ['email', ''],
            ['email', 'not-an-email'],
            ['email', 'not-an-email@stillborked'],
            ['password', ''],
            ['password', '2Short!'],
            ['password', 'Password'],
            ['password', 'password'],
            ['password', 'Password123'],
            ['password', '1Password!'],
            ['password', 'MissingANumber!'],
            ['password', 'MissingASpecialChar1'],
            ['password', 'MissingASpecialChar123'],
            ['password', 'missinganuppercasechar123!'],
            ['password_confirmation', ''],
        ];
    }

    public function testItEnsuresEmailDoesNotAlreadyExist()
    {
        $user = User::factory()->create();

        $this->postJson(route('api.register', [
            'email' => $user->email,
        ]))->assertUnprocessable()->assertJsonValidationErrorFor('email');
    }

    public function testItEnsuresPasswordConfirmationMatchesPassword()
    {
        $this->postJson(route('api.register', [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => 'SecretlyInLoveWithBiggsButDontTell!1',
            'password_confirmation' => 'ThisPasswordConfirmationDoesNotMatch',
        ]))->assertUnprocessable()->assertJsonValidationErrorFor('password_confirmation');
    }

    public function testItCreatesANewUserOnSuccessfulRegistration()
    {
        $user = [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => 'SecretlyInLoveWithBiggsButDontTell!1',
            'password_confirmation' => 'SecretlyInLoveWithBiggsButDontTell!1',
        ];

        $this->postJson(route('api.register', $user))
            ->assertSuccessful();

        $this->assertDatabaseHas(User::class, [
            'name' => $user['name'],
            'email' => $user['email'],
        ]);
    }

    public function testItRegistersAUsersAndReturnsAnAccessTokenOnSuccess()
    {
        $user = [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => 'SecretlyInLoveWithBiggsButDontTell!1',
            'password_confirmation' => 'SecretlyInLoveWithBiggsButDontTell!1',
        ];

        $this->postJson(route('api.register', $user))
            ->assertSuccessful()
            ->assertJson(fn (AssertableJson $json) => $json->has('token')->etc());
    }

    public function testItRegistersAUsersAndReturnsTheUsersNameOnSuccess()
    {
        $user = [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => 'SecretlyInLoveWithBiggsButDontTell!1',
            'password_confirmation' => 'SecretlyInLoveWithBiggsButDontTell!1',
        ];

        $this->postJson(route('api.register', $user))
            ->assertSuccessful()
            ->assertJson(fn (AssertableJson $json) => $json->where('name', $user['name'])->etc());
    }

    public function testAnAuthenticatedUserCanNotRegisterThanksToMiddleware()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.register', [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => 'SecretlyInLoveWithBiggsButDontTell!1',
            'password_confirmation' => 'SecretlyInLoveWithBiggsButDontTell!1',
        ]))->assertStatus(Response::HTTP_PRECONDITION_FAILED);
    }
}
