@props([
    'barClass' => 'bg-warning',
    'trackClass' => 'bg-white/50',
    'heightClass' => 'h-1',
])

<div
    x-data="livewireNavigateProgress()"
    x-init="register()"
    x-show="navigating"
    x-transition.opacity
    x-cloak
    class="fixed top-0 left-0 right-0 z-50 pointer-events-none"
>
    <div class="{{ $heightClass }} {{ $trackClass }}">
        <div
            class="{{ $heightClass }} {{ $barClass }} transition-all duration-200 ease-out"
            :style="{ width: progress + '%' }"
        ></div>
    </div>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('livewireNavigateProgress', () => ({
                    navigating: false,
                    progress: 0,
                    register() {
                        document.addEventListener('livewire:navigate', () => this.start());
                        document.addEventListener('livewire:navigated', () => this.finish());
                    },
                    start() {
                        this.navigating = true;
                        this.progress = 12;
                        this.bump();
                    },
                    bump() {
                        if (! this.navigating || this.progress >= 90) {
                            return;
                        }

                        this.progress += 8;
                        setTimeout(() => this.bump(), 180);
                    },
                    finish() {
                        this.progress = 100;
                        setTimeout(() => {
                            this.navigating = false;
                            this.progress = 0;
                        }, 150);
                    },
                }));
            });
        </script>
    @endpush
@endonce

