<?php

namespace App\Services;

use App\Constants\TokenConstants;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * Service d' authentification 
 */
class AuthService
{

    public function __construct(
        private readonly UserService $userService,
    ) {}

    /**
     * Generate access and refresh tokens.
     */
    public function generateTokens(Utilisateur $user): array
    {
        // Access Token
        $accessToken = $user->createToken(
            TokenConstants::DEFAULT_ACCESS_TOKEN_NAME,
            [TokenConstants::ABILITY_ALL],
            now()->addMinutes(TokenConstants::DEFAULT_ACCESS_TOKEN_EXPIRY_MINUTES)
        );

        // Refresh Token
        $refreshToken = $user->createToken(
            TokenConstants::DEFAULT_REFRESH_TOKEN_NAME,
            [TokenConstants::ABILITY_REFRESH],
            now()->addDays(TokenConstants::DEFAULT_REFRESH_TOKEN_EXPIRY_DAYS)
        );

        return [
            'access_token' => $accessToken->plainTextToken,
            'refresh_token' => $refreshToken->plainTextToken,
            'token_type' => TokenConstants::TOKEN_TYPE,
            'expires_in' => TokenConstants::DEFAULT_ACCESS_TOKEN_EXPIRY_MINUTES * 60,
        ];
    }


    public function login(array $credentials): array
    {
        $user = $this->verifyUserCredentials($credentials['email'], $credentials['password']);

        return $this->generateTokens($user);
    }

    /**
     * Vérifier les credentials d'un utilisateur
     * 
     * @param string $email Email de l' user
     * @param string $password Mot de passe
     * @return Utilisateur
     * @throws ValidationException
     */
    public  function verifyUserCredentials(string $email, string $password): Utilisateur
    {
        $utilisateur = Utilisateur::where('email', $email)->first();

        if (!$utilisateur || !Hash::check($password, $utilisateur->password)) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants fournis sont incorrects.'],
            ]);
        }


        return $utilisateur;
    }


    /**
     * Refresh tokens using a refresh token.
     */
    public function refresh(Utilisateur $user): array
    {
        // Revoke all existing tokens to ensure a clean state
        $user->tokens()->delete();

        return $this->generateTokens($user);
    }

    /**
     * Logout user by revoking current token.
     */
    public function logout(Utilisateur $user): void
    {
        $token = PersonalAccessToken::findToken($user->currentAccessToken());

        if ($token) {
            $token->delete();
        }
    }
}
