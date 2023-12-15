<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_registers_a_new_user()
    {
        // Given: Prepare the user data
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        // When: Send a POST request to register the user
        $response = $this->postJson('/api/register', $userData);

        // Then: Assert the response
        $response->assertStatus(201)
            ->assertJson([
                'status' => true,
            ])->assertJsonStructure([
            'data' => [
                'user',
                'token',
            ],
        ]);
    }

    /** @test */
    public function it_returns_error_for_missing_required_field_on_registration()
    {
        // Given: Prepare the user data with a missing required field (e.g., 'email')
        $userData = [
            'name' => 'Test User',
            'password' => 'password',
        ];

        // When: Send a POST request to register the user with missing data
        $response = $this->postJson('/api/register', $userData);

        // Then: Assert that the response contains an error message for missing required fields
        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'email',
            ]);
    }

    /** @test */
    public function it_logs_in_a_user_with_correct_credentials()
    {
        // Given: Create a user
        $email = 'test@example.com';
        $password = 'password';

        $user = User::factory()->create([
            'email' => $email,
            'password' => bcrypt($password),
        ]);

        // Prepare user credentials
        $userCredentials = [
            'email' => $email,
            'password' => $password,
        ];

        // When: Send a POST request to log in the user
        $response = $this->postJson('/api/login', $userCredentials);

        // Then: Assert the response for a successful login
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'user',
                    'token',
                ],
            ]);
    }

    /** @test */
    public function it_returns_error_for_missing_required_field_on_login()
    {
        // Given: Prepare user credentials with a missing required field (e.g., 'email')
        $userCredentials = [
            'password' => 'password',
        ];

        // When: Send a POST request to log in with missing data
        $response = $this->postJson('/api/login', $userCredentials);

        // Then: Assert that the response contains an error message for missing required fields
        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'email',
            ]);
    }

    /** @test */
    public function it_returns_error_for_incorrect_login_credentials()
    {
        // Given: Prepare user credentials with incorrect password
        $userCredentials = [
            'email' => 'test@example.com',
            'password' => 'incorrect_password',
        ];

        // When: Send a POST request to log in with incorrect credentials
        $response = $this->postJson('/api/login', $userCredentials);

        // Then: Assert that the response contains an error message for incorrect credentials
        $response->assertStatus(401)
            ->assertJson([
                'status' => false,
            ]);
    }

    /** @test */
    public function it_returns_user_profile_when_authenticated()
    {
        // Given: Create a user and authenticate
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $token = $user->createToken('authToken')->plainTextToken;

        // Set the Bearer token for authentication
        $headers = ['Authorization' => "Bearer $token"];

        // When: Send a GET request to get user profile
        $response = $this->withHeaders($headers)->get('/api/account/profile');

        // Then: Assert the response for a successful retrieval of the user profile
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'id',
                    'name',
                ],
            ]);
    }

    /** @test */
    public function it_returns_unauthorized_for_unauthenticated_user_profile_access()
    {
        // When: Send a GET request to access user profile without authentication
        $response = $this->withHeaders(['Accept' => 'application/json'])->get('/api/account/profile');

        // Then: Assert the response for unauthorized access
        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    /** @test */
    public function it_logs_out_authenticated_user()
    {
        // Given: Create a user and authenticate
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $token = $user->createToken('authToken')->plainTextToken;

        // Set the Bearer token for authentication
        $headers = ['Authorization' => "Bearer $token"];

        // When: Send a POST request to log out the user
        $response = $this->withHeaders($headers)->post('/api/logout');

        // Then: Assert the response for a successful logout
        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
            ]);
    }

    /** @test */
    public function it_returns_unauthorized_for_unauthenticated_user_logout()
    {
        // When: Send a POST request to log out without authentication
        $response = $this->withHeaders(['Accept' => 'application/json'])->post('/api/logout');

        // Then: Assert the response for unauthorized access
        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }
}
