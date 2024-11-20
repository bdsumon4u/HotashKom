<?php

namespace App\Extensions;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Container\BindingResolutionException;

class DatabaseSessionHandler extends \Illuminate\Session\DatabaseSessionHandler
{
    /**
     * Add the user information to the session payload.
     *
     * @param  array  $payload
     * @return $this
     *
     * @throws BindingResolutionException
     */
    protected function addUserInformation(&$payload)
    {
        if ($this->container->bound(Guard::class)) {
            // info(($this->user() ? get_class($this->user()) : null));
            $payload['userable_type'] = $this->user() ? get_class($this->user()) : null;
            $payload['userable_id'] = $this->userId();
        }

        return $this;
    }

    /**
     * Get the currently authenticated user's ID.
     *
     * @return mixed
     *
     * @throws BindingResolutionException
     */
    protected function user()
    {
        return $this->container->make(Guard::class)->user();
    }
}
