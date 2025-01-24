( function ( $ ) {
	const CartFlowsHelper = {
		getUrlParameter( param ) {
			const page_url = decodeURIComponent(
					window.location.search.substring( 1 )
				),
				url_variables = page_url.split( '&' );
			let parameter_name, i;

			for ( i = 0; i < url_variables.length; i++ ) {
				parameter_name = url_variables[ i ].split( '=' );

				if ( parameter_name[ 0 ] === param ) {
					return parameter_name[ 1 ] === undefined
						? true
						: parameter_name[ 1 ];
				}
			}
		},
		is_doing_offer_ajax: false,
	};

	const trigger_offer_events = function ( ajax_data ) {
		if ( ajax_data.offer_action ) {
			// Prepare the offer data to sent to event.
			const event_data = {
				action: ajax_data.action,
				flow_id: ajax_data.flow_id,
				input_qty: ajax_data.input_qty,
				offer_action: ajax_data.offer_action,
				offer_type: ajax_data.offer_type,
				order_id: ajax_data.order_id,
				product_id: ajax_data.product_id,
				step_id: ajax_data.step_id,
				variation_id: ajax_data.variation_id,
			};

			// Create a custom event and dispatch it.
			document.dispatchEvent(
				new CustomEvent( ajax_data.action, {
					bubbles: true, // The bubbles will make sure that the code executes in the line-by-line pattern only.
					detail: event_data, // eslint-disable-line quote-props
				} )
			);
		}
	};

	const wcf_process_offer = function ( ajax_data ) {
		ajax_data._nonce = cartflows_offer[ ajax_data.action + '_nonce' ];

		// Stop sending multiple ajax requests at the same time to avoid duplicate orders.
		if ( ! CartFlowsHelper.is_doing_offer_ajax ) {
			CartFlowsHelper.is_doing_offer_ajax = true;
		} else {
			console.log(
				'Muliple Clicks detected: Offer accepted ajax is already in progress.'
			);
			return;
		}

		$.ajax( {
			url: cartflows.ajax_url,
			data: ajax_data,
			dataType: 'json',
			type: 'POST',
			success( data ) {
				const msg = data.message;
				const msg_class = 'wcf-payment-' + data.status;
				// Trigger the offer accept/reject events.
				trigger_offer_events( ajax_data );
				$( 'body' ).trigger( 'wcf-update-msg', [ msg, msg_class ] );
				// Updating the state to ajax complete.
				CartFlowsHelper.is_doing_offer_ajax = false;
				setTimeout( function () {
					window.location.href = data.redirect;
				}, 600 );
			},
		} );
	};

	const wcf_offer_button_action = function () {
		$( 'a[href*="wcf-up-offer-yes"]' ).each( function ( e ) {
			const $this = $( this );

			if ( e === 0 ) {
				$this.attr( 'id', 'wcf-upsell-offer' );
			} else {
				$this.attr( 'id', 'wcf-upsell-offer-' + e );
			}
		} );

		$( 'a[href*="wcf-down-offer-yes"]' ).each( function ( e ) {
			const $this = $( this );

			if ( e === 0 ) {
				$this.attr( 'id', 'wcf-downsell-offer' );
			} else {
				$this.attr( 'id', 'wcf-downsell-offer-' + e );
			}
		} );

		$( document ).on(
			'click',
			'a[href*="wcf-up-offer"], a[href*="wcf-down-offer"]',
			function ( e ) {
				e.preventDefault();

				const $this = $( this ),
					is_disabled = $this.attr( 'disabled' ) ? true : false;

				if ( ! is_disabled ) {
					$this.attr( 'disabled', true );
				} else {
					// Skip the workflow if clicked multiple times on the same buttons.
					console.log(
						'Restricted action: Multiple button clicks detected.'
					);
					return false;
				}

				const href = $this.attr( 'href' ),
					step_id = cartflows_offer.step_id,
					product_id = cartflows_offer.product_id,
					order_id = cartflows_offer.order_id,
					order_key = cartflows_offer.order_key,
					flow_id = cartflows.current_flow;
				let offer_action = 'yes',
					offer_type = 'upsell',
					variation_id = 0,
					input_qty = 0;

				if ( href.indexOf( 'wcf-up-offer' ) !== -1 ) {
					offer_type = 'upsell';

					if ( href.indexOf( 'wcf-up-offer-yes' ) !== -1 ) {
						offer_action = 'yes';
					} else {
						offer_action = 'no';
					}
				}

				if ( href.indexOf( 'wcf-down-offer' ) !== -1 ) {
					offer_type = 'downsell';

					if ( href.indexOf( 'wcf-down-offer-yes' ) !== -1 ) {
						offer_action = 'yes';
					} else {
						offer_action = 'no';
					}
				}

				if ( 'yes' === offer_action ) {
					const variation_wrapper = $(
						'.wcf-offer-product-variation'
					);

					if ( variation_wrapper.length > 0 ) {
						const variation_form = variation_wrapper.find(
								'.variations_form'
							),
							variation_input = variation_form.find(
								'input.variation_id'
							);

						// Set variation id here.
						variation_id = parseInt( variation_input.val() );

						if (
							$( '.var_not_selected' ).length > 0 ||
							'' === variation_id ||
							0 === variation_id
						) {
							// variation_form.find('.variations select').addClass('var_not_selected');
							variation_form
								.find( '.variations select' )
								.each( function () {
									if ( $( this ).val().length === 0 ) {
										$( this ).addClass(
											'var_not_selected'
										);
									}
								} );

							$( [
								document.documentElement,
								document.body,
							] ).animate(
								{
									scrollTop:
										variation_form
											.find( '.variations select' )
											.offset().top - 100,
								},
								1000
							);

							return false;
						}
					}
				}

				if (
					'yes' === cartflows_offer.skip_offer &&
					'yes' === offer_action
				) {
					return;
				}

				$( 'body' ).trigger( 'wcf-show-loader', offer_action );

				if ( 'yes' === offer_action ) {
					action = 'wcf_' + offer_type + '_accepted';
				} else {
					action = 'wcf_' + offer_type + '_rejected';
				}

				const quantity_wrapper = $( '.wcf-offer-product-quantity' );

				if ( quantity_wrapper.length > 0 ) {
					const quantity_input = quantity_wrapper.find(
						'input[name="quantity"]'
					);
					const quantity_value = parseInt( quantity_input.val() );

					if ( quantity_value > 0 ) {
						input_qty = quantity_value;
					}
				}

				const ajax_data = {
					action: '',
					offer_action,
					offer_type,
					step_id,
					product_id,
					variation_id,
					input_qty,
					order_id,
					order_key,
					flow_id,
					stripe_sca_payment: false,
					stripe_intent_id: '',
					_nonce: '',
				};

				if ( 'yes' === offer_action ) {
					if ( 'stripe' === cartflows_offer.payment_method ) {
						ajax_data.action = 'wcf_stripe_sca_check';
						ajax_data._nonce =
							cartflows_offer.wcf_stripe_sca_check_nonce;
						$.ajax( {
							url: cartflows.ajax_url,
							data: ajax_data,
							dataType: 'json',
							type: 'POST',
							success( response ) {
								if (
									response.hasOwnProperty( 'intent_secret' )
								) {
									const stripe = Stripe( response.stripe_pk );
									stripe
										.handleCardPayment(
											response.intent_secret
										)
										.then( function ( res ) {
											if ( res.error ) {
												throw res.error;
											}

											if (
												'requires_capture' !==
													res.paymentIntent.status &&
												'succeeded' !==
													res.paymentIntent.status
											) {
												return;
											}

											ajax_data.action = action;
											ajax_data.stripe_sca_payment = true;
											ajax_data.stripe_intent_id =
												res.paymentIntent.id;
											wcf_process_offer( ajax_data );
										} )
										.catch( function () {
											window.location.reload();
										} );
								} else {
									ajax_data.action = action;
									wcf_process_offer( ajax_data );
								}
							},
						} );
					} else if (
						'mollie_wc_gateway_creditcard' ===
							cartflows_offer.payment_method ||
						'mollie_wc_gateway_ideal' ===
							cartflows_offer.payment_method
					) {
						wcf_handle_mollie_payment_gateways(
							ajax_data,
							cartflows_offer.payment_method
						);
					} else if (
						'ppcp-gateway' === cartflows_offer.payment_method
					) {
						wcf_init_create_paypal_payments_order(
							ajax_data,
							cartflows_offer.payment_method
						);
					} else if (
						'cpsw_stripe' === cartflows_offer.payment_method ||
						'cpsw_stripe_element' === cartflows_offer.payment_method
					) {
						if (
							'cpsw_stripe' === cartflows_offer.payment_method
						) {
							ajax_data.action = 'wcf_cpsw_create_payment_intent';
							ajax_data._nonce =
								cartflows_offer.wcf_cpsw_create_payment_intent_nonce;
						} else {
							ajax_data.action =
								'wcf_cpsw_create_payment_element_intent';
							ajax_data._nonce =
								cartflows_offer.wcf_cpsw_create_payment_element_intent_nonce;
						}
						$.ajax( {
							url: cartflows.ajax_url,
							data: ajax_data,
							dataType: 'json',
							type: 'POST',
							success( response ) {
								if (
									response.hasOwnProperty(
										'client_secret'
									) &&
									'' !== response.client_secret
								) {
									const stripe = Stripe( response.cpsw_pk );
									stripe
										.confirmCardPayment(
											response.client_secret
										)
										.then( function ( resp ) {
											console.log( resp );
											if ( resp.error ) {
												throw resp.error;
											}
											if (
												'requires_capture' !==
													resp.paymentIntent.status &&
												'succeeded' !==
													resp.paymentIntent.status
											) {
												return;
											}

											ajax_data.action = action;
											ajax_data.cpsw_intent_id =
												resp.paymentIntent.id;
											ajax_data.cpsw_payment_method =
												resp.paymentIntent.payment_method;
											wcf_process_offer( ajax_data );
										} )
										.catch( function () {
											window.location.reload();
										} );
								} else {
									ajax_data.action = action;
									wcf_process_offer( ajax_data );
								}
							},
						} );
					} else if (
						'woocommerce_payments' ===
						cartflows_offer.payment_method
					) {
						ajax_data.action = 'wcf_woop_create_payment_intent';
						ajax_data.wcf_wc_payment_nonce =
							cartflows_offer.wcf_woop_create_payment_intent_nonce;
						$.ajax( {
							url: cartflows.ajax_url,
							data: ajax_data,
							dataType: 'json',
							type: 'POST',
							success( response ) {
								if (
									response.hasOwnProperty(
										'client_secret'
									) &&
									'' !== response.client_secret &&
									'succeeded' !== response.status
								) {
									const stripe = Stripe(
										response.client_public
									);

									stripe
										.confirmCardPayment(
											response.client_secret
										)
										.then( function ( resp ) {
											console.log( resp );
											if ( resp.error ) {
												throw resp.error;
											}

											if (
												'requires_capture' !==
													resp.paymentIntent.status &&
												'succeeded' !==
													resp.paymentIntent.status
											) {
												console.log(
													'Order not complete. Received status: ' +
														resp.paymentIntent
															.status
												);
												return;
											}

											ajax_data.action = action;
											ajax_data.woop_intent_id =
												resp.paymentIntent.id;
											ajax_data.woop_payment_method =
												resp.paymentIntent.payment_method;
											wcf_process_offer( ajax_data );
										} )
										.catch( function () {
											window.location.reload();
										} );
								} else {
									ajax_data.action = action;
									ajax_data.woop_intent_id =
										response.client_intend;
									wcf_process_offer( ajax_data );
								}
							},
						} );
					} else if (
						'cppw_paypal' === cartflows_offer.payment_method
					) {
						ajax_data.action = 'wcf_cppw_create_paypal_order';
						ajax_data._nonce =
							cartflows_offer.wcf_cppw_create_paypal_order_nonce;
						$.ajax( {
							url: cartflows.ajax_url,
							data: ajax_data,
							dataType: 'json',
							type: 'POST',
							success( response ) {
								if ( 'succeeded' === response.status ) {
									ajax_data.action = action;
									ajax_data.transaction_id =
										response.transaction_id;
									wcf_process_offer( ajax_data );
								} else {
									ajax_data.action = action;
									wcf_process_offer( ajax_data );
								}
							},
						} );
					} else {
						ajax_data.action = action;
						wcf_process_offer( ajax_data );
					}
				} else {
					ajax_data.action = action;
					wcf_process_offer( ajax_data );
				}

				return false;
			}
		);
	};

	const wcf_handle_mollie_payment_gateways = function ( ajax_data, gateway ) {
		if ( 'mollie_wc_gateway_creditcard' === gateway ) {
			ajax_data.action = 'wcf_mollie_creditcard_process';
			ajax_data._nonce =
				cartflows_offer.wcf_mollie_creditcard_process_nonce;
		}
		if ( 'mollie_wc_gateway_ideal' === gateway ) {
			ajax_data.action = 'wcf_mollie_ideal_process';
			ajax_data._nonce = cartflows_offer.wcf_mollie_ideal_process_nonce;
		}
		$.ajax( {
			url: cartflows.ajax_url,
			data: ajax_data,
			dataType: 'json',
			type: 'POST',
			success( response ) {
				console.log( response );
				if ( 'success' === response.result ) {
					window.location.href = response.redirect;
				} else {
					ajax_data.action = action;
					wcf_process_offer( ajax_data );
				}
			},
		} );
	};

	/*
	 * Function to Create the PayPal Payments order as the offer is accepted.
	 */
	const wcf_init_create_paypal_payments_order = function ( ajax_data ) {
		ajax_data.action = 'wcf_create_paypal_payments_order';
		ajax_data._nonce = cartflows_offer.wcf_create_paypal_order_nonce;

		$.ajax( {
			url: cartflows.ajax_url,
			data: ajax_data,
			dataType: 'json',
			type: 'POST',
			success( response ) {
				console.log( response );
				if ( 'success' === response.status ) {
					// Redirect to PayPal's page for approving the order by making the payment.
					window.location.href = response.redirect;
				} else {
					ajax_data.action =
						'wcf_' + ajax_data.offer_type + '_accepted';
					wcf_process_offer( ajax_data );
				}
			},
		} );
	};

	/*
	 * Function to capture the PayPal Payments order once it is created.
	 */
	const wcf_capture_paypal_payments_order = function () {
		// Capture the call after approving the created order.

		if ( 'undefined' !== typeof cartflows_offer ) {
			const is_ppcp_return = CartFlowsHelper.getUrlParameter(
				'wcf-ppcp-return'
			);

			let ppcp_nonce = '';

			if ( is_ppcp_return ) {
				ppcp_nonce = cartflows_offer.wcf_capture_paypal_order_nonce;

				const ajax_data = {
					action: 'wcf_capture_paypal_order',
					step_id: cartflows_offer.step_id,
					order_id: cartflows_offer.order_id,
					_nonce: ppcp_nonce,
				};

				$( 'body' ).trigger( 'wcf-show-loader', 'yes' );

				$.ajax( {
					url: cartflows.ajax_url,
					data: ajax_data,
					dataType: 'json',
					type: 'POST',
					success( response ) {
						console.log( response );

						if ( 'success' === response.status ) {
							const offer_type = cartflows_offer.offer_type;

							const data = {
								action: 'wcf_' + offer_type + '_accepted',
								offer_action: 'yes',
								offer_type,
								step_id: cartflows_offer.step_id,
								product_id: cartflows_offer.product_id,
								order_id: cartflows_offer.order_id,
								order_key: cartflows_offer.order_key,
								flow_id: cartflows.current_flow,
								stripe_sca_payment: false,
								stripe_intent_id: '',
								_nonce: ppcp_nonce,
							};

							wcf_process_offer( data );
						} else {
							data.action = data.action =
								'wcf_' + data.offer_type + '_accepted';
							wcf_process_offer( data );
						}
					},
				} );
			}
		}
	};

	const wcf_offer_quantity_shortcode = function () {
		/* Offer Quantity Shortcode */
		const offer_quantity = $( '.wcf-offer-product-quantity' );

		if ( offer_quantity.length > 0 ) {
			/* Sync all variation and quanity */
			$( '.wcf-offer-product-quantity .qty' ).on( 'change', function () {
				const qty = $( this ).val();

				$( '.wcf-offer-product-quantity .qty' ).val( qty );
			} );
		}
	};

	const wcf_offer_variation_shortcode = function () {
		/* Offer Variation Shortcode */
		const offer_variation = $(
			'.wcf-offer-product-variation .variations select'
		);

		if ( offer_variation.length > 0 ) {
			$( '.wcf-variable-price-range' ).show().siblings().remove();

			// $('.wcf-offer-product-variation input.variation_id').change(function(){
			$( '.wcf-offer-product-variation .variations select' ).on(
				'change',
				function () {
					const $this = $( this );
					const attr_val = $this.data( 'attribute_name' );

					if ( 0 !== $this.length ) {
						const selected_option = $this.val();

						$(
							'.wcf-offer-product-variation [data-attribute_name ="' +
								attr_val +
								'"]'
						).each( function () {
							$( this ).val( selected_option );
							if (
								'' === selected_option ||
								0 === selected_option
							) {
								$( this ).addClass( 'var_not_selected' );
							} else {
								$( this ).removeClass( 'var_not_selected' );
							}
						} );
					}
				}
			);

			$( '.variations_form' ).on(
				'found_variation',
				function ( event, variation ) {
					const discount_type = cartflows_offer.discount_type,
						discount_value = cartflows_offer.discount_value,
						currency_symbol = cartflows_offer.currency_symbol,
						regular_price = variation.display_price;
					let custom_price = variation.display_price;

					if ( discount_value > 0 ) {
						if ( 'discount_percent' === discount_type ) {
							custom_price =
								custom_price -
								( custom_price * discount_value ) / 100;
						} else if ( 'discount_price' === discount_type ) {
							custom_price = custom_price - discount_value;
						}
					}

					let price_html =
						'<span class="wcf-single-variation-price">';

					if ( regular_price !== custom_price ) {
						price_html += '<span class="wcf-regular-price del">';
						price_html +=
							currency_symbol + regular_price.toFixed( 2 );
						price_html += '</span>';
						price_html += '<span class="wcf-discount-price">';
						price_html +=
							currency_symbol + custom_price.toFixed( 2 );
						price_html += '</span>';
					} else {
						price_html += '<span class="wcf-regular-price">';
						price_html +=
							currency_symbol + regular_price.toFixed( 2 );
						price_html += '</span>';
					}

					price_html += '</span>';

					$( '.wcf-offer-price' ).html( price_html );
				}
			);
		} else {
			$( '.wcf-variable-price-range' ).remove();
			$( '.wcf-offer-price-inner' ).show();
		}
	};

	const wcf_offer_image_shortcode = function () {
		/* Offer product gallery */
		const product_gallery = jQuery( '.woocommerce-product-gallery' );

		if ( product_gallery.length > 0 ) {
			if (
				product_gallery.find( '.woocommerce-product-gallery__image' )
					.length >= 1
			) {
				const slider_obj = jQuery(
					'.woocommerce-product-gallery'
				).flexslider( {
					animation: 'slide',
					animationLoop: false,
					controlNav: 'thumbnails',
					selector: '.slides .woocommerce-product-gallery__image',
					directionNav: false,
				} );

				$( window ).on( 'load', function () {
					slider_obj.trigger( 'resize' );
				} );

				/* Variation change gallery */
				const variations_form = $( '.variations_form' );

				if ( variations_form.length > 0 ) {
					/**
					 * Reset the slide position if the variation has a different image than the current one
					 *
					 * @param {Object} variation variation data.
					 */
					const maybe_trigger_slide_position_reset = function (
						variation
					) {
						const $form = variations_form,
							$product_gallery = $(
								'.woocommerce-product-gallery'
							),
							new_image_id =
								variation && variation.image_id
									? variation.image_id
									: '';
						let reset_slide_position = false;

						if ( $form.attr( 'current-image' ) !== new_image_id ) {
							reset_slide_position = true;
						}

						$form.attr( 'current-image', new_image_id );

						if ( reset_slide_position ) {
							$product_gallery.flexslider( 0 );
						}
					};

					/**
					 * Sets product images for the chosen variation
					 *
					 * @param {Object} variation variation data.
					 */
					const variations_image_update = function ( variation ) {
						const $form = variations_form,
							$product_gallery = $(
								'.woocommerce-product-gallery'
							),
							$gallery_nav = $product_gallery.find(
								'.flex-control-nav'
							);

						if (
							variation &&
							variation.image &&
							variation.image.src &&
							variation.image.src.length > 1
						) {
							// See if the gallery has an image with the same original src as the image we want to switch to.
							const galleryHasImage =
								$gallery_nav.find(
									'li img[data-o_src="' +
										variation.image.gallery_thumbnail_src +
										'"]'
								).length > 0;

							// If the gallery has the image, reset the images. We'll scroll to the correct one.
							if ( galleryHasImage ) {
								variations_image_reset();
							}

							// See if gallery has a matching image we can slide to.
							const slideToImage = $gallery_nav.find(
								'li img[src="' +
									variation.image.gallery_thumbnail_src +
									'"]'
							);

							if ( slideToImage.length > 0 ) {
								slideToImage.trigger( 'click' );
								$form.attr(
									'current-image',
									variation.image_id
								);
								window.setTimeout( function () {
									$( window ).trigger( 'resize' );
									$product_gallery.trigger(
										'woocommerce_gallery_init_zoom'
									);
								}, 20 );
								return;
							}

							const $product_img_wrap = $product_gallery
									.find(
										'.woocommerce-product-gallery__image, .woocommerce-product-gallery__image--placeholder'
									)
									.eq( 0 ),
								$product_img = $product_img_wrap.find(
									'.wp-post-image'
								),
								$product_link = $product_img_wrap
									.find( 'a' )
									.eq( 0 );

							$product_img.wc_set_variation_attr(
								'src',
								variation.image.src
							);
							$product_img.wc_set_variation_attr(
								'height',
								variation.image.src_h
							);
							$product_img.wc_set_variation_attr(
								'width',
								variation.image.src_w
							);
							$product_img.wc_set_variation_attr(
								'srcset',
								variation.image.srcset
							);
							$product_img.wc_set_variation_attr(
								'sizes',
								variation.image.sizes
							);
							$product_img.wc_set_variation_attr(
								'title',
								variation.image.title
							);
							$product_img.wc_set_variation_attr(
								'data-caption',
								variation.image.caption
							);
							$product_img.wc_set_variation_attr(
								'alt',
								variation.image.alt
							);
							$product_img.wc_set_variation_attr(
								'data-src',
								variation.image.full_src
							);
							$product_img.wc_set_variation_attr(
								'data-large_image',
								variation.image.full_src
							);
							$product_img.wc_set_variation_attr(
								'data-large_image_width',
								variation.image.full_src_w
							);
							$product_img.wc_set_variation_attr(
								'data-large_image_height',
								variation.image.full_src_h
							);
							$product_img_wrap.wc_set_variation_attr(
								'data-thumb',
								variation.image.src
							);
							const $gallery_img = $gallery_nav.find(
								'li:eq(0) img'
							);
							$gallery_img.wc_set_variation_attr(
								'src',
								variation.image.gallery_thumbnail_src
							);
							$product_link.wc_set_variation_attr(
								'href',
								variation.image.full_src
							);
						} else {
							variations_image_reset();
						}

						window.setTimeout( function () {
							$( window ).trigger( 'resize' );
							maybe_trigger_slide_position_reset( variation );
						}, 20 );
					};

					/**
					 * Reset main image to defaults.
					 */
					const variations_image_reset = function () {
						const $product_gallery = $(
								'.woocommerce-product-gallery'
							),
							$gallery_nav = $product_gallery.find(
								'.flex-control-nav'
							),
							$gallery_img = $gallery_nav.find( 'li:eq(0) img' ),
							$product_img_wrap = $product_gallery
								.find(
									'.woocommerce-product-gallery__image, .woocommerce-product-gallery__image--placeholder'
								)
								.eq( 0 ),
							$product_img = $product_img_wrap.find(
								'.wp-post-image'
							),
							$product_link = $product_img_wrap
								.find( 'a' )
								.eq( 0 );

						$product_img.wc_reset_variation_attr( 'src' );
						$product_img.wc_reset_variation_attr( 'width' );
						$product_img.wc_reset_variation_attr( 'height' );
						$product_img.wc_reset_variation_attr( 'srcset' );
						$product_img.wc_reset_variation_attr( 'sizes' );
						$product_img.wc_reset_variation_attr( 'title' );
						$product_img.wc_reset_variation_attr( 'data-caption' );
						$product_img.wc_reset_variation_attr( 'alt' );
						$product_img.wc_reset_variation_attr( 'data-src' );
						$product_img.wc_reset_variation_attr(
							'data-large_image'
						);
						$product_img.wc_reset_variation_attr(
							'data-large_image_width'
						);
						$product_img.wc_reset_variation_attr(
							'data-large_image_height'
						);
						$product_img_wrap.wc_reset_variation_attr(
							'data-thumb'
						);
						$gallery_img.wc_reset_variation_attr( 'src' );
						$product_link.wc_reset_variation_attr( 'href' );
					};

					$( '.variations_form' ).on(
						'found_variation',
						function ( event, variation ) {
							variations_image_update( variation );
						}
					);
				}
			}
		}
	};

	// ******************** For Image Slider ********************
	$( window ).on( 'elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction(
			'frontend/element_ready/offer-product-image.default',
			wcf_offer_image_shortcode
		);
	} );

	const wcf_offer_shortcode_events = function () {
		wcf_offer_quantity_shortcode();

		wcf_offer_variation_shortcode();

		wcf_offer_image_shortcode();
	};

	const wcf_facebook_pixel = function () {
		jQuery( document ).ajaxComplete( function ( event, xhr ) {
			if ( ! xhr.hasOwnProperty( 'responseJSON' ) ) {
				return;
			}
			const fragmants = xhr.responseJSON.hasOwnProperty( 'fragments' )
				? xhr.responseJSON.fragments
				: null;
			if (
				fragmants &&
				fragmants.hasOwnProperty( 'added_to_cart_data' )
			) {
				fbq(
					'track',
					'AddToCart',
					fragmants.added_to_cart_data.added_to_cart
				);
			}

			const cartflows_fb_data = cartflows.fb_setting;

			if (
				'enable' ===
					cartflows_fb_data.facebook_pixel_add_payment_info &&
				fragmants &&
				fragmants.hasOwnProperty( 'fb_add_payment_info_data' )
			) {
				// Update cart data for add payment info event
				cartflows.fb_add_payment_info_data =
					fragmants.fb_add_payment_info_data;
			}
		} );
	};

	const wcf_tiktok_pixel = function () {
		jQuery( document ).ajaxComplete( function ( event, xhr ) {
			if ( ! xhr.hasOwnProperty( 'responseJSON' ) ) {
				return;
			}

			/* global ttq */

			const fragmants = xhr.responseJSON.hasOwnProperty( 'fragments' )
				? xhr.responseJSON.fragments
				: null;

			if (
				fragmants &&
				fragmants.hasOwnProperty( 'tiktok_added_to_cart_data' )
			) {
				ttq.track(
					'AddToCart',
					fragmants.tiktok_added_to_cart_data?.added_to_cart
				);
			}

			const cartflows_tiktok_data = cartflows.tik_setting;

			if (
				'enable' ===
					cartflows_tiktok_data?.enable_tiktok_add_payment_info &&
				fragmants &&
				fragmants.hasOwnProperty( 'tiktok_add_payment_info_data' )
			) {
				// Update cart data for add payment info event
				cartflows.tiktok_add_payment_info_data =
					fragmants.tiktok_add_payment_info_data;
			}
		} );
	};

	const wcf_google_analytics = function () {
		jQuery( document ).ajaxComplete( function ( event, xhr, settings ) {
			if ( ! xhr.hasOwnProperty( 'responseJSON' ) ) {
				return;
			}

			const cartflows_ga_data = cartflows.ga_setting;
			const ga_add_to_cart = cartflows_ga_data.enable_add_to_cart;

			const ga_payment_info = cartflows_ga_data.enable_add_payment_info;

			const is_checkout_page = cartflows.is_checkout_page;
			if ( is_checkout_page ) {
				// track only if the bump order is accepted.
				const fragmants = xhr.responseJSON.hasOwnProperty( 'fragments' )
					? xhr.responseJSON.fragments
					: null;

				if ( settings.hasOwnProperty( 'data' ) ) {
					if (
						settings.data.indexOf(
							'action=wcf_bump_order_process'
						) !== -1
					) {
						if ( 'enable' === ga_add_to_cart ) {
							if (
								fragmants &&
								fragmants.hasOwnProperty(
									'ga_added_to_cart_data'
								)
							) {
								gtag(
									'event',
									'add_to_cart',
									JSON.parse(
										fragmants.ga_added_to_cart_data
											.add_to_cart
									)
								);
							}

							if (
								fragmants &&
								fragmants.hasOwnProperty(
									'ga_remove_to_cart_data'
								)
							) {
								gtag(
									'event',
									'remove_from_cart',
									JSON.parse(
										fragmants.ga_remove_to_cart_data
											.remove_from_cart
									)
								);
							}
						}

						if ( 'enable' === ga_payment_info ) {
							if (
								fragmants &&
								fragmants.hasOwnProperty(
									'ga_add_payment_info_data'
								)
							) {
								// Update cart data for add payment info event
								cartflows.add_payment_info_data =
									fragmants.ga_add_payment_info_data;
							}
						}
					}
				}
			}
		} );
	};

	const wcf_google_ads_pixel = function () {
		jQuery( document ).ajaxComplete( function ( event, xhr, settings ) {
			if ( ! xhr.hasOwnProperty( 'responseJSON' ) ) {
				return;
			}

			const cartflows_gads_data = cartflows?.gads_setting;
			const gads_add_to_cart = cartflows_gads_data?.enable_google_ads_add_to_cart;

			const gads_payment_info = cartflows_gads_data?.enable_google_ads_add_payment_info;

			const is_checkout_page = cartflows?.is_checkout_page;
			if ( is_checkout_page ) {
				// track only if the bump order is accepted.
				const fragmants = xhr.responseJSON.hasOwnProperty( 'fragments' )
					? xhr.responseJSON.fragments
					: null;

				if ( settings.hasOwnProperty( 'data' ) ) {
					if (
						settings.data.indexOf(
							'action=wcf_bump_order_process'
						) !== -1
					) {
						if ( 'enable' === gads_add_to_cart ) {
							if (
								fragmants &&
								fragmants.hasOwnProperty(
									'gads_added_to_cart_data'
								)
							) {
								gtag(
									'event',
									'add_to_cart',
									JSON.parse(
										fragmants.gads_added_to_cart_data
											.add_to_cart
									)
								);
							}

							if (
								fragmants &&
								fragmants.hasOwnProperty(
									'gads_remove_to_cart_data'
								)
							) {
								gtag(
									'event',
									'remove_from_cart',
									JSON.parse(
										fragmants.gads_remove_to_cart_data
											.remove_from_cart
									)
								);
							}
						}

						if ( 'enable' === gads_payment_info ) {
							if (
								fragmants &&
								fragmants.hasOwnProperty(
									'gads_add_payment_info_data'
								)
							) {
								// Update cart data for add payment info event
								cartflows.add_payment_info_data =
									fragmants.gads_add_payment_info_data;
							}
						}
					}
				}
			}
		} );
	};

	$( function () {
		$( 'body' ).on( 'wcf-show-loader', function ( event, action ) {
			if ( 'no' === action ) {
				jQuery( '.wcf-note-yes' ).hide();
				jQuery( '.wcf-note-no' ).show();
				jQuery( '.wcf-process-msg' ).hide();
			}

			$( '.wcf-loader-bg' ).addClass( 'show' );
		} );

		$( 'body' ).on( 'wcf-hide-loader', function () {
			console.log( 'Hide Loader' );
			$( '.wcf-loader-bg' ).removeClass( 'show' );
		} );
		$( 'body' ).on( 'wcf-update-msg', function ( event, msg, msg_class ) {
			$( '.wcf-order-msg .wcf-process-msg' )
				.text( msg )
				.addClass( msg_class )
				.show();
		} );

		wcf_offer_button_action();
		wcf_offer_shortcode_events();

		if ( '1' !== cartflows.is_pb_preview ) {
			wcf_facebook_pixel();
			wcf_tiktok_pixel();
			wcf_google_analytics();
			wcf_google_ads_pixel();
		}

		if ( 'undefined' !== typeof cartflows_offer ) {
			const is_mollie_return = CartFlowsHelper.getUrlParameter(
				'wcf-mollie-return'
			);
			let mollie_nonce = '';

			if ( is_mollie_return ) {
				if (
					'mollie_wc_gateway_creditcard' ===
					cartflows_offer.payment_method
				) {
					mollie_nonce =
						cartflows_offer.wcf_mollie_creditcard_process_nonce;
				}
				if (
					'mollie_wc_gateway_ideal' === cartflows_offer.payment_method
				) {
					mollie_nonce =
						cartflows_offer.wcf_mollie_ideal_process_nonce;
				}

				const offer_type = cartflows_offer.offer_type;

				const ajax_data = {
					action: 'wcf_' + offer_type + '_accepted',
					offer_action: 'yes',
					offer_type,
					step_id: cartflows_offer.step_id,
					product_id: cartflows_offer.product_id,
					order_id: cartflows_offer.order_id,
					order_key: cartflows_offer.order_key,
					flow_id: cartflows.current_flow,
					stripe_sca_payment: false,
					stripe_intent_id: '',
					_nonce: mollie_nonce,
				};

				wcf_process_offer( ajax_data );
			}
		}

		wcf_capture_paypal_payments_order();
	} );
} )( jQuery );
