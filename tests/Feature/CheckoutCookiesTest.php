<?php

use App\Livewire\Checkout;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cookie;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('saves customer details in cookies when not oninda or not resell mode', function () {
    config(['app.oninda' => false, 'app.resell' => false]);

    Livewire::test(Checkout::class)
        ->call('updateField', 'name', 'John Doe');

    $cookie = collect(Cookie::getQueuedCookies())->first(fn ($c) => $c->getName() === 'name');
    expect($cookie)->not->toBeNull();
    expect($cookie->getValue())->toBe('John Doe');
});

it('does not save customer details in cookies when oninda and resell mode are active', function () {
    config(['app.oninda' => true, 'app.resell' => true]);

    $user = User::create([
        'name' => 'John Doe Reseller',
        'email' => 'john@example.com',
        'phone_number' => '+8801700000000',
        'password' => bcrypt('password'),
    ]);
    auth('user')->login($user);

    Livewire::test(Checkout::class)
        ->call('updateField', 'name', 'John Doe');

    $cookie = collect(Cookie::getQueuedCookies())->first(fn ($c) => $c->getName() === 'name');
    expect($cookie)->toBeNull();
});

it('does not refill customer details from cookies when oninda and resell mode are active', function () {
    config(['app.oninda' => true, 'app.resell' => true]);

    $user = User::create([
        'name' => 'John Doe Reseller',
        'email' => 'john@example.com',
        'phone_number' => '+8801700000000',
        'password' => bcrypt('password'),
    ]);
    auth('user')->login($user);

    Livewire::withCookie('name', 'John Doe')
        ->test(Checkout::class)
        ->assertSet('name', '');
});

it('refills customer details from cookies when not oninda or not resell mode', function () {
    config(['app.oninda' => false, 'app.resell' => false]);

    Livewire::withCookie('name', 'John Doe')
        ->test(Checkout::class)
        ->assertSet('name', 'John Doe');
});
