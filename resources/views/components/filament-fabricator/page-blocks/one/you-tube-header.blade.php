@aware(['page'])

<style>
.youtube-header-section {
    position: relative;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 4rem 0 6rem 0;
    overflow: hidden;
}

.youtube-header-section::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 60px;
    background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 283.5 27.8' preserveAspectRatio='none'%3E%3Cpath fill='%23ffffff' d='M283.5,9.7c0,0-7.3,4.3-14,4.6c-6.8,0.3-12.6,0-20.9-1.5c-11.3-2-33.1-10.1-44.7-5.7s-12.1,4.6-18,7.4c-6.6,3.2-20,9.6-36.6,9.3C131.6,23.5,99.5,7.2,86.3,8c-1.4,0.1-6.6,0.8-10.5,2c-3.8,1.2-9.4,3.8-17,4.7c-3.2,0.4-8.3,1.1-14.2,0.9c-1.5-0.1-6.3-0.4-12-1.6c-5.7-1.2-11-3.1-15.8-3.7C6.5,9.2,0,10.8,0,10.8V0h283.5V9.7z'/%3E%3C/svg%3E") no-repeat center bottom;
    background-size: cover;
}

.youtube-header-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
    position: relative;
    z-index: 2;
}

.youtube-header-content {
    text-align: center;
    max-width: 800px;
    margin: 0 auto;
}

.youtube-header-headline {
    font-size: 3rem;
    font-weight: bold;
    margin-bottom: 1.5rem;
    line-height: 1.2;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.youtube-header-headline .highlight {
    background: linear-gradient(45deg, #ffd700, #ffed4e);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    position: relative;
    display: inline-block;
}

.youtube-header-headline .highlight::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(45deg, #ffd700, #ffed4e);
    border-radius: 2px;
    animation: highlightPulse 2s ease-in-out infinite;
}

@keyframes highlightPulse {
    0%, 100% { opacity: 0.7; transform: scaleX(1); }
    50% { opacity: 1; transform: scaleX(1.05); }
}

.youtube-header-description {
    font-size: 1.25rem;
    margin-bottom: 2rem;
    line-height: 1.6;
    opacity: 0.95;
}

.youtube-header-video {
    margin: 2rem 0;
}

.youtube-header-video-container {
    position: relative;
    width: 100%;
    /* max-width: 600px; */
    margin: 0 auto;
    padding-bottom: 56.25%;
    border-radius: 1rem;
    overflow: hidden;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
}

.youtube-header-iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border: none;
}

.youtube-header-button {
    display: inline-block;
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
    color: white;
    padding: 1.25rem 2.5rem;
    font-size: 1.25rem;
    font-weight: 600;
    text-decoration: none;
    border-radius: 50px;
    transition: all 0.3s ease;
    box-shadow: 0 8px 25px rgba(255, 107, 107, 0.4);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.youtube-header-button:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 35px rgba(255, 107, 107, 0.6);
    color: white;
    text-decoration: none;
}

@media (max-width: 768px) {
    .youtube-header-section {
        padding: 3rem 0 5rem 0;
    }

    .youtube-header-headline {
        font-size: 2.25rem;
    }

    .youtube-header-description {
        font-size: 1.125rem;
    }

    .youtube-header-button {
        padding: 1rem 2rem;
        font-size: 1.125rem;
    }
}
</style>

<section class="youtube-header-section">
    <div class="youtube-header-container">
        <div class="youtube-header-content">
            <!-- Animated Headline -->
            @if($headline ?? false)
                <h3 class="youtube-header-headline">
                    {!! $headline !!}
                </h3>
            @endif

            <!-- Description -->
            @if($description ?? false)
                <div class="youtube-header-description">
                    {!! $description !!}
                </div>
            @endif

            <!-- YouTube Video -->
            @if($youtubeLink ?? false)
                <div class="youtube-header-video">
                    @php
                        // Extract video ID from YouTube URL
                        $videoId = '';
                        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $youtubeLink, $matches)) {
                            $videoId = $matches[1];
                        }
                    @endphp

                    @if($videoId)
                        <div class="youtube-header-video-container">
                            <iframe
                                class="youtube-header-iframe"
                                src="https://www.youtube.com/embed/{{ $videoId }}?rel=0&modestbranding=1&autoplay=1&play_on_mobile=1"
                                title="YouTube video player"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen>
                            </iframe>
                        </div>
                    @else
                        <div style="padding: 2rem; background: rgba(255, 255, 255, 0.1); border-radius: 1rem; color: rgba(255, 255, 255, 0.8);">
                            <p>Invalid YouTube URL provided</p>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Order Button -->
            <a href="#order" class="youtube-header-button">
                অর্ডার করতে চাই
            </a>
        </div>
    </div>
</section>
