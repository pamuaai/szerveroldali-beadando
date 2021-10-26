<?php

namespace Database\Factories;

use App\Models\Movie;
use Illuminate\Database\Eloquent\Factories\Factory;

class MovieFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Movie::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->name() . '\'s Newest Movie',
            'director' => $this->faker->name(),
            'description' => $this->faker->paragraph(),
            'year' => $this->faker->year(),
            'length' => random_int(10000, 20000),
            'image' => $this->faker->imageUrl(640, 480),
            'ratings_enabled' => $this->faker->boolean(),
        ];
    }
}
