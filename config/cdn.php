<?php

return [
    /*
    |--------------------------------------------------------------------------
    | CDN Configuration
    |--------------------------------------------------------------------------
    |
    | Configure CDN URLs for external libraries. When enabled, these will
    | replace local asset files with CDN versions for better performance.
    |
    */

    'enabled' => env('CDN_ENABLED', true),

    'provider' => env('CDN_PROVIDER', 'jsdelivr'), // jsdelivr, cdnjs, unpkg

    'assets' => [
        'jquery' => [
            'version' => '3.7.1',
            'jsdelivr' => 'https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js',
            'cdnjs' => 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js',
            'unpkg' => 'https://unpkg.com/jquery@3.7.1/dist/jquery.min.js',
        ],
        'bootstrap' => [
            'version' => '4.6.2',
            'css' => [
                'jsdelivr' => 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css',
                'cdnjs' => 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.2/css/bootstrap.min.css',
                'unpkg' => 'https://unpkg.com/bootstrap@4.6.2/dist/css/bootstrap.min.css',
            ],
            'js' => [
                'jsdelivr' => 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js',
                'cdnjs' => 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.2/js/bootstrap.bundle.min.js',
                'unpkg' => 'https://unpkg.com/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js',
            ],
        ],
        'fontawesome' => [
            'version' => '6.5.1',
            'css' => [
                'jsdelivr' => 'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css',
                'cdnjs' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
                'unpkg' => 'https://unpkg.com/@fortawesome/fontawesome-free@6.5.1/css/all.min.css',
            ],
        ],
        'owl-carousel' => [
            'version' => '2.3.4',
            'css' => [
                'jsdelivr' => 'https://cdn.jsdelivr.net/npm/owl.carousel@2.3.4/dist/assets/owl.carousel.min.css',
                'cdnjs' => 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css',
                'unpkg' => 'https://unpkg.com/owl.carousel@2.3.4/dist/assets/owl.carousel.min.css',
            ],
            'js' => [
                'jsdelivr' => 'https://cdn.jsdelivr.net/npm/owl.carousel@2.3.4/dist/owl.carousel.min.js',
                'cdnjs' => 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js',
                'unpkg' => 'https://unpkg.com/owl.carousel@2.3.4/dist/owl.carousel.min.js',
            ],
        ],
        'svg4everybody' => [
            'version' => '2.1.11',
            'js' => [
                'jsdelivr' => 'https://cdn.jsdelivr.net/npm/svg4everybody@2.1.11/dist/svg4everybody.min.js',
                'cdnjs' => 'https://cdnjs.cloudflare.com/ajax/libs/svg4everybody/2.1.11/svg4everybody.min.js',
                'unpkg' => 'https://unpkg.com/svg4everybody@2.1.11/dist/svg4everybody.min.js',
            ],
        ],
        'jquery-3.5.1' => [
            'version' => '3.5.1',
            'js' => [
                'jsdelivr' => 'https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js',
                'cdnjs' => 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js',
                'unpkg' => 'https://unpkg.com/jquery@3.5.1/dist/jquery.min.js',
            ],
        ],
        'popper' => [
            'version' => '2.11.8',
            'js' => [
                'jsdelivr' => 'https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js',
                'cdnjs' => 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.8/umd/popper.min.js',
                'unpkg' => 'https://unpkg.com/@popperjs/core@2.11.8/dist/umd/popper.min.js',
            ],
        ],
        'bootstrap-5' => [
            'version' => '5.3.3',
            'css' => [
                'jsdelivr' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
                'cdnjs' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css',
                'unpkg' => 'https://unpkg.com/bootstrap@5.3.3/dist/css/bootstrap.min.css',
            ],
            'js' => [
                'jsdelivr' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
                'cdnjs' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js',
                'unpkg' => 'https://unpkg.com/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
            ],
        ],
        'datatables' => [
            'version' => '1.13.7',
            'css' => [
                'jsdelivr' => 'https://cdn.jsdelivr.net/npm/datatables.net-bs5@1.13.7/css/dataTables.bootstrap5.min.css',
                'cdnjs' => 'https://cdnjs.cloudflare.com/ajax/libs/datatables.net-bs5/1.13.7/dataTables.bootstrap5.min.css',
                'unpkg' => 'https://unpkg.com/datatables.net-bs5@1.13.7/css/dataTables.bootstrap5.min.css',
            ],
            'js' => [
                'jsdelivr' => 'https://cdn.jsdelivr.net/npm/datatables.net@1.13.7/js/jquery.dataTables.min.js',
                'cdnjs' => 'https://cdnjs.cloudflare.com/ajax/libs/datatables.net/1.13.7/js/jquery.dataTables.min.js',
                'unpkg' => 'https://unpkg.com/datatables.net@1.13.7/js/jquery.dataTables.min.js',
            ],
            'js-bootstrap5' => [
                'jsdelivr' => 'https://cdn.jsdelivr.net/npm/datatables.net-bs5@1.13.7/js/dataTables.bootstrap5.min.js',
                'cdnjs' => 'https://cdnjs.cloudflare.com/ajax/libs/datatables.net-bs5/1.13.7/js/dataTables.bootstrap5.min.js',
                'unpkg' => 'https://unpkg.com/datatables.net-bs5@1.13.7/js/dataTables.bootstrap5.min.js',
            ],
        ],
        'select2' => [
            'version' => '4.1.0',
            'css' => [
                'jsdelivr' => 'https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css',
                'cdnjs' => 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0/css/select2.min.css',
                'unpkg' => 'https://unpkg.com/select2@4.1.0/dist/css/select2.min.css',
            ],
            'js' => [
                'jsdelivr' => 'https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js',
                'cdnjs' => 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0/js/select2.min.js',
                'unpkg' => 'https://unpkg.com/select2@4.1.0/dist/js/select2.min.js',
            ],
        ],
        'hammerjs' => [
            'version' => '2.0.8',
            'js' => [
                'jsdelivr' => 'https://cdn.jsdelivr.net/npm/hammerjs@2.0.8/hammer.min.js',
                'cdnjs' => 'https://cdnjs.cloudflare.com/ajax/libs/hammer.js/2.0.8/hammer.min.js',
                'unpkg' => 'https://unpkg.com/hammerjs@2.0.8/hammer.min.js',
            ],
        ],
        'jquery-hammerjs' => [
            'version' => '2.0.0',
            'js' => [
                'jsdelivr' => 'https://cdn.jsdelivr.net/npm/jquery-hammerjs@2.0.0/jquery.hammer.min.js',
                'cdnjs' => 'https://cdnjs.cloudflare.com/ajax/libs/jquery-hammerjs/2.0.0/jquery.hammer.min.js',
                'unpkg' => 'https://unpkg.com/jquery-hammerjs@2.0.0/jquery.hammer.min.js',
            ],
        ],
    ],
];
