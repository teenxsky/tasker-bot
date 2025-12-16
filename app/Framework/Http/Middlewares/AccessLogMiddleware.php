<?php

declare(strict_types=1);

namespace App\Framework\Http\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class AccessLogMiddleware
{
    /**
     * Обрабатывает входящий HTTP-запрос.
     *
     * @param  Closure(Request): (Response) $next
     * @throws Throwable
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $response = $next($request);
            $status   = $response->getStatusCode();
        } catch (Throwable $throwable) {
            $status = method_exists($throwable, 'getStatusCode')
                ? $throwable->getStatusCode()
                : 500;

            $this->logRequest($request, $status);
            throw $throwable;
        }

        $this->logRequest($request, $status);

        return $response;
    }

    private function logRequest(Request $request, int $status): void
    {
        if (App::runningUnitTests()) {
            return;
        }

        /** @var string $protocol */
        $protocol = $request->server('SERVER_PROTOCOL', 'HTTP/1.1');
        $method   = $request->getMethod();
        $uri      = $request->getPathInfo();
        $source   = $request->getClientIp() ?? 'unknown';

        $message = sprintf('%s %s - %s %d - %s', $protocol, $method, $uri, $status, $source);

        if ($status >= 500) {
            Log::error($message);
        } else {
            Log::info($message);
        }
    }
}
