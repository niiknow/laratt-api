<?php
use Niiknow\Laratt\LarattServiceProvider as r;

Route::group(['prefix' => 'v1', 'middleware' => 'api'], function () {
    r::routeModel('Profile');
    r::routeTables('TableController');
});
