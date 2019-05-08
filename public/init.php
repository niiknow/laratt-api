<?php
/**
 * This file is use to initialize laravel app on shared hosting servers; i.e.
 * servers that do not allow shell_exec or exec.  This allow for using
 * rachidlaasri/laravel-installer package by visiting /install
 *
 * Therefore, all configurations are now done from the front-end.
 *
 */
$rootPath = __DIR__ . '/..';
$envFile  = $rootPath . '/.env';

function changeMods($path)
{
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));

    foreach ($iterator as $item) {
        @chmod($item, 0775);
    }
}

/**
 * @param $l
 */
function genuid($l = 32)
{
    return substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyz'), 0, $l);
}

if (!file_exists($envFile)) {
    copy($envFile . '.example', $envFile);

    @changeMods(realpath($rootPath . '/storage/framework/'));
    @changeMods(realpath($rootPath . '/storage/logs/'));
    @changeMods(realpath($rootPath . '/bootstrap/cache/'));

    require __DIR__ . '/../vendor/autoload.php';

    // generate APP_ENV and API_KEY
    $file = file_get_contents($envFile);
    $file = preg_replace('/APP_KEY=.*/', 'APP_KEY=base64:' . base64_encode(genuid()), $file, 1);
    $file = preg_replace('/API_KEY=.*/', 'API_KEY=' . genuid(), $file, 1);
    file_put_contents($envFile, $file);
}

header('Location: install');
exit();
