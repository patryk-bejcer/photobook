<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use Faker\Factory as Faker;

class AuthTest extends TestCase
{

    /**
     * Login as default API user and get token back.
     *
     * @return void
     */
    public function testRegister()
    {
        $faker = Faker::create();
        $baseUrl = Config::get('app.url') . '/api/auth/register';
        $password = Config::get('api.apiPassword');

        $response = $this->json('POST', $baseUrl . '/', [
            'name' =>  $faker->name,
            'email' => $faker->email,
            'password' => $password,
            'password_confirmation' => $password
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message', 'user'
            ]);
    }


    /**
     * Login as default API user and get token back.
     *
     * @return void
     */
    public function testLogin()
    {
        $baseUrl = Config::get('app.url') . '/api/auth/login';
        $email = Config::get('api.apiEmail');
        $password = Config::get('api.apiPassword');

        $response = $this->json('POST', $baseUrl . '/', [
            'email' => $email,
            'password' => $password
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
               'user', 'access_token', 'token_type', 'expires_in'
            ]);
    }

    /**
     * Test logout.
     *
     * @return void
     */
    public function testLogout()
    {
        $user = User::where('email', 'user1@mail.com')->first();
        $token = JWTAuth::fromUser($user);
        $baseUrl =  Config::get('app.url') . '/api/auth/logout?token=' . $token;

        $response = $this->json('POST', $baseUrl, []);

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'message' => 'User successfully signed out'
            ]);
    }

    /**
     * Test token refresh.
     *
     * @return void
     */
    public function testRefresh()
    {
        $user = User::where('email', 'user1@mail.com')->first();
        $token = JWTAuth::fromUser($user);
        $baseUrl =  Config::get('app.url') . '/api/auth/refresh?token=' . $token;

        $response = $this->json('POST', $baseUrl, []);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'user', 'access_token', 'token_type', 'expires_in'
            ]);
    }

    /**
     * Get all users.
     *
     * @return void
     */
    public function testGetUserProfile()
    {
        $user = User::where('email', 'user1@mail.com')->first();
        $token = JWTAuth::fromUser($user);
        $baseUrl = Config::get('app.url') . '/api/auth/user-profile?token=' . $token;

        $response = $this->json('GET', $baseUrl . '/', []);

        $response->assertStatus(200);
    }
}
