<?php
if (!ini_get('auto_detect_line_endings')) {
    ini_set('auto_detect_line_endings', '1');
}

if (!function_exists('remix')) {
    /**
     * @param $url
     */
    function remix($url)
    {
        $vendorjs = mix('js/vendor.js');
        $pfx      = explode('?', $vendorjs);

        return count($pfx) > 1 ? '/' . $url . '?' . $pfx[1] : $url;
    }
}

if (!function_exists('mapResourceRoute')) {
    /**
     * @param $modelName
     * @param $prefix
     * @param $idField
     */
    function mapResourceRoute($modelName, $prefix = 'api', $idField = 'id')
    {
        $model = mb_strtolower($modelName);

        Route::match(
            ['get', 'delete'],
            $model . '/query',
            $modelName . 'Controller@query'
        )->name("$prefix.$model" . 's.query');

        Route::match(
            ['get'],
            $model . '/data',
            $modelName . 'Controller@data'
        )->name("$prefix.$model" . 's.data');

        Route::match(
            ['post'],
            $model . '/create',
            $modelName . 'Controller@create'
        )->name("$prefix.$model" . 's.create');

        Route::match(
            ['get'],
            $model . '/{' . $idField . '}/retrieve',
            $modelName . 'Controller@retrieve'
        )->name('laratt.' . $model . 's.retrieve');

        Route::match(
            ['post'],
            $model . '/{' . $idField . '}/restore',
            $modelName . 'Controller@restore'
        )->name('laratt.' . $model . 's.restore');

        Route::match(
            ['post'],
            $model . '/{' . $idField . '}/update',
            $modelName . 'Controller@update'
        )->name("$prefix.$model" . 's.update');

        Route::match(
            ['post', 'delete'],
            $model . '/{' . $idField . '}/delete',
            $modelName . 'Controller@delete'
        )->name("$prefix.$model" . 's.delete');

        Route::match(
            ['post'],
            $model . '/import',
            $modelName . 'Controller@import'
        )->name("$prefix.$model" . 's.import');
    }
}

if (!function_exists('myParseDate')) {
    /**
     * @param  $value
     * @return mixed
     */
    function myParseDate($value)
    {
        if (is_string($value)) {
            $value = trim($value);
            if (empty($value)) {
                $value = null;
            }

            try {
                $value = \Carbon\Carbon::parse($value);
            } catch (\Exception $err) {
                $value = null;
            }
        }

        return $value;
    }
}
