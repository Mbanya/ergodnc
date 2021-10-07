<?php

namespace Database\Factories;

use App\Models\Image;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

class ImageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Image::class;

    /**
     * Define the model's default state.
     *
     * @return array
     * @throws Exception
     */
    public function definition()
    {
        return [
            //
            'path'=>'image.png',
//            'resource_type'=>'image',
//            'resource_id' => random_int(1,100)
        ];
    }
}
