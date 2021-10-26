<?php

namespace Database\Factories;

use App\Models\Rating;
use App\Models\User;
use App\Models\Movie;
use Illuminate\Database\Eloquent\Factories\Factory;

class RatingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Rating::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => rand(1, User::count()),
            'movie_id' => rand(1, Movie::count()),
            'rating' => random_int(1, 5),
            'comment' => $this->faker->paragraph(),
        ];
    }
}
