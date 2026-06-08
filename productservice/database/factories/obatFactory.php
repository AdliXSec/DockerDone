<?php

namespace Database\Factories;

use App\Models\obat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<obat>
 */
class obatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = ['Analgesik', 'Antibiotik', 'Vitamin', 'Antiseptik', 'Suplemen', 'Obat Batuk'];
        $forms = ['Tablet', 'Kapsul', 'Sirup', 'Salep', 'Drops'];

        return [
            'name' => ucfirst($this->faker->word()) . ' ' . $this->faker->randomElement($forms),
            'category' => $this->faker->randomElement($categories),
            'price' => $this->faker->numberBetween(5, 150) * 1000,
            'stock' => $this->faker->numberBetween(10, 200),
            'description' => $this->faker->sentence(10),
        ];
    }
}
