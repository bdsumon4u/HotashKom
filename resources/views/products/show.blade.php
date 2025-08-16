@extends('layouts.yellow.master')
@php $services = setting('services') @endphp
@push('styles')
    <link rel="stylesheet" href="{{ asset('strokya/vendor/xzoom/xzoom.css') }}">
    <link rel="stylesheet" href="{{ asset('strokya/vendor/xZoom-master/example/css/demo.css') }}">
    <style>
        /* Enhanced product detail styles */
        .product-detail-enhanced {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            padding: 2rem 0;
        }

        .product-gallery-enhanced {
            background: #fff;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
        }

        .product-info-enhanced {
            background: #fff;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
        }

        .product__name {
            color: #2d3748;
            font-weight: 700;
            margin-bottom: 1rem;
            line-height: 1.3;
        }

        .product__prices {
            font-size: 1.8rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 1.5rem;
        }

        .product__description {
            color: #4a5568;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .product__option {
            background: #f7fafc;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            border: 1px solid #e2e8f0;
        }

        .product__option-label {
            color: #2d3748;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .product__actions {
            background: #f7fafc;
            border-radius: 8px;
            padding: 0.5rem;
            margin-top: 1.5rem;
        }

        .btn-primary-enhanced {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 25px;
            padding: 0.8rem 2rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-primary-enhanced:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        .product-tabs-enhanced {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            margin-top: 3rem;
        }

        .product-tabs__list {
            background: #f7fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        /* Old price styling */
        .product-card__old-price {
            color: #dc3545 !important;
            text-decoration: line-through !important;
            margin-left: 10px;
            font-weight: normal !important;
        }

        /* Target del tags specifically */
        del.product-card__old-price {
            color: #dc3545 !important;
            text-decoration: line-through !important;
            margin-left: 10px;
            font-weight: normal !important;
        }

        /* Target spans inside del tags */
        del.product-card__old-price span {
            color: #dc3545 !important;
            text-decoration: line-through !important;
        }

        /* Product card styles moved to master layout */
        }

        @media (max-width: 1200px) {
            .products-view__list[data-layout="grid-5-full"] {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        @media (max-width: 992px) {
            .products-view__list[data-layout="grid-4-full"],
            .products-view__list[data-layout="grid-5-full"] {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 768px) {
            .products-view__list[data-layout="grid-4-full"],
            .products-view__list[data-layout="grid-5-full"] {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 576px) {
            .products-view__list[data-layout="grid-4-full"],
            .products-view__list[data-layout="grid-5-full"] {
                grid-template-columns: 1fr;
            }
        }

        .product-tabs__item {
            position: relative;
            color: #4a5568;
            transition: all 0.3s ease;
        }

        .product-tabs__item--active {
            color: #667eea;
            background: #fff;
        }

        .product-tabs__item--active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 3px 3px 0 0;
        }

        .product-tabs__content {
            background: #fff;
            padding: 2rem;
        }

        .product__meta {
            background: #f7fafc;
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1.5rem;
        }

        .product__meta li {
            color: #4a5568;
            margin-bottom: 0.5rem;
        }

        .product__rating-stars {
            color: #ffd700;
        }

        .product__availability {
            display: inline-block;
            background: linear-gradient(135deg, #48bb78, #38a169);
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            .product-detail-enhanced {
                padding: 1rem 0;
            }

            /* Product info padding adjusted for mobile */

            .product__name {
                font-size: 1.5rem;
            }

            .product__prices {
                font-size: 1.5rem;
            }
        }
        @media (min-width:992px){.product--layout--columnar .product__content{-ms-grid-columns:380px auto 260px;grid-template-columns:[gallery] 380px [info] auto [sidebar] 260px;grid-template-rows:auto auto auto auto;grid-column-gap:0}.product--layout--columnar .product__gallery{grid-row-start:1;grid-row-end:4;min-height:0}}@media (min-width:992px) and (-ms-high-contrast:none),screen and (min-width:992px) and (-ms-high-contrast:active){.product--layout--columnar .product__gallery{margin-right:0}}@media (min-width:992px){.product--layout--columnar .product__info{-ms-grid-row:1;-ms-grid-column:2;grid-row:1;grid-column:info;padding:0 30px;min-height:0}.product--layout--columnar .product__sidebar{-ms-grid-row:1;-ms-grid-row-span:4;-ms-grid-column:3;grid-column:sidebar;grid-row-start:1;grid-row-end:4;min-height:0;border-left:2px solid #f0f0f0;padding-top:10px;padding-left:30px;padding-bottom:20px}.product--layout--columnar .product__footer{-ms-grid-row:2;-ms-grid-column:2;grid-row:2;grid-column:info;padding:0 30px;min-height:0}.product--layout--columnar .product__wishlist-compare{position:absolute;display:-ms-flexbox;display:flex;-ms-flex-direction:column;flex-direction:column;right:8px}.product--layout--columnar .product__wishlist-compare>*+*{margin-top:2px}.product--layout--columnar .product__name{font-size:24px;margin-bottom:12px;padding-right:16px}.product--layout--columnar .product__description{margin:10px 0 12px;font-size:15px}.product--layout--columnar .product__features{display:block}.product--layout--columnar .product__meta{margin:12px 0 0;padding:0;border-top:none}.product--layout--columnar .product__meta-availability{display:none}.product--layout--columnar .product__footer{-ms-flex-direction:column;flex-direction:column}.product--layout--columnar .product__share-links{margin:12px 0 0 1px}.product--layout--columnar .product__prices{margin-top:20px;margin-bottom:24px;line-height:28px}.product--layout--columnar .product__new-price{display:block}.product--layout--columnar .product__quantity{width:88px}.product--layout--columnar .product__actions{-ms-flex-wrap:nowrap;flex-wrap:nowrap}.product--layout--columnar .product__actions-item--addtocart{-ms-flex-positive:1;flex-grow:1}.product--layout--columnar .product__actions-item--addtocart .btn{width:100%;padding-left:.5rem;padding-right:.5rem}.product--layout--columnar .product__actions-item--compare,.product--layout--columnar .product__actions-item--wishlist{display:none}.product--layout--columnar .product__availability{display:block;font-size:14px}}@media (min-width:992px) and (max-width:1199px){.product--layout--columnar .product__content{-ms-grid-columns:320px auto 200px;grid-template-columns:[gallery] 320px [info] auto [sidebar] 200px}.product--layout--columnar .product__sidebar{padding-left:24px}.product--layout--columnar .product__option{margin-bottom:12px}.product--layout--columnar .product__actions{-ms-flex-wrap:wrap;flex-wrap:wrap;margin:0}.product--layout--columnar .product__quantity{width:100px}.product--layout--columnar .product__actions-item{margin:0}.product--layout--columnar .product__actions-item--addtocart{margin-top:16px}}@media (min-width:992px){.product--layout--sidebar .product__content{-ms-grid-columns:50% 50%;grid-template-columns:[gallery] calc(50% - 16px) [info] calc(50% - 16px);grid-column-gap:32px}}@media (min-width:992px) and (-ms-high-contrast:none),screen and (min-width:992px) and (-ms-high-contrast:active){.product--layout--sidebar .product__gallery{margin-right:32px}}@media (min-width:992px){.product--layout--sidebar .product__name{font-size:24px;margin-bottom:12px}.product--layout--sidebar .product__footer{display:block;margin-top:18px}.product--layout--sidebar .product__share-links{margin:12px 0 0}}@media (min-width:992px) and (max-width:1199px){.product--layout--quickview .product__content{grid-template-columns:[gallery] calc(50% - 16px) [info] calc(50% - 16px);grid-column-gap:32px}.product--layout--quickview .product__name{margin-bottom:12px}.product--layout--quickview .product__footer{display:block;margin-top:18px}.product--layout--quickview .product__share-links{margin:12px 0 0}}@media (min-width:768px) and (max-width:991px){.product--layout--quickview .product__content{display:block}.product--layout--quickview .product__gallery{margin-bottom:24px}.product--layout--quickview .product__name{font-size:24px;margin-bottom:18px}}.product-gallery__featured{box-shadow:inset 0 0 0 2px #f2f2f2;padding:2px;border-radius:2px}.product-gallery__featured a{display:block;padding:20px}.product-gallery__carousel{margin-top:16px}.product-gallery__carousel-item{display:block;box-shadow:inset 0 0 0 2px #f2f2f2;padding:12px;border-radius:2px}.product-gallery__carousel-item--active{box-shadow:inset 0 0 0 2px var(--primary)}.product-tabs{margin-top:50px}.product-tabs__list{display:-ms-flexbox;display:flex;overflow-x:auto;-webkit-overflow-scrolling:touch;margin-bottom:-2px}.product-tabs__list:after,.product-tabs__list:before{content:"";display:block;width:8px;-ms-flex-negative:0;flex-shrink:0}.product-tabs__item{font-size:20px;padding:18px 48px;border-bottom:2px solid transparent;color:inherit;font-weight:500;border-radius:3px 3px 0 0;transition:all .15s}.product-tabs__item:hover{color:inherit;background:#f7f7f7;border-bottom-color:#d9d9d9}.product-tabs__item:first-child{margin-left:auto}.product-tabs__item:last-child{margin-right:auto}.product-tabs__item--active{transition-duration:0s}.product-tabs__item--active,.product-tabs__item--active:hover{cursor:default;border-bottom-color:var(--primary);background:transparent}.product-tabs__content{border:2px solid #f0f0f0;border-radius:2px;padding:80px 90px}.product-tabs__pane{overflow:hidden;height:0;opacity:0;transition:opacity .5s}.product-tabs__pane--active{overflow:visible;height:auto;opacity:1}.product-tabs--layout--sidebar .product-tabs__item{padding:14px 30px}.product-tabs--layout--sidebar .product-tabs__content{padding:48px 50px}@media (min-width:992px) and (max-width:1199px){.product-tabs__content{padding:60px 70px}}
        .product__content{display:-ms-grid;display:grid;-ms-grid-columns:50% 50%;grid-template-columns:[gallery] calc(50% - 20px) [info] calc(50% - 20px);grid-template-rows:auto auto auto auto auto;grid-column-gap:40px}.product__gallery{-ms-grid-row:1;-ms-grid-row-span:6;-ms-grid-column:1;grid-row-start:1;grid-row-end:6;min-height:0}@media (-ms-high-contrast:none),screen and (-ms-high-contrast:active){.product__gallery{margin-right:40px}}.product__info{-ms-grid-row:1;-ms-grid-column:2;position:relative;min-height:0}.product__sidebar{-ms-grid-row:2;-ms-grid-column:2}.product__footer{-ms-grid-row:3;-ms-grid-column:2}.product__wishlist-compare{display:none}.product__name{margin-bottom:22px}.product__rating{display:-ms-flexbox;display:flex;margin-bottom:5px}.product__rating-stars{padding-top:2px;margin-right:12px}.product__rating-legend{font-size:14px;line-height:20px;color:#b3b3b3}.product__rating-legend a{color:inherit;transition:color .2s}.product__rating-legend a:hover{color:#1a66ff}.product__rating-legend span{content:"/";padding:0 7px}.product__description{font-size:16px}.product__features{display:none;list-style:none;padding:0;margin:0;font-size:14px}.product__features li{padding:1px 0 1px 13px;position:relative}.product__features li:before{content:"";display:block;position:absolute;left:0;top:9px;width:5px;height:5px;border-radius:2.5px;border:1px solid currentColor}.product__meta{list-style:none;margin:12px 0 0;padding:12px 0 0;display:-ms-flexbox;display:flex;-ms-flex-wrap:wrap;flex-wrap:wrap;color:#444;font-size:14px;border-top:1px solid #ebebeb}.product__meta li{margin-right:18px}.product__meta a{color:inherit;transition:color .2s}.product__meta a:hover{color:#1a66ff}.product__availability{display:none}.product__prices{margin-bottom:14px;font-size:20px;font-weight:700;letter-spacing:-.03em;color:#3d464d}.product__new-price{color:#32cc32;margin-right:5px;}.product__old-price{color:#ff2626;font-weight:400;text-decoration:line-through}.product__option{margin-bottom:18px}.product__option:last-child{margin-bottom:0}.product__option-label{font-size:15px;text-transform:uppercase;font-weight:500;color:#6c757d;padding-bottom:2px}.product__actions{display:-ms-flexbox;display:flex;-ms-flex-wrap:wrap;flex-wrap:wrap;margin:-4px 0}.product__actions-item{margin:4px 0}.product__quantity{width:120px}.product__footer{margin-top:32px;display:-ms-flexbox;display:flex;-ms-flex-pack:justify;justify-content:space-between}.product__share-links{margin:2px 0 2px 24px}@media (min-width:992px) and (max-width:1199px){.product__name{font-size:24px;margin-bottom:18px}}@media (min-width:768px) and (max-width:991px){.product__content{-ms-grid-columns:44% 56%;grid-template-columns:[gallery] calc(44% - 15px) [info] calc(56% - 15px);grid-column-gap:30px}}@media (min-width:768px) and (max-width:991px) and (-ms-high-contrast:none),screen and (min-width:768px) and (max-width:991px) and (-ms-high-contrast:active){.product__gallery{margin-right:30px}}@media (min-width:768px) and (max-width:991px){.product__name{font-size:24px;margin-bottom:18px}.product__footer{display:block;margin-top:18px}.product__share-links{margin:12px 0 0}}@media (max-width:767px){.product__content{display:block}.product__gallery{margin-bottom:24px}.product__name{font-size:24px;margin-bottom:18px}}@media (max-width:559px){.product__footer{display:block;margin-top:24px}.product__share-links{margin:12px 0 0}}@media (min-width:992px){.product--layout--columnar .product__content{-ms-grid-columns:380px auto 260px;grid-template-columns:[gallery] 380px [info] auto [sidebar] 260px;grid-template-rows:auto auto auto auto;grid-column-gap:0}.product--layout--columnar .product__gallery{grid-row-start:1;grid-row-end:4;min-height:0}}@media (min-width:992px) and (-ms-high-contrast:none),screen and (min-width:992px) and (-ms-high-contrast:active){.product--layout--columnar .product__gallery{margin-right:0}}@media (min-width:992px){.product--layout--columnar .product__info{-ms-grid-row:1;-ms-grid-column:2;grid-row:1;grid-column:info;padding:0 30px;min-height:0}.product--layout--columnar .product__sidebar{-ms-grid-row:1;-ms-grid-row-span:4;-ms-grid-column:3;grid-column:sidebar;grid-row-start:1;grid-row-end:4;min-height:0;border-left:2px solid #f0f0f0;padding-top:10px;padding-left:30px;padding-bottom:20px}.product--layout--columnar .product__footer{-ms-grid-row:2;-ms-grid-column:2;grid-row:2;grid-column:info;padding:0 30px;min-height:0}.product--layout--columnar .product__wishlist-compare{position:absolute;display:-ms-flexbox;display:flex;-ms-flex-direction:column;flex-direction:column;right:8px}.product--layout--columnar .product__wishlist-compare>*+*{margin-top:2px}.product--layout--columnar .product__name{font-size:24px;margin-bottom:12px;padding-right:16px}.product--layout--columnar .product__description{margin:10px 0 12px;font-size:15px}.product--layout--columnar .product__features{display:block}.product--layout--columnar .product__meta{margin:12px 0 0;padding:0;border-top:none}.product--layout--columnar .product__meta-availability{display:none}.product--layout--columnar .product__footer{-ms-flex-direction:column;flex-direction:column}.product--layout--columnar .product__share-links{margin:12px 0 0 1px}.product--layout--columnar .product__prices{margin-top:20px;margin-bottom:24px;line-height:28px}.product--layout--columnar .product__new-price{display:block}.product--layout--columnar .product__quantity{width:88px}.product--layout--columnar .product__actions{-ms-flex-wrap:nowrap;flex-wrap:nowrap}.product--layout--columnar .product__actions-item--addtocart{-ms-flex-positive:1;flex-grow:1}.product--layout--columnar .product__actions-item--addtocart .btn{width:100%;padding-left:.5rem;padding-right:.5rem}.product--layout--columnar .product__actions-item--compare,.product--layout--columnar .product__actions-item--wishlist{display:none}.product--layout--columnar .product__availability{display:block;font-size:14px}}@media (min-width:992px) and (max-width:1199px){.product--layout--columnar .product__content{-ms-grid-columns:320px auto 200px;grid-template-columns:[gallery] 320px [info] auto [sidebar] 200px}.product--layout--columnar .product__sidebar{padding-left:24px}.product--layout--columnar .product__option{margin-bottom:12px}.product--layout--columnar .product__actions{-ms-flex-wrap:wrap;flex-wrap:wrap;margin:0}.product--layout--columnar .product__quantity{width:100px}.product--layout--columnar .product__actions-item{margin:0}.product--layout--columnar .product__actions-item--addtocart{margin-top:16px}}@media (min-width:992px){.product--layout--sidebar .product__content{-ms-grid-columns:50% 50%;grid-template-columns:[gallery] calc(50% - 16px) [info] calc(50% - 16px);grid-column-gap:32px}}@media (min-width:992px) and (-ms-high-contrast:none),screen and (min-width:992px) and (-ms-high-contrast:active){.product--layout--sidebar .product__gallery{margin-right:32px}}@media (min-width:992px){.product--layout--sidebar .product__name{font-size:24px;margin-bottom:12px}.product--layout--sidebar .product__footer{display:block;margin-top:18px}.product--layout--sidebar .product__share-links{margin:12px 0 0}}@media (min-width:992px) and (max-width:1199px){.product--layout--quickview .product__content{grid-template-columns:[gallery] calc(50% - 16px) [info] calc(50% - 16px);grid-column-gap:32px}.product--layout--quickview .product__name{margin-bottom:12px}.product--layout--quickview .product__footer{display:block;margin-top:18px}.product--layout--quickview .product__share-links{margin:12px 0 0}}@media (min-width:768px) and (max-width:991px){.product--layout--quickview .product__content{display:block}.product--layout--quickview .product__gallery{margin-bottom:24px}.product--layout--quickview .product__name{font-size:24px;margin-bottom:18px}}.product-gallery__featured{box-shadow:inset 0 0 0 2px #f2f2f2;padding:2px;border-radius:2px}.product-gallery__featured a{display:block;padding:20px}.product-gallery__carousel{margin-top:16px}.product-gallery__carousel-item{display:block;box-shadow:inset 0 0 0 2px #f2f2f2;padding:12px;border-radius:2px}.product-gallery__carousel-item--active{box-shadow:inset 0 0 0 2px var(--primary)}.product-tabs{margin-top:50px}.product-tabs__list{display:-ms-flexbox;display:flex;overflow-x:auto;-webkit-overflow-scrolling:touch;margin-bottom:-2px}.product-tabs__list:after,.product-tabs__list:before{content:"";display:block;width:8px;-ms-flex-negative:0;flex-shrink:0}.product-tabs__item{font-size:20px;padding:18px 48px;border-bottom:2px solid transparent;color:inherit;font-weight:500;border-radius:3px 3px 0 0;transition:all .15s}.product-tabs__item:hover{color:inherit;background:#f7f7f7;border-bottom-color:#d9d9d9}.product-tabs__item:first-child{margin-left:auto}.product-tabs__item:last-child{margin-right:auto}.product-tabs__item--active{transition-duration:0s}.product-tabs__item--active,.product-tabs__item--active:hover{cursor:default;border-bottom-color:var(--primary);background:transparent}.product-tabs__content{border:2px solid #f0f0f0;border-radius:2px;padding:80px 90px}.product-tabs__pane{overflow:hidden;height:0;opacity:0;transition:opacity .5s}.product-tabs__pane--active{overflow:visible;height:auto;opacity:1}.product-tabs--layout--sidebar .product-tabs__item{padding:14px 30px}.product-tabs--layout--sidebar .product-tabs__content{padding:48px 50px}@media (min-width:992px) and (max-width:1199px){.product-tabs__content{padding:60px 70px}}@media (min-width:768px) and (max-width:991px){.product-tabs .product-tabs__item{padding:14px 30px}.product-tabs .product-tabs__content{padding:40px 50px}}@media (max-width:767px){.product-tabs .product-tabs__item{padding:14px 30px}.product-tabs .product-tabs__content{padding:24px}}.products-list__body{-ms-flex-wrap:wrap;flex-wrap:wrap}.products-list__body,.products-list__item{display:-ms-flexbox;display:flex}.products-list__item .product-card{width:100%;-ms-flex-negative:0;flex-shrink:0}.products-list[data-layout^=grid-] .product-card .product-card__image{padding:3px}.products-list[data-layout^=grid-] .product-card .product-card__info{padding:0 24px}.products-list[data-layout^=grid-] .product-card .product-card__actions{padding:0 24px 24px}.products-list[data-layout^=grid-] .product-card .product-card__availability,.products-list[data-layout^=grid-] .product-card .product-card__description,.products-list[data-layout^=grid-] .product-card .product-card__features-list{display:none}.products-list[data-layout^=grid-][data-with-features=true] .product-card .product-card__features-list{display:block}.products-list[data-layout=grid-3-sidebar] .products-list__body{margin:-8px -6px}.products-list[data-layout=grid-3-sidebar] .products-list__item{width:calc(33.33333% - 12px);margin:8px 6px}.products-list[data-layout=grid-3-sidebar] .product-card .product-card__buttons .btn{font-size:.875rem;height:calc(1.875rem + 2px);line-height:1.25;padding:.375rem 1rem;font-weight:500}.products-list[data-layout=grid-3-sidebar] .product-card .product-card__buttons .btn.btn-svg-icon{width:calc(1.875rem + 2px)}@media (hover:hover){.products-list[data-layout=grid-3-sidebar] .product-card .product-card__buttons{display:none}.products-list[data-layout=grid-3-sidebar] .product-card:hover{position:relative;z-index:3}.products-list[data-layout=grid-3-sidebar] .product-card:hover .product-card__buttons{display:-ms-flexbox;display:flex}}@media (-ms-high-contrast:none),screen and (-ms-high-contrast:active){.products-list[data-layout=grid-3-sidebar] .product-card .product-card__buttons{display:none}.products-list[data-layout=grid-3-sidebar] .product-card:hover{position:relative;z-index:3}.products-list[data-layout=grid-3-sidebar] .product-card:hover .product-card__buttons{display:-ms-flexbox;display:flex}}@media (max-width:1199px) and (min-width:480px){.products-list[data-layout=grid-3-sidebar] .product-card .product-card__image{padding:15px}.products-list[data-layout=grid-3-sidebar] .product-card .product-card__badges-list{left:16px;top:16px}.products-list[data-layout=grid-3-sidebar] .product-card .product-card__info{padding:0 15px}.products-list[data-layout=grid-3-sidebar] .product-card .product-card__actions{padding:0 15px 15px}.products-list[data-layout=grid-3-sidebar] .product-card .product-card__buttons .btn{font-size:.8125rem;height:calc(1.5rem + 2px);line-height:1.25;padding:.25rem .5625rem;font-weight:500}.products-list[data-layout=grid-3-sidebar] .product-card .product-card__buttons .btn.btn-svg-icon{width:calc(1.5rem + 2px)}}@media (max-width:1199px) and (min-width:480px) and (hover:hover){.products-list[data-layout=grid-3-sidebar] .product-card .product-card__buttons{display:none}.products-list[data-layout=grid-3-sidebar] .product-card:hover{position:relative;z-index:3}.products-list[data-layout=grid-3-sidebar] .product-card:hover .product-card__buttons{display:-ms-flexbox;display:flex}}@media (max-width:1199px) and (min-width:480px) and (-ms-high-contrast:none),screen and (max-width:1199px) and (min-width:480px) and (-ms-high-contrast:active){.products-list[data-layout=grid-3-sidebar] .product-card .product-card__buttons{display:none}.products-list[data-layout=grid-3-sidebar] .product-card:hover{position:relative;z-index:3}.products-list[data-layout=grid-3-sidebar] .product-card:hover .product-card__buttons{display:-ms-flexbox;display:flex}}@media (max-width:991px) and (min-width:768px){.products-list[data-layout=grid-3-sidebar] .products-list__body{margin:-8px -6px}.products-list[data-layout=grid-3-sidebar] .products-list__item{width:calc(33.33333% - 12px);margin:8px 6px}}@media (max-width:767px) and (min-width:480px){.products-list[data-layout=grid-3-sidebar] .products-list__body{margin:-8px -6px}.products-list[data-layout=grid-3-sidebar] .products-list__item{width:calc(50% - 12px);margin:8px 6px}}@media (max-width:479px){.products-list[data-layout=grid-3-sidebar] .products-list__body{margin:-8px -6px}.products-list[data-layout=grid-3-sidebar] .products-list__item{width:100%;margin:8px 6px}}.products-list[data-layout=grid-4-full] .products-list__body{margin:-8px -6px}.products-list[data-layout=grid-4-full] .products-list__item{width:calc(25% - 12px);margin:8px 6px}.products-list[data-layout=grid-4-full] .product-card .product-card__buttons .btn{font-size:.875rem;height:calc(1.875rem + 2px);line-height:1.25;padding:.375rem 1rem;font-weight:500}.products-list[data-layout=grid-4-full] .product-card .product-card__buttons .btn.btn-svg-icon{width:calc(1.875rem + 2px)}@media (hover:hover){.products-list[data-layout=grid-4-full] .product-card:hover{position:relative;z-index:3;}.products-list[data-layout=grid-4-full] .product-card:hover .product-card__buttons{display:-ms-flexbox;display:flex}}@media (-ms-high-contrast:none),screen and (-ms-high-contrast:active){.products-list[data-layout=grid-4-full] .product-card .product-card__buttons{display:none}.products-list[data-layout=grid-4-full] .product-card:hover{position:relative;z-index:3;}.products-list[data-layout=grid-4-full] .product-card:hover .product-card__buttons{display:-ms-flexbox;display:flex}}@media (max-width:1199px) and (min-width:480px){.products-list[data-layout=grid-4-full] .product-card .product-card__image{padding:3px}.products-list[data-layout=grid-4-full] .product-card .product-card__badges-list{left:3px;top:3px}.products-list[data-layout=grid-4-full] .product-card .product-card__info{padding:0 15px}.products-list[data-layout=grid-4-full] .product-card .product-card__actions{padding:0 15px 15px}.products-list[data-layout=grid-4-full] .product-card .product-card__buttons .btn{font-size:.8125rem;height:calc(1.5rem + 2px);line-height:1.25;padding:.25rem .5625rem;font-weight:500}.products-list[data-layout=grid-4-full] .product-card .product-card__buttons .btn.btn-svg-icon{width:calc(1.5rem + 2px)}}@media (max-width:1199px) and (min-width:480px) and (hover:hover){.products-list[data-layout=grid-4-full] .product-card .product-card__buttons{display:none}.products-list[data-layout=grid-4-full] .product-card:hover{position:relative;z-index:3;}.products-list[data-layout=grid-4-full] .product-card:hover .product-card__buttons{display:-ms-flexbox;display:flex}}@media (max-width:1199px) and (min-width:480px) and (-ms-high-contrast:none),screen and (max-width:1199px) and (min-width:480px) and (-ms-high-contrast:active){.products-list[data-layout=grid-4-full] .product-card .product-card__buttons{display:none}.products-list[data-layout=grid-4-full] .product-card:hover{position:relative;z-index:3;}.products-list[data-layout=grid-4-full] .product-card:hover .product-card__buttons{display:-ms-flexbox;display:flex}}@media (max-width:991px) and (min-width:768px){.products-list[data-layout=grid-4-full] .products-list__body{margin:-8px -6px}.products-list[data-layout=grid-4-full] .products-list__item{width:calc(33.33333% - 12px);margin:8px 6px}}@media (max-width:767px) and (min-width:480px){.products-list[data-layout=grid-4-full] .products-list__body{margin:-8px -6px}.products-list[data-layout=grid-4-full] .products-list__item{width:calc(50% - 12px);margin:8px 6px}}@media (max-width:479px){.products-list[data-layout=grid-4-full] .products-list__body{margin:-5px -0px}.products-list[data-layout=grid-4-full] .products-list__item{width:calc(50% - 8px);margin:5px 4px}}.products-list[data-layout=grid-5-full] .products-list__body{margin:-8px -6px}.products-list[data-layout=grid-5-full] .products-list__item{width:calc(20% - 12px);margin:8px 6px}@media (min-width:480px){.products-list[data-layout=grid-5-full] .product-card .product-card__image{padding:3px}.products-list[data-layout=grid-5-full] .product-card .product-card__badges-list{left:3px;top:3px}.products-list[data-layout=grid-5-full] .product-card .product-card__info{padding:0 15px}.products-list[data-layout=grid-5-full] .product-card .product-card__actions{padding:0 15px 15px}.products-list[data-layout=grid-5-full] .product-card .product-card__buttons .btn{font-size:.8125rem;height:calc(1.5rem + 2px);line-height:1.25;padding:.25rem .5625rem;font-weight:500}.products-list[data-layout=grid-5-full] .product-card .product-card__buttons .btn.btn-svg-icon{width:calc(1.5rem + 2px)}}@media (min-width:480px) and (hover:hover){.products-list[data-layout=grid-5-full] .product-card:hover{position:relative;z-index:3;}.products-list[data-layout=grid-5-full] .product-card:hover .product-card__buttons{display:-ms-flexbox;display:flex}}@media (min-width:480px) and (-ms-high-contrast:none),screen and (min-width:480px) and (-ms-high-contrast:active){.products-list[data-layout=grid-5-full] .product-card .product-card__buttons{display:none}.products-list[data-layout=grid-5-full] .product-card:hover{position:relative;z-index:3;}.products-list[data-layout=grid-5-full] .product-card:hover .product-card__buttons{display:-ms-flexbox;display:flex}}@media (max-width:1199px) and (min-width:992px){.products-list[data-layout=grid-5-full] .products-list__body{margin:-8px -6px}.products-list[data-layout=grid-5-full] .products-list__item{width:calc(25% - 12px);margin:8px 6px}}@media (max-width:991px) and (min-width:768px){.products-list[data-layout=grid-5-full] .products-list__body{margin:-8px -6px}.products-list[data-layout=grid-5-full] .products-list__item{width:calc(33.33333% - 12px);margin:8px 6px}}@media (max-width:767px) and (min-width:480px){.products-list[data-layout=grid-5-full] .products-list__body{margin:-8px -6px}.products-list[data-layout=grid-5-full] .products-list__item{width:calc(50% - 12px);margin:8px 6px}}@media (max-width:479px){.products-list[data-layout=grid-5-full] .products-list__body{margin:-5px -0px}.products-list[data-layout=grid-5-full] .products-list__item{width:calc(50% - 8px);margin:5px 4px}}.products-list[data-layout=list] .products-list__body{margin:-8px 0}.products-list[data-layout=list] .products-list__item{width:100%;margin:8px 0;display:block}.products-list[data-layout=list] .product-card{-ms-flex-direction:row;flex-direction:row}.products-list[data-layout=list] .product-card .product-card__image{-ms-flex-negative:0;flex-shrink:0;padding:24px;width:210px}.products-list[data-layout=list] .product-card .product-card__info{padding:20px 24px 20px 4px}.products-list[data-layout=list] .product-card .product-card__name{font-size:16px;line-height:20px;-ms-flex-positive:0;flex-grow:0}.products-list[data-layout=list] .product-card .product-card__rating{margin-top:7px}.products-list[data-layout=list] .product-card .product-card__description{color:#6c757d;font-size:15px;line-height:22px;margin-top:12px}.products-list[data-layout=list] .product-card .product-card__actions{-ms-flex-negative:0;flex-shrink:0;width:190px;padding:16px 20px;border-left:1px solid #ebebeb}.products-list[data-layout=list] .product-card .product-card__prices{margin-top:16px;font-size:18px}.products-list[data-layout=list] .product-card .product-card__old-price{font-size:14px}.products-list[data-layout=list] .product-card .product-card__buttons{-ms-flex-wrap:wrap;flex-wrap:wrap}.products-list[data-layout=list] .product-card .product-card__addtocart{display:none}.products-list[data-layout=list] .product-card .product-card__addtocart--list{display:block}.products-list[data-layout=list] .product-card .product-card__addtocart{width:100%}.products-list[data-layout=list] .product-card .product-card__addtocart+*{margin-left:0}.products-list[data-layout=list] .product-card .product-card__addtocart~*{margin-top:8px}.products-list[data-layout=list] .product-card .product-card__addtocart,.products-list[data-layout=list] .product-card .product-card__compare,.products-list[data-layout=list] .product-card .product-card__wishlist{font-size:.875rem;height:calc(1.875rem + 2px);line-height:1.25;padding:.375rem 1rem;font-weight:500}.products-list[data-layout=list] .product-card .product-card__addtocart.btn-svg-icon,.products-list[data-layout=list] .product-card .product-card__compare.btn-svg-icon,.products-list[data-layout=list] .product-card .product-card__wishlist.btn-svg-icon{width:calc(1.875rem + 2px)}.products-list[data-layout=list] .product-card .product-card__availability{color:#999;font-size:14px;padding-right:10px}.products-list[data-layout=list] .product-card .product-card__features-list{font-size:14px;line-height:16px;margin-bottom:0}.products-list[data-layout=list] .product-card .product-card__features-list li{padding:3px 0 3px 12px}.products-list[data-layout=list] .product-card .product-card__features-list li:before{top:8px}@media (min-width:992px) and (max-width:1199px){.products-list[data-layout=list] .product-card .product-card__image{width:180px;padding-left:20px;padding-right:20px}.products-list[data-layout=list] .product-card .product-card__info{padding-right:20px;padding-left:0}}@media (max-width:767px){.products-list[data-layout=list] .product-card{-ms-flex-direction:column;flex-direction:column}.products-list[data-layout=list] .product-card .product-card__image{width:250px;padding:20px;margin:0 auto}.products-list[data-layout=list] .product-card .product-card__info{border-top:1px solid #ebebeb;padding:20px}.products-list[data-layout=list] .product-card .product-card__actions{width:auto;border-left:none;border-top:1px solid #ebebeb;padding:20px}.products-list[data-layout=list] .product-card .product-card__buttons{-ms-flex-wrap:nowrap;flex-wrap:nowrap}.products-list[data-layout=list] .product-card .product-card__buttons .btn{font-size:1rem;height:calc(2.25rem + 2px);line-height:1.5;padding:.375rem 1.25rem;font-weight:500}.products-list[data-layout=list] .product-card .product-card__buttons .btn.btn-svg-icon{width:calc(2.25rem + 2px)}.products-list[data-layout=list] .product-card .product-card__addtocart--list{width:auto;margin-right:auto}}.products-view__options{padding-bottom:20px}.products-view__pagination{padding-top:32px}.quickview{padding:60px;position:relative}.quickview__close{position:absolute;right:0;top:0;width:50px;height:50px;display:-ms-flexbox;display:flex;-ms-flex-align:center;align-items:center;-ms-flex-pack:center;justify-content:center;cursor:pointer;background:#fff;border-radius:3px;border:none;fill:#bfbfbf;transition:all .2s;z-index:2}.quickview__close:focus,.quickview__close:hover{fill:gray}.quickview__close:focus{outline:none}@media (min-width:576px) and (max-width:1199px){.quickview{padding:30px}}
        #accordion .card-link {
            display: block;
            font-size: 20px;
            padding: 18px 48px;
            border-bottom: 2px solid transparent;
            color: inherit;
            font-weight: 500;
            border-radius: 3px 3px 0 0;
            transition: all .15s;
        }
        #accordion .card-link:not(.collapsed) {
            border-bottom: 2px solid #000;
            color: #000;
        }

        iframe {
            width: 100%;
        }

        @media (max-width: 768px) {
            .product__option-label {
                display: block;
            }
            .product__actions {
                justify-content: center;
            }
            .product__actions-item {
                width: 100%;
            }
        }
        .product__content {
            @if ($services->enabled ?? false)
            grid-template-columns: [gallery] calc(40% - 30px) [info] calc(40% - 35px) [sidebar] calc(25% - 10px);
            @else
            grid-template-columns: [gallery] calc(50% - 30px) [info] calc(50% - 35px);
            @endif
            grid-column-gap: 10px;
        }

        img {
            max-width: 100%;
            /*height: auto;*/
        }

        .original {
            position: relative;
        }
        .zoom-nav {
            position: absolute;
            top: 0;
            height: 100%;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .zoom-control {
            height: 40px;
            outline: none;
            border: 2px solid black;
            cursor: pointer;
            opacity: 0.8;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 10px;
            width: 40px;
            border-radius: 5px;
            color: #ca3d1c;
            background: transparent;
        }
        .zoom-control:hover {
            opacity: 1;
        }
        .zoom-control:focus {
            outline: none;
        }
    </style>
@endpush

@section('title', $product->name)

@section('content')
    <div class="d-none d-md-block">
        @include('partials.page-header', [
            'paths' => [
                url('/')                => 'Home',
                route('products.index') => 'Products',
            ],
            'active' => $product->name,
        ])
    </div>
    <div class="block mt-3 mt-md-0">
        <div class="container">
            <div class="product product--layout--standard" data-layout="standard">
                <div class="product__content">
                    <div class="xzoom-container d-flex flex-column">
                        <div class="original" style="width: 100%">
                            <img class="xzoom" id="xzoom-default" src="{{ asset($product->base_image->src) }}" xoriginal="{{ asset($product->base_image->src) }}" />
                            <div class="zoom-nav d-none">
                                <button class="zoom-control left">
                                    <i class="fa fa-chevron-left"></i>
                                </button>
                                <button class="zoom-control right">
                                    <i class="fa fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mt-2 xzoom-thumbs d-flex">
                            <a href="{{ asset($product->base_image->src) }}"><img data-detail="{{ route('products.show', $product) }}" class="xzoom-gallery product-base__image" width="80" src="{{ asset($product->base_image->src) }}"  xpreview="{{ asset($product->base_image->src) }}"></a>
                            @foreach($product->additional_images as $image)
                                <a href="{{ asset($image->src) }}">
                                    <img class="xzoom-gallery" width="80" src="{{ asset($image->src) }}">
                                </a>
                            @endforeach
                        </div>
                    </div>
                    <!-- .product__info -->
                    <livewire:product-detail :product="$product" :show-brand-category="!($services->enabled ?? false)" />
                    <!-- .product__info / end -->
                    @if($services->enabled ?? false)
                    <div>
                        @if($product->variations->isNotEmpty())
                        <div class="p-3 mt-2 mb-2 border product__footer">
                            <div class="product__tags tags">
                                @if($product->brand)
                                    <p class="mb-0 text-secondary">
                                        Brand: <a href="{{ route('brands.products', $product->brand) }}" class="text-primary badge badge-light"><big>{{ $product->brand->name }}</big></a>
                                    </p>
                                @endif
                                <div class="mt-2">
                                    <p class="mr-2 mb-0 text-secondary d-inline-block">Categories:</p>
                                    @foreach($product->categories as $category)
                                        <a href="{{ route('categories.products', $category) }}" class="badge badge-primary">{{ $category->name }}</a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="block-features__list flex-column d-none d-md-block">
                            @foreach(config('services.services', []) as $num => $icon)
                                <div class="block-features__item">
                                    <div class="block-features__icon">
                                        <svg width="48px" height="48px">
                                            <use xlink:href="{{ asset($icon) }}"></use>
                                        </svg>
                                    </div>
                                    <div class="block-features__content">
                                        <div class="block-features__title">{{ $services->$num->title }}</div>
                                        <div class="block-features__subtitle">{{ $services->$num->detail }}</div>
                                    </div>
                                </div>
                                @if(!$loop->last)
                                    <div class="block-features__divider"></div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            <div id="accordion" class="mt-3">
                <div class="card">
                    <div class="p-0 card-header">
                        <a class="px-4 card-link" datatoggle="collapse" href="javascript:void(false)">
                            Product Description
                        </a>
                    </div>
                    <div id="collapseOne" class="collapse show" data-parent="#accordion">
                        <div class="p-2 card-body">
                            @if($product->desc_img && $product->desc_img_pos == 'before_content')
                            <div class="text-center">
                                @foreach ($product->images as $image)
                                    <img src="{{ asset($image->src) }}" alt="{{ $product->name }}" class="my-2 border img-fluid">
                                @endforeach
                            </div>
                            @endif

                            {!! $product->description !!}

                            @if($product->desc_img && $product->desc_img_pos == 'after_content')
                            <div class="text-center">
                                @foreach ($product->images as $image)
                                    <img src="{{ asset($image->src) }}" alt="{{ $product->name }}" class="my-2 border img-fluid">
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="mt-3 card">
                    <div class="p-0 card-header">
                        <a class="px-4 card-link" datatoggle="collapse" href="javascript:void(false)">
                            Delivery and Return Policy
                        </a>
                    </div>
                    <div id="collapseTwo" class="collapse show" data-parent="#accordion">
                        <div class="p-2 card-body">
                            {!! (setting('show_option')->productwise_delivery_charge ?? false) ? ($product->delivery_text ?? setting('delivery_text')) : setting('delivery_text') !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- .block-products-carousel -->
    @include('partials.products.pure-grid', [
        'title' => 'Related Products',
        'products' => $products,
        'cols' => setting('related_products.cols') ?? 4,
        'rows' => setting('related_products.rows') ?? 1,
    ])
    <!-- .block-products-carousel / end -->
@endsection

@push('scripts')
    <script src="{{ asset('strokya/vendor/xzoom/xzoom.min.js') }}"></script>
    <script src="{{ asset('strokya/vendor/xZoom-master/example/js/vendor/modernizr.js') }}"></script>
    <script src="{{ asset('strokya/vendor/xZoom-master/example/js/setup.js') }}"></script>
    <script>
        // $(document).ready(function () {
        //     let activeG = 0;
        //     let lastG = 0;
        //     $('.zoom-control.left').click(function () {
        //         let gallery = $('.xzoom-gallery');
        //         gallery.each(function (g, e) {
        //             if ($(e).hasClass('xactive')) {
        //                 activeG = g;
        //             }
        //             lastG = g;
        //         })
        //         const prev = activeG === 0 ? lastG : (activeG - 1);
        //         gallery.eq(prev).trigger('click');
        //     });
        //     $('.zoom-control.right').click(function () {
        //         let gallery = $('.xzoom-gallery');
        //         gallery.each(function (g, e) {
        //             if ($(e).hasClass('xactive')) {
        //                 activeG = g;
        //             }
        //             lastG = g;
        //         })
        //         const next = activeG === lastG ? 0 : (activeG + 1);
        //         gallery.eq(next).trigger('click');
        //     });
        //     setInterval(() => $('.zoom-control.right').trigger('click'), 3000);
        // });
    </script>
@endpush
