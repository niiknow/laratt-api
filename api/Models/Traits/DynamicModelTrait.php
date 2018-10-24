<?php

namespace Api\Models\Traits;

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
            if (!isset($model->uid)) {
                // automatically add uid if not provided
                $model->uid = (string) Str::uuid();
            }
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

    public function setUid($value)
    {
        // we have to do this because we use uid for audit
        // a slug is already an extremely flexible id
        $this->attributes['uid'] = \Str::slug($value);
    }
}
