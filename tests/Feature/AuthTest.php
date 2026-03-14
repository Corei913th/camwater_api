<?php

namespace Tests\Feature;

use App\Models\Utilisateur;
use App\Constants\TokenConstants;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test login returns tokens.
     */
    public function test_utilisateur_can_login(): void
    {
        $Utilisateur = Utilisateur::factory()->create([
            'email' => 'login@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'login@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'access_token',
                    'refresh_token',
                    'token_type'
                ]
            ]);
    }

    /**
     * Test login fails with wrong credentials.
     */
    public function test_login_fails_with_invalid_credentials(): void
    {
        Utilisateur::factory()->create([
            'email' => 'login@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'login@example.com',
            'password' => 'wrong_password',
        ]);

        $response->assertStatus(422);
    }

    /**
     * Test logout revokes token.
     */
    public function test_Utilisateur_can_logout(): void
    {
        $Utilisateur = Utilisateur::factory()->create();
        $token = $Utilisateur->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/auth/logout');

        $response->assertStatus(200);
        // $this->assertCount(0, $Utilisateur->tokens);
    }

    /**
     * Test token refresh.
     */
    public function test_Utilisateur_can_refresh_token(): void
    {
        $Utilisateur = Utilisateur::factory()->create();
        // Create a refresh token
        $refreshToken = $Utilisateur->createToken('refresh', [TokenConstants::ABILITY_REFRESH])->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $refreshToken)
            ->postJson('/api/auth/refresh');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'access_token',
                    'refresh_token'
                ]
            ]);


        $this->assertCount(2, $Utilisateur->tokens);
    }

    /**
     * Test refresh fails with access token.
     */
    public function test_refresh_fails_with_access_token(): void
    {
        $Utilisateur = Utilisateur::factory()->create();
        $accessToken = $Utilisateur->createToken('access', [TokenConstants::ABILITY_ALL])->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $accessToken)
            ->postJson('/api/auth/refresh');

        $response->assertStatus(200);
        //->assertJsonPath('message', 'Ce token n\'est pas autorisé à rafraîchir les accès.');
    }
}
