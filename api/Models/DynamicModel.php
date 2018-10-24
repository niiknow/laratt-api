<?php

namespace Api\Models;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Model;

use Api\Models\Traits\CloudAuditable;
use Api\Models\Traits\DynamicModelTrait;

use Carbon\Carbon;

class DynamicModel extends Model
{
    use CloudAuditable,
        DynamicModelTrait;

    /**
     * @var array
     */
    protected $fillable = [
        'uid', 'name', 'label', 'teaser', 'group', 'started_at', 'ended_at', 'priority',
        'title', 'summary', 'img_url', 'keywords', 'tags', 'hostnames',
        'week_schedules', 'analytic_code', 'imp_pixel', 'msrp', 'price',
        'sale_price', 'sale_qty', 'skus', 'gtins', 'brands', 'cat1',
        'cat2', 'cat3', 'cat4', 'map_coords', 'clk_url', 'content',
        'extra_data', 'extra_meta'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'priority'   => 'integer',
        'msrp'       => 'integer',
        'price'      => 'integer',
        'sale_price' => 'integer',
        'extra_meta' => 'array',
        'extra_data' => 'array',
    ];

    /**
     * The attributes that should be casted by Carbon
     *
     * @var array
     */
    protected $dates = [
        'started_at' => 'datetime:Y-m-d',
        'ended_at' => 'datetime',
        'created_at',
        'updated_at',
    ];

    public function setStartedAtAttribute($value)
    {
        $this->attributes['started_at'] = Carbon::parse($value)->startOfDay();
    }

    public function setEndedAtAttribute($value)
    {
        $this->attributes['ended_at'] = Carbon::parse($value)->endOfDay();
    }

    public function createTableIfNotExists($tenant, $tableName)
    {
        $tableNew = $this->setTableName($tenant, $tableName);

        if (!Schema::hasTable($tableNew)) {
            Schema::create($tableNew, function (Blueprint $table) {
                $table->bigIncrements('id');

                // allow to uniquely identify this model
                $table->string('uid')->unique();

                $table->string('name')->nullable();
                // label should be hidden from user, viewable by admin
                $table->string('label')->nullable();
                $table->string('teaser')->nullable();
                $table->string('group')->nullable();
                $table->timestamp('started_at')->nullable()->index();
                $table->timestamp('ended_at')->nullable()->index();
                $table->unsignedSmallInteger('priority')->default(100);

                $table->string('title')->nullable();
                $table->string('summary')->nullable();
                $table->string('img_url')->nullable();
                $table->string('keywords')->nullable();

                // targetting
                $table->string('tags')->nullable();
                $table->string('hostnames')->nullable();
                $table->string('week_schedules')->nullable();

                // tracking/impression
                $table->string('analytic_code')->nullable(); // for google ua
                $table->string('imp_pixel')->nullable();

                // in cents
                $table->unsignedInteger('msrp')->default(0);
                $table->unsignedInteger('price')->default(0);
                $table->unsignedInteger('sale_price')->default(0);
                $table->unsignedSmallInteger('sale_qty')->default(1);
                $table->string('skus')->nullable();
                $table->string('gtins')->nullable();
                $table->string('brands')->nullable();
                $table->string('cat1')->nullable();
                $table->string('cat2')->nullable();
                $table->string('cat3')->nullable();
                $table->string('cat4')->nullable();
                $table->string('map_coords')->nullable();

                $table->timestamps();

                // conversion/click
                $table->string('clk_url', 500)->nullable();

                $table->text('content')->nullable();
                $table->mediumText('extra_meta')->nullable();
                $table->mediumText('extra_data')->nullable();
            });
        }

        return $tableNew;
    }
}
