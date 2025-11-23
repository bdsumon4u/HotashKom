@props([
    'barClass' => 'bg-warning',
    'trackClass' => 'bg-white/50',
    'heightClass' => 'h-1 md:h-0.5',
])

<div
    x-data="livewireNavigateProgress()"
    x-init="register()"
    x-show="navigating"
    x-transition.opacity
    x-cloak
    class="fixed top-0 left-0 right-0 z-[9999] pointer-events-none"
    style="height: 3px;"
>
    <div class="h-full {{ $trackClass }}" style="background: rgba(255, 255, 255, 0.3);">
        <div
            class="h-full {{ $barClass }} transition-all duration-200 ease-out shadow-lg"
            :style="{ width: progress + '%' }"
            style="box-shadow: 0 0 10px rgba(255, 193, 7, 0.5);"
        ></div>
    </div>
</div>

{{-- Mobile loading overlay --}}
<div
    x-data="mobileLoadingOverlay()"
    x-show="show"
    x-transition.opacity
    x-cloak
    class="fixed inset-0 z-[9998] bg-black/10 backdrop-blur-sm pointer-events-none md:hidden"
    style="display: none;"
    id="mobile-loading-overlay"
></div>

@once
    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('livewireNavigateProgress', () => ({
                    navigating: false,
                    progress: 0,
                    clickTimeout: null,

                    register() {
                        // Listen to Livewire navigation events
                        document.addEventListener('livewire:navigate', () => this.start());
                        document.addEventListener('livewire:navigated', () => this.finish());
                        document.addEventListener('livewire:navigate-error', (e) => {
                            this.finish();
                            this.handleNavigationError(e);
                        });

                        // Intercept clicks on links with wire:navigate for immediate feedback
                        this.interceptClicks();
                        
                        // Add timeout protection for navigation
                        this.setupNavigationTimeout();
                    },
                    
                    setupNavigationTimeout() {
                        let navigationTimeout = null;
                        
                        document.addEventListener('livewire:navigate', () => {
                            // Set a timeout for navigation (3 seconds)
                            navigationTimeout = setTimeout(() => {
                                console.warn('Livewire navigation timeout - falling back to full page reload');
                                if (window.Livewire && window.Livewire.history && window.Livewire.history.currentUrl) {
                                    window.location.href = window.Livewire.history.currentUrl;
                                }
                            }, 3000);
                        });
                        
                        document.addEventListener('livewire:navigated', () => {
                            if (navigationTimeout) {
                                clearTimeout(navigationTimeout);
                                navigationTimeout = null;
                            }
                        });
                        
                        document.addEventListener('livewire:navigate-error', () => {
                            if (navigationTimeout) {
                                clearTimeout(navigationTimeout);
                                navigationTimeout = null;
                            }
                        });
                    },
                    
                    handleNavigationError(e) {
                        console.error('Livewire navigation error:', e);
                        
                        // If it's a server error (520, 522, 500, etc.), fall back to full page reload
                        const error = e.detail?.error || e.error;
                        if (error) {
                            const status = error.status || error.response?.status;
                            if (status >= 500 || status === 0) {
                                // Server error or network error - reload the page
                                const targetUrl = e.detail?.url || window.location.href;
                                console.warn('Server error during navigation, reloading page:', targetUrl);
                                setTimeout(() => {
                                    window.location.href = targetUrl;
                                }, 1000);
                            }
                        }
                    },

                    interceptClicks() {
                        document.addEventListener('click', (e) => {
                            // Find the closest link that has any wire:navigate attribute
                            // This handles clicks on child elements (like images inside links)
                            let link = e.target.closest('a');

                            if (!link) {
                                return;
                            }

                            // Check if the link has any wire:navigate attribute (with any modifier)
                            const hasWireNavigate = Array.from(link.attributes).some(attr =>
                                attr.name.startsWith('wire:navigate')
                            );

                            if (!hasWireNavigate) {
                                return;
                            }

                            const href = link.getAttribute('href');

                            // Skip external links, anchors, and special links
                            if (!href || href.startsWith('#') || href.startsWith('tel:') || href.startsWith('mailto:') || href.startsWith('javascript:')) {
                                return;
                            }

                            // Skip if it's an external URL
                            try {
                                const url = new URL(href, window.location.origin);
                                if (url.origin !== window.location.origin) {
                                    return;
                                }
                            } catch {
                                return;
                            }

                            // Start progress immediately on click
                            this.start();

                            // Clear any existing timeout
                            if (this.clickTimeout) {
                                clearTimeout(this.clickTimeout);
                            }

                            // Fallback: if Livewire doesn't navigate within 500ms, finish
                            this.clickTimeout = setTimeout(() => {
                                if (this.navigating) {
                                    this.finish();
                                }
                            }, 500);
                        }, true); // Use capture phase for earlier interception
                    },

                    start() {
                        this.navigating = true;
                        this.progress = 10;
                        this.bump();

                        // Show mobile overlay
                        this.showMobileOverlay();
                    },

                    showMobileOverlay() {
                        const overlay = document.getElementById('mobile-loading-overlay');
                        if (overlay && overlay._x_dataStack && overlay._x_dataStack[0]) {
                            overlay._x_dataStack[0].show = true;
                        }
                    },

                    hideMobileOverlay() {
                        const overlay = document.getElementById('mobile-loading-overlay');
                        if (overlay && overlay._x_dataStack && overlay._x_dataStack[0]) {
                            overlay._x_dataStack[0].show = false;
                        }
                    },

                    bump() {
                        if (! this.navigating || this.progress >= 90) {
                            return;
                        }

                        this.progress += Math.min(8, (90 - this.progress) * 0.15);
                        setTimeout(() => this.bump(), 150);
                    },

                    finish() {
                        this.progress = 100;

                        setTimeout(() => {
                            this.navigating = false;
                            this.progress = 0;

                            // Hide mobile overlay
                            this.hideMobileOverlay();

                            if (this.clickTimeout) {
                                clearTimeout(this.clickTimeout);
                                this.clickTimeout = null;
                            }
                        }, 200);
                    },
                }));

                // Mobile loading overlay component
                Alpine.data('mobileLoadingOverlay', () => ({
                    show: false,
                }));
            });
        </script>
    @endpush
@endonce

