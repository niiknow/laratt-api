<?php

namespace Api\Extra;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;

class RecipeTableExport implements FromCollection, WithHeadings, WithMapping
{
    protected $headings;

    protected $query;

    protected $columns;

    protected $mappings;

    public function __construct($query)
    {
        $this->columns = array(
            'export_id',
            'export_to',
            'export_rating_value',
            'export_rating_at',
            'export_category1',
            'export_category2',
            'export_category3',
            'export_category4',
            'id',
            'name',
            'image_url',
            'description',
            'recipe_ingredient',
            'recipe_instructions',
            'keywords',
            'recipe_cuisine',
            'recipe_category',
            'recipe_diet',
            'recipe_yield',
            'rating_value',
            'rating_count',
            'review_count',
            'rating_at',
            'prep_time',
            'cook_time',
            'rest_time',
            'total_time',
            'author',
            'date_published',
            'skill_level',
            'recipe_tip',
            'recipe_note',
            'cook_method',
            'serving_size',
            'legacy_id',
            'src_recipe_id',
            'src_image_url',
            'src_url',
            'created_at',
            'updated_at'
        );

        $headings = $this->columns;

        // add more columns for nutritions
        $nutritionColumns = (new \Api\Models\Nutrition())->getFillable();

        foreach ($nutritionColumns as $nut) {
            $headings[] = 'nutrition.' . $nut;
        }

        $this->columns[] = 'nutrition';


        $this->query    = $query;
        $this->headings = $headings;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function map($item): array
    {
        $rst   = [];
        $attrs = array_dot($item->toArray());
        foreach ($this->headings as $key) {
            if (isset($attrs[$key])) {
                $rst[] = $attrs[$key];
            } else {
                $rst[] = '';
            }
        }
        return $rst;
    }

    public function collection()
    {
        return $this->query->get($this->columns);
    }
}
