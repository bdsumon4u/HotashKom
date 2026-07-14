<?php

namespace App\Livewire;

use App\Models\Order;

class ResellerCheckout extends Checkout
{
    #[\Override]
    public function render()
    {
        // Create a temporary Order instance to use its Pathao methods
        $tempOrder = new Order;
        $this->cartUpdated();

        $cartItems = cart()->content();

        $sellingSubtotal = $cartItems->sum(function ($item): float|int {
            $id = $item->id;
            $price = $this->retail[$id]['price'] ?? $item->options->retail_price ?? $item->price;

            return (float) $price * $item->qty;
        });

        $sellingTotal = $sellingSubtotal
            + (float) $this->retailDeliveryFee
            - (float) $this->advanced
            - (float) $this->retailDiscount;

        $cartProductIds = $cartItems->pluck('id')->filter()->unique()->values()->toArray();

        return view('livewire.reseller-checkout', [
            'user' => optional(auth('user')->user()),
            'pathaoCities' => collect($tempOrder->pathaoCityList()),
            'pathaoAreas' => collect($tempOrder->pathaoAreaList($this->city_id)),
            'retail' => $this->retail,
            'advanced' => $this->advanced,
            'retailDeliveryFee' => $this->retailDeliveryFee,
            'retailDiscount' => $this->retailDiscount,
            'sellingSubtotal' => $sellingSubtotal,
            'sellingTotal' => $sellingTotal,
            'packagingCharge' => $this->resolvePackagingCharge(array_flip($cartProductIds)),
        ]);
    }

    #[\Override]
    public function checkout()
    {
        // Check if user is verified before proceeding with checkout
        if (isOninda() && (! auth('user')->user() || ! auth('user')->user()->is_verified)) {
            $this->dispatch('notify', ['message' => 'Please verify your account to place an order', 'type' => 'error']);

            return to_route('user.payment.verification')->with('danger', 'Please verify your account to place an order');
        }

        return parent::checkout();
    }

    #[\Override]
    protected function fillFromCookie(): bool
    {
        return false;
    }

    #[\Override]
    protected function getRedirectRoute(): string
    {
        return 'reseller.thank-you';
    }

    #[\Override]
    protected function getDefaultStatus(): string
    {
        return 'CONFIRMED';
    }
}
