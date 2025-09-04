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

    public function checkout()
    {
        // Check if user is verified before proceeding with checkout
        if (isOninda() && (! auth('user')->user() || ! auth('user')->user()->is_verified)) {
            $this->dispatch('notify', ['message' => 'Please verify your account to place an order', 'type' => 'error']);

            return redirect()->route('user.payment.verification')->with('danger', 'Please verify your account to place an order');
        }

        return parent::checkout();
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
