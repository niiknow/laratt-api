<?php
if (!function_exists('genUid')) {
    function genUid($l = 10)
    {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyz"), 0, $l);
    }
}

if (!function_exists('tenantSlug')) {
    function tenantSlug($tenant)
    {
        return preg_replace('/[^a-z0-9]+/i', '', mb_strtolower($tenant));
    }
}

if (!function_exists('tenantId')) {
    function tenantId()
    {
        $tenant = request()->header('x-tenant');
        if (!isset($tenant)) {
            $tenant = "";
        }

        return tenantSlug($tenant);
    }
}
