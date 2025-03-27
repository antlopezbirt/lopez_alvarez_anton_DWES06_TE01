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

    Además, se ha aprovechado prefix para añadir 'item', que nos ahorra repetir
    también esa palabra en las rutas, quedando así tal y como están definidas
    en el enunciado.

    Las rutas GET hacen una validación suave de los parámetros de la petición.
    Posteriormente, el controlador hará una validación más a fondo si es necesario.

    En el caso de las rutas POST, PUT y DELETE es el controlador quien se encarga
    a solas de validar los parámetros del body de la petición.

    En caso de que no se valide la ruta por este u otro motivo, se llega a la
    ruta de fallback, que devuelve un 404.
*/

Route::controller('App\Http\Controllers\ItemController')->prefix('item')->group(function() {

    // GET
    Route::get('/', 'getAll')->name('itemGetAll');
    Route::get('/{id}', 'getById')->whereNumber('id')->name('itemGetById');
    Route::get('/artist/{artist}', 'getByArtist')->where('artist', '^[a-zA-Z0-9\-]+$')->name('itemGetByArtist');
    Route::get('/format/{format}', 'getByFormat')->where('format', '^[a-zA-Z0-9\-]+$')->name('itemGetByFormat');
    Route::get('/order/{key}/{order}', 'sortByKey')->where('key', '^[a-zA-Z]+$')->where('order', '^[a-zA-Z]+$')->name('itemSortByKey');
    
    // POST
    Route::post('/', 'create')->name('itemCreate');
    Route::put('/{id}', 'update')->name('itemUpdate');
    Route::delete('/', 'delete')->name('itemDelete');

    // FALLBACK
    Route::fallback(function(Request $request) {
        return response()->json([
            'status' => 'Not Found',
            'code' => 404,
            'description' => 'Hay un error en la ruta o en sus parámetros: ' . $request->path(),
            'data' => null
        ]);
    });
});