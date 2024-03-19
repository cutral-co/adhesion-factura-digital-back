<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SolicitudFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        $cuit = $this->faker->randomElement(['20', '21', '22']) // Prefijos válidos
            . str_pad($this->faker->numberBetween(0, 99999999), 8, '0', STR_PAD_LEFT) // Parte central aleatoria
            . $this->faker->randomElement(['0', '1', '9']); // Dígito de verificación válido

        return [
            'lastname' => $this->faker->lastName,
            'name' => $this->faker->firstName,
            'cuit' => $cuit,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'barrio_id' => $this->faker->numberBetween(1, 3),
            'calle' => $this->faker->streetName,
            'altura' => $this->faker->buildingNumber,
            'manzana' => $this->faker->randomLetter,
            'lote' => $this->faker->randomNumber(),
            'piso' => $this->faker->randomNumber(),
            'depto' => $this->faker->randomNumber(),
            'token_verificacion' => $this->faker->md5,
            'ultimo_envio_email' => $this->faker->dateTime,
            'verificado' => $this->faker->boolean,
            'estado_id' => 1,
        ];
    }
}
