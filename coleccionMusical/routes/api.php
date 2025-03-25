<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


/*
    Se han utilizado los métodos "controller" y "group" de Route para agrupar todas las
    peticiones, evitando así repetir el namespace del controlador ItemController
    en todas las rutas.

    https://laravel.com/docs/12.x/routing#route-group-controllers
    
    Además, se ha aprovechado prefix para añadir 'item', que nos ahorra repetir
    también esa palabra en las rutas, quedando así tal y como están definidas
    en el enunciado.
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