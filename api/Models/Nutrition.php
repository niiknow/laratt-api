<?php

namespace Api\Models;

use Illuminate\Database\Eloquent\Model;

class Nutrition extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'calories',
        'carbohydrate_content',
        'cholesterol_content',
        'fat_content',
        'fiber_content',
        'protein_content',
        'saturated_fat_content',
        'serving_size',
        'sodium_content',
        'sugar_content',
        'trans_fat_content',

        'calories_from_fat',
        'potassium_content',
        'vitamin_a_content',
        'vitamin_c_content',
        'vitamin_d_content',
        'calcium_content',
        'iron_content',
        'added_sugar_content'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'calories'                => 'float',
        'carbohydrate_content'    => 'float',
        'cholesterol_content'     => 'float',
        'fat_content'             => 'float',
        'fiber_content'           => 'float',
        'protein_content'         => 'float',
        'saturated_fat_content'   => 'float',
        'sodium_content'          => 'float',
        'sugar_content'           => 'float',
        'trans_fat_content'       => 'float',
        'calories_from_fat'       => 'float',
        'potassium_content'       => 'float',
        'vitamin_a_content'       => 'float',
        'vitamin_c_content'       => 'float',
        'vitamin_d_content'       => 'float',
        'calcium_content'         => 'float',
        'iron_content'            => 'float',
        'added_sugar_content'     => 'float',
    ];

    public function toArray()
    {
        $array = parent::toArray();

        foreach ($array as $key => $attribute) {
            if (isset($attribute)) {
                $array[$key] = number_format($attribute, 2);
            }
        }

        return $array;
    }
}
