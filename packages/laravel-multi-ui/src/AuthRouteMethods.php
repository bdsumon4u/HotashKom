<?php

namespace Hotash\LaravelMultiUi;

class AuthRouteMethods
{
    /**
     * Register the typical authentication routes for an application.
     *
     * @param  array  $options
     * @return void
     */
    public function auth()
    {
        return function ($options = []) {
            // Authentication Routes...
            $this->get($this->getURL('login', $options), 'Auth\LoginController@showLoginForm')->name('login');
            $this->post($this->getURL('login', $options), 'Auth\LoginController@login');
            $this->post($this->getURL('logout', $options), 'Auth\LoginController@logout')->name('logout');

            // Registration Routes...
            if ($options['register'] ?? true) {
                $this->get($this->getURL('register', $options), 'Auth\RegisterController@showRegistrationForm')->name('register');
                $this->post($this->getURL('register', $options), 'Auth\RegisterController@register');
            }

            // Password Reset Routes...
            if ($options['reset'] ?? true) {
                $this->get($this->getURL('password/reset', $options), 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
                $this->post($this->getURL('password/email', $options), 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
                $this->get($this->getURL('password/reset', $options).'/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
                $this->post($this->getURL('password/reset', $options), 'Auth\ResetPasswordController@reset')->name('password.update');
            }

            // Password Confirmation Routes...
            if ($options['confirm'] ?? class_exists($this->prependGroupNamespace('Auth\ConfirmPasswordController'))) {
                $this->get($this->getURL('password/confirm', $options), 'Auth\ConfirmPasswordController@showConfirmForm')->name('password.confirm');
                $this->post($this->getURL('password/confirm', $options), 'Auth\ConfirmPasswordController@confirm');
            }

            // Email Verification Routes...
            if ($options['verify'] ?? false) {
                $this->get($this->getURL('email/verify', $options), 'Auth\VerificationController@show')->name('verification.notice');
                $this->get($this->getURL('email/resend', $options).'/{id}/{hash}', 'Auth\VerificationController@verify')->name('verification.verify');
                $this->post($this->getURL('email/verify', $options), 'Auth\VerificationController@resend')->name('verification.resend');
            }
        };
    }

    /**
     * Get URL
     */
    public function getURL()
    {
        return function ($key, $options) {
            $URLs = $options['URLs'] ?? [];
            $URL = $URLs[$key] ?? $key;

            if (! array_key_exists('prefix', $options)) {
                return $URL;
            }

            $prefix = $options['prefix'];

            if (is_string($prefix)) {
                return $prefix.$URL;
            }

            if (is_array($prefix)) {
                if (array_key_exists('except', $prefix)) {
                    $except = $prefix['except'];

                    if (is_array($except)) {
                        return in_array($key, $except)
                            ? $URL
                            : $prefix['URL'].$URL;
                    }

                    if (is_string($except)) {
                        return $key == $except
                            ? $URL
                            : $prefix['URL'].$URL;
                    }
                }

                if (array_key_exists('only', $prefix)) {
                    $only = $prefix['only'];

                    if (is_array($only)) {
                        return in_array($key, $only)
                            ? $prefix['URL'].$URL
                            : $URL;
                    }

                    if (is_string($only)) {
                        return $key == $only
                            ? $prefix['URL'].$URL
                            : $URL;
                    }
                }
            }
        };
    }
}
