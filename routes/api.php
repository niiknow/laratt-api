<?php
use Niiknow\Laratt\LarattServiceProvider as r;

Route::group(['prefix' => 'v1', 'middleware' => 'api'], function () {
    r::routeModel('Profile', 'api');
    r::routeTables('TableController', 'api');

    mapResourceRoute('DemoContact');
    Route::match(
        ['get', 'post'],
        'democontact/example',
        'DemoContactController@dumbingDownExample'
    )->name('api.democontacts.query');
});
