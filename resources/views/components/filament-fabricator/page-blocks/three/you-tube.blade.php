@aware(['page'])

<style>
.youtube-section {
    padding: 2rem 0;
    background-color: #f9fafb;
}

.youtube-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

.youtube-content {
    max-width: 48rem;
    margin: 0 auto;
}

.youtube-video-wrapper {
    margin-bottom: 1.5rem;
}

.youtube-video-container {
    position: relative;
    width: 100%;
    /* max-width: 32rem; */
    margin: 0 auto;
    padding-bottom: 56.25%;
}

.youtube-iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border-radius: 0.5rem;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.youtube-error {
    padding: 2rem;
    margin: 0 auto;
    max-width: 28rem;
    text-align: center;
    background-color: #e5e7eb;
    border-radius: 0.5rem;
}

.youtube-error p {
    color: #6b7280;
    margin: 0;
}

.youtube-button-wrapper {
    text-align: center;
}

.youtube-button {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
    font-weight: 600;
    color: white;
    background-color: #2563eb;
    border-radius: 0.5rem;
    text-decoration: none;
    transition: background-color 0.2s ease;
}

.youtube-button:hover {
    background-color: #1d4ed8;
}
</style>

<section class="youtube-section">
    <div class="youtube-container">
        <div class="youtube-content">
            <!-- YouTube Video -->
            <div class="youtube-video-wrapper">
                @if($youtubeLink ?? false)
                    @php
                        // Extract video ID from YouTube URL
                        $videoId = '';
                        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $youtubeLink, $matches)) {
                            $videoId = $matches[1];
                        }
                    @endphp

                    @if($videoId)
                        <div class="youtube-video-container">
                            <iframe
                                class="youtube-iframe"
                                src="https://www.youtube.com/embed/{{ $videoId }}?rel=0&modestbranding=1"
                                title="YouTube video player"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen>
                            </iframe>
                        </div>
                    @else
                        <div class="youtube-error">
                            <p>Invalid YouTube URL provided</p>
                        </div>
                    @endif
                @else
                    <div class="youtube-error">
                        <p>No YouTube link provided</p>
                    </div>
                @endif
            </div>

            <!-- Order Button -->
            <div class="youtube-button-wrapper">
                <a href="#order" class="youtube-button">
{{ __('button.order_now') }}
                </a>
            </div>
        </div>
    </div>
</section>
