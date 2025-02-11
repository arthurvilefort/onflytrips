<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_registers_a_user()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Arthur Vilefort',
            'email' => 'arthur@example.com',
            'password' => '123456',
            'password_confirmation' => '123456',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['token']);
    }

    /** @test */
    public function it_logs_in_a_user()
    {
        $user = User::factory()->create([
            'email' => 'arthur@example.com',
            'password' => bcrypt('123456'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'arthur@example.com',
            'password' => '123456',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['token']);
    }

    /** @test */
    public function it_fetches_authenticated_user_profile()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $response = $this->getJson('/api/profile', [
            'Authorization' => "Bearer $token",
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'email' => $user->email
                 ]);
    }

    /** @test */
    public function it_logs_out_a_user()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $response = $this->postJson('/api/logout', [], [
            'Authorization' => "Bearer $token",
        ]);

        $response->assertStatus(200);
    }
}
