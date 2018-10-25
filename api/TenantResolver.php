<?php

namespace Api;

class TenantResolver
{
    public static function slug($tenant)
    {
        return preg_replace('/[^a-z0-9]+/i', '', mb_strtolower($tenant));
    }

    /**
     * Method for resolving tenant
     * @return
     */
    public static function resolve()
    {
        // @codeCoverageIgnoreStart
        $resolver = config('laratt.resolver', '');

        if (!empty($resolver) && is_callable($resolver)) {
            $tenant = call_user_func($resolver);
        } else {
            $tenant = request()->header('x-tenant');
        }

        if (!isset($tenant)) {
            $tenant = "";
        }
        // @codeCoverageIgnoreEnd

        return self::slug($tenant);
    }
}
