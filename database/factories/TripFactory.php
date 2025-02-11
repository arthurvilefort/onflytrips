<?php

namespace Database\Factories;

use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TripFactory extends Factory
{
    protected $model = Trip::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(), // Cria um usuário para a viagem
            'requester_name' => $this->faker->name(), // Adiciona um nome aleatório
            'destination' => $this->faker->city(),
            'departure_date' => $this->faker->date(),
            'return_date' => $this->faker->date(),
            'status' => 'solicitado',
        ];
    }
}
