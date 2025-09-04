@aware(['page'])

<style>
.countdown-section {
    padding: 3rem 0;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    text-align: center;
}

.countdown-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

.countdown-wrapper {
    display: flex;
    justify-content: center;
    gap: 2rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}

.countdown-item {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 0.5rem;
    padding: 1.5rem 1rem;
    min-width: 100px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.countdown-digits {
    display: block;
    font-size: 2.5rem;
    font-weight: bold;
    line-height: 1;
    margin-bottom: 0.5rem;
    color: #fff;
}

.countdown-label {
    display: block;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    opacity: 0.9;
}

.countdown-content {
    max-width: 800px;
    margin: 0 auto;
}

.countdown-title {
    font-size: 2.5rem;
    font-weight: bold;
    margin-bottom: 1rem;
    line-height: 1.2;
}

.countdown-subtitle {
    font-size: 1.5rem;
    margin-bottom: 1rem;
    opacity: 0.9;
    line-height: 1.3;
}

.countdown-note {
    font-size: 1.125rem;
    opacity: 0.8;
    line-height: 1.4;
}

@media (max-width: 768px) {
    .countdown-wrapper {
        gap: 1rem;
    }

    .countdown-item {
        min-width: 80px;
        padding: 1rem 0.75rem;
    }

    .countdown-digits {
        font-size: 2rem;
    }

    .countdown-title {
        font-size: 2rem;
    }

    .countdown-subtitle {
        font-size: 1.25rem;
    }
}
</style>

<section class="countdown-section">
    <div class="countdown-container">
        <!-- Countdown Timer -->
        <div class="countdown-wrapper" id="countdown-timer" data-deadline="{{ strtotime($deadline) }}">
            <div class="countdown-item">
                <span class="countdown-digits" id="days">00</span>
                <span class="countdown-label">Days</span>
            </div>
            <div class="countdown-item">
                <span class="countdown-digits" id="hours">00</span>
                <span class="countdown-label">Hours</span>
            </div>
            <div class="countdown-item">
                <span class="countdown-digits" id="minutes">00</span>
                <span class="countdown-label">Minutes</span>
            </div>
            <div class="countdown-item">
                <span class="countdown-digits" id="seconds">00</span>
                <span class="countdown-label">Seconds</span>
            </div>
        </div>

        <!-- Content -->
        <div class="countdown-content">
            @if($title ?? false)
                <h2 class="countdown-title">{{ $title }}</h2>
            @endif

            @if($subtitle ?? false)
                <h2 class="countdown-subtitle">{{ $subtitle }}</h2>
            @endif

            @if($note ?? false)
                <h2 class="countdown-note">{{ $note }}</h2>
            @endif
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const countdownElement = document.getElementById('countdown-timer');
    const deadline = parseInt(countdownElement.getAttribute('data-deadline')) * 1000; // Convert to milliseconds

    function updateCountdown() {
        const now = new Date().getTime();
        const timeLeft = deadline - now;

        if (timeLeft < 0) {
            // Countdown finished
            document.getElementById('days').textContent = '00';
            document.getElementById('hours').textContent = '00';
            document.getElementById('minutes').textContent = '00';
            document.getElementById('seconds').textContent = '00';
            return;
        }

        // Calculate time units
        const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
        const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

        // Update display
        document.getElementById('days').textContent = days.toString().padStart(2, '0');
        document.getElementById('hours').textContent = hours.toString().padStart(2, '0');
        document.getElementById('minutes').textContent = minutes.toString().padStart(2, '0');
        document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');
    }

    // Update immediately
    updateCountdown();

    // Update every second
    setInterval(updateCountdown, 1000);
});
</script>
