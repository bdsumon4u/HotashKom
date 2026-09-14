@php
    // Calculate average rating and total reviews using the pre-loaded collection
    $approvedReviews = $product->relationLoaded('reviews')
        ? $product->reviews->where('approved', true)
        : $product->reviews()->where('approved', true)->with('ratings')->get();

    $overallRatings = $approvedReviews->flatMap(function ($review) {
        return $review->relationLoaded('ratings')
            ? $review->ratings->where('key', 'overall')
            : $review->ratings()->where('key', 'overall')->get();
    });

    $averageRating = $overallRatings->count() > 0 ? $overallRatings->avg('value') : 0;
    $totalReviews = $approvedReviews->count();

    // Sort and limit reviews in memory to avoid extra database query
    if ($product->relationLoaded('reviews')) {
        $reviews = $approvedReviews->sortByDesc('created_at')->take(10);
    } else {
        $reviews = $product->reviews()
            ->where('approved', true)
            ->with('user:id,name', 'ratings')
            ->orderBy('created_at', 'desc')
            ->take($perPage = 10)
            ->get();
    }
    $perPage = 10;
@endphp

<div class="reviews-section">
    @if ($totalReviews > 0)
        <div class="reviews-summary d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="reviews-summary-score">{{ number_format($averageRating, 1) }}</div>
                <div>
                    <div class="reviews-summary-stars">
                        @for ($i = 1; $i <= 5; $i++)
                            @if ($i <= floor($averageRating))
                                <i class="fa fa-star"></i>
                            @elseif($i - 0.5 <= $averageRating)
                                <i class="fa fa-star-half-alt"></i>
                            @else
                                <i class="far fa-star" style="color: #cbd5e1;"></i>
                            @endif
                        @endfor
                    </div>
                    <div class="reviews-summary-count">
                        Based on {{ $totalReviews }} {{ Str::plural('verified review', $totalReviews) }}
                    </div>
                </div>
            </div>
            <div>
                <a href="#review-form-container" class="reviews-write-btn" onclick="document.getElementById('order_id')?.focus()">
                    <i class="fas fa-pen-to-square"></i>
                    <span>Write a Review</span>
                </a>
            </div>
        </div>
    @endif

    <div class="review-form-container" id="review-form-container"
        data-review-submitted="{{ session('review_submitted') ? 'true' : 'false' }}">
        <div class="review-form-header">
            <span class="review-form-badge"><i class="fas fa-pen-to-square"></i></span>
            <div>
                <h6 class="review-form-title">Write a Customer Review</h6>
                <p class="review-form-subtitle">Share your experience to help others make the right choice</p>
            </div>
        </div>

        <form action="{{ route('products.reviews.store', $product) }}" method="POST" id="review-form">
            @csrf
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="order_id">Order ID <span class="text-danger">*</span></label>
                        <input type="text" name="order_id" id="order_id" class="form-control"
                            placeholder="e.g. 10582" value="{{ old('order_id') }}" required>
                        <small class="form-text text-muted">Found in your order confirmation SMS.</small>
                        @error('order_id')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="phone_number">Phone Number <span class="text-danger">*</span></label>
                        <input type="text" name="phone_number" id="phone_number" class="form-control"
                            placeholder="017XXXXXXXX" value="{{ old('phone_number') }}" required>
                        <small class="form-text text-muted">Phone number used during purchase.</small>
                        @error('phone_number')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="rating">Your Rating <span class="text-danger">*</span></label>
                        <div class="rating-input-wrapper">
                            <div class="rating-input">
                                @for ($i = 5; $i >= 1; $i--)
                                    <input type="radio" name="rating" value="{{ $i }}"
                                        id="rating-{{ $i }}" {{ old('rating') == $i ? 'checked' : '' }}
                                        class="rating-radio">
                                    <label for="rating-{{ $i }}" class="star-label">
                                        <i class="far fa-star"></i>
                                    </label>
                                @endfor
                            </div>
                        </div>
                        <input type="hidden" name="rating_required" value="1" id="rating-required-field">
                        @error('rating')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="review">Your Review <span class="text-danger">*</span></label>
                <textarea name="review" id="review" rows="4" class="form-control"
                    placeholder="What did you like or dislike about this product? How is the quality?" required minlength="10" maxlength="1000">{{ old('review') }}</textarea>
                <small class="form-text text-muted">Minimum 10 characters, maximum 1000 characters.</small>
                @error('review')
                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group mb-3">
                <label class="form-check" for="recommend">
                    <input type="checkbox" name="recommend" value="1" id="recommend" class="form-check-input"
                        {{ old('recommend', true) ? 'checked' : '' }}>
                    <span class="form-check-label">
                        <i class="fas fa-thumbs-up mr-1 text-primary"></i> I recommend this product
                    </span>
                </label>
            </div>

            @if (session('success'))
                <div class="alert alert-success d-flex align-items-center gap-2 mb-3">
                    <i class="fas fa-circle-check"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger mb-3">
                    <ul class="mb-0 pl-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2">
                <i class="fas fa-paper-plane"></i>
                <span>Submit Review</span>
            </button>
        </form>
    </div>

    <div class="reviews-list" id="reviews-list">
        @if ($reviews->count() > 0)
            <h6 class="reviews-list-title">Verified Customer Reviews ({{ $totalReviews }})</h6>
            @foreach ($reviews as $review)
                @php
                    $userName = $review->user->name ?? 'Anonymous';
                    $userInitials = strtoupper(
                        substr($userName, 0, 1) .
                            (strlen($userName) > 1 && strpos($userName, ' ') !== false
                                ? substr($userName, strpos($userName, ' ') + 1, 1)
                                : ''),
                    );
                    $avatarSeed = $review->user_id ?? crc32($userName);
                    $avatarColors = ['3498db', 'e74c3c', '2ecc71', 'f39c12', '9b59b6', '1abc9c', 'e67e22', '34495e'];
                    $avatarColor = $avatarColors[abs($avatarSeed) % count($avatarColors)];
                    $avatarUrl =
                        'https://ui-avatars.com/api/?name=' .
                        urlencode($userName) .
                        '&background=' .
                        $avatarColor .
                        '&color=fff&size=64&bold=true&format=png';
                    $rating = $review->ratings->where('key', 'overall')->first()->value ?? 0;
                @endphp
                <div class="review-item">
                    <div class="d-flex align-items-start gap-3">
                        <div class="review-avatar flex-shrink-0">
                            <img src="{{ $avatarUrl }}" alt="{{ $userName }}" class="rounded-circle"
                                width="44" height="44"
                                onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'64\' height=\'64\'%3E%3Crect fill=\'%23ddd\' width=\'64\' height=\'64\'/%3E%3Ctext fill=\'%23999\' font-family=\'Arial\' font-size=\'24\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\'%3E{{ $userInitials }}%3C/text%3E%3C/svg%3E'">
                        </div>
                        <div class="review-content flex-grow-1 min-w-0">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <strong class="review-author-name">{{ $userName }}</strong>
                                    <span class="review-verified-badge">
                                        <i class="fas fa-circle-check"></i> Verified Buyer
                                    </span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="review-rating">
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= $rating)
                                                <i class="fa fa-star"></i>
                                            @else
                                                <i class="far fa-star" style="color: #cbd5e1 !important;"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <span class="review-date">{{ $review->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            <div class="review-text">{{ $review->review }}</div>
                            @if ($review->recommend)
                                <div class="mt-2">
                                    <span class="review-recommend-badge">
                                        <i class="fas fa-thumbs-up text-primary"></i> Recommends this product
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach

            @if ($totalReviews > $perPage)
                <div class="text-center mt-3">
                    <button type="button" class="btn btn-outline-primary" id="load-more-reviews"
                        data-product-slug="{{ $product->slug }}" data-page="1">
                        Load More Reviews
                    </button>
                </div>
            @endif
        @else
            <div class="reviews-empty-state">
                <div class="reviews-empty-icon">
                    <i class="fas fa-comments"></i>
                </div>
                <h6 class="reviews-empty-title">No Reviews Yet</h6>
                <p class="reviews-empty-text">Have you bought this product? Be the first to share your thoughts!</p>
                <a href="#review-form-container" class="btn btn-sm btn-primary" onclick="document.getElementById('order_id')?.focus()">
                    <i class="fas fa-pen-to-square mr-1"></i> Write a Review
                </a>
            </div>
        @endif
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Star rating interaction
            const ratingInputs = document.querySelectorAll('.rating-input .rating-radio');
            ratingInputs.forEach(input => {
                input.addEventListener('change', function() {
                    const value = parseInt(this.value);
                    const labels = document.querySelectorAll('.rating-input label');
                    labels.forEach((label, index) => {
                        const labelIndex = 5 - index;
                        if (labelIndex <= value) {
                            label.querySelector('i').classList.remove('far');
                            label.querySelector('i').classList.add('fa');
                        } else {
                            label.querySelector('i').classList.remove('fa');
                            label.querySelector('i').classList.add('far');
                        }
                    });
                });

                // Set initial state for checked inputs
                if (input.checked) {
                    const value = parseInt(input.value);
                    const labels = document.querySelectorAll('.rating-input label');
                    labels.forEach((label, index) => {
                        const labelIndex = 5 - index;
                        if (labelIndex <= value) {
                            label.querySelector('i').classList.remove('far');
                            label.querySelector('i').classList.add('fa');
                        }
                    });
                }
            });

            // Handle form validation - ensure rating is selected before submit
            const reviewForm = document.getElementById('review-form');
            if (reviewForm) {
                reviewForm.addEventListener('submit', function(e) {
                    const selectedRating = document.querySelector('.rating-input .rating-radio:checked');
                    if (!selectedRating) {
                        e.preventDefault();
                        e.stopPropagation();

                        // Show error message
                        const ratingGroup = document.querySelector('.rating-input').closest('.form-group');
                        let errorMsg = ratingGroup.querySelector('.rating-error');
                        if (!errorMsg) {
                            errorMsg = document.createElement('small');
                            errorMsg.className = 'text-danger d-block rating-error';
                            errorMsg.textContent = 'Please select a rating before submitting your review.';
                            ratingGroup.appendChild(errorMsg);
                        }

                        // Focus on the first rating input for accessibility
                        const firstRating = document.querySelector('.rating-input .rating-radio');
                        if (firstRating) {
                            firstRating.focus();
                        }

                        // Scroll to rating section
                        const ratingContainer = document.querySelector('.rating-input');
                        if (ratingContainer) {
                            ratingContainer.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });
                        }

                        return false;
                    } else {
                        // Remove error message if rating is selected
                        const ratingGroup = document.querySelector('.rating-input').closest('.form-group');
                        const errorMsg = ratingGroup.querySelector('.rating-error');
                        if (errorMsg) {
                            errorMsg.remove();
                        }
                    }
                });
            }

            // Load more reviews
            const loadMoreBtn = document.getElementById('load-more-reviews');
            if (loadMoreBtn) {
                loadMoreBtn.addEventListener('click', function() {
                    const productSlug = this.dataset.productSlug;
                    const page = parseInt(this.dataset.page) + 1;
                    const btn = this;

                    btn.disabled = true;
                    btn.textContent = 'Loading...';

                    fetch(`/products/${productSlug}/reviews?page=${page}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            const loadMoreBtn = document.getElementById('load-more-reviews');
                            const reviewsList = document.getElementById('reviews-list');

                            data.reviews.forEach(review => {
                                const rating = review.ratings && review.ratings.length > 0 ?
                                    review.ratings.find(r => r.key === 'overall')?.value || 0 :
                                    0;
                                const userName = review.user && review.user.name ? review.user
                                    .name : 'Anonymous';
                                const userInitials = userName.length > 0 ?
                                    userName.charAt(0).toUpperCase() + (userName.indexOf(' ') >
                                        0 ? userName.charAt(userName.indexOf(' ') + 1)
                                        .toUpperCase() : '') :
                                    'A';
                                const avatarSeed = review.user_id || review.user?.id || 0;
                                const avatarColors = ['3498db', 'e74c3c', '2ecc71', 'f39c12',
                                    '9b59b6', '1abc9c', 'e67e22', '34495e'
                                ];
                                let hash = 0;
                                const seedStr = String(avatarSeed || userName);
                                for (let i = 0; i < seedStr.length; i++) {
                                    hash = ((hash << 5) - hash) + seedStr.charCodeAt(i);
                                    hash = hash & hash;
                                }
                                const avatarColor = avatarColors[Math.abs(hash) % avatarColors
                                    .length];
                                const avatarUrl =
                                    `https://ui-avatars.com/api/?name=${encodeURIComponent(userName)}&background=${avatarColor}&color=fff&size=64&bold=true&format=png`;
                                const fallbackAvatar =
                                    `data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='64' height='64'%3E%3Crect fill='%23ddd' width='64' height='64'/%3E%3Ctext fill='%23999' font-family='Arial' font-size='24' x='50%25' y='50%25' text-anchor='middle' dy='.3em'%3E${userInitials}%3C/text%3E%3C/svg%3E`;

                                const reviewHtml = `
                            <div class="p-2 review-item border-bottom">
                                <div class="d-flex">
                                    <div class="mr-3 review-avatar">
                                        <img src="${avatarUrl}" alt="${userName}" class="rounded-circle" width="48" height="48" onerror="this.src='${fallbackAvatar}'">
                                    </div>
                                    <div class="review-content flex-grow-1">
                                        <div class="mb-2 d-flex justify-content-between align-items-start">
                                            <div>
                                                <strong class="review-author-name">${escapeHtml(userName)}</strong>
                                                <span class="ml-2 text-muted small review-date">${escapeHtml(review.created_at)}</span>
                                            </div>
                                            <div class="d-flex align-items-center review-rating">
                                                ${generateStars(rating)}
                                            </div>
                                        </div>
                                        <p class="mb-2 review-text">${escapeHtml(review.review)}</p>
                                        ${review.recommend ? '<span class="badge badge-success review-recommend-badge"><i class="fa fa-check"></i> Recommended</span>' : ''}
                                    </div>
                                </div>
                            </div>
                        `;
                                if (loadMoreBtn) {
                                    loadMoreBtn.insertAdjacentHTML('beforebegin', reviewHtml);
                                } else {
                                    reviewsList.insertAdjacentHTML('beforeend', reviewHtml);
                                }
                            });

                            if (data.pagination.has_more) {
                                btn.dataset.page = page;
                                btn.disabled = false;
                                btn.textContent = 'Load More Reviews';
                            } else {
                                btn.remove();
                            }
                        })
                        .catch(error => {
                            console.error('Error loading reviews:', error);
                            btn.disabled = false;
                            btn.textContent = 'Load More Reviews';
                        });
                });
            }

            function generateStars(rating) {
                let html = '';
                for (let i = 1; i <= 5; i++) {
                    if (i <= rating) {
                        html += '<i class="fa fa-star text-warning"></i>';
                    } else {
                        html += '<i class="far fa-star text-muted"></i>';
                    }
                }
                return html;
            }

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            // Scroll to review form only if review was just submitted
            const reviewFormContainer = document.getElementById('review-form-container');
            if (reviewFormContainer) {
                // Check if review was submitted by checking the data attribute set from session flash
                const reviewSubmitted = reviewFormContainer.getAttribute('data-review-submitted') === 'true';
                const hasSuccess = reviewFormContainer.querySelector('.alert-success');
                const hasErrors = reviewFormContainer.querySelector('.alert-danger') || reviewFormContainer
                    .querySelector('.text-danger');

                // Only scroll if review was submitted AND we have success/error messages
                const shouldScroll = reviewSubmitted && (hasSuccess || hasErrors);

                if (shouldScroll) {
                    // Check if the reviews accordion is collapsed and expand it
                    const collapseThree = document.getElementById('collapseThree');
                    let needsExpansion = false;

                    if (collapseThree && collapseThree.classList.contains('collapse') && !collapseThree.classList
                        .contains('show')) {
                        needsExpansion = true;
                        // Expand the accordion if it's collapsed (using jQuery if available, otherwise Bootstrap 5)
                        if (typeof jQuery !== 'undefined' && jQuery.fn.collapse) {
                            jQuery(collapseThree).collapse('show');
                        } else if (typeof bootstrap !== 'undefined' && bootstrap.Collapse) {
                            const bsCollapse = new bootstrap.Collapse(collapseThree, {
                                toggle: true
                            });
                        } else {
                            // Fallback: manually add show class
                            collapseThree.classList.add('show');
                        }
                    }

                    // Scroll to the form after a delay to allow accordion to expand
                    setTimeout(function() {
                        reviewFormContainer.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start',
                            inline: 'nearest'
                        });
                    }, needsExpansion ? 400 : 100);
                }
            }
        });
    </script>
@endpush
