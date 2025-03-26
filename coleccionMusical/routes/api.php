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

    Algunas rutas GET "pre-validan" los parámetros de la petición con los métodos
    genéricos que provee Laravel. Si se supera esta validación, el controlador
    se encargará de ejecutar otra validación más exigente, también para los métodos
    POST, PUT y DELETE. En caso contrario, se llega a la ruta de fallback, que
    devuelve un 404 con un mensaje personalizado.
*/

Route::controller('App\Http\Controllers\ItemController')->prefix('item')->group(function() {

    // GET
    Route::get('/', 'getAll')->name('itemGetAll');
    Route::get('/{id}', 'getById')->whereNumber('id')->name('itemGetById');
    Route::get('/artist/{artist}', 'getByArtist')->whereAlphaNumeric('artist')->name('itemGetByArtist');
    Route::get('/format/{format}', 'getByFormat')->whereAlphaNumeric('format')->name('itemGetByFormat');
    Route::get('/order/{key}/{order}', 'sortByKey')
        ->whereIn('key', ['title','artist','format','year','origYear','label','rating', 'comment','buyPrice', 'condition','sellPrice'])
        ->whereIn('order', ['asc', 'desc'])
        ->name('itemSortByKey');
    
    // POST
    Route::post('/create', 'create')->name('itemCreate');
    Route::put('/update', 'update')->name('itemUpdate');
    Route::delete('/delete', 'delete')->name('itemDelete');

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