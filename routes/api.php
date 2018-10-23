<?php

Route::group(['prefix' => 'v1'], function () {
    // specifically map user and dynamic api routes
    Route::match(
        ['get'],
        'users',
        'UserController@list'
    )->name('api.users.list');

    Route::match(
        ['post', 'put', 'patch'],
        'users/create',
        'UserController@create'
    )->name('api.users.create');

    Route::match(
        ['get'],
        'users/{id}/retrieve',
        'UserController@retrieve'
    )->name('api.users.retrieve');

    Route::match(
        ['post', 'put', 'patch'],
        'users/{id}/update',
        'UserController@update'
    )->name('api.users.update');

    Route::match(
        ['post', 'delete'],
        'users/{id}/delete',
        'UserController@delete'
    )->name('api.users.delete');

    // table stuff
    Route::match(
        ['get'],
        'tables',
        'TableController@list'
    )->name('api.table.list');

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
        'tables/{table}/{id}/update',
        'TableController@update'
    )->name('api.table.update');

    Route::match(
        ['post', 'delete'],
        'tables/{table}/{id}/delete',
        'TableController@delete'
    )->name('api.table.delete');
});
