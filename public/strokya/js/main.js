(function ($) {
    "use strict";

    let passiveSupported = false;

    try {
        const options = Object.defineProperty({}, 'passive', {
            get: function() {
                passiveSupported = true;
            }
        });

        window.addEventListener('test', null, options);
    } catch(err) {}


    /*
    // initialize custom numbers
    */
    // $(function () {
    //     $('.input-number').customNumber();
    // });


    /*
    // topbar dropdown
    */
    $(function() {
        $('.topbar-dropdown__btn').on('click', function() {
            $(this).closest('.topbar-dropdown').toggleClass('topbar-dropdown--opened');
        });

        $(document).on('click', function (event) {
            $('.topbar-dropdown')
                .not($(event.target).closest('.topbar-dropdown'))
                .removeClass('topbar-dropdown--opened');
        });
    });


    /*
    // dropcart, drop search
    */
    $(function() {
        $('.indicator--trigger--click .indicator__button').on('click', function(event) {
            event.preventDefault();

            const dropdown = $(this).closest('.indicator');

            if (dropdown.is('.indicator--opened')) {
                dropdown.removeClass('indicator--opened');
            } else {
                dropdown.addClass('indicator--opened');
                dropdown.find('.drop-search__input').focus();
            }
        });

        $('.indicator--trigger--click .drop-search__input').on('keydown', function(event) {
            if (event.which === 27) {
                $(this).closest('.indicator').removeClass('indicator--opened');
            }
        });

        $(document).on('click', function (event) {
            if ($(event.target).closest('.xzoom-container, .xzoom-thumbs, .zoom-control, .xzoom-preview, .xzoom-lens, .xzoom-source, .xzoom-loading, .xzoom-caption').length) {
                return;
            }
            $('.indicator')
                .not($(event.target).closest('.indicator'))
                .removeClass('indicator--opened');
        });
    });


    /*
    // megamenu position
    */
    $(function() {
        $('.nav-panel__nav-links .nav-links__item').on('mouseenter', function() {
            const megamenu = $(this).find('.nav-links__megamenu');

            if (megamenu.length) {
                const container = megamenu.offsetParent();
                const containerWidth = container.width();
                const megamenuWidth = megamenu.width();
                const itemPosition = $(this).position().left;
                const megamenuPosition = Math.round(Math.min(itemPosition, containerWidth - megamenuWidth));

                megamenu.css('left', megamenuPosition + 'px');
            }
        });
    });


    /*
    // mobile search
    */
    $(function() {
        const mobileSearch = $('.mobile-header__search');

        if (mobileSearch.length) {
            $('.indicator--mobile-search .indicator__button').on('click', function() {
                if (mobileSearch.is('.mobile-header__search--opened')) {
                    mobileSearch.removeClass('mobile-header__search--opened');
                } else {
                    mobileSearch.addClass('mobile-header__search--opened');
                    mobileSearch.find('input')[0].focus();
                }
            });

            mobileSearch.find('.mobile-header__search-button--close').on('click', function() {
                mobileSearch.removeClass('mobile-header__search--opened');
            });

            document.addEventListener('click', function(event) {
                if (!$(event.target).closest('.indicator--mobile-search, .mobile-header__search').length) {
                    mobileSearch.removeClass('mobile-header__search--opened');
                }
            }, true);
        }
    });


    /*
    // departments, sticky header
    */
    $(function() {
        /*
        // departments
        */
        const CDepartments = function(element) {
            const self = this;

            element.data('departmentsInstance', self);

            this.element = element;
            this.body = this.element.find('.departments__body');
            this.button = this.element.find('.departments__button');
            this.mode = this.element.is('.departments--fixed') ? 'fixed' : 'normal';
            this.fixedBy = $(this.element.data('departments-fixed-by'));
            this.fixedHeight = 0;

            if (this.mode === 'fixed' && this.fixedBy.length) {
                this.fixedHeight = this.fixedBy.offset().top - this.body.offset().top + this.fixedBy.outerHeight();
                this.body.css('height', this.fixedHeight + 'px');
            }

            this.button.on('click', function(event) {
                self.clickOnButton(event);
            });

            $('.departments__links-wrapper', this.element).on('transitionend', function (event) {
                if (event.originalEvent.propertyName === 'height') {
                    $(this).css('height', '');
                    $(this).closest('.departments').removeClass('departments--transition');
                }
            });

            document.addEventListener('click', function(event) {
                self.element.not($(event.target).closest('.departments')).each(function() {
                    if (self.element.is('.departments--opened')) {
                        self.element.data('departmentsInstance').close();
                    }
                });
            }, true);
        };
        CDepartments.prototype.clickOnButton = function(event) {
            event.preventDefault();

            if (this.element.is('.departments--opened')) {
                this.close();
            } else {
                this.open();
            }
        };
        CDepartments.prototype.setMode = function(mode) {
            this.mode = mode;

            if (this.mode === 'normal') {
                this.element.removeClass('departments--fixed');
                this.element.removeClass('departments--opened');
                this.body.css('height', 'auto');
            }
            if (this.mode === 'fixed') {
                this.element.addClass('departments--fixed');
                this.element.addClass('departments--opened');
                this.body.css('height', this.fixedHeight + 'px');
            }
        };
        CDepartments.prototype.close = function() {
            if (this.element.is('.departments--fixed')) {
                return;
            }

            const content = this.element.find('.departments__links-wrapper');
            const startHeight = content.height();

            content.css('height', startHeight + 'px');
            this.element
                .addClass('departments--transition')
                .removeClass('departments--opened');

            content.css('height', '');
        };
        CDepartments.prototype.open = function() {
            const content = this.element.find('.departments__links-wrapper');
            const startHeight = content.height();

            this.element
                .addClass('departments--transition')
                .addClass('departments--opened');

            const endHeight = content.height();

            content.css('height', startHeight + 'px');
            content.css('height', endHeight + 'px');
        };

        const departments = new CDepartments($('.departments'));


        /*
        // sticky header
        */
        const nav = $('.nav-panel--sticky');

        if (nav.length) {
            const departmentsMode = departments.mode;
            const defaultPosition = nav.offset().top;
            let stuck = false;

            window.addEventListener('scroll', function() {
                if (window.pageYOffset > defaultPosition) {
                    if (!stuck) {
                        nav.addClass('nav-panel--stuck');
                        stuck = true;

                        if (departmentsMode === 'fixed') {
                            departments.setMode('normal');
                        }
                    }
                } else {
                    if (stuck) {
                        nav.removeClass('nav-panel--stuck');
                        stuck = false;

                        if (departmentsMode === 'fixed') {
                            departments.setMode('fixed');
                        }
                    }
                }
            }, passiveSupported ? {passive: true} : false);
        }
    });


    /*
    // block slideshow
    */
    // hk-carousel-init-guard
    $(function() {
        $('.block-slideshow .owl-carousel').each(function() {
            const owl = $(this);

            if (owl.data('owl.carousel')) {
                owl.trigger('play.owl.autoplay', [3000]);
                return;
            }

            if (owl.hasClass('owl-loaded')) {
                const restoredItems = owl
                    .find('> .owl-stage-outer > .owl-stage > .owl-item:not(.cloned)')
                    .children()
                    .detach();

                if (restoredItems.length) {
                    owl.empty().append(restoredItems);
                }

                owl.removeClass('owl-loaded owl-hidden owl-refresh owl-drag')
                    .removeAttr('style');
            }

            owl.owlCarousel({
                items: 1,
                nav: true,
                dots: true,
                loop: true,
                autoplay: true,
                autoplayTimeout: 3000,
                autoplaySpeed: 500,
                autoplayHoverPause: true,
            });

            owl.trigger('play.owl.autoplay', [3000]);
        });
    });

    /*
    // products carousel
    */
    $(function() {
        $('.block-products-carousel').each(function() {
            const layout = $(this).data('layout');
            const options = {
                items: 4,
                margin: 14,
                nav: false,
                dots: false,
                loop: false,
                autoplay: true,
                autoplayTimeout: 3000,
                autoplaySpeed: 500,
                stagePadding: 1
            };
            const layoutOptions = {
                'grid-cat': {
                    responsive: {
                        1200: {items: 8, margin: 14},
                        992:  {items: 8, margin: 10},
                        768:  {items: 5, margin: 10},
                        576:  {items: 5, margin: 5},
                        475:  {items: 3, margin: 5},
                        0:    {items: 3, margin: 5}
                    }
                },
                'grid-4': {
                    responsive: {
                        1200: {items: 4, margin: 14},
                        992:  {items: 4, margin: 10},
                        768:  {items: 3, margin: 10},
                        576:  {items: 2, margin: 10},
                        475:  {items: 2, margin: 5},
                        0:    {items: 2, margin: 5}
                    }
                },
                'grid-4-sm': {
                    responsive: {
                        1200: {items: 4, margin: 14},
                        992:  {items: 3, margin: 10},
                        768:  {items: 3, margin: 10},
                        576:  {items: 2, margin: 10},
                        475:  {items: 2, margin: 5},
                        0:    {items: 1, margin: 5}
                    }
                },
                'grid-5': {
                    responsive: {
                        1200: {items: 5, margin: 12},
                        992:  {items: 4, margin: 10},
                        768:  {items: 3, margin: 10},
                        576:  {items: 2, margin: 10},
                        475:  {items: 2, margin: 5},
                        0:    {items: 2, margin: 5}
                    }
                },
                'horizontal': {
                    items: 3,
                    responsive: {
                        1200: {items: 3, margin: 14},
                        992:  {items: 3, margin: 10},
                        768:  {items: 2, margin: 10},
                        576:  {items: 1, margin: 5},
                        475:  {items: 1, margin: 5},
                        0:    {items: 1, margin: 5}
                    }
                },
            };
            const owl = $('.owl-carousel', this);

            // Prevent a second Owl instance and recover stale Back/Forward carousel markup.
            if (owl.data('owl.carousel')) {
                return;
            }

            if (owl.hasClass('owl-loaded')) {
                const restoredItems = owl
                    .find('> .owl-stage-outer > .owl-stage > .owl-item:not(.cloned)')
                    .children()
                    .detach();

                if (restoredItems.length) {
                    owl.empty().append(restoredItems);
                }

                owl.removeClass('owl-loaded owl-hidden owl-refresh owl-drag')
                    .removeAttr('style');
            }

            let cancelPreviousTabChange = function() {};

            owl.owlCarousel($.extend({}, options, layoutOptions[layout]));

            $(this).find('.block-header__group').on('click', function(event) {
                const block = $(this).closest('.block-products-carousel');

                event.preventDefault();

                if ($(this).is('.block-header__group--active')) {
                    return;
                }

                cancelPreviousTabChange();

                block.addClass('block-products-carousel--loading');
                $(this).closest('.block-header__groups-list').find('.block-header__group--active').removeClass('block-header__group--active');
                $(this).addClass('block-header__group--active');

                // timeout ONLY_FOR_DEMO! you can replace it with an ajax request
                let timer;
                timer = setTimeout(function() {
                    let items = block.find('.owl-carousel .owl-item:not(".cloned") .block-products-carousel__column');

                    /*** this is ONLY_FOR_DEMO! / start */
                    /**/ const itemsArray = items.get();
                    /**/ const newItemsArray = [];
                    /**/
                    /**/ while (itemsArray.length > 0) {
                    /**/     const randomIndex = Math.floor(Math.random() * itemsArray.length);
                    /**/     const randomItem = itemsArray.splice(randomIndex, 1)[0];
                    /**/
                    /**/     newItemsArray.push(randomItem);
                    /**/ }
                    /**/ items = $(newItemsArray);
                    /*** this is ONLY_FOR_DEMO! / end */

                    block.find('.owl-carousel')
                        .trigger('replace.owl.carousel', [items])
                        .trigger('refresh.owl.carousel')
                        .trigger('to.owl.carousel', [0, 0]);

                    $('.product-card__quickview', block).on('click', function() {
                        quickview.clickHandler.apply(this, arguments);
                    });

                    block.removeClass('block-products-carousel--loading');
                }, 1000);
                cancelPreviousTabChange = function() {
                    // timeout ONLY_FOR_DEMO!
                    clearTimeout(timer);
                    cancelPreviousTabChange = function() {};
                };
            });

            $(this).find('.block-header__arrow--left').on('click', function() {
                owl.trigger('prev.owl.carousel', [500]);
            });
            $(this).find('.block-header__arrow--right').on('click', function() {
                owl.trigger('next.owl.carousel', [500]);
            });
        });
    });


        // hk-carousel-autoplay-resume
    function resumeHkSlideshow() {
        $('.block-slideshow .owl-carousel').each(function() {
            const owl = $(this);

            if (owl.data('owl.carousel')) {
                owl.trigger('refresh.owl.carousel');
                owl.trigger('play.owl.autoplay', [3000]);
            }
        });
    }

    if (! window.__hkSlideshowResumeRegistered) {
        window.__hkSlideshowResumeRegistered = true;

        window.addEventListener('pageshow', function() {
            setTimeout(resumeHkSlideshow, 50);
        });

        document.addEventListener('visibilitychange', function() {
            if (! document.hidden) {
                resumeHkSlideshow();
            }
        });
    }

    /*
    // collapse
    */
    $(function () {
        $('[data-collapse]').each(function (i, element) {
            const collapse = element;
            const openedClass = $(element).data('collapse-opened-class');

            $('[data-collapse-trigger]', collapse).on('click', function () {
                const item = $(this).closest('[data-collapse-item]');
                const content = item.children('[data-collapse-content]');
                const itemParents = item.parents();

                itemParents.slice(0, itemParents.index(collapse) + 1).filter('[data-collapse-item]').css('height', '');

                if (item.is('.' + openedClass)) {
                    const startHeight = content.height();

                    content.css('height', startHeight + 'px');
                    item.removeClass(openedClass);

                    content.css('height', '');
                } else {
                    const startHeight = content.height();

                    item.addClass(openedClass);

                    const endHeight = content.height();

                    content.css('height', startHeight + 'px');
                    content.css('height', endHeight + 'px');
                }
            });

            $('[data-collapse-content]', collapse).on('transitionend', function (event) {
                if (event.originalEvent.propertyName === 'height') {
                    $(this).css('height', '');
                }
            });
        });
    });

    /*
    // mobilemenu
    */
    $(function () {
        const body = $('body');
        const mobilemenu = $('.mobilemenu');

        if (mobilemenu.length) {
            const open = function() {
                const bodyWidth = body.width();
                body.css('overflow', 'hidden');
                body.css('paddingRight', (body.width() - bodyWidth) + 'px');

                mobilemenu.addClass('mobilemenu--open');
            };
            const close = function() {
                body.css('overflow', 'auto');
                body.css('paddingRight', '');

                mobilemenu.removeClass('mobilemenu--open');
            };


            $('.mobile-header__menu-button').on('click', function() {
                open();
            });
            $('.mobilemenu__backdrop, .mobilemenu__close').on('click', function() {
                close();
            });
        }
    });


    /*
    // tooltips
    */
    $(function () {
        $('[data-toggle="tooltip"]').tooltip({trigger: 'hover'});
    });
})(jQuery);
