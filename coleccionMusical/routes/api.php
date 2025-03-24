<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


// '/items' => 'ItemController@index',
// '/items/get' => 'ItemController@getAll',
// '/item/get/{id}' => 'ItemController@getById',
// '/items/artist/{artist}' => 'ItemController@getByArtist',
// '/items/format/{format}' => 'ItemController@getByFormat',
// '/items/order/{key}/{order}' => 'ItemController@sortByKey',
// '/item/create' => 'ItemController@create',
// '/item/update' => 'ItemController@update',
// '/item/delete' => 'ItemController@delete'


/*
    Se ha utilizado el método controller de Route para agrupar todas las
    peticiones, evitando así repetir el namespace del controlador ItemController
    para todas las rutas.
    Además, se ha añadido el prefijo 'item', que nos ahorra repetir también esa
    palabra en las rutas, tal y como están definidas en el enunciado.
*/

Route::controller('App\Http\Controllers\ItemController')->prefix('item')->group(function() {
    Route::get('/', 'getAll')->name('itemGetAll');
    Route::get('/{id}', 'getById')->name('itemGetById');
    Route::get('/artist/{artist}', 'getByArtist')->name('itemGetByArtist');
    Route::get('/format/{format}', 'getByFormat')->name('itemGetByFormat');
    Route::get('/order/{key}/{order}', 'sortByKey')->name('itemSortByKey');
    Route::post('/create', 'create')->name('itemCreate');
    Route::put('/update', 'update')->name('itemUpdate');
    Route::get('/delete', 'delete')->name('itemDelete');
});