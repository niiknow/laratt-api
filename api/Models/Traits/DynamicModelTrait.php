<?php

namespace Api\Models\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Config;

use Illuminate\Support\Str;

use Carbon\Carbon;

use Api\TenantResolver;

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

    public function tableCreate($table, $tenant = null)
    {
        // \Log::info($table);
        $this->createTableIfNotExists($tenant, $table);
        return $this;
    }

    public function tableFill($uid, $data, $table, $tenant = null)
    {
        $item = $this->tableFind($uid, $table);
        if (isset($item)) {
            $item->fill($data);
        }
        return $item;
    }

    public function tableFind($uid, $table, $tenant = null)
    {
        $this->createTableIfNotExists($tenant, $table);
        $tn   = $this->getTable();
        $item = $this->query()->from($tn)->where('uid', $uid)->first();
        if (isset($item)) {
            $item->setTableName($tenant, $table);
        }
        return $item;
    }

    public function setTableName($tenant, $tableName)
    {
        if ($tenant == null) {
            $tenant = TenantResolver::resolve();
        }

        $newName     = TenantResolver::slug($tenant) . '_' . TenantResolver::slug($tableName);
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
