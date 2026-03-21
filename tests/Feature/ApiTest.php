<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Abonne;
use App\Models\Facture;
use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    protected $token = '';

    protected function setUp(): void
    {
        parent::setUp();

        // Authenticate a Utilisateur for all API tests
        $Utilisateur = Utilisateur::factory()->create([
            'role' => Role::ADMIN,
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);
        Sanctum::actingAs($Utilisateur);
        $this->token = $Utilisateur->currentAccessToken();
    }

    /**
     * Test getting abonnes list.
     */
    public function test_can_list_abonnes(): void
    {
        Abonne::factory()->count(3)->create();

        $response = $this->withHeader('Authorization', 'Bearer '.$this->token)->getJson('/api/abonnes');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ]);
    }

    /**
     * Test creating an abonne.
     */
    public function test_can_create_abonne(): void
    {
        $data = [
            'nom' => 'Doe',
            'prenom' => 'John',
            'ville' => 'Yaoundé',
            'quartier' => 'Etoudi',
            'numeroCompteur' => 'CPT'.rand(1000, 9999),
            'typeAbonnement' => 'DOMESTIQUE',
        ];

        $response = $response = $this->withHeader('Authorization', 'Bearer '.$this->token)->postJson('/api/abonnes', $data);

        $response->assertStatus(201)
            ->assertJsonPath('data.nom', 'Doe');

        $this->assertDatabaseHas('abonnes', ['numeroCompteur' => $data['numeroCompteur']]);
    }

    /**
     * Test showing a single abonne.
     */
    public function test_can_get_single_abonne(): void
    {
        $abonne = Abonne::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer '.$this->token)->getJson("/api/abonnes/{$abonne->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $abonne->id);
    }

    /**
     * Test updating an abonne.
     */
    public function test_can_update_abonne(): void
    {
        $abonne = Abonne::factory()->create();
        $newData = ['nom' => 'UpdatedName'];

        $response = $this->withHeader('Authorization', 'Bearer '.$this->token)->putJson("/api/abonnes/{$abonne->id}", $newData);

        $response->assertStatus(200)
            ->assertJsonPath('data.nom', 'UpdatedName');
    }

    /**
     * Test deleting an abonne.
     */
    public function test_can_delete_abonne(): void
    {
        $abonne = Abonne::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer '.$this->token)->deleteJson("/api/abonnes/{$abonne->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('abonnes', ['id' => $abonne->id]);
    }

    // ──────────────── Factures ────────────────

    /**
     * Test getting factures list.
     */
    public function test_can_list_factures(): void
    {
        Facture::factory()->count(2)->create();

        $response = $this->withHeader('Authorization', 'Bearer '.$this->token)->getJson('/api/factures');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ]);
    }

    /**
     * Test generating a facture.
     */
    public function test_can_generate_facture(): void
    {
        $abonne = Abonne::factory()->create();

        $data = [
            'abonneId' => $abonne->id,
            'consommation' => 100,
        ];

        $response = $this->withHeader('Authorization', 'Bearer '.$this->token)->postJson('/api/factures/generer', $data);

        $response->assertStatus(201)
            ->assertJsonPath('success', true);
    }

    /**
     * Test showing a single facture.
     */
    public function test_can_get_single_facture(): void
    {
        $facture = Facture::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer '.$this->token)->getJson("/api/factures/{$facture->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $facture->id);
    }
}
