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
            $('.indicator')
                .not($(event.target).closest('.indicator'))
                .removeClass('indicator--opened');
        });
    });


    /*
    // product tabs
    */
    $(function () {
        $('.product-tabs').each(function (i, element) {
            $('.product-tabs__list', element).on('click', '.product-tabs__item', function (event) {
                event.preventDefault();

                const tab = $(this);
                const content = $('.product-tabs__pane' + $(this).attr('href'), element);

                if (content.length) {
                    $('.product-tabs__item').removeClass('product-tabs__item--active');
                    tab.addClass('product-tabs__item--active');

                    $('.product-tabs__pane').removeClass('product-tabs__pane--active');
                    content.addClass('product-tabs__pane--active');
                }
            });

            const currentTab = $('.product-tabs__item--active', element);
            const firstTab = $('.product-tabs__item:first', element);

            if (currentTab.length) {
                currentTab.trigger('click');
            } else {
                firstTab.trigger('click');
            }
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
    $(function() {
        $('.block-slideshow .owl-carousel').owlCarousel({
            items: 1,
            nav: true,
            dots: true,
            loop: true,
            autoplay: true,
            autoplayTimeout: 3000,
            autoplaySpeed: 500,
            autoplayHoverPause: true,
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
                loop: true,
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


    /*
    // product gallery
    */
    const initProductGallery = function(element, layout) {
        layout = layout !== undefined ? layout : 'standard';

        const options = {
            dots: false,
            margin: 10
        };
        const layoutOptions = {
            standard: {
                responsive: {
                    1200: {items: 5},
                    992: {items: 4},
                    768: {items: 3},
                    480: {items: 5},
                    380: {items: 4},
                    0: {items: 3}
                }
            },
            sidebar: {
                responsive: {
                    768: {items: 4},
                    480: {items: 5},
                    380: {items: 4},
                    0: {items: 3}
                }
            },
            columnar: {
                responsive: {
                    768: {items: 4},
                    480: {items: 5},
                    380: {items: 4},
                    0: {items: 3}
                }
            },
            quickview: {
                responsive: {
                    1200: {items: 5},
                    768: {items: 4},
                    480: {items: 5},
                    380: {items: 4},
                    0: {items: 3}
                }
            }
        };

        const gallery = $(element);

        const image = gallery.find('.product-gallery__featured .owl-carousel');
        const carousel = gallery.find('.product-gallery__carousel .owl-carousel');

        image
            .owlCarousel({items: 1, dots: false})
            .on('changed.owl.carousel', syncPosition);

        carousel
            .on('initialized.owl.carousel', function () {
                carousel.find('.product-gallery__carousel-item').eq(0).addClass('product-gallery__carousel-item--active');
            })
            .owlCarousel($.extend({}, options, layoutOptions[layout]));

        carousel.on('click', '.owl-item', function(e){
            e.preventDefault();

            image.data('owl.carousel').to($(this).index(), 300, true);
        });

        function syncPosition (el) {
            let current = el.item.index;

            carousel
                .find('.product-gallery__carousel-item')
                .removeClass('product-gallery__carousel-item--active')
                .eq(current)
                .addClass('product-gallery__carousel-item--active');
            const onscreen = carousel.find('.owl-item.active').length - 1;
            const start = carousel.find('.owl-item.active').first().index();
            const end = carousel.find('.owl-item.active').last().index();

            if (current > end) {
                carousel.data('owl.carousel').to(current, 100, true);
            }
            if (current < start) {
                carousel.data('owl.carousel').to(current - onscreen, 100, true);
            }
        }
    };

    $(function() {
        $('.product').each(function () {
            const gallery = $(this).find('.product-gallery');

            if (gallery.length > 0) {
                initProductGallery(gallery[0], $(this).data('layout'));
            }
        });
    });

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
