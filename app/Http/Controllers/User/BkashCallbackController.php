<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\User;
use Ihasan\Bkash\Facades\Bkash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BkashCallbackController extends Controller
{
    /**
     * Handle bKash payment callback for verification payments
     */
    public function callback(Request $request)
    {
        $paymentId = $request->input('paymentID');
        $status = $request->input('status');

        if ($status !== 'success') {
            return redirect()->route('user.profile')
                ->with('error', 'Payment was not successful')
                ->with('payment_id', $paymentId);
        }

        try {
            // Get payment info from session
            $paymentInfo = session('verification_payment');

            if (! $paymentInfo) {
                return redirect()->route('user.profile')
                    ->with('error', 'Payment session expired. Please try again.');
            }

            // Execute the payment
            $response = Bkash::executePayment($paymentId);

            // Check if payment was successful
            if (($response['transactionStatus'] ?? '') === 'Completed') {
                DB::transaction(function () use ($paymentInfo, $response) {
                    // Update user verification status
                    $user = User::find($paymentInfo['user_id']);
                    $user->update(['is_verified' => true]);

                    // Increment coupon usage if applied
                    if ($paymentInfo['coupon_id']) {
                        $coupon = Coupon::find($paymentInfo['coupon_id']);
                        if ($coupon) {
                            $coupon->incrementUsage();
                        }
                    }

                    // Store payment record (you might want to create a payments table)
                    // For now, we'll just log it
                    Log::info('Verification payment completed', [
                        'user_id' => $paymentInfo['user_id'],
                        'amount' => $paymentInfo['amount'],
                        'payment_id' => $response['paymentID'],
                        'transaction_id' => $response['trxID'] ?? null,
                        'coupon_id' => $paymentInfo['coupon_id'],
                    ]);
                });

                // Clear session
                session()->forget('verification_payment');

                return redirect()->route('user.profile')
                    ->with('success', 'Payment successful! Your account has been verified.');
            } else {
                return redirect()->route('user.profile')
                    ->with('error', 'Payment failed or was cancelled.');
            }

        } catch (\Exception $e) {
            Log::error('Payment callback error: '.$e->getMessage());

            return redirect()->route('user.profile')
                ->with('error', 'Payment verification failed. Please contact support.');
        }
    }
}
