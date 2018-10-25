<?php
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
            // @codeCoverageIgnoreStart
            $tenant = "";
            // @codeCoverageIgnoreEnd
        }

        return tenantSlug($tenant);
    }
}
