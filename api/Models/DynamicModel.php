<?php

namespace Api\Models;

use Illuminate\Database\Eloquent\Model;

use Api\Models\Traits\CloudAuditable;
use Api\Models\Traits\DynamicModelTrait;

class DynamicModel extends Model
{
    use CloudAuditable,
        DynamicModelTrait;

    /**
     * @var array
     */
    protected $fillable = [
        'cid', 'name', 'label', 'teaser', 'group', 'started_at', 'ended_at', 'priority',
        'title', 'desc', 'img_url', 'keywords', 'extra_data', 'tags', 'hostnames',
        'week_schedules', 'analytic_code', 'imp_pixel', 'clk_url', 'clk_pixel',
        'styles', 'scripts', 'msrp', 'price', 'sale_price', 'sale_qty', 'skus',
        'gtins', 'brands', 'cat1', 'cat2', 'cat3', 'cat4', 'ship_weight',
        'ship_width', 'ship_height', 'ship_length', 'tax_groups',
        'map_coords'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'priority'   => 'integer',
        'msrp'       => 'integer',
        'price'      => 'integer',
        'sale_price' => 'integer',
        'extra_data' => 'object'
    ];

    /**
     * The attributes that should be casted by Carbon
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'started_at',
        'ended_at'
    ];
}
