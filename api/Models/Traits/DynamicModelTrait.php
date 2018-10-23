<?php

namespace Api\Models\Traits;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Config;

use Illuminate\Support\Str;

use Carbon\Carbon;

/**
 * Add ability to audit to the cloud - such as s3
 * Enable revision support on s3
 */
trait DynamicModelTrait
{
    public static function bootDynamicModelTrait()
    {
        static::creating(function ($model) {
            $model->uid = (string) Str::uuid();
        });
    }

    public function tableCreate($table)
    {
        // \Log::info($table);
        $this->createTableIfNotExists(tenantId(), $table);
        return $this;
    }

    public function tableFill($id, $data, $table)
    {
        $item = $this->tableFind($id, $table);
        $item->fill($data);
        return $item;
    }

    public function tableFind($id, $table)
    {
        $this->createTableIfNotExists(tenantId(), $table);
        $tn   = $this->getTable();
        $item = $this->query()->from($tn)->where('id', $id)->first();
        $item->setTableName(tenantId(), $table);
        return $item;
    }

    public function setTableName($tenant, $tableName)
    {
        $newName     = tenantSlug($tenant) . '_' . tenantSlug($tableName);
        $this->table = $newName;
        return $newName;
    }

    public function createTableIfNotExists($tenant, $tableName)
    {
        $tableNew = $this->setTableName($tenant, $tableName);

        if (!Schema::hasTable($tableNew)) {
            Schema::create($tableNew, function (Blueprint $table) {
                $table->increments('id');

                // allow to uniquely identify this model
                $table->uuid('uid')->unique();

                // model has a unique name
                $table->string('name')->unique();
                // this should be hidden from user, viewable by admin
                $table->string('label')->nullable();
                $table->string('teaser')->nullable();
                $table->string('group')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('ended_at')->nullable();
                $table->unsignedInteger('priority')->default(100);

                $table->string('title')->nullable();
                $table->text('desc')->nullable();
                $table->string('img_url')->nullable();
                $table->string('keywords')->nullable();
                $table->mediumText('extra_data')->nullable();

                // targetting
                $table->string('tags')->nullable();
                $table->string('hostnames')->nullable();
                $table->string('week_schedules')->nullable();

                // tracking/impression
                $table->string('analytic_code')->nullable(); // for google ua
                $table->text('imp_pixel')->nullable();

                // conversion/click
                $table->string('clk_url')->nullable();
                $table->text('clk_pixel')->nullable();

                // extra decoration
                $table->text('styles')->nullable();
                $table->text('scripts')->nullable();

                // in cents
                $table->unsignedInteger('msrp')->default(0);
                $table->unsignedInteger('price')->default(0);
                $table->unsignedInteger('sale_price')->default(0);
                $table->unsignedInteger('sale_quantity')->default(1);
                $table->string('skus')->nullable();
                $table->string('gtins')->nullable();
                $table->string('brands')->nullable();
                $table->string('cat1')->nullable();
                $table->string('cat2')->nullable();
                $table->string('cat3')->nullable();
                $table->string('cat4')->nullable();
                $table->string('ship_weight')->nullable();
                $table->string('ship_width')->nullable();
                $table->string('ship_height')->nullable();
                $table->string('ship_length')->nullable();
                $table->string('tax_groups')->nullable();
                $table->text('map_coords')->nullable();

                $table->timestamps();
            });
        }

        return $tableNew;
    }
}
