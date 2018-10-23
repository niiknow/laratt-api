<?php

function mapResourceRoute($controller)
{
    $resource   = strtolower(
        str_replace('Controller', '', $controller)
    );
    $model      = rtrim($resource, 's');
    $modelNames = array_reverse(explode('\\', $model));
    $modelName  = $modelNames[0];
    $prefix     = 'api.' . $modelName;

    Route::match(
        ['post','get','head'],
        $resource . '/{model}/read',
        $controller . '@show'
    )->name($prefix . '.read');

    Route::match(
        ['post', 'put', 'patch'],
        $resource . '/{model}/update',
        $controller . '@update'
    )->name($prefix . '.update');
    Route::match(
        ['post','delete'],
        $resource . '/{model}/delete',
        $controller . '@destroy'
    )->name($prefix . '.delete');
    Route::match(
        ['post','get','head'],
        $resource . '/list',
        $controller . '@index'
    )->name($prefix . '.list');
    Route::match(
        ['post'],
        $resource . '/create', $controller . '@store'
    )->name($prefix . '.create');
    Route::match(
        ['post'],
        $resource . '/bulk', $controller . '@bulk'
    )->name($prefix . '.bulk');
}

Route::group(['prefix' => 'v1', 'middleware' => 'api'], function () {
    // specifically map user and dynamic api routes
    Route::match(
        ['get'],
        'user/{id}',
        'UserController@list'
    )->name('api.users.list');

    Route::match(
        ['post', 'put', 'patch'],
        'user/{id}/create',
        'UserController@create'
    )->name('api.users.create');

    Route::match(
        ['get'],
        'user/{id}/retrieve',
        'UserController@retrieve'
    )->name('api.users.retrieve');

    Route::match(
        ['post', 'put', 'patch'],
        'user/{id}/update',
        'UserController@update'
    )->name('api.users.update');

    Route::match(
        ['post', 'delete'],
        'user/{id}/delete',
        'UserController@delete'
    )->name('api.users.delete');

    // table stuff
    Route::match(
        ['get'],
        'table/{table}/{id}',
        'ServiceController@list'
    )->name('api.table.list');

    Route::match(
        ['post', 'put', 'patch'],
        'table/{table}/{id}/create',
        'ServiceController@create'
    )->name('api.table.create');

    Route::match(
        ['get'],
        'table/{table}/{id}/retrieve',
        'ServiceController@retrieve'
    )->name('api.table.retrieve');

    Route::match(
        ['post', 'put', 'patch'],
        'table/{table}/{id}/update',
        'ServiceController@update'
    )->name('api.table.update');

    Route::match(
        ['post', 'delete'],
        'table/{table}/{id}/delete',
        'ServiceController@delete'
    )->name('api.table.delete');
});
