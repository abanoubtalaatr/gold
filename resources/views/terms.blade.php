<!DOCTYPE html>
<html class="no-js" lang="{{ app()->getLocale() }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
<!-- change from 'rtl' to 'ltr' to switch direction -->

<head>
    <meta charset="UTF-8" />
    <meta name="description" content="{{ __('terms.meta_description') }}" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <!-- The above 4 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <!-- Title  -->
    <title>{{ __('terms.page_title') }}</title>

    <!-- Favicon  -->
    <link rel="icon" href="{{asset('assets/img/favicon.svg')}}" />

    <!-- ***** All CSS Files ***** -->

    <!-- Style css -->
    <link rel="stylesheet" href="{{asset('assets/css/style.css')}}" />

    @if(app()->isLocale('ar'))
    <!-- RTL CSS for Arabic -->
    <style>
        body {
            direction: rtl;
            text-align: right;
        }
        
        .navbar-nav {
            flex-direction: row-reverse;
        }
        
        .breadcrumb {
            justify-content: flex-end;
        }
        
        .content li {
            text-align: right;
        }
        
        .dropdown-menu {
            right: 0;
            left: auto;
        }
        
        #content h1 {
            font-size: inherit;
        }

        /* Disable automatic breadcrumb separators */
        .breadcrumb-area .breadcrumb-content .breadcrumb .breadcrumb-item + .breadcrumb-item::before {
            content: none !important;
        }
    </style>
    @else
    <style>
        #content h1 {
            font-size: inherit;
        }

        /* Disable automatic breadcrumb separators */
        .breadcrumb-area .breadcrumb-content .breadcrumb .breadcrumb-item + .breadcrumb-item::before {
            content: none !important;
        }
    </style>
    @endif
</head>

<body>
    <div class="main">
        <!-- ***** Preloader Start ***** -->
        <div class="preloader-main">
            <div class="preloader-wapper">
                <svg class="preloader" xmlns="http://www.w3.org/2000/svg" version="1.1" width="600" height="200">
                    <defs>
                        <filter id="goo" x="-40%" y="-40%" height="200%" width="400%">
                            <feGaussianBlur in="SourceGraphic" stdDeviation="10" result="blur" />
                            <feColorMatrix in="blur" mode="matrix" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 19 -8"
                                result="goo" />
                        </filter>
                    </defs>
                    <g filter="url(#goo)">
                        <circle class="dot" cx="50" cy="50" r="25" fill="#D4AF37" />
                        <circle class="dot" cx="50" cy="50" r="25" fill="#D4AF37" />
                    </g>
                </svg>
                <div>
                    <div class="loader-section section-left"></div>
                    <div class="loader-section section-right"></div>
                </div>
            </div>
        </div>
        <!-- ***** Preloader End ***** -->

        <!-- ***** Header Start ***** -->
        @include('header')
        <!-- ***** Header End ***** -->

        <!-- ***** Breadcrumb Area Start ***** -->
        <section id="home" class="breadcrumb-area layout-2 has-overlay overlay-gradient d-flex align-items-center">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <!-- Breamcrumb Content -->
                        <div class="breadcrumb-content text-center">
                            <h2 class="text-white">{{ __('terms.breadcrumb.title') }}</h2>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb d-flex justify-content-center align-items-center gap-2">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('landing') }}" class="text-white text-decoration-none hover:text-primary transition-colors">
                                            {{ __('terms.breadcrumb.home') }}
                                        </a>
                                    </li>
                                    <li class="breadcrumb-item text-white-50">
                                        <i class="fa-solid {{ app()->isLocale('ar') ? 'fa-chevron-left' : 'fa-chevron-right' }} fa-xs"></i>
                                    </li>
                                    <li class="breadcrumb-item active text-white" aria-current="page">
                                        {{ __('terms.breadcrumb.title') }}
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- ***** Breadcrumb Area End ***** -->

        <!-- ***** Terms & Conditions Content Start ***** -->
        <section class="terms-wrapper">
            <div class="container">
                <div class="content" id="content">
                    <ul>
                        <li>
                            <h5>{{ __('terms.content.registration.title') }}</h5>
                            <p>{{ __('terms.content.registration.text') }}</p>
                        </li>
                        <li>
                            <h5>{{ __('terms.content.content_use.title') }}</h5>
                            <p>{{ __('terms.content.content_use.text') }}</p>
                        </li>
                        <li>
                            <h5>{{ __('terms.content.user_obligations.title') }}</h5>
                            <p>{{ __('terms.content.user_obligations.text') }}</p>
                        </li>
                        <li>
                            <h5>{{ __('terms.content.offer_policy.title') }}</h5>
                            <p>{{ __('terms.content.offer_policy.text') }}</p>
                        </li>
                        <li>
                            <h5>{{ __('terms.content.updates_maintenance.title') }}</h5>
                            <p>{{ __('terms.content.updates_maintenance.text') }}</p>
                        </li>
                        <li>
                            <h5>{{ __('terms.content.complaints_feedback.title') }}</h5>
                            <p>{{ __('terms.content.complaints_feedback.text') }}</p>
                        </li>
                        <li>
                            <h5>{{ __('terms.content.account_termination.title') }}</h5>
                            <p>{{ __('terms.content.account_termination.text') }}</p>
                        </li>
                    </ul>
                </div>
            </div>
        </section>
        <!-- ***** Terms & Conditions Content End ***** -->

        <!--====== Footer Area Start ======-->
        @include('footer')
        <!--====== Footer Area End ======-->

        <!--====== Modal Responsive Menu Area Start ======-->
        
        <!--====== Modal Responsive Menu Area End ======-->

        <!--====== Scroll To Top Area Start ======-->
        <div id="scroll-to-top" class="scroll-to-top">
            <a href="#home" class="smooth-anchor">
                <i class="fa-solid fa-arrow-up"></i>
            </a>
        </div>
        <!--====== Scroll To Top Area End ======-->
    </div>

    <!-- ***** All jQuery Plugins ***** -->

    <!-- jQuery(necessary for all JavaScript plugins) -->
    <script src="{{asset('assets/js/vendor/jquery.min.js')}}"></script>

    <!-- Bootstrap js -->
    <script src="{{asset('assets/js/vendor/popper.min.js')}}"></script>
    <script src="{{asset('assets/js/vendor/bootstrap.min.js')}}"></script>

    <!-- Plugins js -->
    <script src="{{asset('assets/js/vendor/slider.min.js')}}"></script>
    <script src="{{asset('assets/js/vendor/owl.carousel.min.js')}}"></script>
    <script src="{{asset('assets/js/vendor/counterup.js')}}"></script>
    <script src="{{asset('assets/js/vendor/waypoint.js')}}"></script>
    <script src="{{asset('assets/js/vendor/aos.js')}}"></script>
    <script src="{{asset('assets/js/vendor/wow.min.js')}}"></script>
    <script src="{{asset('assets/js/vendor/countdown.min.js')}}"></script>
    <script src="{{asset('assets/js/vendor/gallery.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dompurify/3.0.3/purify.min.js"></script>

    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />

    <!-- Main js -->
    <script src="{{asset('assets/js/main.js')}}"></script>
</body>

</html>