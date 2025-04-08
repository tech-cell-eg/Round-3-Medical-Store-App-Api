<?php

use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

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
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
          if ($request->is('api/*')) {
                 return response()->json([
                     'status' => false,
                     'message' => 'المورد غير موجود.',
                     'error' => $e->getMessage(),
                     'code' => 404,
                 ], 404);
               }
         });

         $exceptions->render(function (ModelNotFoundException $e, Request $request) {
           if ($request->is('api/*')) {
             return response()->json([
                 'status' => false,
                 'message' => 'السجل غير موجود.',
                 'error' => $e->getMessage(),
                 'code' => 404,
             ], 404);
           }
         });

         $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) {
           if ($request->is('api/*')) {
             return response()->json([
                 'status' => false,
                 'message' => 'الطريقة غير مسموح بها.',
                 'error' => $e->getMessage(),
                 'code' => 405,
             ], 405);
           }
         });

         $exceptions->render(function (ValidationException $e, Request $request) {
           if ($request->is('api/*')) {
             return response()->json([
                 'status' => false,
                 'message' => 'خطأ في التحقق من البيانات.',
                 'errors' => $e->errors(),
                 'code' => 422,
             ], 422);
           }
         });

         $exceptions->render(function (AuthenticationException $e, Request $request) {
           if ($request->is('api/*')) {
             return response()->json([
                 'status' => false,
                 'message' => 'غير مصرح لك بالوصول.',
                 'error' => $e->getMessage(),
                 'code' => 401,
             ], 401);
           }
         });

         $exceptions->render(function (Throwable $e, Request $request) {
           if ($request->is('api/*')) {
             return response()->json([
                 'status' => false,
                 'message' => 'حدث خطأ غير متوقع.',
                 'error' => $e->getMessage(),
                 'code' => 500,
             ], 500);
           }
         });

  })->create();
