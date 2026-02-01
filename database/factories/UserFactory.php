<?php

namespace Database\Factories;

use App\Concerns\Tokens;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => null,//'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'auth_token' => app(Tokens::class)->generate(),
            'auth_token_generated_at' => now(),
            'remember_token' => Str::random(10),
            'onboarding_completed_at' => now(),
        ];
    }

    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }

    public function expiredAuthToken()
    {
        return $this->state(function (array $attributes) {
            return [
                'auth_token_generated_at' => now()->subMinutes(2 * config('settings.unverified_user_expires_minutes')),
            ];
        });
    }

    public function needsOnboarding()
    {
        return $this->state(function (array $attributes) {
            return [
                'onboarding_completed_at' => null,
            ];
        });
    }
}
