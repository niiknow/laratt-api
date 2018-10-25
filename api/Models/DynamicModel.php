<?php

namespace Api\Models;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Model;

use Api\Models\Traits\CloudAuditable;
use Api\Models\Traits\DynamicModelTrait;

use Carbon\Carbon;

use Api\TenantResolver;

class DynamicModel extends Model
{
    use CloudAuditable,
        DynamicModelTrait;

    /**
     * @var array
     */
    protected $fillable = [
        'uid', 'name', 'label', 'teaser', 'group', 'started_at', 'ended_at', 'priority',
        'title', 'summary', 'image_url', 'keywords', 'tags', 'hostnames', 'geos',
        'week_schedules', 'analytic_code', 'imp_pixel', 'msrp', 'price',
        'sale_price', 'sale_qty', 'skus', 'gtins', 'brands', 'cat1',
        'cat2', 'cat3', 'cat4', 'map_coords', 'clk_url', 'content',
        'data', 'meta', 'var', 'job_id'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'priority'   => 'integer',
        'msrp'       => 'integer',
        'price'      => 'integer',
        'sale_price' => 'integer',
        'meta' => 'array',
        'data' => 'array',
        'var' => 'array',
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

    protected $hidden = ['no_audit'];

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

        // only need to improve performance in prod
        if (config('env') === 'production' && \Cache::has($tableNew)) {
            return $tableNew;
        }

        if (!Schema::hasTable($tableNew)) {
            Schema::create($tableNew, function (Blueprint $table) {
                $table->bigIncrements('id');

                // allow to uniquely identify this model
                $table->string('uid')->unique();

                // example, name: home slider
                $table->string('name')->nullable();
                // label should be hidden from user, viewable by admin
                // example: location x, y, and z home slider
                $table->string('label')->nullable();
                $table->string('teaser')->nullable(); // ex: sale sale sale
                $table->string('group')->nullable()->index(); // ex: sales, daily
                $table->timestamp('started_at')->nullable()->index();
                $table->timestamp('ended_at')->nullable()->index();
                $table->unsignedSmallInteger('priority')->default(100);

                $table->string('title')->nullable(); // ex: box of chocolate
                $table->string('summary')->nullable(); // ex: summay of box
                $table->string('image_url')->nullable(); // ex: picture of box
                $table->string('keywords')->nullable(); // ex: valentine, birthday, ...

                // targeting data, for advertising
                $table->string('geos')->nullable();
                $table->string('tags')->nullable();
                $table->string('hostnames')->nullable(); // ex: example.com,go.com
                $table->string('week_schedules')->nullable(); // csv of 101 to 724

                // tracking/impression
                $table->string('analytic_code')->nullable(); // for google ua
                $table->string('imp_pixel')->nullable(); // track display

                // ecommerce stuff, value should be in cents - no decimal
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
                $table->string('map_coords')->nullable(); // hot map coordinates

                $table->timestamps();

                // conversion/click tracking url
                $table->string('clk_url', 500)->nullable();

                $table->mediumText('content')->nullable(); // detail description of things
                // things that are hidden from the user
                // like tax_group, ship_weight/length/height
                $table->mediumText('meta')->nullable();
                // things that are shown like extra images
                $table->mediumText('data')->nullable();
                // variant color, size, price, etc...
                $table->mediumText('var')->nullable();
                $table->uuid('job_id')->nullable()->index();
            });

            // cache database check for 12 hours or half a day
            \Cache::add($tableNew, 'true', 60*12);
        }

        return $tableNew;
    }
}
