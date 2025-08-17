<?php

namespace App\Livewire;

class ResellerCheckout extends Checkout
{
    public function render()
    {
        // Create a temporary Order instance to use its Pathao methods
        $tempOrder = new \App\Models\Order;
        $this->cartUpdated();

        return view('livewire.reseller-checkout', [
            'user' => optional(auth('user')->user()),
            'pathaoCities' => collect($tempOrder->pathaoCityList()),
            'pathaoAreas' => collect($tempOrder->pathaoAreaList($this->city_id)),
        ]);
    }

    protected function fillFromCookie()
    {
        return false;
    }

    protected function getRedirectRoute()
    {
        return 'reseller.thank-you';
    }

    protected function getDefaultStatus()
    {
        return 'CONFIRMED';
    }
}
