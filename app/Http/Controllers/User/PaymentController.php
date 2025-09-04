<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Ihasan\Bkash\Facades\Bkash;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Show payment form for account verification
     */
    public function showPaymentForm(Request $request)
    {
        $user = auth()->user();
        $verificationFee = config('app.verification_fee', 1000);
        $appliedCoupon = null;
        $discountAmount = 0;
        $finalAmount = $verificationFee;

        // Check if coupon is applied
        if ($request->has('coupon_code')) {
            $coupon = Coupon::findByCode($request->coupon_code);
            if ($coupon && $coupon->isValid()) {
                $appliedCoupon = $coupon;
                $discountAmount = $coupon->calculateDiscount($verificationFee);
                $finalAmount = $verificationFee - $discountAmount;
            }
        }

        return view('user.payment.verification', compact(
            'user',
            'verificationFee',
            'appliedCoupon',
            'discountAmount',
            'finalAmount'
        ));
    }

    /**
     * Apply coupon and redirect back to payment form
     */
    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string|max:50',
        ]);

        $coupon = Coupon::findByCode($request->coupon_code);

        if (! $coupon) {
            return redirect()->route('user.payment.verification')
                ->with('error', 'Invalid coupon code.');
        }

        if (! $coupon->isValid()) {
            return redirect()->route('user.payment.verification')
                ->with('error', 'This coupon is not valid or has expired.');
        }

        return redirect()->route('user.payment.verification', ['coupon_code' => $coupon->code])
            ->with('success', 'Coupon applied successfully!');
    }

    /**
     * Create bKash payment for verification
     */
    public function createPayment(Request $request)
    {
        $request->validate([
            'coupon_code' => 'nullable|string|max:50',
        ]);

        $user = auth()->user();
        $verificationFee = config('app.verification_fee', 1000);
        $appliedCoupon = null;
        $discountAmount = 0;
        $finalAmount = $verificationFee;

        // Apply coupon if provided
        if ($request->coupon_code) {
            $coupon = Coupon::findByCode($request->coupon_code);
            if ($coupon && $coupon->isValid()) {
                $appliedCoupon = $coupon;
                $discountAmount = $coupon->calculateDiscount($verificationFee);
                $finalAmount = $verificationFee - $discountAmount;
            }
        }

        try {
            // Create bKash payment
            $payment = Bkash::createPayment([
                'amount' => $finalAmount,
                'payer_reference' => 'user_'.$user->id,
                'callback_url' => route('user.bkash.callback'),
                'merchant_invoice_number' => 'VERIFY-'.$user->id.'-'.time(),
            ]);

            // Store payment info in session for callback
            session([
                'verification_payment' => [
                    'user_id' => $user->id,
                    'amount' => $finalAmount,
                    'original_amount' => $verificationFee,
                    'discount_amount' => $discountAmount,
                    'coupon_id' => $appliedCoupon ? $appliedCoupon->id : null,
                    'payment_id' => $payment['paymentID'],
                    'merchant_invoice_number' => $payment['merchantInvoiceNumber'],
                ],
            ]);

            // Redirect to bKash payment page
            return redirect()->away($payment['bkashURL']);

        } catch (\Exception $e) {
            return redirect()->route('user.payment.verification')
                ->with('error', 'Failed to create payment: '.$e->getMessage());
        }
    }
}
