<?php

namespace App\Http\Middleware;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Authenticate extends Middleware
{
    /**
     * Where to redirect unauthenticated users.
     *
     * @var string
     */
    protected $redirectModelNameTo;

    protected $redirectAdminTo = '/';

    protected $redirectUserTo = '/login';

    /**
     * Handle an unauthenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    protected function unauthenticated($request, array $guards)
    {
        throw new AuthenticationException(
            'Unauthenticated.', $guards, $this->shouldRedirectTo($request, $guards)
        );
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function shouldRedirectTo($request, array $guards)
    {
        if (! $request->expectsJson()) {
            $redirectTo = 'redirect'.Str::studly(Arr::get($guards, 0)).'To';

            if (! isset($this->$redirectTo) || is_null($this->$redirectTo)) {
                $this->$redirectTo = route(Arr::get($guards, 0).'.login');
            }

            return $this->$redirectTo;
        }
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('/');
        }
    }
}
