<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Display a listing of reviews.
     */
    public function index(Request $request)
    {
        abort_if(request()->user()->is('salesman'), 403, 'You don\'t have permission.');

        $query = Review::with(['reviewable', 'user:id,name,email', 'ratings'])
            ->orderBy('created_at', 'desc');

        // Filter by approval status
        if ($request->has('status')) {
            if ($request->status === 'pending') {
                $query->where('approved', false);
            } elseif ($request->status === 'approved') {
                $query->where('approved', true);
            }
        } else {
            // Default: show pending reviews first
            $query->orderBy('approved', 'asc');
        }

        // Filter by reviewable type
        if ($request->has('type') && $request->type === 'products') {
            $query->where('reviewable_type', Product::class);
        }

        $reviews = $query->paginate(20)->appends(request()->query());

        return $this->view(compact('reviews'));
    }

    /**
     * Approve a review.
     */
    public function approve(Review $review)
    {
        abort_if(request()->user()->is('salesman'), 403, 'You don\'t have permission.');

        $product = $review->reviewable;

        if (! $product instanceof Product) {
            return back()->withErrors(['error' => 'Invalid review.']);
        }

        $product->approveReview($review->id);

        return back()->with('success', 'Review has been approved.');
    }

    /**
     * Reject/Delete a review.
     */
    public function destroy(Review $review)
    {
        abort_if(request()->user()->is('salesman'), 403, 'You don\'t have permission.');

        $product = $review->reviewable;

        if (! $product instanceof Product) {
            return back()->withErrors(['error' => 'Invalid review.']);
        }

        $product->deleteReview($review->id);

        return back()->with('success', 'Review has been deleted.');
    }

    /**
     * Bulk approve reviews.
     */
    public function bulkApprove(Request $request)
    {
        abort_if(request()->user()->is('salesman'), 403, 'You don\'t have permission.');

        $request->validate([
            'review_ids' => 'required|array',
            'review_ids.*' => 'exists:reviews,id',
        ]);

        $reviews = Review::whereIn('id', $request->review_ids)
            ->with('reviewable')
            ->get();

        foreach ($reviews as $review) {
            if ($review->reviewable instanceof Product) {
                $review->reviewable->approveReview($review->id);
            }
        }

        return back()->with('success', count($reviews).' reviews have been approved.');
    }

    /**
     * Bulk delete reviews.
     */
    public function bulkDelete(Request $request)
    {
        abort_if(request()->user()->is('salesman'), 403, 'You don\'t have permission.');

        $request->validate([
            'review_ids' => 'required|array',
            'review_ids.*' => 'exists:reviews,id',
        ]);

        $reviews = Review::whereIn('id', $request->review_ids)
            ->with('reviewable')
            ->get();

        foreach ($reviews as $review) {
            if ($review->reviewable instanceof Product) {
                $review->reviewable->deleteReview($review->id);
            }
        }

        return back()->with('success', count($reviews).' reviews have been deleted.');
    }
}
