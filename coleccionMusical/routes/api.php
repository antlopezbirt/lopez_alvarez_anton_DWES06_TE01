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

    Las rutas GET validan los parámetros de la petición con los métodos
    que provee Laravel en la clase Route.

    En el caso de las rutas POST, PUT y DELETE es el controlador quien se encarga
    de validar el contenido de la petición.

    En caso de que no se valide la ruta por este u otro motivo, se llega a la
    ruta de fallback, que devuelve un 404.
*/

Route::controller('App\Http\Controllers\ItemController')->prefix('item')->group(function() {

    // GET
    Route::get('/', 'getAll')->name('itemGetAll');
    Route::get('/{id}', 'getById')->whereNumber('id')->name('itemGetById');
    Route::get('/artist/{artist}', 'getByArtist')->where('artist', '^[a-zA-Z0-9\-]+$')->name('itemGetByArtist');
    Route::get('/format/{format}', 'getByFormat')->where('format', '^[a-zA-Z0-9\-]+$')->name('itemGetByFormat');
    Route::pattern('order', '(?i)order(?-i)');
    Route::get('/order/{key}/{order}', 'sortByKey')
        ->whereIn('key', ['id','title','artist','format','year','origYear','label','rating', 'comment','buyPrice', 'condition','sellPrice'])
        ->where('order', '^asc|ASC|desc|DESC$')
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