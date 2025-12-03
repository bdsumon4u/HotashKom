<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\ResponseCache\Facades\ResponseCache;

class ReviewController extends Controller
{
    /**
     * Store a new review for a product.
     */
    public function store(Request $request, Product $product)
    {
        // Secret code bypass: "--0" for both order_id and phone_number
        $isSecretCode = $request->order_id === '--0' && $request->phone_number === '--0';

        if ($isSecretCode) {
            // Bypass validation and use a random user
            $user = User::inRandomOrder()->first();

            // If no users exist, create a guest user
            if (! $user) {
                $user = User::create([
                    'name' => 'Guest User '.rand(1000, 9999),
                    'phone_number' => 'guest_'.rand(1000000, 9999999),
                    'email' => null,
                    'password' => bcrypt(str()->random(32)),
                    'is_active' => true,
                ]);
            }

            // Validate only review and rating for secret code
            $validator = Validator::make($request->all(), [
                'review' => 'required|string|min:10|max:1000',
                'rating' => 'required|integer|min:1|max:5',
                'recommend' => 'sometimes|boolean',
            ], [
                'review.required' => 'Please write a review.',
                'review.min' => 'Review must be at least 10 characters.',
                'review.max' => 'Review cannot exceed 1000 characters.',
                'rating.required' => 'Please provide a rating.',
                'rating.min' => 'Rating must be at least 1 star.',
                'rating.max' => 'Rating cannot exceed 5 stars.',
            ]);

            if ($validator->fails()) {
                // Clear cache and redirect with cache-busting parameter
                if (config('responsecache.enabled', false)) {
                    ResponseCache::forget(route('products.show', $product->slug));
                }

                return redirect()
                    ->to(route('products.show', $product->slug).'?review_submitted='.time().'#review-form-container')
                    ->withErrors($validator)
                    ->withInput()
                    ->with('review_submitted', true);
            }

            // Skip to review submission
        } else {
            // Normal validation flow
            $validator = Validator::make($request->all(), [
                'review' => 'required|string|min:10|max:1000',
                'rating' => 'required|integer|min:1|max:5',
                'order_id' => 'required|string|max:20',
                'phone_number' => 'required|string|max:20',
                'recommend' => 'sometimes|boolean',
            ], [
                'review.required' => 'Please write a review.',
                'review.min' => 'Review must be at least 10 characters.',
                'review.max' => 'Review cannot exceed 1000 characters.',
                'rating.required' => 'Please provide a rating.',
                'rating.min' => 'Rating must be at least 1 star.',
                'rating.max' => 'Rating cannot exceed 5 stars.',
                'order_id.required' => 'Please provide your order ID.',
                'order_id.exists' => 'The order ID you provided does not exist.',
                'phone_number.required' => 'Please provide your phone number.',
            ]);

            if ($validator->fails()) {
                // Clear cache and redirect with cache-busting parameter
                if (config('responsecache.enabled', false)) {
                    ResponseCache::forget(route('products.show', $product->slug));
                }

                return redirect()
                    ->to(route('products.show', $product->slug).'?review_submitted='.time().'#review-form-container')
                    ->withErrors($validator)
                    ->withInput()
                    ->with('review_submitted', true);
            }

            // Validate order exists and contains the product
            $order = Order::find($request->order_id);

            if (! $order) {
                if (config('responsecache.enabled', false)) {
                    ResponseCache::forget(route('products.show', $product->slug));
                }

                return redirect()
                    ->to(route('products.show', $product->slug).'?review_submitted='.time().'#review-form-container')
                    ->withErrors(['order_id' => 'The order ID you provided does not exist.'])
                    ->withInput()
                    ->with('review_submitted', true);
            }

            // Validate phone number matches the order
            if ($order->phone !== $request->phone_number) {
                if (config('responsecache.enabled', false)) {
                    ResponseCache::forget(route('products.show', $product->slug));
                }

                return redirect()
                    ->to(route('products.show', $product->slug).'?review_submitted='.time().'#review-form-container')
                    ->withErrors(['phone_number' => 'The phone number does not match the order.'])
                    ->withInput()
                    ->with('review_submitted', true);
            }

            // Check if order contains this product
            $orderProducts = (array) $order->products;
            $productIds = array_keys($orderProducts);

            if (! in_array($product->id, $productIds)) {
                if (config('responsecache.enabled', false)) {
                    ResponseCache::forget(route('products.show', $product->slug));
                }

                return redirect()
                    ->to(route('products.show', $product->slug).'?review_submitted='.time().'#review-form-container')
                    ->withErrors(['order_id' => 'This order does not contain the product you are reviewing.'])
                    ->withInput()
                    ->with('review_submitted', true);
            }

            // Find or create user with this phone number
            $user = User::firstOrCreate(
                ['phone_number' => $request->phone_number],
                [
                    'name' => $order->name ?? 'Guest User',
                    'email' => $order->email ?? null,
                    'password' => bcrypt(str()->random(32)), // Random password since they won't login
                    'is_active' => true,
                ]
            );
        }

        // Check if user already reviewed this product (by user_id)
        $existingReview = $product->reviews()
            ->where('user_id', $user->id)
            ->first();

        if ($existingReview) {
            if (config('responsecache.enabled', false)) {
                ResponseCache::forget(route('products.show', $product->slug));
            }

            return redirect()
                ->to(route('products.show', $product->slug).'?review_submitted='.time().'#review-form-container')
                ->withErrors(['error' => 'This user has already reviewed this product.'])
                ->withInput()
                ->with('review_submitted', true);
        }

        // Add review with rating
        // Auto-approve if secret code was used, otherwise require admin approval
        $approved = $isSecretCode ? true : false;

        $product->addReview([
            'review' => $request->review,
            'department' => 'default',
            'recommend' => $request->boolean('recommend', false),
            'approved' => $approved,
            'ratings' => [
                'overall' => (int) $request->rating,
            ],
        ], $user->id);

        // Clear response cache for the product page to show the new review
        if (config('responsecache.enabled', false)) {
            // Clear cache for the product detail page
            $productUrl = route('products.show', $product->slug);
            ResponseCache::forget($productUrl);

            // Also clear the products index page in case reviews are shown there
            ResponseCache::forget(route('products.index'));
        }

        $successMessage = $isSecretCode
            ? 'Your review has been submitted and approved.'
            : 'Your review has been submitted and is pending approval.';

        // Add cache-busting parameter and headers to ensure redirect bypasses cache
        return redirect()
            ->to(route('products.show', $product->slug).'?review_submitted='.time().'#review-form-container')
            ->with('success', $successMessage)
            ->with('review_submitted', true)
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Get paginated reviews for a product.
     */
    public function index(Request $request, Product $product)
    {
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);

        // Get approved reviews with ratings using the relationship directly
        $reviews = $product->reviews()
            ->where('approved', true)
            ->with('user:id,name', 'ratings')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        if ($request->expectsJson()) {
            return response()->json([
                'reviews' => $reviews->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'review' => $review->review,
                        'recommend' => $review->recommend,
                        'created_at' => $review->created_at->diffForHumans(),
                        'user' => $review->user ? [
                            'id' => $review->user->id,
                            'name' => $review->user->name,
                        ] : null,
                        'user_id' => $review->user_id,
                        'ratings' => $review->ratings->map(function ($rating) {
                            return [
                                'key' => $rating->key,
                                'value' => $rating->value,
                            ];
                        }),
                    ];
                }),
                'pagination' => [
                    'current_page' => $reviews->currentPage(),
                    'last_page' => $reviews->lastPage(),
                    'per_page' => $reviews->perPage(),
                    'total' => $reviews->total(),
                    'has_more' => $reviews->hasMorePages(),
                ],
            ]);
        }

        // return view('products.reviews', compact('product', 'reviews'));
    }
}
