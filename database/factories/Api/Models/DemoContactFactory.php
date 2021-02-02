<?php
namespace Database\Factories\Api\Models;

use Api\Models\DemoContact;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DemoContactFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DemoContact::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'email'      => $this->faker->unique()->safeEmail,
            'first_name' => $this->faker->firstName,
            'last_name'  => $this->faker->lastName,
            'photo_url'  => $this->faker->imageURL(),
            'address1'   => $this->faker->streetAddress,
            'address2'   => $this->faker->streetName,
            'city'       => $this->faker->city,
            'state'      => $this->faker->state,
            'postal'     => $this->faker->postcode,
            'country'    => $this->faker->country,
            'phone'      => $this->faker->phoneNumber,
            'occupation' => $this->faker->jobTitle,
            'employer'   => $this->faker->company,
            'note'       => $this->faker->sentence,
            'lat'        => $this->faker->latitude,
            'lng'        => $this->faker->longitude
        ];
    }
}
