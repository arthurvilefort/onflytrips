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

    /** @test */
    public function only_admins_can_approve_or_cancel_trips()
    {
        $admin = User::factory()->create(['is_admin' => 1]);
        $adminToken = auth()->login($admin);
    
        $user = User::factory()->create();
        $trip = Trip::factory()->create([
            'user_id' => $user->id,
            'status' => 'solicitado',
        ]);
    
        $response = $this->putJson("/api/trips/{$trip->id}/status", [
            'status' => 'aprovado'
        ], ['Authorization' => "Bearer $adminToken"]);
    
        $response->assertStatus(200);
    
        $this->assertDatabaseHas('trips', [
            'id' => $trip->id,
            'status' => 'aprovado',
        ]);
    }

    /** @test */
    public function only_admins_can_see_all_trips()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $otherUser = User::factory()->create();

        $trip1 = Trip::factory()->create(['user_id' => $user->id]);
        $trip2 = Trip::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->getJson('/api/trips', ['Authorization' => "Bearer $token"]);

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['id' => $trip1->id])
            ->assertJsonMissing(['id' => $trip2->id]); 

        $admin = User::factory()->create(['is_admin' => 1]);
        $adminToken = auth()->login($admin);

        $response = $this->getJson('/api/trips', ['Authorization' => "Bearer $adminToken"]);

        $response->assertStatus(200)
            ->assertJsonCount(2) 
            ->assertJsonFragment(['id' => $trip1->id])
            ->assertJsonFragment(['id' => $trip2->id]);
    }
}
