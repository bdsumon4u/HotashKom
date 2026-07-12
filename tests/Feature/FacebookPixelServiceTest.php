<?php

use App\Services\FacebookPixelService;
use FacebookAds\Object\ServerSide\UserData;
use Hotash\FacebookPixel\Facades\MetaPixel;
use Illuminate\Support\Defer\DeferredCallbackCollection;

it('correctly maps country code on UserData during tracking', function () {
    // Set config values so explosion loop runs at least once
    config(['meta-pixel.meta_pixel' => '123456789:test-token:test-code']);

    // Spy on the send method
    MetaPixel::shouldReceive('userData')
        ->andReturn(new UserData);

    MetaPixel::shouldReceive('setPixelId')->once()->with('123456789');
    MetaPixel::shouldReceive('setToken')->once()->with('test-token');
    MetaPixel::shouldReceive('setTestEventCode')->once()->with('test-code');

    $capturedUserData = null;
    MetaPixel::shouldReceive('send')
        ->once()
        ->with(
            'Lead',
            Mockery::any(),
            Mockery::any(),
            Mockery::on(function ($userData) use (&$capturedUserData) {
                $capturedUserData = $userData;

                return $userData instanceof UserData;
            }),
            Mockery::any()
        );

    $service = new FacebookPixelService;
    $service->trackEvent(
        eventName: 'Lead',
        customData: [],
        userData: ['country' => 'BD']
    );

    // Force deferred callbacks to execute
    app(DeferredCallbackCollection::class)->invoke();

    expect($capturedUserData)->not->toBeNull();
    // FacebookAds SDK stores country codes in lowercase in getCountryCodes() or via property
    expect($capturedUserData->getCountryCodes())->toBe(['bd']);
});
