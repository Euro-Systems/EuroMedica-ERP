<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * El password actual utilizado por la factory.
     */
    protected static ?string $password;

    /**
     * Define el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Genera un nombre completo aleatorio
            'name' => fake()->name(),
            
            // Genera un correo electrónico único y seguro para pruebas
            'email' => fake()->unique()->safeEmail(),
            
            // Marca la fecha y hora actuales como verificación del correo electrónico
            'email_verified_at' => now(),
            
            // Reutiliza la contraseña encriptada en memoria o la genera si aún no existe
            'password' => static::$password ??= Hash::make('password'),
            
            // Genera una cadena aleatoria de 10 caracteres para la funcionalidad "recordarme"
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indica que la dirección de correo electrónico del modelo no debe estar verificada.
     */
    public function unverified(): static
    {
        // Modifica el estado del atributo 'email_verified_at' para dejarlo como nulo
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}