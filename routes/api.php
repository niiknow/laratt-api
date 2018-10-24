<?php

Route::group(['prefix' => 'v1', 'middleware' => 'api'], function () {
    // specifically map user and dynamic api routes
    Route::match(
        ['get','delete'],
        'profiles/list',
        'ProfileController@list'
    )->name('api.profiles.list');

    Route::match(
        ['get'],
        'profiles/data',
        'ProfileController@data'
    )->name('api.profiles.data');

    Route::match(
        ['post', 'put', 'patch'],
        'profiles/create',
        'ProfileController@create'
    )->name('api.profiles.create');

    Route::match(
        ['get'],
        'profiles/{id}/retrieve',
        'ProfileController@retrieve'
    )->name('api.profiles.retrieve');

    Route::match(
        ['post', 'put', 'patch'],
        'profiles/{id}/upsert',
        'ProfileController@upsert'
    )->name('api.profiles.upsert');

    Route::match(
        ['post', 'delete'],
        'profiles/{id}/delete',
        'ProfileController@delete'
    )->name('api.profiles.delete');

    // table stuff
    Route::match(
        ['get','delete'],
        'tables/list',
        'TableController@list'
    )->name('api.table.list');

    Route::match(
        ['get'],
        'tables/data',
        'TableController@data'
    )->name('api.table.data');

    Route::match(
        ['post', 'put', 'patch'],
        'tables/{table}/create',
        'TableController@create'
    )->name('api.table.create');

    Route::match(
        ['get'],
        'tables/{table}/{id}/retrieve',
        'TableController@retrieve'
    )->name('api.table.retrieve');

    Route::match(
        ['post', 'put', 'patch'],
        'tables/{table}/{id}/upsert',
        'TableController@upsert'
    )->name('api.table.upsert');

    Route::match(
        ['post', 'delete'],
        'tables/{table}/{id}/delete',
        'TableController@delete'
    )->name('api.table.delete');
});
