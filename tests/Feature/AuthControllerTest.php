<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    /** @dataProvider loginDataProvider */
    public function testItValidatesRequiredLoginDetails($field, $input)
    {
        $this->postJson(route('api.auth.login', [
            $field => $input,
        ]))->assertUnprocessable()->assertJsonValidationErrorFor($field);
    }

    private function loginDataProvider(): array
    {
        return [
            ['email', ''],
            ['email', 'not-an-email'],
            ['email', 'not-an-email@stillborked'],
            ['password', ''],
        ];
    }

    public function testThrowsAValidationExceptionIfUserExistsByPasswordIsIncorrect()
    {
        $userCredentials = [
            'email' => 'cassian.andor@disneyoverlords.com',
            'password' => 'SecretlyInLoveWithBiggsButDontTell',
        ];

        User::factory()->create($userCredentials);

        $this->postJson(route('api.auth.login', [
            'email' => $userCredentials['email'],
            'password' => 'MissedLikeAStormTrooper',
        ]))->assertUnprocessable()->assertJsonValidationErrors('email');
    }

    public function testItReturnsAnAccessTokenOnSuccessfulLogin()
    {
        $userCredentials = [
            'email' => 'cassian.andor@disneyoverlords.com',
            'password' => 'SecretlyInLoveWithBiggsButDontTell',
        ];

        User::factory()->create($userCredentials);

        $this->postJson(route('api.auth.login'), $userCredentials)
            ->assertSuccessful()
            ->assertJson(fn (AssertableJson $json) => $json->has('token')->whereNot('token', null)->etc());
    }

    public function testItReturnsTheUsersNameOnSuccessfulLogin()
    {
        $userCredentials = [
            'name' => 'Cassian Andor',
            'email' => 'cassian.andor@disneyoverlords.com',
            'password' => 'SecretlyInLoveWithBiggsButDontTell',
        ];

        User::factory()->create($userCredentials);

        $this->postJson(route('api.auth.login'), $userCredentials)
            ->assertSuccessful()
            ->assertJson(fn (AssertableJson $json) => $json->where('name', $userCredentials['name'])->etc());
    }

    public function testAnUnauthenticatedUserCanNotAccessLogoutRoute()
    {
        $this->withoutExceptionHandling();

        $this->expectException(AuthenticationException::class);

        $this->deleteJson(route('api.auth.logout'));
    }

    public function testAuthenticatedUserCanLogout()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->deleteJson(route('api.auth.logout'))
            ->assertSuccessful();
    }
}
