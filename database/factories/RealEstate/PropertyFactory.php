<?php

namespace Database\Factories\RealEstate;

use App\Models\City;
use App\Models\RealEstate\Category;
use App\Models\RealEstate\Equipment;
use App\Models\RealEstate\Layout;
use App\Models\RealEstate\Property;
use App\Models\RealEstate\PropertyType;
use App\Models\RealEstate\Regulation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class PropertyFactory extends Factory
{
    protected $model = Property::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'property_type_id' => PropertyType::inRandomOrder()->first()->id,
            'category_id' => Category::inRandomOrder()->first()->id,
            'city_id' => City::inRandomOrder()->first()->id,
            'title' => fake()->sentence(4),
            'description' => fake()->paragraphs(6, true),
            'price' => fake()->numberBetween(30, 300),
            'room' => fake()->numberBetween(1, 6),
            'bathroom' => fake()->numberBetween(1, 3),
            'dining_room' => fake()->numberBetween(0, 2),
            'kitchen' => fake()->numberBetween(1, 2),
            'living_room' => fake()->numberBetween(1, 2),
            'balcony' => fake()->boolean(),
            'outdoor_space' => fake()->boolean(),
            'address' => fake()->address(),
            'regulation' => fake()->paragraphs(3, true),
            'airbnb' => 'https://www.airbnb.com/rooms/'.fake()->numberBetween(10000000, 99999999),
            'is_published' => fake()->boolean(80),
        ];
    }

    public function configure(): PropertyFactory|Factory
    {
        return $this->afterCreating(function (Property $property) {
            // Attacher des équipements aléatoires (entre 2 et 5)
            $property->equipments()->attach(
                Equipment::inRandomOrder()->limit(fake()->numberBetween(2, 5))->pluck('id')
            );

            // Attacher des régulations aléatoires (entre 1 et 3)
            $property->regulations()->attach(
                Regulation::inRandomOrder()->limit(fake()->numberBetween(1, 3))->pluck('id')
            );

            // Attacher des layouts aléatoires (entre 1 et 4)
            $property->layouts()->attach(
                Layout::inRandomOrder()->limit(fake()->numberBetween(1, 4))->pluck('id')
            );

            // Ajouter des images aléatoires
            $numberOfImages = fake()->numberBetween(3, 6);
            for ($i = 0; $i < $numberOfImages; $i++) {
                $property
                    ->addMediaFromUrl('https://picsum.photos/1280/720')
                    ->toMediaCollection('images');
            }
        });
    }

    /**
     * Indicate that the property is published.
     */
    public function published(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'is_published' => true,
            ];
        });
    }

    /**
     * Indicate that the property is draft.
     */
    public function draft(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'is_published' => false,
            ];
        });
    }
}
