/*
 * Theme js functions file.
 */
 
var $ 				= jQuery.noConflict(),
	kapeeOwlParam 	= kapeeOwlParam || {},
	kapee_options 	= kapee_options || {}; 
(function($) { 
	
    "use strict";
	
	var kapee 		= kapee || {};
	
	kapee.kapeePreLoader = function() {
		// Preloader
		var loader = $( '.kapee-site-preloader' );
		if ( loader.length ) {
			$( window ).on( 'beforeunload', function() {
				loader.fadeIn( 500, function() {
					loader.fadeIn( 100 )
				});
			});
			
			$( window ).on('load', function () {
            loader.fadeOut( 800 );
			});
		}
	}
	
	kapee.init = function() {
		kapee.$doc          	= $(document);
		kapee.$html    			= $('html');
		kapee.$body 			= $(document.body);
		kapee.$window 			= $(window);
		kapee.$windowWidth 		= $(window).width();
		kapee.$windowHeight 	= $(window).height();
		kapee.ajaxXHR 			= null;
		kapee.isPostLoading 	= false;
		kapee.$tooltip 			= $('.product-buttons a,.whishlist-button a');
		kapee.$swatches 		= kapee.$doc.find( 'div.kapee-swatches-wrap' );
		kapee.$swatchForm 		= kapee.$doc.find( 'form.kapee-swatches-wrap.variations_form' );
				
		this.isCheckRTL();
		this.mobileDevice();
		this.BrowserDetection();
		this.addSpinner();
		this.backToTop();
		this.imagelazyload();
		this.reinitLazyload();
		this.initNanoScroller();
		this.initMagnaficPopup();
		this.newsLetterPopup();	
		this.kapeeMegamenu();
		this.mobileMenu();
		this.stickyHeader();		
		this.stickySidebar();
		this.canvasSidebar();
		this.widgetMenuToggle();
		this.widgetToggle();
		this.footerWidgetCollapse();
		this.widgetMaxLimitItem();
		this.kapeeOwlCarousel();
		this.MasonryGrid();
		this.postGalleryCarousel();
		this.loadmorePosts();
		this.socialShare();
		this.portfolioFilters();
		this.portfolioLoadMore();
		
		//Woocommerce
		this.productLiveSearch();
		this.swapLoginSignupFrom();
		this.userLoginSignupPopup();
		this.miniCartWidget();		
		this.addToWishlist();
		this.wishlistCount();		
		this.addToCompare();
		this.removeToCompare();
		//this.preventComparelink();
		this.addToCart();
		this.addToCartAjax();		
		this.productQuickView();
		this.productShowFilter();
		this.productShowHideFilters();
		this.productFilterAjax();	
		this.productHover();	
		this.productSwatch();
		this.initAjaxLoad();
		this.loadmoreProducts();
		this.tooltip();
		this.productImageZoom();
		this.productGallerySlickSlider();
		this.productSaleCountdown();
		this.productReviewLink();
		this.productPriceSummary();
		this.getProductSizeChart();
		this.getProductTermsConditions();
		this.productQuantityPlusMinus();
		this.productQuickShop();
		this.productBoughtTogetherInit();
		this.wooProductTabsAccordian();
		this.wooProductTabsToggle();		
		this.wooCheckoutStep();
		this.resetVariations();
		this.variationChangeevent();
		this.wcfm_vendor();
		
		//Elements
		this.kapeeEqualTabsHeight();
		this.kapeeTabEffect();
		this.kapeeAjaxTab();
		this.kapeeResponsiveTab();
		this.kapeeProgressbar();
		this.kapeeCounterUp();
		this.imageGaleryMasonry();
		this.BackgroundParallax();
		
	};
	
	kapee.isCheckRTL = function(){
		/*
		* If check is site RTL
		*/		
		$('html[dir="rtl"] body').addClass('rtl');
		var kapee_rtl = false;
		if($('body,html').hasClass('rtl')){ 
			kapee_rtl =  true;
		}	
		
		return kapee_rtl;
	};
	
	kapee.mobileDevice = function() {
		var window_size = jQuery('body').innerWidth();
		if(window_size < 991){
			jQuery('body').addClass('kapee-mobile-device');
		}else{
			jQuery('body').removeClass('kapee-mobile-device');
		}
		 kapee.$window.on('resize', function () {
			var window_size = jQuery('body').innerWidth();
			if(window_size < 991){
				jQuery('body').addClass('kapee-mobile-device');
			}else{
				jQuery('body').removeClass('kapee-mobile-device');
			}
		}); 
	};
	
	kapee.BrowserDetection = function () {
		//Check if browser is IE
		if (navigator.userAgent.search("MSIE") >= 0) {
			jQuery('body').addClass('browser-msie');
		}
		//Check if browser is Chrome
		else if (navigator.userAgent.search("Chrome") >= 0) {
			jQuery('body').addClass('browser-chrome');
		}
		//Check if browser is Firefox 
		else if (navigator.userAgent.search("Firefox") >= 0) {
			jQuery('body').addClass('browser-firefox');
		}
		//Check if browser is Safari
		else if (navigator.userAgent.search("Safari") >= 0 && navigator.userAgent.search("Chrome") < 0) {
			jQuery('body').addClass('browser-safari');
		}
		//Check if browser is Opera
		else if (navigator.userAgent.search("Opera") >= 0) {
			jQuery('body').addClass('browser-opera');
		}
	};
	
	kapee.addSpinner = function(){
		/*
		* Add Spinner
		*/
		
		$( document ).ajaxStart(function() {
			 $('.wcml_currency_switcher').append('<span class="kapee-spinner"></span>');
		});
		
		$( document ).ajaxComplete(function() {
			kapee.reinitLazyload();
		  $('.wcml_currency_switcher > span').remove();
		});	
		
			
	};	
	
	kapee.backToTop = function(){
		//*******************************************************************
		//* Back to top button 
		//********************************************************************/
		var el = $('.back-to-top');
		kapee.$window.on('scroll',function(){				
			if(el.length > 0){
				if( kapee.$window.scrollTop() > 150 ){
					el.fadeIn(400);	
				}else{
					el.fadeOut(400);	
				}	
			}	
		});
		
		el.on('click', function (e) {
				$('html,body').animate({scrollTop:0}, 600);	
				return false;
		});				
	};
	
	kapee.imagelazyload = function(){
		if ( kapee.$body.find('.lazy').length > 0 && kapee_options.lazy_load ) {
			var lazy_args = [];
			lazy_args.afterLoad      = function (element) {
				element.removeClass('lazy');
				element.removeClass('loading');
				element.addClass('lazy-loaded');
			};
			lazy_args.effect         = "fadeIn";
			lazy_args.enableThrottle = true;
			lazy_args.throttle       = 250;
			lazy_args.effectTime     = 1000;
			lazy_args.threshold      = 0;
			kapee.$body.find('.lazy').lazy(lazy_args);		
			
			
		}
	};
	
	kapee.reinitLazyload = function(){
		if ( !kapee_options.lazy_load )  return;
		$(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
			kapee.imagelazyload();			
		});
		$(document).on('removed_from_cart', function (e) {
			kapee.imagelazyload();			
		});
	}
	
	kapee.initNanoScroller = function() {
		/*
		* Nano Scroller
		*/		
		if( $(window).width() < 1024 ) return;
		$(".kapee-scroll").nanoScroller({
			paneClass: 'kapee-scroll-pane',
			sliderClass: 'kapee-scroll-slider',
			contentClass: 'kapee-scroll-content',
			preventPageScrolling: false
		});

		$( 'body' ).on( 'wc_fragments_refreshed wc_fragments_loaded added_to_cart', function() {
			$(".widget_shopping_cart .kapee-scroll").nanoScroller({
				paneClass: 'kapee-scroll-pane',
				sliderClass: 'kapee-scroll-slider',
				contentClass: 'kapee-scroll-content',
				preventPageScrolling: false
			});
		} );
	}
	
	kapee.initMagnaficPopup = function (){		
		
		var wordpress_galery = $(document).find('.gallery');
		wordpress_galery.each(function(index){
			var current_gallery = $(this);
			$(current_gallery).magnificPopup({
			delegate: 'img',
			type: 'image',
			removalDelay: 500,
			callbacks: {
				beforeOpen: function() {
					this.st.image.markup = this.st.image.markup.replace('mfp-figure', 'mfp-figure mfp-with-anim');
					this.st.mainClass = 'mfp-with-zoom mfp-img-mobile';
				},
				elementParse: function(item) {  item.src = item.el.attr('src'); }
			},
			image: {
				verticalFit: true
			},
			gallery: {
				enabled: true,
				navigateByImgClick: true
			},
		});
			
		});
		 
		init_magnificpopup('.portfolios-list','a.project-zoom');
		init_magnificpopup('.kapee-gallery-carousel','.owl-item:not(.cloned) a');
		init_magnificpopup('.kapee-portfolio-image:not(.kapee-gallery-carousel)','.kapee-post-gallery__image a');
		init_magnificpopup('.related.portfolios .kapee-carousel','.owl-item:not(.cloned) a.project-zoom');
		init_magnificpopup('.kapee-portfolios-carousel','.owl-item:not(.cloned) a.project-zoom');
		init_magnificpopup('.kapee-image-gallery.image-gallery-normal-grid','a');
		init_magnificpopup('.kapee-image-gallery.image-gallery-masonry-grid','a');
		init_magnificpopup('.kapee-image-gallery.image-gallery-carousel','.owl-item:not(.cloned) a');
		
		function init_magnificpopup(container,delegate){
			
			var container_wrap = $(document).find(container);
			
			if( typeof('container_wrap') !== 'undefined' && container_wrap != '' ) {
				container_wrap.each(function(index){
					var portfolio_item = $(this);
					$(portfolio_item).magnificPopup({
						delegate    : delegate,
						type: 'image',
						removalDelay: 500,
						callbacks: {
							beforeOpen: function() {
								this.st.image.markup = this.st.image.markup.replace('mfp-figure', 'mfp-figure mfp-with-anim');
								this.st.mainClass = 'mfp-with-zoom mfp-img-mobile';
							}
						},
						image: {
							verticalFit: true
						},
						gallery: {
							enabled: true,
							navigateByImgClick: false
						},
					});
					
				});
			}
		}
		
		$('.link-popup').magnificPopup({
			type: 'image',
			removalDelay: 500,
			callbacks: {
				beforeOpen: function() {
					this.st.image.markup = this.st.image.markup.replace('mfp-figure', 'mfp-figure mfp-with-anim');
					this.st.mainClass = 'mfp-with-zoom mfp-img-mobile';
				}
			},
			image: {
				verticalFit: true
			},
		});
		
		var $ombed_vids = $(".kapee-video-popup");
		if( $ombed_vids.length > 0 ) {
			$ombed_vids.each(function () {
				var $mfp_popup_link_non_html5 = $(this);

				$($mfp_popup_link_non_html5).magnificPopup({
					disableOn: 700,
					type: 'iframe',
					mainClass: 'mfp-fade product-video-popup',
					removalDelay: 160,
					preloader: false,
					fixedContentPos: false,
					iframe: {
						patterns: {
							youtube: {
								index: 'youtube.com/',
								id: function(url) {
									var m = url.match(/[\\?\\&]v=([^\\?\\&]+)/);
									if ( !m || !m[1] ) return null;
									return m[1];
								},
								src: '//www.youtube.com/embed/%id%?autoplay=1&rel=0'
							},
							youtu: {
								index: 'youtu.be',
								id: '/',
								src: '//www.youtube.com/embed/%id%?autoplay=1&rel=0'
							},
							vimeo: {
								index: 'vimeo.com/',
								id: function(url) {
									var m = url.match(/(https?:\/\/)?(www.)?(player.)?vimeo.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/);
									if ( !m || !m[5] ) return null;
									return m[5];
								},
								src: '//player.vimeo.com/video/%id%?autoplay=1'
							},
						}
					}
				});
			});
		}		
    };
	
	kapee.newsLetterPopup = function(){		
		
		var popup_enable 		= kapee_options.newsletter_args.popup_enable,
			popup_display_on 	= kapee_options.newsletter_args.popup_display_on,
			popup_delay 		= kapee_options.newsletter_args.popup_delay,
			x_scroll 			= kapee_options.newsletter_args.popup_x_scroll,
			show_for_mobile 	= kapee_options.newsletter_args.show_for_mobile,
			popup_exit 			= false,
			startinterval 		= false,
			popup_closed 		= Cookies.set( 'kapee_newsletter_closed' ),
			$news_letter_wrap 	= $('.kapee-newsletter-popup');
		
		if( ! $news_letter_wrap.length ) {
			return false;
		}
		
		//yith-woocompare
		if( jQuery(document).find('#yith-woocompare').length > 0 ){
			return false;
		}
		
		if( ! popup_enable || popup_closed == 'do-not-show' || kapee_options.maintenance_mode || ( ! show_for_mobile && $(window).width() < 768 ) ){
			return false; 
		}
		
		if( popup_display_on == 'exit' ){
			jQuery(document).on('mouseleave', function (e) {
				show_popup();
			});
		}else if( popup_display_on == 'scroll' ){

            jQuery(window).scroll(function () {
                var h = jQuery(document).height() - jQuery(window).height();
                var sp = jQuery(window).scrollTop();
                var p = parseInt(sp / h * 100);

                if (p >= x_scroll) {
                   show_popup();                
                }
            });
        }else{
			if( popup_delay ){
				setTimeout(function(){show_popup(); }, popup_delay * 1000);	
			}else{
				show_popup();
			}			         
        }
		var newsletter_btn = $('.header-newsletter');
		newsletter_btn.on('click',function(){
			 show_popup();    
		});
			
		$news_letter_wrap.find('.mc4wp-form').submit(function () {
            Cookies.set('kapee_newsletter_closed', 'do-not-show', { expires: 1, path: '/' });
        });
		
		function show_popup() {
			if( popup_exit ) return;
			popup_exit = true;
			$.magnificPopup.open({
				type: 'inline',
				removalDelay: 500,
				items: {
					src: '.kapee-newsletter-popup' ,					
				},
				callbacks: {
					open: function () {
						var popupWrap = $( '.kapee-newsletter-popup' );
						popupWrap.addClass('animated fadeInLeft');						
					},							
					beforeClose: function() {
						var popupWrap = $( '.kapee-newsletter-popup' );
						popupWrap.removeClass('fadeInLeft').addClass('fadeOutRight');
					}, 
					close: function() {
						this.content.removeClass('animated fadeOutRight'); 
						// check box click
						if($('#newsletter-donotshow:checked') && $('#newsletter-donotshow:checked').val() == 'do-not-show'){
							Cookies.set('kapee_newsletter_closed', 'do-not-show', { expires: 1, path: '/' });
						}
					}
				},				
			});
		}
	};
	
	kapee.wowAnimation = function() {
		// WOW ANIMATION
		if($('.wow').length > 0)
		{
			var wow = new WOW(
			{
			  boxClass:     'wow',      // animated element css class (default is wow)
			  animateClass: 'animated', // animation css class (default is animated)
			  offset:       50,          // distance to the element when triggering the animation (default is 0)
			  mobile:       false       // trigger animations on mobile devices (true is default)
			});
			wow.init();	
		}	
	}
	
	kapee.kapeeMegamenu = function(){
		
		var main_menu_wrap 				= $('.main-navigation').find('ul.menu');
		
		main_menu_wrap.on('hover', ' > li.kapee-megamenu-dropdown', function(e) {
			setOffset( $(this) );
		});
		jQuery(window).resize(function() {
			main_menu_wrap.on('hover', ' > li.kapee-megamenu-dropdown', function(e) {
				setOffset( $(this) );
			});
		});

		var setOffset = function( li ) {

			var megaMenuWrapper 		= li.find(' > .kapee-megamenu-wrapper'),
				megaMenuHolder 			= li.find(' .kapee-megamenu-holder');
			
			megaMenuWrapper.attr('style', '');

			var container	 			= $('.site-header .container'),
				containerWidth 			= container.outerWidth(),
				containerOffsetLeft 	= container.offset().left + 15,
				containerPaddingLeft 	= parseInt(container.css('padding-left')),
				containerPaddingRight 	= parseInt(container.css('padding-right')),
				viewportWidth 		  	= containerWidth - containerPaddingLeft - containerPaddingRight;
				
			if( li.hasClass( 'kapee-megamenu-item-full-width' ) ) { 
				megaMenuHolder.css({
					width: viewportWidth
				});
			}
			
			var	megaMenuWrapperWidth	= megaMenuWrapper.outerWidth(),
				megaMenuWrapperOffset	= megaMenuWrapper.offset();		
				
			if( ! megaMenuWrapperWidth || ! megaMenuWrapperOffset ) return;
			
			var mega_menu_wrapOffsetRight = viewportWidth - megaMenuWrapperOffset.left - megaMenuWrapperWidth;
			
			if( $('body').hasClass('rtl') && mega_menu_wrapOffsetRight + megaMenuWrapperWidth + containerOffsetLeft >= viewportWidth && ( li.hasClass( 'kapee-megamenu-dropdown' ) ) ) {
				
				var toLeft = mega_menu_wrapOffsetRight + megaMenuWrapperWidth - viewportWidth + containerOffsetLeft;
				megaMenuWrapper.css({
					right: - toLeft
				}); 

			}else if( megaMenuWrapperOffset.left + megaMenuWrapperWidth - containerOffsetLeft >= viewportWidth && ( li.hasClass( 'kapee-megamenu-dropdown' ) ) ) {
				
				var toRight = megaMenuWrapperOffset.left + megaMenuWrapperWidth - viewportWidth - containerOffsetLeft;
				megaMenuWrapper.css({
					left: - toRight
				}); 
			}				
		};	
	}
	
	kapee.mobileMenu = function(){
		/*
		* Mobile menu
		*/
		
		//Menu wrapper
		$(document).on('click', '.mobile-nav-tabs li', function(e) {
			if(!$(this).hasClass("active")){
				var cn=$(this).data("menu");
				$(this).parent().find(".active").removeClass("active");
				$(this).addClass("active");
				$(".mobile-nav-content").removeClass("active").fadeOut(300);
				$(".mobile-"+cn+"-menu").addClass("active").fadeIn(300);
			}
		});
		
		//Menu
		var $mobileMenu 	= $('#mobile-menu-wrapper'),
			$closeSidebar 	= $('.kapee-mask-overaly');
		$( '.mobile-navbar .navbar-toggle,.mobile-element .navbar-toggle' ).on( 'click', function ( e ) {
			e.preventDefault();			
			if ( ! $mobileMenu.hasClass('opened') ) {
				$mobileMenu.addClass('opened');
				$closeSidebar.addClass('opened');
			}
		});

		$( '.mobile-main-menu li.menu-item-has-children' ).append( '<span class="menu-toggle"></span>' );
		
		$mobileMenu.on('click', '.menu-item-has-children > .menu-toggle', function (e) {
			e.preventDefault();

			$(this).closest('li').siblings().find('ul').slideUp();
			$(this).closest('li').siblings().removeClass('active');
			$(this).closest('li').siblings().find('li').removeClass('active');

			$(this).closest('li').children('ul').slideToggle();
			$(this).closest('li').toggleClass('active');

		});
		
		kapee.$body.on('click', '.kapee-mask-overaly', function (e) {
			if ( $mobileMenu.hasClass( 'opened' ) ) {
				$mobileMenu.removeClass('opened');
				$closeSidebar.removeClass('opened');
			}
		});
		
		kapee.$window.on('resize', function () {
			if ( kapee.$window.width() > 991 ) {
				if ( $mobileMenu.hasClass( 'opened' ) ) {
					$mobileMenu.removeClass('opened');
					$closeSidebar.removeClass('opened');
				}
			}
		});
	};
	
	kapee.stickyHeader = function(){
		//*******************************************************************
		//*  Sticky Header.
		//*******************************************************************/
		var $header = $('.site-header'),
			$stickyElements = $('.header-sticky'),
			$firstSticky = $stickyElements.first(),
			headerHeight = $header.outerHeight(),
			$window = $(window),
			isSticked = false,
			adminBarHeight = $('#wpadminbar').outerHeight(),
			stickShowAfter = 300;
			stickShowAfter = headerHeight + 5;
			
			 $(window).on('scroll', function () {
				var windowSize = $(window).width();
				//Disable sticky in desktop
				if( windowSize > 992 && (!kapee_options.sticky_header)){
					return false; 
				}
				// Disable sticky in tablet
				if( windowSize <= 992 && windowSize > 480 && (!kapee_options.sticky_header_tablet)){
					return false;
				}
				//Disable sticky in mobile
				if( windowSize <= 480 && (!kapee_options.sticky_header_mobile)){
					return false;
				}
				var stickyOffset = stickShowAfter;
				var currentScroll = $(this).scrollTop();
				if (currentScroll > stickyOffset) {					
					stickHeader();
				} else {
					unstickHeader();
				}
			});
			function stickHeader() {
				if (isSticked) return
				isSticked = true
				$header.addClass('header-sticked');
				$stickyElements.css({
					'top': adminBarHeight+'px'
				});
			}

			function unstickHeader() {
				if (!isSticked) return

				isSticked = false
				$header.removeClass('header-sticked');
				$stickyElements.css({
                        'top': 0
                    });
			}
	}
	
	kapee.stickySidebar = function(){
		//*******************************************************************
		//*  Sticky Sidebar.
		//*******************************************************************/
		if ( !kapee_options.sticky_sidebar ) return;
		$(document).ready(function(){
			if( $( window ).width() <= 768  ) return;
			var sticky_sidebar = $("#secondary .sidebar-inner");	
			var offset = 15;
			if ($('#header .header-sticky')[0]) {
				offset = $('#header .header-sticky').height() + 30;
			}
			sticky_sidebar.stick_in_parent({ offset_top: offset });				
			$( window ).resize(function() {                    
				if ( $( window ).width() <= 768 ) {
					sticky_sidebar.trigger('sticky_kit:detach');
					
				}else{
					sticky_sidebar.stick_in_parent({
						offset_top: offset
					});
				}
			});
		});
	};
	
	kapee.canvasSidebar = function(){
		//*******************************************************************
		//*  Canvas Sidebar.
		//*******************************************************************/
		if ( !kapee_options.sidebar_canvas_mobile ) return;
		var sidebar_canvas 		= $('.kapee-canvas-sidebar .kp-canvas-sidebar, .kapee-mobile-navbar .kp-canvas-sidebar');
		var secondary = $('#secondary');
        var closeSidebar 	= $('.kapee-mask-overaly');
		
		sidebar_canvas.on('click', function(e) {
			e.preventDefault();
			
			if ( ! secondary.hasClass('opened') ) {
				secondary.addClass('opened');
				closeSidebar.addClass('opened');
			}					
		});
		
		kapee.$body.on('click', '.kapee-mask-overaly, .close-sidebar', function (e) {
			e.preventDefault();
			secondary.removeClass('opened');
			closeSidebar.removeClass('opened');
		});	
		
		kapee.$window.on('resize', function () {
			if ( kapee.$window.width() > 767 ) {
				if ( secondary.hasClass( 'opened' ) ) {
					secondary.removeClass('opened');
					closeSidebar.removeClass('opened');
				}
			}
		});
		
	};
	
	kapee.widgetMenuToggle = function(){
		//*******************************************************************
		//* Widget Menu Toggle
		//*******************************************************************/
		
		if( kapee_options.widget_menu_toggle) {
			/* Wordpress Menu widget */
			$('#secondary .widget .menu-item > a').each(function(){
				if( $(this).siblings('ul.sub-menu').length > 0 ) {
					var $childIndicator = $('<span class="child-indicator"></span>');

					$(this).siblings('.sub-menu').hide();
					$('.current-menu-item > .sub-menu').show();
					$('.current-menu-parent > .sub-menu').show();
					if($(this).siblings('.sub-menu').is(':visible')){
						$childIndicator.addClass( 'open-item' );
					}

					$childIndicator.on( 'click', function(){
						$(this).parent().siblings('.sub-menu').toggle( 'fast', function(){
							if($(this).is(':visible')){
								$childIndicator.addClass( 'open-item' );
							}else{
								$childIndicator.removeClass( 'open-item' );
							}
						});
						return false;
					});
					$(this).append($childIndicator);
				}
			});
			
			/* Product/Category widget */
			$('#secondary .widget .cat-item > a').each(function(){
				if( $(this).siblings('ul.children').length > 0 ) {
					var $childIndicator = $('<span class="child-indicator"></span>');

					$(this).siblings('.children').hide();
					$('.current-cat > .children').show();
					$('.current-cat-parent > .children').show();
					if($(this).siblings('.children').is(':visible')){
						$childIndicator.addClass( 'open-item' );
					}

					$childIndicator.on( 'click', function(){
						$(this).parent().siblings('.children').toggle( 'fast', function(){
							if($(this).is(':visible')){
								$childIndicator.addClass( 'open-item' );
							}else{
								$childIndicator.removeClass( 'open-item' );
							}
						});
						return false;
					});
					$(this).append($childIndicator);
				}
			});
		}		
	};
	
	kapee.widgetToggle = function(){
		//*******************************************************************
		//* Widget Menu Toggle
		//*******************************************************************/		
		if( kapee_options.widget_toggle) {
			$( document ).find('.widget-area .widget').addClass('widget-toggle').removeClass('closed');
			$( document ).on( 'click', '.widget-area .widget .widget-title, .dokan-widget-area .widget .widget-title', function(e) {
				//$(this).unbind();
				e.stopImmediatePropagation();
				if ($(this).next().is(':visible')){
                    $(this).parent().addClass('closed');
                } else {
                    $(this).parent().removeClass('closed');
                }
                $(this).next().stop().slideToggle(200);
			});			
		}
	};
	
	kapee.footerWidgetCollapse = function(){
		//*******************************************************************
		//* Footer Widget Collapse
		//*******************************************************************/	
		if ($(window).width() > 576) {
			return;
		}
		$( document ).on( 'click', '.kapee-mobile-device .footer-widget-collapse .widget .widget-title', function(e) {
			var $title = $(this);
			var $widget = $title.parent();
			var $content = $widget.find('> *:not(.widget-title)');

			if ($widget.hasClass('footer-widget-opened')) {
				$widget.removeClass('footer-widget-opened');
				$content.stop().slideUp(200);
			} else {
				$widget.addClass('footer-widget-opened');
				$content.stop().slideDown(200);
			}
					
		});
				
	};
	
	kapee.widgetMaxLimitItem = function(){
		//*******************************************************************
		//* Widget Hide Max Limit Item
		//*******************************************************************/
		if( kapee_options.widget_hide_max_limit_item) {
			var js_translate_text = kapee_options.js_translate_text;
			$('.widget .widget-title + ul').hideMaxListItems({
				'max': kapee_options.number_of_show_widget_items,
				'speed': 500,
				'moreText': js_translate_text.show_more,
				'lessText': js_translate_text.show_less
			});
		}
	};
	
	kapee.kapeeOwlCarousel = function() { 
		/*
		 * Owl carousel slider
		 */

		if ( kapeeOwlParam.length === 0 || typeof kapeeOwlParam.owlCarouselArg === 'undefined' ) {
			return; 
		}
		
		function add_owl_overlayclass(){
			$('.owl-stage-outer .product-wrapper').mouseenter(function(){
			var slider_elemnt = $(this).closest('.products.kapee-carousel');
			slider_elemnt.find('.owl-stage-outer').addClass('overlay');
		}).mouseleave(function(){
			var slider_elemnt = $(this).closest('.products.kapee-carousel');
			slider_elemnt.find('.owl-stage-outer').removeClass('overlay');
		});
		}
		setTimeout(function() {add_owl_overlayclass()}, 1000)
		$.each( kapeeOwlParam.owlCarouselArg, function ( id, owlCarouselArg ) {
			
			var loop 				= ( owlCarouselArg.slider_loop ) ? true : false,
				autoplay 			= ( owlCarouselArg.slider_autoplay ) ? true : false,							
				autoplayHoverPause 	= ( autoplay && owlCarouselArg.slider_autoplayHoverPause ) ? true : false,
				autoplaytimeout 	= owlCarouselArg.slider_autoplaytimeout,
				smartspeed 			= parseInt(owlCarouselArg.slider_smartspeed),
				
				rewind 				= ( owlCarouselArg.slider_rewind ) ? true : false,
				nav 				= ( owlCarouselArg.slider_nav ) ? true : false,				
				center 				= ( owlCarouselArg.slider_center ) ? true : false,				
				dots 				= ( owlCarouselArg.slider_dots ) ? true : false,
				autoHeight 			= ( owlCarouselArg.slider_autoHeight ) ? true : false,
				touchDrag			= ( owlCarouselArg.slider_touchDrag ) ? true : false,
				animateIn 			= owlCarouselArg.slider_animatein,
				animateOut 			= owlCarouselArg.slider_animateout,
				margin 				= owlCarouselArg.slider_margin,
				rs_extra_large 		= ( owlCarouselArg.rs_extra_large > 0 ) ? owlCarouselArg.rs_extra_large : 5,
				rs_large 			= ( owlCarouselArg.rs_large > 0 ) ? owlCarouselArg.rs_large : 4,
				rs_medium 			= ( owlCarouselArg.rs_medium > 0 ) ? owlCarouselArg.rs_medium : 3,
				rs_small 			= ( owlCarouselArg.rs_small > 0 ) ? owlCarouselArg.rs_small : 2,
				rs_extra_small 		= ( owlCarouselArg.rs_extra_small > 0 ) ? owlCarouselArg.rs_extra_small : 2,
				numItems 			= null,
				slider_element 		= null;
				
			if($('#'+id).hasClass('kapee-carousel')){
				numItems 			= $( '#'+id ).children().length;
				slider_element 		= $( '#'+id );
			}else{
				numItems 			= $( '#'+id).find( '.kapee-carousel').children().length;
				slider_element 		= $( '#'+id ).find( '.kapee-carousel');
			}
				
			slider_element.owlCarousel({				
				autoplay			: autoplay,
				autoplayHoverPause	: autoplayHoverPause,
				autoplayTimeout		: autoplaytimeout,
				smartSpeed 			: smartspeed,
				rewind				: rewind,				
				nav					: nav,
				center				: center,
				navText				: ['',''],
				dots				: dots,
				autoHeight			: autoHeight,
				touchDrag			: ( kapee.$windowWidth <  768 && touchDrag ) ? true : false,				
				animateIn			: animateIn,
				animateOut			: animateOut,
				margin				: margin,
				rtl 				: ( kapee.isCheckRTL() ) ? true : false,
				responsive			: {
					0:{
						items	: rs_extra_small,
						loop	: ( numItems >= rs_extra_small && loop) ? true : false,
						nav		: false,
						mouseDrag: false,
						touchDrag: true						
					},
					576:{
						items	: rs_small,
						loop	: ( numItems >= rs_small && loop) ? true : false,
						nav		: false,
						mouseDrag: false,
						touchDrag: true	
					},
					768:{
						items	: rs_medium,
						loop	: ( numItems >= rs_medium && loop) ? true : false,
						nav		: false,
					},
					992:{
						items	: rs_large,
						loop	: ( numItems >= rs_large && loop) ? true : false,
					},					
					1200:{
						items 	: rs_extra_large,
						loop	: ( numItems >= rs_extra_large && loop) ? true : false,
					}
				},
				onInitialized: function(){
					slider_element.addClass('owl-theme');
				}
			}).on('changed.owl.carousel', function(event) {
				kapee.imagelazyload();
			});			

		} );	
	};
	
	kapee.MasonryGrid = function (){
		
		/*
		* Init Masonry grid
		*/
		if($( '.articles-list.masonry-grid' ).length){
			 kapee.$body.imagesLoaded(function () {
				kapee.$body.find('.articles-list.masonry-grid').isotope({
					itemSelector: '.post',
					layoutMode: 'masonry'
				});
			});			
		}
	};
	
	kapee.postGalleryCarousel = function (){
		/*
		*  Post Gallery Carousel
		*/
		$('.kapee-gallery-carousel').owlCarousel({
			loop			: true,
			autoplay 		: false,
			autoplayTimeout : 3000,
			rtl 			: ( kapee.isCheckRTL() ) ? true : false,
			//rewind		: true,
			smartSpeed		: 750,
			nav 			: true,
			navText			: ['',''],
			dots			: true,		
			items			: 1
		});
		$( '.kapee-gallery-carousel').addClass('owl-theme owl-center');
	};
	
	kapee.loadmorePosts = function(){
		
		$('.kapee-blog-load-more .kapee-load-more').on('click',function(){
			
			var load_more_btn = $(this);
			var page = parseInt(load_more_btn.attr('data-page'));
			var attr = load_more_btn.attr('data-attribute');
			var post_wrap = load_more_btn.closest('.kapee-element').find('.articles-list');
            var wrap_id = load_more_btn.closest('.kapee-element').attr('id');
			var data = {
				action: 'kapee_loadmore_posts',
				nonce: kapee_options.nonce,
				attr: attr,
				page: page,
			};
			if(load_more_btn.hasClass('process')){ return false;}
			kapee.loadAjaxPost(load_more_btn,data,post_wrap,wrap_id);
		});
		var animationFrame = function () {
			$('.kapee-blog-load-more a.infinity-scroll').each(function (i, val) {
				var load_more_btn = $(this);
				var page = parseInt(load_more_btn.attr('data-page'));
				var attr = load_more_btn.attr('data-attribute');
				var post_wrap = load_more_btn.closest('.kapee-element').find('.articles-list');
				var wrap_id = load_more_btn.closest('.kapee-element').attr('id');
				var bottomOffset = post_wrap.offset().top + post_wrap.height() - $(window).scrollTop();
				if (bottomOffset < window.innerHeight && bottomOffset > 0) {
					if(load_more_btn.hasClass('process')){ 
						kapee.isPostLoading = true;
					}else{
						kapee.isPostLoading = false;
					}
					var page = parseInt(load_more_btn.attr('data-page'));
					if(!load_more_btn.hasClass('kapee-loadmore-disabled')){ 
						load_more_btn.trigger('click');
					}
				}
			});
		}
		
		var scrollHandler = function () {
			requestAnimationFrame(animationFrame);
		};                    
		$(window).scroll(scrollHandler);
	}
	
	kapee.socialShare = function (){
		/*
		* Social Share
		*/
		kapee.$doc.on('click', '.print-share-btn', function(){
			window.print();
			return false;
		});

		/*
		 * Open Share buttons in a popup
		 */
		kapee.$doc.on('click', '.social-share a', function(){
			var link = jQuery(this).attr('href');
			if( link != '#' ){
				window.open( link, 'TIEshare', 'height=450,width=760,resizable=0,toolbar=0,menubar=0,status=0,location=0,scrollbars=0' );
				return false;
			}
		});
	};
	
	kapee.portfolioFilters = function (){
		
		/*
		* Portfolio Filters
		*/
		
		if ( ! $('.portfolios-list').length > 0 ) return;
		var $layoutMode='masonry';
		
		if($( '.portfolios-list' ).length){
			$( '.portfolios-list' ).each(function(){
				if($( this ).hasClass('simple-grid') ){
					$layoutMode='fitRows';
				}
				var portfolio_container = $( this );
				// initialize Masonry after all images have loaded
                portfolio_container.imagesLoaded(function() {
					portfolio_container.isotope({
						itemSelector: '.portfolio-post-loop',
						isOriginLeft: ! $('body').hasClass('rtl'),
						layoutMode: $layoutMode
					});
				 });
			});
			
		}
		
		$('.filter-categories').on('click', 'a', function(e) {
			e.preventDefault();
			
			var $portfolio_container = $(this).closest('.portfolio-filter').next('.portfolios-list');
			if($portfolio_container.hasClass('simple-grid') ){
				$layoutMode='fitRows';
			}	
			$('.filter-categories').find('.active').removeClass('active');
			$(this).addClass('active');
			var filterValue = $(this).attr('data-filter');
			$portfolio_container.isotope({
				filter		: filterValue,
				layoutMode	: $layoutMode,
			});
			//return false;
		});
		
	};
	
	kapee.portfolioLoadMore = function () {
		
		$('.kapee-portfolio-load-more .kapee-load-more').on('click',function(){
						
			var $this = $(this),
				portfolio_wrap = $this.parent().parent().parent().find('.portfolios-list'),
				data_attr = $this.parent().data(),
				atts = data_attr.attribute,
				page = parseInt($this.parent().attr('data-page')),
				load_more_label = data_attr.load_more_label,
				loading_finished_msg = data_attr.loading_finished_msg;
				$this.addClass('request-running');
			if($this.hasClass('kapee-loadmore-disabled')){
				return;
			}
			$this.html('<span class="loading"> '+kapee_options.js_translate_text.loading_txt+'</span>');
			var data = {
					attr: atts,
					page: page,
					nonce: kapee_options.nonce,
					action: 'kapee_loadmore_portfolios',
				};
		
			$.ajax({
				url: kapee_options.ajax_url,
				data: data,
				dataType: 'json',
				method: 'POST',
				success: function (response) {

					var portfolio_items = $(response.html);
					if (portfolio_items) {
						if (portfolio_wrap.hasClass('masonry-grid')) {
							// initialize Masonry after all images have loaded
							portfolio_wrap.append(portfolio_items).isotope('appended', portfolio_items);
							portfolio_wrap.imagesLoaded().progress(function () {
								portfolio_wrap.isotope('layout');								
							});
						} else {
							portfolio_wrap.append(portfolio_items);
						}						
					}
					if ($.trim(response.show_bt) == '0') {
						$this.addClass('disabled kapee-loadmore-disabled').html(loading_finished_msg);
					} else {
						$this.parent().attr('data-page', page + 1);					
						$this.html(load_more_label);
					}

				},
				error: function (response) {
					console.log('ajax error');
				},
				complete: function () {
					kapee.isPostLoading = false;
					kapee.imagelazyload();
					kapee.initMagnaficPopup();			
					$this.removeClass('request-running');	
				},
			});
					
		});
		
		
		var animationFrame = function () {
			$('.kapee-portfolio-load-more a.infinity-scroll').each(function (i, val) {
				var $this = $(this),
				portfolio_wrap = $this.parent().parent().parent().find('.portfolios-list');
				var bottomOffset = portfolio_wrap.offset().top + portfolio_wrap.height() - $(window).scrollTop();
				if (bottomOffset < window.innerHeight && bottomOffset > 0) {
					if($this.hasClass('request-running')){ 
						kapee.isPostLoading = true;
					}else{
						kapee.isPostLoading = false;
					}
					
					if(!$this.hasClass('kapee-loadmore-disabled')){ 
						if(!kapee.isPostLoading){
							kapee.isPostLoading = true;
							$this.trigger('click');
						}
					}
				}
			});
		}
		
		var scrollHandler = function () {
			requestAnimationFrame(animationFrame);
		};                    
		$(window).scroll(scrollHandler);
		
	};
	
	// Kapee product live search
	kapee.productLiveSearch = function () {
		$('.trending-search-results').hide();
		 var serviceUrl = kapee_options.ajax_url + '?action=kapee_ajax_search';
		$('.site-header .kapee-ajax-search').each(function(){
			
			var append 				= $(this).find('.search-results-wrapper'),
				container 			= $(this),
				search_categories 	= $(this).find('.categories-filter'),
				product_cat 		= '';

			if (search_categories.length && search_categories.val() !== '') {
				serviceUrl += '&product_cat=' + search_categories.val();
			}
		
			$(this).find('.search-field').keyup(function(){
				 var search_text = $(this).val();
				 if(search_text.length < 3){
					$('.trending-search-results').show();
				 }else{
					 $('.trending-search-results').hide();
				 }
			});
			 $(this).find('.search-field').focus(function() {
				 var search_text = $(this).val();
				 if(search_text.length < 3){
					$('.trending-search-results').show();
				 }else{
					 $('.trending-search-results').hide();
				 }
			});
			$(this).find('.search-field').focusout(function() {
				//$('.trending-search-results').hide();
			});
			 $(this).find('.search-field').devbridgeAutocomplete({
				minChars        : 3,
				appendTo        : append,
				triggerSelectOnValidInput: false,
				serviceUrl      : serviceUrl,
				onSearchStart   : function () { 
					container.find('.search-submit').removeClass('kapee-spinner');
					container.find('.search-submit').addClass('kapee-spinner');
					$('.trending-search-results').hide();
				},
				onSelect        : function (suggestion) {
					if (suggestion.id != -1) {
						window.location.href = suggestion.url;
					} 						
				},
				onSearchComplete: function () { 
					container.find('.search-submit').removeClass('kapee-spinner');
				},
				beforeRender: function (container) {					
					$(container).removeAttr('style');
				},
				formatResult: function (suggestion, currentValue) {
					
						var pattern = '(' + $.Autocomplete.utils.escapeRegExChars(currentValue) + ')';
						var html = '';
						if(suggestion.img) html += '<img class="search-image" src="'+suggestion.img+'">';
						html += '<div class="search-name">'+suggestion.value.replace(new RegExp(pattern, 'gi'), '<strong>$1<\/strong>')+'</div>';
						if(suggestion.price) html += '<span class="search-price">'+suggestion.price+'</span>';
						if(suggestion.rating) html += '<span class="search-rating">'+suggestion.rating+'</span>';

						return html;
					}
			});

			  if( search_categories.length ){
					var searchForm = $(this).find('.search-field').devbridgeAutocomplete();

					search_categories.on( 'change', function( e ){

						if( search_categories.val() != '' ) {
							searchForm.setOptions({
								serviceUrl:  serviceUrl + '&product_cat=' + search_categories.val()
							});
						} else{
							searchForm.setOptions({
								serviceUrl:  serviceUrl
							});
						}

						// update suggestions
						searchForm.hide();
						searchForm.onValueChange();
					});
				}
		 });
		 
		/* Hide .trending-search-results */
		$(document).mouseup(function (e){
			var container = $(".kapee-ajax-search");			
			if (!container.is(e.target) && container.has(e.target).length === 0){				
				$(".trending-search-results").hide();				
			}
		}); 
	};
	
	kapee.swapLoginSignupFrom = function () {
		/*
		* Swap Login Signup Form
		*/
		var userSignup 	= $('.new-signup');
		var userSignin	= $('.user-signin');
		
		userSignup.on('click', function(e) {
			e.preventDefault();	
			$('.customer-login').removeClass('active')
			$('.customer-signup').addClass('active');
			
		});
		userSignin.on('click', function(e) {
			e.preventDefault();
			$('.customer-signup').removeClass('active');
			$('.customer-login').addClass('active');
			
		});
	}
	
	kapee.userLoginSignupPopup = function () {
		/*
		* User Login Signup Popup
		*/

		if( !kapee_options.login_register_popup || $('body').hasClass('woocommerce-account') )  return false;
		
		$('.customer-signinup').magnificPopup({
			type: 'inline',
			preloader: false,			
			removalDelay: 500,
			items: {
				src: '#kapee-signin-up-popup' ,					
			},
			//mainClass: 'animated bounceIn',
			callbacks: {
				open: function() {
					var closeSidebar 	= $('.kapee-mask-overaly');
					var mobileSidebar = $('#mobile-menu-wrapper');
					closeSidebar.removeClass('opened');
					mobileSidebar.removeClass('opened');
					$('.kapee-signin-up-popup').addClass('animated fadeInLeft');
				},
				beforeClose: function() {
						var popupWrap = $( '.kapee-signin-up-popup' );
						popupWrap.removeClass('fadeInLeft').addClass('fadeOutRight');
					}, 
				close: function() {
					$('.kapee-signin-up-popup').removeClass('animated fadeOutRight');
					$('.kapee-signin-up-popup').find('.signin-up-error-message').remove();
				}
			} 
		});
		
		$(document).on('click', '#kapee-signin-up-popup .woocommerce-login-button .button', function(e) {			
			var $this = $(this);
			var $loginform = $this.closest('form');			
			$this.addClass('loading');
		});
		
		$(document).on('click', '#kapee-signin-up-popup .woocommerce-form-register .woocommerce-Button.button', function(e) {			
			var $this = $(this),
			$regform = $this.closest('form');			
			$this.addClass('loading');		
		});			
	};
	
	kapee.miniCartWidget = function () {
		/*
		 * Mini Cart Widget Sidebar
		 */
		 
		if ( ! kapee_options.header_minicart_popup ) { return false; }

		var headerCart 		= $('.header-cart');
		var miniCartSidebar = $('.minicart-slide-wrapper');
        var closeSidebar 	= $('.kapee-mask-overaly');
		var mobileSidebar 	= $('#mobile-menu-wrapper');
		
		
		headerCart.on('click', function(e) {
			
			if( $('body').hasClass('woocommerce-cart') || $('body').hasClass('woocommerce-checkout') ) { return; };
			
			e.preventDefault();
			kapee.imagelazyload();
			if ( ! miniCartSidebar.hasClass('opened') ) {
				miniCartSidebar.addClass('opened');
				closeSidebar.addClass('opened');				
			}
			setTimeout(function(){
			 kapee.imagelazyload();
			}, 200);

			
		});
		
		kapee.$body.on('click', '.kapee-mask-overaly, .close-sidebar', function (e) {
			e.preventDefault();
			miniCartSidebar.removeClass('opened');
			closeSidebar.removeClass('opened');
			mobileSidebar.removeClass('opened');
		});	
		 
		kapee.$doc.keyup( function( e ) {
            if ( e.keyCode === 27 ) {
				miniCartSidebar.removeClass('opened');
				closeSidebar.removeClass('opened');
			}
        });
	};
	
	kapee.addToWishlist = function(){
		/*
		* Add wishlist loader
		*/
       kapee.$body.on("click", ".add_to_wishlist", function() {
			// Bootstrap tooltips hide
			var tooltip_hide = ('.yith-wcwl-add-to-wishlist a');			
			$(tooltip_hide).tooltip('hide');
			
            $(this).addClass("loading");
        });
	}
	
	kapee.wishlistCount = function(){
		/*
		* Ajax Count Wishlist Product
		*/
		
		var kapee_ajax_wishlist_count = function() {
			$.ajax({
				type: 'POST',
				url: yith_wcwl_l10n.ajax_url,
				data      : {
					action	: 'kapee_ajax_wishlist_count',
					nonce   : kapee_options.nonce,
				},
				beforeSend: function () {
				},
				complete  : function () {
				},			
				success   : function (data) {
					$('span.header-wishlist-count').html(data);
					kapee.tooltip();
				}
			});
		};
		$('body').on( 'added_to_wishlist removed_from_wishlist', kapee_ajax_wishlist_count );
	};
	
	
	
	kapee.addToCompare = function () {
		
		/*
		* Add to compare list
		*/
		var button = $("a.compare");

        kapee.$body.on("click", "a.compare", function() {
            $(this).addClass("loading");
        });

        kapee.$body.on("yith_woocompare_open_popup", function() {
            button.removeClass("loading");
            kapee.$body.addClass("compare-opened");
        });

        kapee.$body.on('click', '#cboxClose, #cboxOverlay', function() {
            kapee.$body.removeClass("compare-opened");
        });
	}
	
	kapee.removeToCompare = function () {
		/*
		* Remove to compare list
		*/
		kapee.$body.on("click", ".compare-list tr.remove a", function() {			
            $(this).addClass('loading');
        });
	}
	
	kapee.addToCart = function () {
		/*
		 *  Adding to cart
		 */
		 $('body').on('added_to_cart', function(event, fragments, cart_hash) {
			if( $('.header-cart').length > 0 ) {
				if (kapee_options.product_open_cart_mini == '1') {
					$('.header-cart').trigger('click');
				}
			}
		 });
		 
		 $( document.body ).on( 'updated_cart_totals', function(){
			setTimeout(function(){
			 kapee.imagelazyload();
			}, 200);
		});
	};
	
	kapee.addToCartAjax = function () {
		/*
		 *  Adding to cart Ajax
		 */
		if ( ! kapee_options.product_add_to_cart_ajax ) { return; }
		
		$('.single_add_to_cart_button').addClass('single_add_to_cart_ajax_button');
		 kapee.$body.find('form.cart').on('click', '.single_add_to_cart_button', function (e) {
			 
			var $productWrapper = $(this).parents('.single-product-page');
            if ($productWrapper.hasClass('product-type-external')) return;          

            var $form = $(this).closest('form.cart'),
                $singleBtn =  $(this),
				product_id = $form.find('input[name=add-to-cart]').val() || $singleBtn.val();
			if ($singleBtn.hasClass('disabled')) {
				return;
			}
            if ($singleBtn.hasClass('quick-buy-proceed')) {
				return;
			}
			if ($form.length > 0) {
                e.preventDefault();
            } else {
                return;
            }
			var data = {
				action			: 'kapee_ajax_add_to_cart',
				'add-to-cart'	: product_id,
				nonce   		: kapee_options.nonce,
			};

			$form.serializeArray().forEach(function (element) {
				data[element.name] = element.value;
			});
			if ($singleBtn.hasClass('loading')) {
				return;
			}
			$singleBtn.addClass('loading');
			
			$(document.body).trigger('adding_to_cart', [$singleBtn, data]);
			$.ajax({
				type: 'post',
				url: kapee_options.ajax_url,
				data: data,
				beforeSend: function (response) {
					$singleBtn.removeClass('added').removeClass('not-added');
				},
				success: function (response) {
					if (response.error & response.product_url) {
					  window.location = response.product_url;
					  return;
					} else {
						if (typeof wc_add_to_cart_params !== 'undefined') {
							if (wc_add_to_cart_params.cart_redirect_after_add === 'yes') {
								window.location = wc_add_to_cart_params.cart_url;
								return;
							}
						}					
						
						// Show notices
                        if( response.notices.indexOf( 'error' ) > 0 ) {
                            $('.woocommerce-notices-wrapper').empty().append(response.notices);
							$singleBtn.addClass('not-added').removeClass('loading');
                        } else {
                            if (kapee_options.product_open_cart_mini == '1') {
								$('.header-cart').trigger('click');
							}
							$singleBtn.addClass('added').removeClass('loading');
                            $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $singleBtn]);
                        }
						
					}
				},
			});
			
		});
	};
	
	kapee.productQuickView = function () {
		/*
		* Product Quickview
		*/
		kapee.$doc.on("click", ".quickview-btn", function(e) {
            e.preventDefault();
			var $btn = $(this),pid;
			if($btn.hasClass('loading')) return;
			$btn.addClass('loading');
			var pid           = $btn.attr( 'data-id' );
			$.ajax( {
				url    : kapee_options.ajax_url,
				data   : {
					action	: 'kapee_product_quick_view',
					pid   	: pid,
					nonce   : kapee_options.nonce,
				},
				timeout: 10000,
				success: function( response ) {
					$.magnificPopup.open({
						alignTop: true,
						removalDelay: 500,
						overflowY: 'scroll',
						items: {
						  src: '<div class="animated fadeInLeft kapee-quick-view">' + response + '</div>', 
						  type: 'inline'
						},
						callbacks: {
							open: function () {
														
							},							
							beforeClose: function() {
								var quickViewWrap = $( '.kapee-quick-view' );
								quickViewWrap.addClass('fadeOutRight');
							},
						},
					});
					kapee.productGallerySlickSlider();
					setTimeout(function() {
						var form_variation = $( '.product-quick-view' ).find( '.variations_form' );
						if ( form_variation.length > 0 ) {
							form_variation.trigger( 'check_variations' );
							form_variation.trigger( 'reset_image' );						
							form_variation.wc_variation_form();
							form_variation.find( 'select' ).change();
							kapee.singlePageSwatch();
							
						}
						var quickViewWrap = $( '.kapee-quick-view' );
						var imgHeight = quickViewWrap.find( '.images' ).outerHeight() + 30;
						var minHeight = 450;
						if(imgHeight < minHeight){
							imgHeight = minHeight;
						}
						$( '.kapee-quick-view .row > div' ).css({
							'height': imgHeight
						});
						$(".kapee-scroll").nanoScroller({
							paneClass: 'kapee-scroll-pane',
							sliderClass: 'kapee-scroll-slider',
							contentClass: 'kapee-scroll-content',
							preventPageScrolling: false
						});
					}, 1000);
					
					
					$btn.removeClass( 'loading' );
					
					kapee.addToCartAjax();
					kapee.productQuickShop();
					kapee.tooltip();
					kapee.imagelazyload();
					
				},
				error  : function( error ) {
					console.log( error );
					$btn.removeClass( 'loading' );
				},

			} );
        });
	}
	
	kapee.productShowFilter = function () {
		$('.product-show .show-number').on('change', function () {
			 var url = $(this).val(); // get selected value
			if (kapee.$body.hasClass('kapee-catalog-ajax-filter')) {
				$(document.body).trigger('kapee_shop_filter_ajax', url, $(this));
			}else{
				if (url) { // require a URL
					window.location = url; // redirect
				}
				return false;
			}
      });
	}
	
	// Get price js slider
	kapee.priceSlider = function () {
		// woocommerce_price_slider_params is required to continue, ensure the object exists
		if (typeof woocommerce_price_slider_params === 'undefined') {
			return false;
		}
		
		if (!$('#main-content').find('.widget_price_filter').length) {
			return false;
		}
		
		// Get markup ready for slider
		$('input#min_price, input#max_price').hide();
		$('.price_slider, .price_label').show();

		// Price slider uses jquery ui
		var min_price = $('.price_slider_amount #min_price').data('min'),
			max_price = $('.price_slider_amount #max_price').data('max'),
			current_min_price = parseInt(min_price, 10),
			current_max_price = parseInt(max_price, 10);

		if ($('.price_slider_amount #min_price').val() != '') {
			current_min_price = parseInt($('.price_slider_amount #min_price').val(), 10);
		}
		if ($('.price_slider_amount #max_price').val() != '') {
			current_max_price = parseInt($('.price_slider_amount #max_price').val(), 10);
		}

		$(document.body).on('price_slider_create price_slider_slide', function (event, min, max) {
			if (woocommerce_price_slider_params.currency_pos === 'left') {

				$('.price_slider_amount span.from').html(woocommerce_price_slider_params.currency_symbol + min);
				$('.price_slider_amount span.to').html(woocommerce_price_slider_params.currency_symbol + max);

			} else if (woocommerce_price_slider_params.currency_pos === 'left_space') {

				$('.price_slider_amount span.from').html(woocommerce_price_slider_params.currency_symbol + ' ' + min);
				$('.price_slider_amount span.to').html(woocommerce_price_slider_params.currency_symbol + ' ' + max);

			} else if (woocommerce_price_slider_params.currency_pos === 'right') {

				$('.price_slider_amount span.from').html(min + woocommerce_price_slider_params.currency_symbol);
				$('.price_slider_amount span.to').html(max + woocommerce_price_slider_params.currency_symbol);

			} else if (woocommerce_price_slider_params.currency_pos === 'right_space') {

				$('.price_slider_amount span.from').html(min + ' ' + woocommerce_price_slider_params.currency_symbol);
				$('.price_slider_amount span.to').html(max + ' ' + woocommerce_price_slider_params.currency_symbol);

			}

			$(document.body).trigger('price_slider_updated', [min, max]);
		});
		if (typeof $.fn.slider !== 'undefined') {
			$('.price_slider').slider({
				range  : true,
				animate: true,
				min    : min_price,
				max    : max_price,
				values : [current_min_price, current_max_price],
				create : function () {

					$('.price_slider_amount #min_price').val(current_min_price);
					$('.price_slider_amount #max_price').val(current_max_price);

					$(document.body).trigger('price_slider_create', [current_min_price, current_max_price]);
				},
				slide  : function (event, ui) {

					$('input#min_price').val(ui.values[0]);
					$('input#max_price').val(ui.values[1]);

					$(document.body).trigger('price_slider_slide', [ui.values[0], ui.values[1]]);
				},
				change : function (event, ui) {

					$(document.body).trigger('price_slider_change', [ui.values[0], ui.values[1]]);
				}
			});
		}
	};
	
	kapee.productShowHideFilters = function () {
		$('.archive.woocommerce').on('click','.kapee-product-filter-btn',function(e) {
			var $this = $(this),
			filter_content = $('#kapee-filter-widgets');
			$this.toggleClass("active");
			filter_content.toggleClass('active');
			filter_content.slideToggle('slow');			
      });
	}
	
	kapee.productFilterAjax = function () {
		if (!kapee.$body.hasClass('kapee-catalog-ajax-filter')) {
			return;
		}
		/* Price range filter*/
		$(document.body).on('price_slider_change', function (event, ui) {
			var form = $('.price_slider').closest('form').get(0),
			$form = $(form),
			url = $form.attr('action') + '?' + $form.serialize();
			$(document.body).trigger('kapee_shop_filter_ajax', url, $(this));
		});
		kapee.$body.on('click', '.widget_product_categories ul a, .widget_rating_filter ul a, .widget_layered_nav_filters ul a, .widget_product_tag_cloud a', function (e) {
            e.preventDefault();
            var url = $(this).attr('href');
            $(document.body).trigger('kapee_shop_filter_ajax', url, $(this));
        });
		kapee.$body.on('click','.products-view a',function(e) {			
			e.preventDefault();
			var $this = $(this),
			url = $this.attr('href');			
			$this.siblings().removeClass("active");
			$this.addClass('active');
			$(document.body).trigger('kapee_shop_filter_ajax', url, $this);
		});
		
		/* Shop paginataion filter*/
		$('.archive.woocommerce').on('change','.product-show .show-number',function(e) {			
			e.stopPropagation();
			var $this = $(this);
			var url = $this.val();
			$(document.body).trigger('kapee_shop_filter_ajax', url, $this);
		});
		
		/* Shop paginataion filter*/
		$( document ).on( 'click', '.kapee-pagination .page-numbers:not(.current)', function(e) {
			e.preventDefault();
			var $this = $(this);
			var url = $this.attr('href');
			
			$(document.body).trigger('kapee_shop_filter_ajax', url, $this);
		});
		
		$('.archive.woocommerce').find('.woocommerce-ordering').off('change');
		$('.archive.woocommerce').on('change','.woocommerce-ordering',function(e) {
			var $this = $(this);
			var $select = $(e.currentTarget).find('.orderby');
			var url = window.location.href.replace(/&orderby(=[^&]*)?|^orderby(=[^&]*)?&?/g, '').replace(/\?orderby(=[^&]*)?|^orderby(=[^&]*)?&?/g, '?').replace(/\?$/g, '');

			if (url.indexOf('?') !== -1) {
				url = url + ('&orderby=' + $select.val());
			} else {
				url = url + ('?orderby=' + $select.val());
			}
			$(document.body).trigger('kapee_shop_filter_ajax', url, $this);
		});
		
				
		/* Sorting filter*/
		$('.archive.woocommerce').on('click','.kapee_widget_product_sorting li',function(e) {
			e.preventDefault();
			var $this = $(this);
			var element = $this.find('a');
			var url = $this.find('a').attr('href');
			$this.siblings().removeClass("chosen");
			if($this.hasClass('chosen')){
                $this.removeClass('chosen');
            }else{
                $this.addClass('chosen');
            };
			
			$(document.body).trigger('kapee_shop_filter_ajax', url, element);
			
		});
		/* Clear filter*/
		$('.archive.woocommerce').on('click','.widget_layered_nav_filters a, .kapee-clear-filters-wrapp a',function(e) {
			e.preventDefault();
			var $this = $(this);
			
			var url = $this.attr('href');
			
			$(document.body).trigger('kapee_shop_filter_ajax', url, $this);
			
		});
		/* Attribute/Rating filter*/
		$('.archive.woocommerce').on('click','.widget_layered_nav li,.widget_rating_filter li,.widget_layered_nav_filters li',function(e) {
			e.preventDefault();
			var $this = $(this);
			var element = $this.find('a');
			var url = $this.find('a').attr('href');
			
			if($this.hasClass('chosen')){
                $this.removeClass('chosen');
            }else{
                $this.addClass('chosen');
            };
			
			$(document.body).trigger('kapee_shop_filter_ajax', url, element);
			
		});
		
		$(document.body).on('kapee_shop_filter_ajax', function (e, url, element) {

			var $page_title = $('#page-title'),
				$product_container = $('#main-content .products-wrap'),
			    $main_content = $('#main-content');
				
			$('#kapee-filter-widgets').slideUp(200);
			if ($product_container.length > 0) {
				var position = $('.products').offset().top - 200;
				$('html, body').stop().animate({
						scrollTop: position
					},
					1200
				);
			}
			
			if ('?' == url.slice(-1)) {
				url = url.slice(0, -1);
			}

			url = url.replace(/%2C/g, ',');

			history.pushState(null, null, url);

			$(document.body).trigger('kapee_ajax_filter_before_send_request', [url, element]);

			if (kapee.ajaxXHR) {
				kapee.ajaxXHR.abort();
			}
	
			kapee.ajaxXHR = $.get(url, function (res) {
				$main_content.replaceWith($(res).find('#main-content'));
                $page_title.replaceWith($(res).find('#page-title').clone());
				$(document.body).trigger('kapee_ajax_filter_request_end', [res, url]);
			}, 'html');
		});
		
		$(document.body).on('kapee_ajax_filter_before_send_request', function () {
			
			var $product_container = $('#main-content .products-wrap');
			$product_container.addClass('products_overlay');
			$product_container.append('<div class="kapee_product_loading loading"></div>');
			$('#secondary').removeClass('opened');
			$('.kapee-mask-overaly').removeClass('opened');
			
		});
		
		$(document.body).on('kapee_ajax_filter_request_end', function () {
			kapee.priceSlider();
			kapee.kapeeOwlCarousel();
			kapee.initAjaxLoad();
			kapee.tooltip();
			kapee.imagelazyload();
			kapee.widgetToggle();
			kapee.widgetMenuToggle();
			kapee.widgetMaxLimitItem();
			kapee.canvasSidebar();
			kapee.stickySidebar();
			kapee.swatchInLoop();
			kapee.productQuickView();
			$('.kapee-mask-overaly').removeClass('opened');
		});
	}
	
	kapee.productHover = function (){
		/*
		* productHover
		*/
		$('.product-style-4 .product').mouseenter(function(){
			var product_elemnt = $(this);
			var  product_info_elemnt = product_elemnt.find('.product-info');
			var variations_elemnt = product_elemnt.find('.product-variations');
			var variations_height = variations_elemnt.height();			
			if(variations_height && variations_height > 0){				
				product_info_elemnt.css('transform', 'translate3d(0px, -'+variations_height+'px, 0px)');
			} 
		}).mouseleave(function(){
			var product_elemnt = $(this);
			var  product_info_elemnt = product_elemnt.find('.product-info');
			if(product_info_elemnt){
				product_info_elemnt.css('transform', 'translate3d(0px, 0px, 0px)');
			}
			
		});
	};
	
	kapee.productSwatch = function(){
		this.singlePageSwatch();
		this.swatchInLoop();
	}
	kapee.singlePageSwatch = function () {

		var self = this,
			variationForm 		= kapee.$doc.find( 'form.kapee-swatches-wrap.variations_form' ),			
			$term 				= variationForm.find( '.swatch-term' ),
			$activeTerm 		= variationForm.find(
				'.swatch-term:not(.swatch-disabled)' );
		self.$swatchForm 		= variationForm;
		
		// load default value
		$term.each( function () {
			var $this 		= $( this ),
				term 		= $this.attr( 'data-term' ),
				attr 		= $this.parent().attr( 'data-attribute' ),
				$selectbox 	= self.$swatchForm.find( 'select#' + attr ),
				val 		= $selectbox.val();				
			if ( val != '' && term == val ) {				
				$( this ).addClass( 'swatch-selected' );
			}
		} );
		$activeTerm.unbind( 'click' ).on( 'click', function () {

			var $this 		= $( this ),
				term 		= $this.attr( 'data-term' ),
				attr 		= $this.parent().attr( 'data-attribute' ),
				$selectbox 	= self.$swatchForm.find( 'select#' + attr );

			if ( $this.hasClass( 'swatch-disabled' ) ) {
				return false;
			}

			$selectbox.val( term ).trigger( 'change' );
			$this.closest('.kapee-swatches').find( '.swatch-selected' ).removeClass( 'swatch-selected' );
			$this.addClass( 'swatch-selected' );
		} );

		self.$swatchForm.on( 'woocommerce_update_variation_values',
			function () {
				
				self.$swatchForm.find( 'select' ).each( function () {
					var $this 		= $( this );
					var $swatch 	= $this.parent().parent().find( '.kapee-swatches' );
					
					$swatch.find( '.swatch-term' ).removeClass( 'swatch-enabled' ).addClass( 'swatch-disabled' );

					$this.find( 'option.enabled' ).each( function () {
						var val 	= $( this ).val();
						$swatch.find(
							'.swatch-term[data-term="' + val + '"]' ).removeClass( 'swatch-disabled' ).addClass( 'swatch-enabled' );
					} );
				} );
			} );

		self.$swatchForm.on( 'reset_data', function () {
			
		// load default value
		$term.each( function () {
			var $this = $( this ),
				term = $this.attr( 'data-term' ),
				attr = $this.parent().attr( 'data-attribute' ),
				$selectbox = self.$swatchForm.find( 'select#' + attr ),
				val = $selectbox.val();				
			if ( val != '' && term == val ) {				
				$( this ).addClass( 'swatch-selected' );
			}else{
				$( this ).removeClass( 'swatch-selected' );
			}
		} );
		
		} );

	}
	kapee.swatchInLoop = function(){
		var self 			= this,
		swatchesInLoop 		= kapee.$doc.find( 'div.kapee-swatches-wrap' );
		self.$swatches 		= swatchesInLoop;
		self.$swatches.each( function () {
			var $swatches 	= $( this ),
			$term 			= $swatches.find(
				'.swatch-term:not(.swatch-disabled)' ),
			$resetBtn 		= $swatches.find(
				'.reset_variations--loop' ),
			$product 		= $swatches.closest('.product'),
			variationData 	= $.parseJSON(
			$swatches.attr( 'data-product_variations' ) );
			
			if ( $swatches.find( '.kapee-swatches' ).length == 0 ) {
				$swatches.addClass( 'swatch-empty' );
			}
			
			$term.unbind( 'click' ).on( 'click', function () {

				var $this = $( this );

				if ( $this.hasClass( 'swatch-disabled' ) ) {
					return false;
				}

				var term = $this.attr( 'data-term' );				
				
				$product.find( '.swatch-term' ).removeClass( 'swatch-disabled swatch-enabled' );
				$this.parent().find( '.swatch-term.swatch-selected' ).removeClass( 'swatch-selected' );

				if ( $this.hasClass( 'swatch-selected' ) ) {
					$this.parent().removeClass( 'swatch-activated' );
					$product.removeClass( 'swatch-product-swatched' );

					if ( !$product.find( '.swatch-selected' ).length ) {
						$resetBtn.trigger( 'click' );
					}
				} else {
					$this.parent().addClass( 'swatch-activated' );
					$this.addClass( 'swatch-selected' );

					$product.addClass( 'swatch-product-swatched' );
					//$resetBtn.show();
				}
				
				var attributes 			= self.getChosenAttributes(
					$swatches ),
					currentAttributes 	= attributes.data;
				if ( attributes.count === attributes.chosenCount ) {
					self.updateAttributes( $swatches, variationData );

					var matching_variations = self.findMatchingVariations(
						variationData, currentAttributes ),
						variation = matching_variations.shift();

					if ( variation ) {
						// Found variation
						self.foundVariation( $swatches, variation );
					} else {
						
						$resetBtn.trigger( 'click' );
					}
				} else {
					
					self.updateAttributes( $swatches, variationData );
				} 

			} );
			
			$resetBtn.unbind( 'click' ).on( 'click', function () {

				$product.removeClass( 'swatch-product-swatched' );

				$swatches.removeAttr( 'data-variation_id' );
				$swatches.find( '.swatch-swatch' ).removeClass( 'swatch-activated' );
				$swatches.find( '.swatch-term' ).removeClass(
					'swatch-enabled swatch-disabled swatch-selected' );
				
				// reset image
				self.variationsImageUpdate( false, $product );

				$( this ).hide();

				return false;
			} );					
		});
	};
	
	kapee.getChosenAttributes = function ( $swatches ) {

		var data = {},
			count = 0,
			chosen = 0,
			$swatch = $swatches.find( '.kapee-swatches' );

		$swatch.each( function () {
				var attribute_name = 'attribute_' +
						$( this ).attr( 'data-attribute' ),
					value = $( this ).find( '.swatch-term.swatch-selected' ).attr( 'data-term' ) || '';

				if ( value.length > 0 ) {
					chosen++;
				}

				count++;
				data[ attribute_name ] = value;
			//}
		} );

		return {
			'count': count,
			'chosenCount': chosen,
			'data': data,
		};
	}
	
	kapee.updateAttributes = function ( $swatches, variationData ) {

		var self = this,
			attributes = self.getChosenAttributes( $swatches ),
			currentAttributes = attributes.data,
			available_options_count = 0,
			$swatch = $swatches.find( '.kapee-swatches' );

		$swatch.each( function ( idx, el ) {

			var current_attr_sw = $( el ),
				current_attr_name = 'attribute_' +
					current_attr_sw.attr(
						'data-attribute' ),
				selected_attr_val = current_attr_sw.find(
					'.swatch-term.swatch-selected' ).attr( 'data-term' ),
				selected_attr_val_valid = true,
				checkAttributes = $.extend( true, {},
					currentAttributes );
			
			checkAttributes[ current_attr_name ] = '';
			
			var variations = self.findMatchingVariations(
				variationData, checkAttributes );
			
			// Loop through variations.
			for (var num in variations) {
				if ( typeof variations[ num ] !== 'undefined' ) {
					var variationAttributes = variations[ num ].attributes;

					for (var attr_name in variationAttributes) {
						if ( variationAttributes.hasOwnProperty(
								attr_name ) ) {
							var attr_val = variationAttributes[ attr_name ],
								variation_active = '';
							
							if ( attr_name === current_attr_name ) {
								if ( variations[ num ].variation_is_active ) {
									variation_active = 'enabled';
								}
								
								if ( attr_val ) {
									// available
									current_attr_sw.find(
										'.swatch-term[data-term="' + attr_val + '"]' ).addClass( 'swatch-' + variation_active );
								}
								else {
									// apply for all swatches
									current_attr_sw.find( '.swatch-term' ).addClass( 'swatch-' + variation_active );
								}
							}
						}
					}
				}
			}

			available_options_count = current_attr_sw.find(
				'.swatch-term.swatch-enabled' ).length;

			if ( selected_attr_val && (
					available_options_count === 0 || current_attr_sw.find(
						'.swatch-term.swatch-enabled[data-term="' +
						self.addSlashes( selected_attr_val ) + '"]' ).length ===
					0
				) ) {
				selected_attr_val_valid = false;
			}

			// Disable terms not available
			current_attr_sw.find( '.swatch-term:not(.swatch-enabled)' ).addClass( 'swatch-disabled' );

			// Choose selected value.
			if ( selected_attr_val ) {
				// If the previously selected value is no longer available,
				// fall back to the placeholder (it's going to be there).
				if ( !selected_attr_val_valid ) {
					current_attr_sw.find( '.swatch-term.swatch-selected' ).removeClass( 'swatch-selected' );
				}
			}
			else {
				current_attr_sw.find( '.swatch-term.swatch-selected' ).removeClass( 'swatch-selected' );
			}
		} );
	}
	
	kapee.addSlashes = function ( string ) {
		string = string.replace( /'/g, '\\\'' );
		string = string.replace( /"/g, '\\\"' );
		return string;
	}

	kapee.findMatchingVariations = function ( variationData, settings ) {
		var matching = [];
		for (var i = 0; i < variationData.length; i++) {
			var variation = variationData[ i ];

			if ( this.isMatch( variation.attributes, settings ) ) {
				matching.push( variation );
			}
		}
		return matching;
	}

	kapee.isMatch = function ( variation_attributes, attributes ) {
		var match = true;
		for (var attr_name in variation_attributes) {
			if ( variation_attributes.hasOwnProperty( attr_name ) ) {
				var val1 = variation_attributes[ attr_name ];
				var val2 = attributes[ attr_name ];
				if ( val1 !== undefined && val2 !== undefined &&
					val1.length !== 0 && val2.length !== 0 &&
					val1 !== val2 ) {
					match = false;
				}
			}
		}
		return match;
	}

	kapee.foundVariation = function ( $swatches, variation ) {

		var self = this,
		$product = $swatches.closest( '.product' );
		// add variation id
		$swatches.attr( 'data-variation_id', variation.variation_id );

		// update image
		self.variationsImageUpdate( variation, $product );
		
	}

	/**
	 * Stores a default attribute for an element so it can be reset later
	 */
	kapee.setVariationAttr = function ( $el, attr, value ) {
		if ( undefined === $el.attr( 'data-o_' + attr ) ) {
			$el.attr( 'data-o_' + attr, (
				!$el.attr( attr )
			) ? '' : $el.attr( attr ) );
		}
		if ( false === value ) {
			$el.removeAttr( attr );
		}
		else {
			$el.attr( attr, value );
		}
	}

	/**
	 * Reset a default attribute for an element so it can be reset later
	 */
	kapee.resetVariationAttr = function ( $el, attr ) {
		if ( undefined !== $el.attr( 'data-o_' + attr ) ) {
			$el.attr( attr, $el.attr( 'data-o_' + attr ) );
		}
	}

	kapee.variationsImageUpdate = function ( variation, $product ) {

		var self = this,
			$product_img = $product.find( 'img.front-image' );
		
		if ( variation && variation.image_src && variation.image.src && variation.image_src.length > 1 ) {
			
			self.setVariationAttr( $product_img, 'src',
				variation.image_src[ 0 ] );
			self.setVariationAttr( $product_img, 'srcset',
				variation.image_srcset );
			self.setVariationAttr( $product_img, 'sizes',
				variation.image_sizes );
		}
		else {
			self.resetVariationAttr( $product_img, 'src' );
			self.resetVariationAttr( $product_img, 'srcset' );
			self.resetVariationAttr( $product_img, 'sizes' );
		}

	}
	
	kapee.initAjaxLoad = function(){ 
		var button = $( '.kapee-ajax-load' );

		button.each( function( i, val ) {
			var _option = $( this ).data();
			
			if ( _option !== undefined ) {
				var page      = _option.total_page,
					container = _option.container,
					container_element = _option.container_element,
					layout    = _option.layout,
					load_more_label    = _option.load_more_label,
					loading_finished_msg    = _option.loading_finished_msg,
					loading_msg    = kapee_options.js_translate_text.loading_txt,					
					isLoading = false,
					anchor    = $( val ).find( 'a' ),
					next      = $( anchor ).attr( 'href' ),
					i         = 2;

				if ( layout == 'load-more-button' ) {
					$( val ).on( 'click', 'a', function( e ) {
						e.preventDefault();
						anchor = $( val ).find( 'a' );
						next   = $( anchor ).attr( 'href' );

						$( anchor ).html( '<span class="loading"> '+loading_msg+'</span>' );
						
						getData();
					});
				}  else if( layout == 'infinity-scroll' ) {
					var animationFrame = function() {
						anchor = $( val ).find( 'a' );
						next   = $( anchor ).attr( 'href' );

						var bottomOffset = $( '.' + container ).offset().top + $( '.' + container ).height() - $( window ).scrollTop();

						if ( bottomOffset < window.innerHeight && bottomOffset > 0 && ! isLoading ) {
							if ( ! next )
								return;
							isLoading = true;
							if ( page >= i ) {
								$( anchor ).html( '<span class="loading"> '+loading_msg+'</span>' );
								getData();
							}
						}
					}

					var scrollHandler = function() {
						requestAnimationFrame( animationFrame );
					};

					$( window ).scroll( scrollHandler );
				} 
				var getData = function() {
					$.get( next + '', function( data ) {
						var content    = $( '.' + container, data ).wrapInner( '' ).html(),
							newElement = $( '.' + container, data ).find( '.' + container_element );
						next = $( anchor, data ).attr( 'href' );
						if ($('.masonry-grid').length > 0) {
							
							$( '.'+ container ).append(newElement).isotope( 'appended', newElement );
						$( '.'+ container ).imagesLoaded().progress(function() {
							$( '.'+ container ).isotope('layout');
						});
						
						}else{
							$( '.' + container ).append(newElement);
						}
						$( anchor ).text( load_more_label );

						if ( page > i ) {
							if ( kapee_options.permalink == 'plain' ) {
								var link = next.replace( /paged=+[0-9]+/gi, 'paged=' + ( i + 1 ) );
							} else {
								var link = next.replace( /page\/+[0-9]+\//gi, 'page/' + ( i + 1 ) + '/' );
							}

							$( anchor ).attr( 'href', link );
						} else {
							$( anchor ).text( loading_finished_msg );
							$( anchor ).attr( 'href', 'javascript:void(0);' ).addClass( 'disabled' );
						}
						isLoading = false;
						i++;
						kapee.initMagnaficPopup();
						kapee.priceSlider();
						kapee.kapeeOwlCarousel();
						kapee.tooltip();
						kapee.imagelazyload();
						kapee.widgetToggle();
						kapee.widgetMenuToggle();
						kapee.widgetMaxLimitItem();
						kapee.canvasSidebar();
						kapee.stickySidebar();
						kapee.swatchInLoop();
						kapee.productQuickView();
					});
				}
			}
		});
	}
	
	kapee.loadmoreProducts = function(){
		var load_more_products_button = $('.kapee-products-load-more');
		$('.kapee-products-load-more .kapee-load-more').on('click',function(){
			
			var load_more_btn = $(this);
			var page = parseInt(load_more_btn.attr('data-page'));
			var attr = load_more_btn.attr('data-attribute');
			var post_wrap = load_more_btn.closest('.kapee-element').find('.products-wrap');
            var wrap_id = load_more_btn.closest('.kapee-element').attr('id');
			var data = {
				action: 'kapee_loadmore_product',
				nonce: kapee_options.nonce,
				attr: attr,
				page: page,
			};
			if(load_more_btn.hasClass('loading')){ return false;}
			kapee.loadAjaxPost(load_more_btn,data,post_wrap,wrap_id);
		});
		var animationFrame = function () {
			$('.kapee-products-load-more a.infinity-scroll').each(function (i, val) {
				var load_more_btn = $(this);
				var page = parseInt(load_more_btn.attr('data-page'));
				var attr = load_more_btn.attr('data-attribute');
				var post_wrap = load_more_btn.closest('.kapee-element').find('.products-wrap');
				var wrap_id = load_more_btn.closest('.kapee-element').attr('id');
				var bottomOffset = post_wrap.offset().top + post_wrap.height() - $(window).scrollTop();
				if (bottomOffset < window.innerHeight && bottomOffset > 0) {
					
					var page = parseInt(load_more_btn.attr('data-page'));
					if(!load_more_btn.hasClass('kapee-loadmore-disabled')){ 
						if(!kapee.isPostLoading){
							kapee.isPostLoading = true;
							load_more_btn.trigger('click');
						}
					}
				}
			});
		}
		
		var scrollHandler = function () {
			requestAnimationFrame(animationFrame);
		};                    
		$(window).scroll(scrollHandler);		
	}
	
	kapee.loadAjaxPost = function( btn, data, element_wrap, parantElement ){
		var load_more_label = btn.data('load_more_label');
		var loading_finished_msg = btn.data('loading_finished_msg');
		var label_txt = '';
		btn.addClass('process');
		if(btn.hasClass('kapee-loadmore-disabled')){
			return;
		}
		btn.html('<span class="loading"> '+kapee_options.js_translate_text.loading_txt+'</span>');
		$.ajax({
			url: kapee_options.ajax_url,
			data: data,
			dataType: 'json',
			method: 'POST',
			success: function(response) {
				var items = $('' + response['html'] + '');
				if ($.trim(response['success']) == 'ok') {
					//element_wrap.append(items);
					
					if ($('.masonry-grid').length > 0) {
												
						 setTimeout(function () {
						  element_wrap.imagesLoaded().masonry().append(items).masonry( 'appended', items).masonry('layout');
						 }, 500);
						 
						 
					}else{
						element_wrap.append(items);
					}
					
					if ($.trim(response['show_bt']) == '0') {
						$('#' +parantElement + ' .kapee-load-more').addClass('disabled kapee-loadmore-disabled').html(loading_finished_msg);
					} else {
						$('#' +parantElement + ' .kapee-load-more').attr('data-page', data['page'] + 1);
						btn.html(load_more_label);
					}
				}
			},
			error: function(data) {
				console.log('ajax error');
			},
			complete: function() {
				kapee.isPostLoading = false;
				kapee.imagelazyload();
				kapee.initMagnaficPopup();
				kapee.swatchInLoop();				
				kapee.tooltip();
				kapee.productQuickView();					
				btn.removeClass('process');				
			},
		});
	}
	
	kapee.tooltip = function () {
		
		if ( ! kapee_options.product_tooltip ) { return; }
		
		var tooltip_left = ('.whishlist-button a,.yith-wcwl-add-to-wishlist .yith-wcwl-wishlistaddedbrowse a,.product-style-2:not(.list-view) .product-buttons .cart-button,.product-style-2:not(.list-view) .product-buttons .compare-button a,.product-style-2:not(.list-view) .product-buttons .quickview-button a');
		// Bootstrap tooltips
		$(tooltip_left).tooltip({
			animation: false,
			container: 'body',
			trigger : 'hover',
			placement : !kapee.isCheckRTL() ? 'left':'right',
			title: function() {
				return $(this).text();
			}
		});
		$('.products:not(.list-view):not(.product-style-1.grid-columns-4):not(.product-style-1.grid-columns-3):not(.product-style-1.grid-columns-6):not(.product-style-3.grid-columns-4):not(.product-style-3.grid-columns-3):not(.product-style-3.grid-columns-6):not(.product-style-4.grid-columns-4):not(.product-style-4.grid-columns-3):not(.product-style-4.grid-columns-6):not(.product-style-5.grid-columns-4):not(.product-style-5.grid-columns-3):not(.product-style-5.grid-columns-6) .product-buttons .cart-button,.has-sidebar .products:not(.list-view):not(.product-style-1.grid-columns-4):not(.product-style-1.grid-columns-6):not(.product-style-3.grid-columns-4):not(.product-style-3.grid-columns-6):not(.product-style-4.grid-columns-4):not(.product-style-4.grid-columns-6):not(.product-style-5.grid-columns-4):not(.product-style-5.grid-columns-6) .product-buttons .cart-button,.product-buttons .compare-button a,.product-buttons .quickview-button a,.kapee-tooltip').tooltip({
			animation: false,
			container: 'body',
			trigger : 'hover',
			title: function() {
				if( $(this).find('.added_to_cart').length > 0 ) return $(this).find('.add_to_cart_button').text();
				return $(this).text();
			}
		});	 
				
	}
	
	kapee.productImageZoom = function(){
		/*
		 * Single Product image zoom
		 */
		
		if ( ! kapee_options.product_image_zoom ) { return; }
		
		if ( $( '.product-gallery-image .woocommerce-product-gallery__image' ).length > 0 ) {
			var img = $( '.product-gallery-image .woocommerce-product-gallery__image' );
			img.zoom({
				touch: false
			});
		}
	};
	
	kapee.productGallerySlickSlider = function() {
		/*
		 * Product Thumbnails slider
		 */
		//setTimeout(function(){ $( '.kapee-slick-slider' ).not( '.slick-initialized' ).slick(); }, 1000);
		$( '.kapee-slick-slider' ).not( '.slick-initialized' ).slick();
		
		$( '.product-gallery-image.kapee-slick-slider' ).on('afterChange', function(event, slick, currentSlide, nextSlide){
			$('.slick-slide').removeClass('flex-active-slide');
			$('.slick-current').addClass('flex-active-slide');
		});
			
		// Reset the index of image on product variation
		$(document).on( 'found_variation', '.variations_form', function( es, variation ) {		
			if ( variation && variation.image && variation.image.src && variation.image.src.length > 1 ) {
				//setTimeout(function(){ $( '.kapee-slick-slider' ).slick( 'slickGoTo', 0 ); }, 1000);
				$( '.kapee-slick-slider' ).slick( 'slickGoTo', 0 );
			}
		}).on('reset_image', function () {
			$('.kapee-slick-slider').slick( 'slickGoTo', 0 );
			//setTimeout(function(){ $( '.kapee-slick-slider' ).slick( 'slickGoTo', 0 ); }, 1000);
		});
	};
	
	kapee.productSaleCountdown = function() {
		/*
		 * Product Sale CountDown
		 */
		
		$('.product-countdown').each(function(){
			var $this 	= $(this),
				template 	= '',
				data = $this.data(),
				until 	= new Date( 
					data.year,
					data.month -1 || 0,
					data.day || 1,
					data.hours || 0,
					data.minutes || 0,
					data.seconds || 0 
				);
				
			if(data.countdown_style =='countdown-box' ) {
				template = '<span class="days">{dnn}<span>'+kapee_options.js_translate_text.days_text+'</span></span><span class="hour">{hnn}<span>'+kapee_options.js_translate_text.hours_text+'</span></span><span class="minute">{mnn}<span>'+kapee_options.js_translate_text.mins_text+'</span></span><span class="second">{snn}<span>'+kapee_options.js_translate_text.secs_text+'</span></span>';
			}else{
				template = '{dnn}'+kapee_options.js_translate_text.sdays_text+':{hnn}'+kapee_options.js_translate_text.shours_text+':{mnn}'+kapee_options.js_translate_text.smins_text+':{snn}'+kapee_options.js_translate_text.ssecs_text;
			}
			
			// initialize
			$this.countdown({
				until 	: until,
				format 	: 'DHMS',
				layout	: template,
				isRTL	: false
			});
		});
	};
	
	kapee.productReviewLink = function() {
		/*
		 * Scroll Show Product Review Tab
		 */
		
		$('.woocommerce-product-rating .rating-counts').on('click', function (e) {
			$('.woocommerce-review-link').trigger('click');
			 var tabpanel = '#tab-title-reviews';
			 $('html,body').animate({scrollTop:$(tabpanel).offset().top-100}, 750);
		});
	};
	kapee.productPriceSummary = function () {
		/*
		 * Product Price Summary Popup in Mobile
		 */				
		$( document ).on( 'click', '.kapee-mobile-device .exclamation-mark.open', function() {
			var _this = $(this);
			_this.removeClass('open');
			_this.find('.kapee-arrow').addClass('active');
			
		});
		$( document ).on( 'click', '.kapee-mobile-device .product-price-summary .kapee-close', function() {
			$('.product-price-summary .kapee-arrow').removeClass('active');
			$('.product-price-summary').addClass('open');			
		});		
	};
	
	kapee.getProductSizeChart = function () {
		/*
		 * Get Product Size Chart
		 */	
		$('.kapee-ajax-size-chart').on('click', function (e) {
			e.preventDefault();
			 var id = $(this).data('id'); // get post value
			 var data = {
				action			: 'kapee_ajax_get_size_chart',
				'id'			: id,
				nonce   		: kapee_options.nonce,
			};
			var chart_btn = $(this);
			if(chart_btn.hasClass('loading')){
				return false;
			}
			chart_btn.addClass('loading');
			$.ajax({
				type: 'post',
				url: kapee_options.ajax_url,
				data: data,
				beforeSend: function (response) {
					
				},
				complete: function (response) {
					chart_btn.removeClass('loading');
				},
				success: function (response) {
					$(this).magnificPopup({
						removalDelay: 500,
						items: {
							src: response,
							type: 'inline'
						},
						callbacks: {
							open: function () {
								var popupWrap = $( '.kapee-product-sizechart' );
								popupWrap.addClass('animated fadeInLeft');						
							},							
							beforeClose: function() {
								var popupWrap = $( '.kapee-product-sizechart' );
								popupWrap.removeClass('fadeInLeft').addClass('fadeOutRight');
							}, 
							close: function() {
								var popupWrap = $( '.kapee-product-sizechart' );
								popupWrap.removeClass('animated fadeOutRight');
								
							}
						},
					}).magnificPopup('open');
				},
			});
			
      });
	}
	
	kapee.getProductTermsConditions = function () {
		/*
		 * Get Product Terms & Conditions
		 */	
		var cache = [];
		$('.kapee-ajax-block').on('click', function (e) {
			e.preventDefault();
			$(document).find('.product-term-detail').removeClass('active');
			var html = "<div class='product-term-detail kapee-arrow active'> <span class='loading'></span></div>";
			var process = false;
			var $this = $(this);
			var content = $(this).closest('.product-term-wrap').find('.product-term-detail');
			var id = $(this).data('id'); // get post value
			if(id == ''){return false;}
			if( cache['term_condition_'+id] ) {
				$(".product-term-detail").not(content).removeClass('active');
				if(content.length){
					content.addClass('active');
				}else{
					$(this).after(html);
					$this.closest('.product-term-wrap').find('.product-term-detail').addClass('active').html(cache['term_condition_'+id]);
				}
                return;
            }
			 var data = {
				action			: 'kapee_ajax_get_product_terms_conditions',
				'id'			: id,
				nonce   		: kapee_options.nonce,
			};
			
			$(this).after(html);
			//return false;
			$.ajax({
				type: 'post',
				url: kapee_options.ajax_url,
				data: data,
				beforeSend: function (response) {
					
				},
				complete: function (response) {
					content.removeClass('loading');
				},
				success: function (response) {
					//content.html(response);
					$this.closest('.product-term-wrap').find('.product-term-detail').addClass('active').html(response);
					cache['term_condition_'+id] = response;
				},
			});
			
      });
	  
	  /* Hide .terms */
		$(document).mouseup(function (e){
			var container = $(".product-term-wrap");			
			if (!container.is(e.target) && container.has(e.target).length === 0){				
				$(".product-term-detail").removeClass('active');				
			}
		}); 
		
		$( document ).on( 'click', '.product-term-wrap .kapee-close', function() {
			$(".product-term-detail").removeClass('active');
		});
		
	}
	
	kapee.productQuantityPlusMinus = function() {
		/*
		 * Product Quantity Plus/Minus
		 */
		$( document ).on( 'click', '.quantity .plus, .quantity .minus', function() {
            // Get values
            var $qty        = $( this ).closest( '.quantity' ).find( '.qty'),
                currentVal  = parseFloat( $qty.val() ),
                max         = parseFloat( $qty.attr( 'max' ) ),
                min         = parseFloat( $qty.attr( 'min' ) ),
                step        = $qty.attr( 'step' );

            // Format values
            if ( ! currentVal || currentVal === '' || currentVal === 'NaN' ) currentVal = 0;
            if ( max === '' || max === 'NaN' ) max = '';
            if ( min === '' || min === 'NaN' ) min = 0;
            if ( step === 'any' || step === '' || step === undefined || parseFloat( step ) === 'NaN' ) step = '1';

            // Change the value
            if ( $( this ).is( '.plus' ) ) {
                if ( max && ( max == currentVal || currentVal > max ) ) {
                    $qty.val( max );
                } else {
                    $qty.val( currentVal + parseFloat( step ) );
                }
            } else {
                if ( min && ( min == currentVal || currentVal < min ) ) {
                    $qty.val( min );
                } else if ( currentVal > 0 ) {
                    $qty.val( currentVal - parseFloat( step ) );
                }
            }

            // Trigger change event
            $qty.trigger( 'change' );
        });
	};
	
	kapee.productQuickShop = function () {
		/*
		 * Product Buy Now Button click
		 */
		$('body').on('click', '.kapee_quick_buy_button', function() {
			if (kapee_options.product_add_to_cart_ajax) {
				$('.single_add_to_cart_button').addClass('quick-buy-proceed');
			}
			var $this = $(this);
			var product_id = $(this).attr('data-product-id');
			var product_type = $(this).attr('data-product-type');
			var selected = $('form.cart input#kapee_quick_buy_product_' + product_id);
			var productform = selected.parent();
			
			var submit_btn = productform.find('[type="submit"]');
			var is_disabled = submit_btn.is(':disabled');
			
			if ( is_disabled ) {
				$('html, body').animate({
					scrollTop: submit_btn.offset().top - 200
				}, 900);
			} else {
				if(!$this.hasClass('disable')){
					productform.append('<input type="hidden" value="true" name="kapee_quick_buy" />');
				}
				productform.find('.single_add_to_cart_button').trigger('click');
			} 
		});
		
		$('form.cart').change(function () {
			var is_submit_disabled = $(this).find('[type="submit"]').is(':disabled');
			if ( is_submit_disabled ) {
				$('.kapee_quick_buy_button').attr('disabled', 'disable');
			} else {
				$('.kapee_quick_buy_button').removeAttr('disabled');
			}
		});
	}
	
	kapee.productBoughtTogetherInit = function(){ 
		/* Procut Bought Together */
		var self = this;
		// check box click
		$('body').on('click', '.kapee-bought-together-products .product-checkbox input[type=checkbox]', function() {
			if ($(this).is(":checked")) {
				$(this).closest('.product-wrapper').removeClass('kapee-disable-product');				
			}else{
				$(this).closest('.product-wrapper').addClass('kapee-disable-product');
			}
			self.productBoughtTogetherChangeEvent();
		});
		// check all
		self.productBoughtTogetherCheckAllItems();
		// add to cart
		self.productBoughtTogetherAddToCart();
	}
	kapee.productBoughtTogetherChangeEvent = function() {
		var self = this;
		$('.add-items-to-cart').addClass('loading');
		
		var total_price = self.product_bought_together_get_total_price();
		var addon_total_price = self.product_addons_get_total_price();
		var total_addon = self.product_bought_together_product_count();
		if(total_addon){
			$('.add-items-to-cart').removeAttr("disabled");
		}else{
			$('.add-items-to-cart').attr("disabled", true);
		}
		$( '.addons-item .addon-count' ).html( total_addon );
				$( '.addons-item span.items-price' ).html( self.kapee_woo_formated_price(addon_total_price) );
				$( '.items-total span.total-price' ).html( self.kapee_woo_formated_price(total_price) );
		$('.add-items-to-cart').removeClass('loading');
	}
	
	kapee.kapee_woo_formated_price = function(number){
		var self = this;
		return self.kapee_formated_price(number, kapee_options.price_thousand_separator,
		kapee_options.price_decimal_separator, kapee_options.price_decimals, 
		kapee_options.currency_symbol,kapee_options.price_format);
	}
	
	kapee.kapee_formated_price = function(number, thousand_sep, decimal_sep, tofixed, symbol, woo_price_format){
		  var before_text = '';
        var after_text = '';
        number = number || 0;
        tofixed = !isNaN(tofixed = Math.abs(tofixed)) ? tofixed : 2;
        symbol = symbol !== undefined ? symbol : "$";
        thousand_sep = thousand_sep || ",";
        decimal_sep = decimal_sep || ".";
        var negative = number < 0 ? "-" : "",
            i = parseInt(number = Math.abs(+number || 0).toFixed(tofixed), 10) + "",
            j = (j = i.length) > 3 ? j % 3 : 0;
        
        symbol = '<span class="woocommerce-Price-currencySymbol">' + symbol + '</span>';
        
        switch (woo_price_format) {
            case '%1$s%2$s':
                //left
                before_text += symbol;
                break;
            case '%1$s %2$s':
                //left with space
                before_text += symbol + ' ';
                break;
            case '%2$s%1$s':
                //right
                after_text += symbol;
                break;
            case '%2$s %1$s':
                //right with space
                after_text += ' ' + symbol;
                break;
            default:
                //default
                before_text += symbol;
        }       
        
        var woo_price_return = before_text +
            negative + (j ? i.substr(0, j) + thousand_sep : "" ) +
            i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousand_sep) +
            (tofixed ? decimal_sep + Math.abs(number - i).toFixed(tofixed).slice(2) : "") +
            after_text;
        
        woo_price_return = '<span class="woocommerce-Price-amount amount">' + woo_price_return + '</span>';
        
        return woo_price_return;
	}
	
	kapee.productBoughtTogetherCheckAllItems = function(){
		var self = this;
		$('body').on('click', '.check-all-items', function(){
			$('.kapee-together-product:checkbox').not(this).prop('checked', this.checked);
			if ($(this).is(":checked")) {
				$('.kapee-together-product:checkbox').prop('checked', true);  
			} else {
				$('.kapee-together-product:checkbox').prop("checked", false);
			}

			self.productBoughtTogetherChangeEvent();
		});
	}
	
	kapee.productBoughtTogetherAddToCart = function(){
		var self = this;
		$('body').on('click', '.add-items-to-cart:not(.loading)', function(e){
			e.preventDefault();
			var self_this = $(this);
			self_this.addClass('loading');
			
			var all_product_ids = self.product_bought_together_get_checked_product_ids();
			var msg='';
			if( all_product_ids.length === 0 ) {
				msg = kapee_options.bought_together_error;
			} else {
				
				setTimeout(function () {
					for (var i = 0; i < all_product_ids.length; i++ ) {
					$.ajax({
						type: "POST",
						async: false,
						url: kapee_options.ajax_url,
						data: {
							'product_id': all_product_ids[i],
							'action': 'woocommerce_add_to_cart'
						},
						success : function( response ) {
							self.product_bought_together_refresh_fragments( response );
						}
					});
				}
				var miniCartSidebar = $('.minicart-slide-wrapper');
				var closeSidebar 	= $('.kapee-mask-overaly');
				if ( ! miniCartSidebar.hasClass('opened') ) {
					miniCartSidebar.addClass('opened');
					closeSidebar.addClass('opened');
					self.initNanoScroller();
				}
				self_this.removeClass('loading');
				}, 300); 
				
			}
			if(msg != ''){
				$( '.kapee-wc-message' ).html(msg).show(100);
				self_this.removeClass('loading');
				setTimeout(function () {
					$( '.kapee-wc-message' ).slideUp(500);
					
				}, 3000);				
			}
			
		});
	}
	kapee.product_bought_together_get_total_price = function(){
		var tprice = 0,itemprice =0;
		itemprice = $('.items-total-price .item-price').data('itemprice');
		tprice = parseFloat(itemprice);
		$('.kapee-bought-together-products .product-checkbox input[type=checkbox]').each(function() {
			if( $(this).is(':checked') ) {
				tprice += parseFloat( $(this).data( 'price' ) );
			}
		});
		return tprice;
	}
	kapee.product_addons_get_total_price = function(){
		var tprice = 0;
		
		$('.kapee-bought-together-products .product-checkbox input[type=checkbox]').each(function() {
			if( $(this).is(':checked') ) {
				tprice += parseFloat( $(this).data( 'price' ) );
			}
		});
		return tprice;
	}
	kapee.product_bought_together_product_count = function(){
		var pcount = 0;
		$('.kapee-bought-together-products .product-checkbox input[type=checkbox]').each(function() {
			if ($(this).is(':checked')) {
				pcount++;
			}
		});
		return pcount;
	}
	
	// get checked product ids
	kapee.product_bought_together_get_checked_product_ids = function(){
		var pids = [],pidd;
		pidd = $('.items-total-price .item-price').data('id');
		
		pids.push( pidd);
		$('.kapee-bought-together-products .product-checkbox input[type=checkbox]').each(function() {
			if( $(this).is(':checked') ) {
				pids.push( $(this).data( 'id' ) );
			}
		});
		return pids;
	}
	
	// get checked product ids
	kapee.product_bought_together_refresh_fragments = function(response){
		var frags = response.fragments;

            // Block fragments class
            if ( frags ) {
                $.each( frags, function( key ) {
                    $( key ).addClass( 'updating' );
                });
            }
            if ( frags ) {
                $.each( frags, function( key, value ) {
                    $( key ).replaceWith( value );
                });
            }
	}
	
	kapee.wooProductTabsAccordian = function(){
		if( ( $('.woocommerce-tabs.accordion-layout').length > 0 ) || ( $('.woocommerce-tabs.tabs-layout').length > 0 ) ){
			
			var $accordion = $('.tab-content-wrap');
			var hash  = window.location.hash;
			var url   = window.location.href;
		
			if ( hash.toLowerCase().indexOf( 'comment-' ) >= 0 || hash === '#reviews' || hash === '#tab-reviews' ) {
				$accordion.find('.title-reviews').addClass('open');
			} else if ( url.indexOf( 'comment-page-' ) > 0 || url.indexOf( 'cpage=' ) > 0 ) {
				$accordion.find('.title-reviews').addClass('open');
			}else if ( hash === '#tab-additional_information' ) {
				$accordion.find('.title-additional_information').addClass('open');
			}  else {
				$accordion.find('.accordion-title').first().addClass('open');
			}
			$accordion.on('click', '.accordion-title', function( e ) {
				e.preventDefault();
				$(this).parent().siblings().find('.woocommerce-Tabs-panel').slideUp('fast');
				$(".accordion-title").not($(this)).removeClass("open");
				$(this).toggleClass("open").next().slideToggle('fast');
			});
			$(document).on('click', 'a.woocommerce-review-link', function(e) {
				$accordion.find('.accordion-title').removeClass("open");
				$accordion.find('.title-reviews').addClass("open");
			});
		}
	}
	
	kapee.wooProductTabsToggle = function(){
		if($('.woocommerce-tabs.toggle-layout').length > 0){
			var $accordion = $('.tab-content-wrap');
			
			$accordion.find('.accordion-title').addClass('open');
			$accordion.find('.woocommerce-Tabs-panel').css("display", "block");
			$accordion.on('click', '.accordion-title', function( e ) {
				e.preventDefault();				
				var accordion = $(this);
				var accordionContent = accordion.next('.woocommerce-Tabs-panel');				
				accordion.toggleClass("open");
				accordionContent.slideToggle(250);				
			});
			$(document).on('click', 'a.woocommerce-review-link', function(e) {
				e.stopPropagation();
			});
		}
	}
	
	kapee.wooCheckoutStep = function() {
		var btn_next = $('.checkout-next-step'),
		wooStepWrap = $('#checkout-wrapper .panel.panel-default'),
		mutistepCheckout = $('#multi-step-checkout');
		if(mutistepCheckout.length > 0 ){ var formscrollTo = mutistepCheckout.offset().top - 30;}
		
		btn_next.on('click',function(){
			var currentPanel 	= $(this).closest("div.panel.panel-default"),			
			this_btn 		= $(this),
			currentstep 		= currentPanel.data('step'),
			steptitle 		= currentPanel.data('steptitle'),
			nextPanel_id 		= this_btn.data("next"),
			nextPanel 			= $("#"+nextPanel_id),
			billing 			= $( '#customer_billing_details' ),
			shipping 			= $( '#customer_shipping_details' ),
			selector 			= null,
			valid           	= false,
			$offset         = 30,
			posted_data     	= {};
			this_btn.addClass('loading');
			$( 'form.woocommerce-checkout .woocommerce-NoticeGroup.woocommerce-NoticeGroup-checkout' ).remove();
			
			if(steptitle == 'billing' || steptitle =='shipping'){
				
				if(steptitle == 'billing'){
					selector = billing;
				}
				if(steptitle == 'shipping'){
					selector = shipping;
				}
				$( selector ).find( '.validate-required input' ).each( function() {
					posted_data[ $( this ).attr( 'name' ) ] = $( this ).val();
				} );

				$( selector ).find( '.validate-required select' ).each( function() {
					posted_data[ $( this ).attr( 'name' ) ] = $( this ).val();
				} );

				$( selector ).find( '.input-checkbox' ).each( function() {
					if ( $( this ).is( ':checked' ) ) {
						posted_data[ $( this ).attr( 'name' ) ] = $( this ).val();
					}
				} );
				
				var data = {
					action		: 'kapee_validate_checkout',
					type		: steptitle,
					posted_data	: posted_data,
					nonce   	: kapee_options.nonce,
				};

				$.ajax( {
					type: 'POST',
					url: kapee_options.ajax_url,
					data: data,
					async: false,
					success: function( response ) {
						valid = response.valid;

						if ( ! response.valid ) {
							currentPanel.find('.panel-body').prepend( response.html );
							$( 'html, body' ).animate( {
								scrollTop: $( 'form.woocommerce-checkout' ).offset().top - $offset },
							'slow' );
						}
						this_btn.removeClass('loading');
					},
					complete: function() {}
				} );
				
			}else {
				valid = true;
			}
			if ( valid ) {
				
			}else{
				
				return false;
			}
		 
			if ($(nextPanel).hasClass('hidden')) {
				currentPanel.removeClass('active');
				currentPanel.addClass('completed');
				currentPanel.find('.panel-collapse').slideUp('slow').removeClass('show');
				this_btn.removeClass('loading');
				$(nextPanel).removeClass('hidden');
				$(nextPanel).addClass('active');
				$(nextPanel).find('.panel-collapse').slideDown('slow').addClass('show');
				 $( 'html, body' ).animate( {
					scrollTop : formscrollTo },
				'slow' );		
				
			}
			return false;
		});
		$('.edit-action').on('click',function(){
			var currentPanel 	= $(this).closest("div.panel.panel-default"),			
			this_btn 		= $(this),
			currentstep 		= currentPanel.data('step');
			currentPanel.removeClass('completed').addClass('active');
			currentPanel.find('.panel-collapse').slideDown('slow').addClass('show');
			kapee_deactivate_steps(currentstep);
		});
		function kapee_deactivate_steps(currentstep){
			var panels = mutistepCheckout.find('.panel');
			panels.each(function( index ) {
				var current_panel = $(this),
				step = current_panel.data('step');
				if(currentstep < step)
				{
					var stepwrap = $("#step-"+step);
					if(stepwrap.hasClass('active')){
						stepwrap.find('.panel-collapse').slideUp('slow').removeClass('show');
						stepwrap.removeClass('active');
						stepwrap.addClass('hidden');
					}
					if(stepwrap.hasClass('completed')){
						stepwrap.removeClass('completed');
						stepwrap.addClass('hidden');
					}
				}
				
				
			});
		}
	}
	
	kapee.resetVariations = function(){
		var price_html = '',
			discount_html = '',
			price_summary_html = '',
			$price_summer_inner_price = $(document).find(".woocommerce div.summary-inner > p.price"),
			$price_summer_inner_discount = $(document).find(".woocommerce div.summary-inner .product-price-discount"),
			$price_summer_inner_price_summary = $(document).find(".woocommerce div.summary-inner .product-price-summary");
		if($price_summer_inner_price.length){
			price_html = $price_summer_inner_price.html();
        }
		if($price_summer_inner_discount.length){
			discount_html = $price_summer_inner_discount.html();
        }
		if($price_summer_inner_price_summary.length){
			price_summary_html = $price_summer_inner_price_summary.html();
        }
		
		$(document).find( ".variations_form" ).on( "reset_data", function () {
				
				$(document).find(".woocommerce div.summary-inner > p.price").html(price_html);
				$(document).find(".woocommerce div.summary-inner .product-price-discount").html(discount_html);
				$(document).find(".woocommerce div.summary-inner .product-price-summary").html(price_summary_html);
				if($(document).find(".woocommerce .kapee-quick-buy").length){
					$(document).find(".kapee_quick_buy_button").addClass('disabled');
				}
		});
	}
	
	kapee.variationChangeevent = function(){
		$(document).find( ".variations_form" ).on( "woocommerce_variation_select_change", function () {
			if($(document).find(".woocommerce .kapee-quick-buy").length){
				$(document).find(".kapee_quick_buy_button").removeClass('disabled');
			}
			$(document).find( ".single_variation_wrap" ).on( "show_variation", function ( event, variation ) {
				var sell_price = variation.display_price,
				regular_price = variation.display_regular_price,
				formate_sell_price,
				formate_regular_price,
				total = 0,
				discount_per = 0,
				discount = regular_price - sell_price,
				formate_discount,
				price_html = variation.price_html,
				test_var = 1;
				
				
				formate_sell_price = kapee.kapee_formated_price(sell_price, kapee_options.price_thousand_separator,
		kapee_options.price_decimal_separator, kapee_options.price_decimals, 
		kapee_options.currency_symbol,kapee_options.price_format);
		
		formate_regular_price = kapee.kapee_formated_price(regular_price, kapee_options.price_thousand_separator,
		kapee_options.price_decimal_separator, kapee_options.price_decimals, 
		kapee_options.currency_symbol,kapee_options.price_format);
		
		formate_discount = kapee.kapee_formated_price(discount, kapee_options.price_thousand_separator,
		kapee_options.price_decimal_separator, kapee_options.price_decimals, 
		kapee_options.currency_symbol,kapee_options.price_format);
				
				if(sell_price != regular_price){
					$(document).find('.woocommerce div.summary-inner .product-price-discount').show();
					$(document).find('.product-price-summary .discount').show();
					$(document).find('.product-price-summary .overall-discount').show();
					discount_per = Math.round( ((regular_price - sell_price)/regular_price) * 100 );
					$(document).find('.woocommerce div.summary-inner > p.price').html('<ins>' + formate_sell_price  + '</ins><del> ' + formate_regular_price + '</del>');
					$(document).find('.woocommerce div.summary-inner .product-price-discount .on-sale span').html( discount_per);
					$(document).find('.product-price-summary .regular-price > span').html( formate_regular_price);
					$(document).find('.product-price-summary .selling-price span').html( formate_sell_price);
					$(document).find('.product-price-summary .discount span').html( discount_per +'%');
					$(document).find('.product-price-summary .total-discount span').html( formate_sell_price);
					$(document).find('.product-price-summary .overall-discount .amount-discount').html( formate_discount);
					$(document).find('.product-price-summary .overall-discount .percentage-discount').html( '(' + discount_per + '%)');
				}else{
					$(document).find('.woocommerce div.summary-inner > p.price').html(formate_regular_price);
					$(document).find('.product-price-summary .regular-price > span').html( formate_regular_price);
					$(document).find('.product-price-summary .selling-price span').html( formate_regular_price);
					$(document).find('.product-price-summary .total-discount span').html( formate_regular_price);
					$(document).find('.woocommerce div.summary-inner .product-price-discount').hide();
					$(document).find('.product-price-summary .discount').hide();
					$(document).find('.product-price-summary .overall-discount').hide();
				}
				
			} );
		} );
	}
	
	kapee.wcfm_vendor = function(){
		//*******************************************************************
		//* WCFM Vendor
		//*******************************************************************/
		if ($('#_kp_product_ids').length <= 0) {
            return false;
        }
        if (typeof $wcfm_product_select_args === 'undefined') {
            return false;
        }

        $('#_kp_product_ids').select2($wcfm_product_select_args);
	};
	
	kapee.kapeeEqualTabsHeight = function(){
		//*******************************************************************
		//* Equal tabs height
		//*******************************************************************/
		setTimeout(function () {
			$('.products-tab-content').each(function () {
				var $this = $(this);
				if ($this.find('.tab-content').length) {
					$this.find('.tab-content').css({
						'height': 'auto'
					});
					var elem_height = 0;
					$this.find('.tab-content').each(function () {
						var this_elem_h = $(this).height();
						if (elem_height < this_elem_h) {
							elem_height = this_elem_h;
						}
					});
					$this.find('.tab-content').height(elem_height);
				}
			});
		}, 4000);
	};
	
	kapee.kapeeTabEffect = function() {
		// effect click
		$(document).on('click', '.products-tabs .nav-tabs a', function (event) {
			
			var tab_id = $(this).attr('href');
			var tab_animated = 'fadeInUp';
			tab_animated = ( tab_animated == undefined || tab_animated == "" ) ? '' : tab_animated;
			if (tab_animated == "") {
				return false;
			}
			$(tab_id).find('.owl-item.active').each(function (i) {
				
				var t = $(this);
				var style = $(this).attr("style");
				var defaultcss = $(this).attr('default-style');
				style = ( style == undefined ) ? '' : style;
				if(defaultcss == undefined ){
					$(this).attr('default-style',style);
				}else{
					style = $(this).attr('default-style');
				}
				var delay = i * 100;
				t.attr("style", style +
					";-webkit-animation-delay:" + delay + "ms;"
					+ "-moz-animation-delay:" + delay + "ms;"
					+ "-o-animation-delay:" + delay + "ms;"
					+ "animation-delay:" + delay + "ms;"
				).addClass(tab_animated + ' animated').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
					t.removeClass(tab_animated + ' animated');
					t.attr("style", style);
				});
			});
			$(tab_id).addClass('active').siblings().removeClass('active');
		});
	}
	
	kapee.kapeeAjaxTab = function() {
		// Ajax tabs
		$(document).on('click', '.products-tab-container [data-ajax="1"]', function (event) {
			
			var datasource = $(this).data('datasource');
			var limit = $(this).data('limit');
			var slider_data = $(this).data('slider_data');
			var tab_id = $(this).attr('href');
			var current_tab = $(this);
			var data = {
				action      :  'kapee_get_products_tab_data',
				nonce    	:   kapee_options.nonce,
				datasource  :   datasource,
				limit  		:   limit,
				slider_data	:   slider_data,
			};
			
			var container = $(this).closest('.products-tab-container').find('.tab-content');
			
			$.post(kapee_options.ajax_url, data, function(response){
				var items = $( '' + response['html'] + '' );
				
				container.html(items);
				kapee.kapeeOwlCarousel();
			});
		});
	}
	
	kapee.kapeeResponsiveTab = function() {
		// Responsive tabs
		$(document).on('click', '[data-trigger="tab"]', function (event) {
			var href = $( this ).attr( 'href' );
			event.preventDefault();
			$( '[data-toggle="tab"][href="' + href + '"]' ).trigger( 'click' );
		} );

	}
	
	kapee.kapeeProgressbar = function() {
		/*
		* Progress Bar
		*/		
		$('.progress .progress-bar').each(function(){
			if (typeof ($.fn.waypoint) != 'undefined') {
				$(this).waypoint(function(){
					var width = $(this).attr('data-value');
					$(this).animate({
							width: width+'%'
						},
						{
							duration: 1000,
							easing: 'swing'
						}
					);
				}, { offset: '100%', triggerOnce: true });
			}
		});	
	}
	
	kapee.kapeeCounterUp = function() {
		/*
		* Progress Bar
		*/		
		jQuery('.counter').counterUp({
            delay: 20,
            time: 2000
        });
	}
	
	kapee.imageGaleryMasonry = function (){
		
		/*
		* Image Gallery Masonry
		*/
		
		if ( ! $('.kapee-image-gallery.image-gallery-masonry-grid').length > 0 ) return;
		var $layoutMode='masonry';
		
		if($( '.kapee-image-gallery.image-gallery-masonry-grid' ).length){
			$( '.kapee-image-gallery.image-gallery-masonry-grid' ).each(function(){
				
				var image_gallery_container = $( this );
				// initialize Masonry after all images have loaded
                image_gallery_container.imagesLoaded(function() {
					image_gallery_container.isotope({
						itemSelector: '.kapee-gallery',
						isOriginLeft: ! $('body').hasClass('rtl'),
						layoutMode: $layoutMode
					});
				 });
			});			
		}		
	};
	
	kapee.BackgroundParallax = function(){
		/*
		* Parallax Background
		*/		
		if ($(window).width() <= 1024) {
			return;
		}
		$('.kapee-parallax-background').each(function() {
			var $this = $(this);
			if ($this.hasClass('wpb_column')) {
				$this.find('> .vc_column-inner').parallax("50%", 0.3);
			} else {
				$this.parallax("50%", 0.3);
			}
		});
	};
	
	/*
	 * Document ready
	 */ 
	$(document).ready(function(){ 
		kapee.kapeePreLoader();
		kapee.init();		
    });
	
	//After screen resize
    $(window).on('load resize', function () {
		var product_summery_sticky = $(".single-product-page .summary-inner");
		var product_images_sticky = $(".single-product-page .product-images .images-inner");
		var product_summery_sticky_height = product_summery_sticky.height();
		var product_images_sticky_height = product_images_sticky.height();
			
        if ($(window).width() > 769) {
			var offset = 15;
			if ($('#header .header-sticky')[0]) {
				offset = $('#header .header-sticky').height() + 30;
			}
			if ( kapee_options.sticky_image_wrapper ){ 
				if(product_summery_sticky_height > product_images_sticky_height){
					product_images_sticky.stick_in_parent({
						offset_top: offset
				  });
				}
				
			}
			if ( kapee_options.sticky_summary_wrapper ){				
				if(product_summery_sticky_height < product_images_sticky_height){
					product_summery_sticky.stick_in_parent({
						offset_top: offset
				  });
				}
			} 
        } else{
				if ( kapee_options.sticky_image_wrapper ){ 
					product_images_sticky.trigger('sticky_kit:detach');
				}
				if ( kapee_options.sticky_summary_wrapper ){	
					product_summery_sticky.trigger('sticky_kit:detach');
				}
		}
    });
	
})(jQuery);