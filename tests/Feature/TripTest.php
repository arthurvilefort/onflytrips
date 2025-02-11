<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Trip;

class TripTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_a_trip()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $response = $this->postJson('/api/trips', [
            'destination' => 'Paris',
            'departure_date' => '2025-05-10',
            'return_date' => '2025-05-20',
            'status' => 'solicitado'
        ], [
            'Authorization' => "Bearer $token",
        ]);

        $response->assertStatus(201)
                 ->assertJson(['destination' => 'Paris']);
    }

    /** @test */
    public function it_lists_trips_for_authenticated_user()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        Trip::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson('/api/trips', [
            'Authorization' => "Bearer $token",
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     '*' => ['id', 'destination', 'departure_date']
                 ]);
    }

    /** @test */
    public function it_filters_trips_by_status()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        Trip::factory()->create(['user_id' => $user->id, 'status' => 'aprovado']);
        Trip::factory()->create(['user_id' => $user->id, 'status' => 'cancelado']);

        $response = $this->getJson('/api/trips?status=aprovado', [
            'Authorization' => "Bearer $token",
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['status' => 'aprovado']);
    }

    /** @test */
    public function it_cancels_a_trip()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $trip = Trip::factory()->create(['user_id' => $user->id, 'status' => 'solicitado']);

        $response = $this->deleteJson("/api/trips/{$trip->id}", [], [
            'Authorization' => "Bearer $token",
        ]);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Viagem cancelada com sucesso']);
    }
}
