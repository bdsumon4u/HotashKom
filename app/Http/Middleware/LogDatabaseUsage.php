<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogDatabaseUsage
{
    /**
     * Only log requests that execute more than this many queries.
     * Set to 0 to log every request (noisy in production).
     */
    private const QUERY_THRESHOLD = 20;

    /**
     * Also log any individual query slower than this (milliseconds).
     */
    private const SLOW_QUERY_MS = 100;

    private int $queryCount = 0;

    private float $totalMs = 0;

    private array $slowQueries = [];

    public function handle(Request $request, Closure $next): Response
    {
        DB::listen(function ($query): void {
            $this->queryCount++;
            $this->totalMs += $query->time;

            if ($query->time >= self::SLOW_QUERY_MS) {
                $this->slowQueries[] = [
                    'sql' => $query->sql,
                    'ms' => round($query->time, 2),
                ];
            }
        });

        return $next($request);
    }

    /**
     * Log after the response has been sent to the client.
     */
    public function terminate(Request $request, Response $response): void
    {
        if ($this->queryCount < self::QUERY_THRESHOLD) {
            return;
        }

        $context = [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'queries' => $this->queryCount,
            'total_ms' => round($this->totalMs, 2),
            'status' => $response->getStatusCode(),
        ];

        if (! empty($this->slowQueries)) {
            $context['slow_queries'] = $this->slowQueries;
        }

        Log::channel('db_usage')->warning("Heavy DB usage: {$this->queryCount} queries on {$request->method()} {$request->path()}", $context);
    }
}
