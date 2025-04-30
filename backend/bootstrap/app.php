<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {

        // Devuelve todas las excepciones de la API como JSON en lugar de HTML
        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {

            if($request->is('api/*')) return true;
            return $request->expectsJson();
        });

        // Personaliza la respuesta para la excepción de ruta no econtrada
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            return response()->json([
                'status' => 'Not Found',
                'code' => 404,
                'description' => 'No existe la ruta: ' . $request->path(),
                'data' => null
            ]);
        });
        
    })->create();
