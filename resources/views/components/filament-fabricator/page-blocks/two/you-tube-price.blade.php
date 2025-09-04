@aware(['page'])

<style>
.youtube-price-section {
    padding: 2rem 0;
    background-color: #ffffff;
}

.youtube-price-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

.youtube-price-content {
    max-width: 800px;
    margin: 0 auto;
    text-align: center;
}

.youtube-price-video {
    margin-bottom: 2rem;
}

.youtube-price-video-container {
    position: relative;
    width: 100%;
    /* max-width: 600px; */
    margin: 0 auto;
    padding-bottom: 56.25%;
}

.youtube-price-iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border-radius: 0.5rem;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.youtube-price-title {
    font-size: 2.5rem;
    font-weight: bold;
    color: #1f2937;
    margin-bottom: 1.5rem;
    line-height: 1.2;
}

.youtube-price-description {
    font-size: 1.125rem;
    color: #6b7280;
    margin-bottom: 2rem;
    line-height: 1.6;
}

.youtube-price-list {
    list-style: none;
    padding: 0;
    margin: 0 0 2rem 0;
    background: #f8fafc;
    border-radius: 0.75rem;
    padding: 1.5rem;
    border: 1px solid #e2e8f0;
}

.youtube-price-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 0;
    border-bottom: 1px solid #e2e8f0;
}

.youtube-price-item:last-child {
    border-bottom: none;
}

.youtube-price-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
}

.youtube-price-title-text {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1f2937;
}

.youtube-price-separator {
    flex: 1;
    height: 1px;
    background-color: #d1d5db;
    margin: 0 1rem;
}

.youtube-price-amount {
    font-size: 1.5rem;
    font-weight: bold;
    color: #059669;
}

.youtube-price-description-text {
    font-size: 0.875rem;
    color: #6b7280;
    margin-top: 0.5rem;
    text-align: left;
}

.youtube-price-button {
    display: inline-block;
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    padding: 1rem 2rem;
    font-size: 1.125rem;
    font-weight: 600;
    text-decoration: none;
    border-radius: 0.5rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
}

.youtube-price-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
    color: white;
    text-decoration: none;
}

@media (max-width: 768px) {
    .youtube-price-title {
        font-size: 2rem;
    }

    .youtube-price-description {
        font-size: 1rem;
    }

    .youtube-price-title-text {
        font-size: 1.125rem;
    }

    .youtube-price-amount {
        font-size: 1.25rem;
    }

    .youtube-price-button {
        padding: 0.875rem 1.5rem;
        font-size: 1rem;
    }
}
</style>

<section class="youtube-price-section">
    <div class="youtube-price-container">
        <div class="youtube-price-content">
            <!-- YouTube Video -->
            <div class="youtube-price-video">
                @if($youtubeLink ?? false)
                    @php
                        // Extract video ID from YouTube URL
                        $videoId = '';
                        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $youtubeLink, $matches)) {
                            $videoId = $matches[1];
                        }
                    @endphp

                    @if($videoId)
                        <div class="youtube-price-video-container">
                            <iframe
                                class="youtube-price-iframe"
                                src="https://www.youtube.com/embed/{{ $videoId }}?rel=0&modestbranding=1&autoplay=1&loop=1&playlist={{ $videoId }}"
                                title="YouTube video player"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen>
                            </iframe>
                        </div>
                    @else
                        <div style="padding: 2rem; background: #f3f4f6; border-radius: 0.5rem; color: #6b7280;">
                            <p>Invalid YouTube URL provided</p>
                        </div>
                    @endif
                @else
                    <div style="padding: 2rem; background: #f3f4f6; border-radius: 0.5rem; color: #6b7280;">
                        <p>No YouTube link provided</p>
                    </div>
                @endif
            </div>

            <!-- Title -->
            @if($title ?? false)
                <h2 class="youtube-price-title">{{ $title }}</h2>
            @endif

            <!-- Description -->
            @if($description ?? false)
                <div class="youtube-price-description">
                    {!! $description !!}
                </div>
            @endif

            <!-- Price List -->
            @if(($priceText ?? false) && ($priceAmount ?? false))
                <ul class="youtube-price-list">
                    <li class="youtube-price-item">
                        <div class="youtube-price-header">
                            <span class="youtube-price-title-text">{{ $priceText }}</span>
                            <span class="youtube-price-separator"></span>
                            <span class="youtube-price-amount">৳{{ $priceAmount }}</span>
                        </div>
                        @if($priceSubtext ?? false)
                            <p class="youtube-price-description-text">{{ $priceSubtext }}</p>
                        @endif
                    </li>
                </ul>
            @endif

            <!-- Order Button -->
            <a href="#order" class="youtube-price-button">
                অর্ডার করতে চাই
            </a>
        </div>
    </div>
</section>
