<?php

namespace Api\Models\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Config;

use Carbon\Carbon;

/**
 * Add ability to audit to the cloud - such as s3
 * Enable revision support on s3
 */
trait CloudAuditable
{
    protected $no_audit = false;

    public static function bootCloudAuditable()
    {
        static::created(function ($auditable) {
            $auditable->cloudAudit('create');
        });

        static::updated(function ($auditable) {
            $changes = $auditable->getDirty();
            $changed = [];
            foreach ($changes as $key => $value) {
                $record = [
                    'key' => $key,
                    'old' => $auditable->getOriginal($key),
                    'new' => $auditable->$key
                ];

                // do not log sensitive data
                if (array_search($key, $auditable->hidden)) {
                    $record['old'] = '***HIDDEN***';
                    $record['new'] = '***HIDDEN***';
                }

                $changed[] = $record;
            }

            $auditable->cloudAudit('update', $changed);
        });

        static::deleted(function ($auditable) {
            $auditable->cloudAudit('delete');
        });
    }

    public function cloudAudit($action, $log = [])
    {
        $bucket = config('admin.auditable.bucket');
        if (!isset($bucket) || strlen($bucket) <= 0) {
            return;
        }

        if ($this->no_audit) {
            return;
        }

        $id = $this->id;
        if (!isset($id)) {
            return;
        }

        $model        = $this->toArray();
        $request      = request();
        $user         = null;
        $now          = Carbon::now('UTC');
        $request_info = [];
        if (isset($request)) {
            $user         = $request->user();
            $route_params = $request->route()->parameters();
            $memory_usage = round(memory_get_peak_usage(true) / 1024 / 1024, 1);
            $request_info = [
                'user'          => $user,
                'id_address'    => $request->ip(),
                'user_agent'    => $request->userAgent(),
                'referer'       => $request->header('referer'),
                'locale'        => $request->header('accept-language'),
                'forwarded_for' => $request->header('x-forwarded-for'),
                'content_type'  => $request->header('content-type'),
                'content_length'=> $request->header('content-length'),
                'device_type'   => $request->device_type,
                'os'            => $request->os,
                'browser'       => $request->browser,
                'client'        => $request->client_information,
                'host'          => $request->getHttpHost(),
                'method'        => $request->method(),
                'path'          => $request->path(),
                'url'           => $request->url(),
                'full_url'      => $request->fullUrl(),
                'route_name'    => $route_name,
                'route_action'  => $request->route()->getActionName(),
                'route_query'   => $request->query(),
                'uname'         => php_uname(),
                'route_params'  => $route_params,
                'mem_use'       => $memory_usage
            ];
        }

        $info = $this->getCloudAuditInfo() ?: [];
        $tn   = $this->getTable();
        $body = array_merge($info, [
            // unique id allow for event idempotency/nonce key
            'uid'          => $this->uid,
            'app_name'     => config('app.name'),
            'class_name'   => get_class($this),
            'table_name'   => $tn,
            'action'       => $action,
            'user_id'      => isset($user) ? $user->id : null,
            'user_request' => $request_info,
            'log'          => $log,
            'model_id'     => $id,
            'model'        => $model,
            'created_at'   => $now->timestamp
        ]);
        $path = $this->getCloudAuditFile($body);

        // store to s3
        \Storage::disk('s3')
            ->getDriver()
            ->getAdapter()
            ->getClient()
            ->upload(
                $bucket,
                $path,
                gzencode(json_encode($body)),
                'private',
                ['params' => [
                        'ContentType' => 'application/json',
                        'ContentEncoding' => 'gzip',
                    ]
                ]
            );

        return $this;
    }

    /**
     * override this function to provide extra audit data
     * @return Array  Audit info
     */
    public function getCloudAuditInfo()
    {
        return [];
    }

    public function getCloudAuditFile($body)
    {
        $table = $body['table_name'];
        $uid   = $body['uid'];
        return "$uid/$table/index.json";
    }

    public function getNoAuditAttribute()
    {
        return $this->no_audit;
    }

    public function setNoAuditAttribute($value)
    {
        $this->no_audit = $value;
    }
}
