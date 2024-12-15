@extends('layouts.yellow.master')

@section('title', 'Products')

@section('content')
    <article class="post- page type-page status-publish hentry">
        <div class="entry-content">
            <div data-elementor-type="wp-page" class="elementor elementor-">
                <section
                    class="elementor-section elementor-top-section elementor-element elementor-element-f24b193 elementor-section-full_width elementor-section-stretched elementor-section-height-default"
                    data-id="f24b193" data-element_type="section"
                    data-settings="{&quot;stretch_section&quot;:&quot;section-stretched&quot;}"
                    style="width: 1440px; left: -43.7969px;">
                    <div class="elementor-container elementor-column-gap-default" style="flex-wrap: wrap;">
                        @foreach ($categories as $category)
                            <div class="elementor-column elementor-col-33 elementor-top-column elementor-element elementor-element-cf732dc"
                                data-id="cf732dc" data-element_type="column">
                                <div class="elementor-widget-wrap elementor-element-populated">
                                    <div class="elementor-element elementor-element-0487980 elementor-widget elementor-widget-image"
                                        data-id="0487980" data-element_type="widget" data-widget_type="image.default">
                                        <div class="elementor-widget-container">
                                            <style>
                                                /*! elementor - v3.14.0 - 26-06-2023 */
                                                .elementor-widget-image {
                                                    text-align: center
                                                }

                                                .elementor-widget-image a {
                                                    display: inline-block
                                                }

                                                .elementor-widget-image a img[src$=".svg"] {
                                                    width: 48px
                                                }

                                                .elementor-widget-image img {
                                                    vertical-align: middle;
                                                    display: inline-block
                                                }
                                            </style><a href="https://maroonedbd.com/product-category/bag_bari/mens-bag/">
                                                <img fetchpriority="high" decoding="async" width="1024" height="585"
                                                    alt="{{ $category->name }}"
                                                    data-srcset="{{ $category->image_src }} 1024w, {{ $category->image_src }} 300w, {{ $category->image_src }} 768w, {{ $category->image_src }} 1050w"
                                                    data-src="{{ $category->image_src }}"
                                                    data-sizes="(max-width: 1024px) 100vw, 1024px"
                                                    class="attachment-large size-large wp-image-12431 lazyloaded"
                                                    src="{{ $category->image_src }}" loading="lazy"
                                                    sizes="(max-width: 1024px) 100vw, 1024px"
                                                    srcset="{{ $category->image_src }} 1024w, {{ $category->image_src }} 300w, {{ $category->image_src }} 768w, {{ $category->image_src }} 1050w"><noscript><img
                                                        fetchpriority="high" decoding="async" width="1024" height="585"
                                                        src="{{ $category->image_src }}"
                                                        class="attachment-large size-large wp-image-12431" alt=""
                                                        srcset="{{ $category->image_src }} 1024w, {{ $category->image_src }} 300w, {{ $category->image_src }} 768w, {{ $category->image_src }} 1050w"
                                                        sizes="(max-width: 1024px) 100vw, 1024px" /></noscript> </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            </div>
        </div>
    </article>
    <div class="products product-style-1 row grid-view products-wrap grid-columns-4 ">
        @foreach($products as $product)
        <div
            class="col-xl-4 col-lg-3 col-md-4 col-sm-6 col-12 product type-product post-15946 status-publish first instock product_cat-shoes-2023 has-post-thumbnail shipping-taxable product-type-simple">
            <div class="product-wrapper">
                <div class="product-image">
                    <div class="whishlist-button">
                        <div class="yith-wcwl-add-to-wishlist add-to-wishlist-15946 wishlist-fragment on-first-load"
                            data-fragment-ref="15946"
                            data-fragment-options="{&quot;base_url&quot;:&quot;&quot;,&quot;in_default_wishlist&quot;:false,&quot;is_single&quot;:false,&quot;show_exists&quot;:false,&quot;product_id&quot;:15946,&quot;parent_product_id&quot;:15946,&quot;product_type&quot;:&quot;simple&quot;,&quot;show_view&quot;:false,&quot;browse_wishlist_text&quot;:&quot;Browse wishlist&quot;,&quot;already_in_wishslist_text&quot;:&quot;The product is already in your wishlist!&quot;,&quot;product_added_text&quot;:&quot;Product added!&quot;,&quot;heading_icon&quot;:&quot;fa-heart-o&quot;,&quot;available_multi_wishlist&quot;:false,&quot;disable_wishlist&quot;:false,&quot;show_count&quot;:false,&quot;ajax_loading&quot;:false,&quot;loop_position&quot;:&quot;after_add_to_cart&quot;,&quot;item&quot;:&quot;add_to_wishlist&quot;}">
                            <div class="yith-wcwl-add-button"> <a href="?add_to_wishlist=15946&amp;_wpnonce=8d160f84a5"
                                    class="add_to_wishlist single_add_to_wishlist" data-product-id="15946"
                                    data-product-type="simple" data-original-product-id="15946" data-title="Add to wishlist"
                                    rel="nofollow" data-original-title="" title=""> <i
                                        class="yith-wcwl-icon fa fa-heart-o"></i> <span>Add to wishlist</span> </a></div>
                        </div>
                    </div> <a href="{{route('products.show', $product)}}"
                        class="woocommerce-LoopProduct-link" target="_self"><img width="1000" height="1000"
                            alt=""
                            class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail front-image lazyloaded"
                            src="{{asset($product->base_image->src)}}" loading="lazy"
                                width="1000" height="1000"
                                class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail front-image"
                                alt=""/></noscript></a>
                </div>
                <div class="product-info">
                    <div class="product-title-rating">
                        <h3 class="product-title"><a href="{{route('products.show', $product)}}"
                                target="_self">{{$product->name}}</a></h3>
                    </div>
                    <div class="product-price-buttons">
                        <div class="product-price"> <span class="price"><a href="#kapee-signin-up-popup"
                                    class="kapee-login-to-see-prices customer-signinup">Login to see price</a></span></div>
                        <div class="product-buttons-variations">
                            <div class="product-buttons">
                                <div class="cart-button"></div>
                                <div class="whishlist-button">
                                    <div class="yith-wcwl-add-to-wishlist add-to-wishlist-15946 wishlist-fragment on-first-load"
                                        data-fragment-ref="15946"
                                        data-fragment-options="{&quot;base_url&quot;:&quot;&quot;,&quot;in_default_wishlist&quot;:false,&quot;is_single&quot;:false,&quot;show_exists&quot;:false,&quot;product_id&quot;:15946,&quot;parent_product_id&quot;:15946,&quot;product_type&quot;:&quot;simple&quot;,&quot;show_view&quot;:false,&quot;browse_wishlist_text&quot;:&quot;Browse wishlist&quot;,&quot;already_in_wishslist_text&quot;:&quot;The product is already in your wishlist!&quot;,&quot;product_added_text&quot;:&quot;Product added!&quot;,&quot;heading_icon&quot;:&quot;fa-heart-o&quot;,&quot;available_multi_wishlist&quot;:false,&quot;disable_wishlist&quot;:false,&quot;show_count&quot;:false,&quot;ajax_loading&quot;:false,&quot;loop_position&quot;:&quot;after_add_to_cart&quot;,&quot;item&quot;:&quot;add_to_wishlist&quot;}">
                                        <div class="yith-wcwl-add-button"> <a
                                                href="{{route('products.index', $product)}}"
                                                class="add_to_wishlist single_add_to_wishlist" data-product-id="15946"
                                                data-product-type="simple" data-original-product-id="15946"
                                                data-title="Add to wishlist" rel="nofollow" data-original-title=""
                                                title=""> <i class="yith-wcwl-icon fa fa-heart-o"></i> <span>Add to
                                                    wishlist</span> </a></div>
                                    </div>
                                </div>
                                <div class="compare-button"> <a
                                        href="{{route('products.index', $product)}}"
                                        class="compare button" data-product_id="15946" rel="nofollow"
                                        data-original-title="" title="">Compare</a></div>
                                <div class="quickview-button"> <a class="quickview-btn"
                                        href="{{route('products.index', $product)}}" data-id="15946"
                                        data-original-title="" title="">Quick View</a></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endsection
