<?php

use App\Support\ReportFailure;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectUsersTo(fn (Request $request) => route($request->user()->dashboardRoute()));
        $middleware->authenticateSessions();
        $middleware->trimStrings(except: ['token']);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->dontFlash(['token']);
        $exceptions->shouldRenderJsonWhen(fn (Request $request) => $request->expectsJson());
        $exceptions->report(fn (Throwable $e) => ReportFailure::log($e, 'request.failed'))->stop();
        $exceptions->respond(function (Response $response, Throwable $exception, Request $request) {
            $status = $response->getStatusCode();
            $message = match ($status) {
                403 => 'You do not have access to this page.',
                404 => 'We could not find that page.',
                419 => 'Your session expired. Return to the form and try again.',
                429 => $response->headers->has('Retry-After')
                    ? 'Too many requests. Please try again in '.$response->headers->get('Retry-After').' seconds.'
                    : 'Too many requests. Please wait a moment and try again.',
                500 => 'Something went wrong. Please try again.',
                default => null,
            };

            if ($message === null) {
                return $response;
            }

            $headers = array_filter([
                'Retry-After' => $response->headers->get('Retry-After'),
                'X-RateLimit-Limit' => $response->headers->get('X-RateLimit-Limit'),
                'X-RateLimit-Remaining' => $response->headers->get('X-RateLimit-Remaining'),
            ], fn ($value) => $value !== null);

            return $request->expectsJson()
                ? response()->json(['message' => $message], $status, $headers)
                : response()->view('errors.friendly', compact('status', 'message'), $status, $headers);
        });
    })->create();
