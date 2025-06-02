<!DOCTYPE html>
<html class="no-js" lang="{{ app()->getLocale() }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
<!-- change from 'rtl' to 'ltr' to switch direction -->

<head>
    <meta charset="UTF-8" />
    <meta name="description" content="" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <!-- The above 4 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <!-- Title  -->
    <title>{{ __('landing.page_title') }}</title>

    <!-- Favicon  -->
    <link rel="icon" href="{{asset('assets/img/favicon.png')}}" />

    <!-- ***** All CSS Files ***** -->

    <!-- Style css -->
    <link rel="stylesheet" href="{{asset('assets/css/style.css')}}" />
    
    @if(app()->isLocale('ar'))
    <!-- RTL CSS for Arabic -->
    <link rel="stylesheet" href="{{asset('css/rtl.css')}}" />
    @endif
    
    @if(app()->isLocale('ar'))
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar-nav {
            flex-direction: row-reverse;
        }
        .text-end-ar {
            text-align: right !important;
        }
        .text-start-ar {
            text-align: left !important;
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
        <header id="header">
            <!-- Navbar -->
            <nav data-aos="zoom-out" data-aos-delay="800" class="navbar gameon-navbar navbar-expand">
                <div class="container header">
                    <!-- Logo -->
                    <a class="navbar-brand" href="index.html">
                        <img src="assets/images/logo.svg" alt="{{ __('landing.site_name') }}" width="100" />
                    </a>

                    <div class="ms-auto"></div>

                    <!-- Navbar Nav -->
                    <ul class="navbar-nav items ms-auto">
                        <li class="nav-item">
                            <a class="nav-link smooth-anchor" href="#home">{{ __('landing.nav.home') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link smooth-anchor" href="#about">{{ __('landing.nav.about') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="terms.html">{{ __('landing.nav.terms') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="privacy.html">{{ __('landing.nav.privacy') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link smooth-anchor" href="#contact">{{ __('landing.nav.contact') }}</a>
                        </li>
                        <li class="nav-item d-flex align-items-center">
                            <div class="dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ app()->isLocale('ar') ? 'عربي' : 'English' }}
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item {{ app()->isLocale('en') ? 'active' : '' }}" href="{{ route('changeLang', ['lang' => 'en']) }}">English</a></li>
                                    <li><a class="dropdown-item {{ app()->isLocale('ar') ? 'active' : '' }}" href="{{ route('changeLang', ['lang' => 'ar']) }}">العربية</a></li>
                                </ul>
                            </div>
                        </li>
                    </ul>

                    <!-- Navbar Toggler -->
                    <ul class="navbar-nav toggle">
                        <li class="nav-item">
                            <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#menu">
                                <i class="icon-menu m-0"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <!-- ***** Header End ***** -->

        <!-- ***** Hero Section Start ***** -->
		<section id="home" class="hero-section layout-2 has-overlay overlay-gradient">
			<div class="container">
				<div class="row justify-content-center align-items-center">
					
					<div class="col-12 text-center">
						<!-- Hero Content -->
						<div class="hero-content">
							<h3 class="text-white text-uppercase">{{ __('landing.hero.title') }}</h3>
							<p class="sub-heading text-white my-4">{{ __('landing.hero.subtitle') }}</p>

							<!-- Download Button -->
							<div class="button-group download-button d-flex align-items-center justify-content-center">
								<a href="download.html">
									<img src="assets/img/content/google-play.png" alt="">
								</a>
								<a href="download.html">
									<img src="assets/img/content/app-store.png" alt="">
								</a>
							</div>
							<span class="d-block fst-italic fw-light text-white mt-3">{{ __('landing.hero.availability') }}</span>
						</div>
					</div>
				</div>
			</div>
		</section>
		<!-- ***** Hero Section End ***** -->

        <!-- ***** Features Area Start ***** -->
		<section id="features" class="features-section pb-5">
			<div class="container">
				<div class="row justify-content-center">
					<div class="col-12 col-md-10 col-lg-7">
						<!-- Intro -->
						<div class="intro text-center mb-4">
							<span class="badge rounded-pill text-bg-light">
								<i class="fa-regular fa-lightbulb"></i>
								<span>{{ __('landing.features.badge') }}</span>
							</span>
							<h2 class="title">{{ __('landing.features.title') }}</h2>
							<p>{{ __('landing.features.subtitle') }}</p>
						</div>
					</div>
				</div>

				<div class="row items">
					@foreach(__('landing.features.items') as $feature)
					<div class="col-12 col-md-6 col-lg-4 item">
						<!-- Image Box -->
						<div class="image-box text-center wow fadeInLeft" data-wow-delay="0.4s">
							<!-- Content -->
							<div class="content">
								<h4 class="mb-3">{{ $feature['title'] }}</h4>
								<p class="mt-3">{{ $feature['description'] }}</p>
							</div>
						</div>
					</div>
					@endforeach
				</div>
			</div>
		</section>
		<!-- ***** Features Area End ***** -->

        <!-- ***** Content Section Start ***** -->
        <section class="content-section" id="about">
            <div class="container">
                <div class="row justify-content-between">
                    <div class="col-12 col-lg-6">
                        <div class="content">
                            <h2 class="mt-0">{{ __('landing.about.title') }}</h2>

                            <ul class="list-group list-group-flush">
                                @foreach(__('landing.about.items') as $item)
                                <li class="list-group-item d-flex border-0 p-0">
                                    <div class="icon layout-2 align-items-start mt-1 {{ app()->isLocale('ar') ? 'ms-2' : 'me-2' }}">
                                        <span class="material-symbols-outlined">task_alt</span>
                                    </div>
                                    <span>{{ $item }}</span>
                                </li>
                                @endforeach
                            </ul>

                        </div>
                    </div>

                    <div class="col-12 col-lg-6 d-flex justify-content-end">
                        <img src="assets/img/content/about-us-img.jpg" alt="">
                    </div>
                </div>
            </div>
        </section>
        <!-- ***** Content Section End ***** -->

       

         <!-- ***** Counter Area Start ***** -->
        <section class="counter-area">
            <div class="container">
                <div class="row items justify-content-center">
                    <div class="col-6 col-md-3 item">
                        <div class="counter-item text-center">
                            <span class="counter">10</span><span>M</span>
                            <h5 class="mt-1 mb-0">{{ __('landing.counter.users') }}</h5>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 item">
                        <div class="counter-item text-center">
                            <span class="counter">23</span><span>K</span>
                            <h5 class="mt-1 mb-0">{{ __('landing.counter.download') }}</h5>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 item">
                        <div class="counter-item text-center">
                            <span class="counter">9</span><span>M</span>
                            <h5 class="mt-1 mb-0">{{ __('landing.counter.customer') }}</h5>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 item">
                        <div class="counter-item text-center">
                            <span class="counter">12</span><span>K</span>
                            <h5 class="mt-1 mb-0">{{ __('landing.counter.developer') }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- ***** Counter Area End ***** -->

         <!-- ***** Screenshots Area Start ***** -->
        <section id="screenshots" class="screenshots-area">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-md-10 col-lg-7">
                        <!-- Intro -->
                        <div class="intro text-center">
                            <h2 class="title">{{ __('landing.screenshots.title') }}</h2>
                        </div>
                    </div>
                </div>
                <div class="app-screenshots-slides">
                    <div class="swiper-container slider-mid">
                        <div class="swiper-wrapper">
                            <!-- Single Slide -->
                            <div class="swiper-slide item">
                                <img src="assets/img/content/screenshot-1.jpg" alt="" />
                            </div>
                            <!-- Single Slide -->
                            <!-- Single Slide -->
                            <div class="swiper-slide item">
                                <img src="assets/img/content/screenshot-11.jpg" alt="" />
                            </div>
                            <!-- Single Slide -->
                            <div class="swiper-slide item">
                                <img src="assets/img/content/screenshot-3.jpg" alt="" />
                            </div>
                            <!-- Single Slide -->
                            <div class="swiper-slide item">
                                <img src="assets/img/content/screenshot-4.jpg" alt="" />
                            </div>
                            <!-- Single Slide -->
                            <div class="swiper-slide item">
                                <img src="assets/img/content/screenshot-5.jpg" alt="" />
                            </div>
                            <!-- Single Slide -->
                            <div class="swiper-slide item">
                                <img src="assets/img/content/screenshot-6.jpg" alt="" />
                            </div>
                            <!-- Single Slide -->
                            <div class="swiper-slide item">
                                <img src="assets/img/content/screenshot-7.jpg" alt="" />
                            </div>
                            <!-- Single Slide -->
                            <div class="swiper-slide item">
                                <img src="assets/img/content/screenshot-8.jpg" alt="" />
                            </div>
                            <!-- Single Slide -->
                            <div class="swiper-slide item">
                                <img src="assets/img/content/screenshot-9.jpg" alt="" />
                            </div>
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                </div>
            </div>
        </section>
        <!-- ***** Screenshots Area End ***** -->

        

        <!-- ***** Download Area Start ***** -->
        <section class="download-area has-overlay overlay-dark">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-md-10 col-lg-9">
                        <!-- Content -->
                        <div class="content text-center">
                            <h2 class="text-white">{{ __('landing.download.title') }}</h2>
                            <p class="text-white">{{ __('landing.download.subtitle') }}</p>

                            <!-- Download Button -->
                            <div class="button-group download-button justify-content-center">
                                <a href="#">
                                    <img src="assets/img/content/google-play.png" alt="" />
                                </a>
                                <a href="#">
                                    <img src="assets/img/content/app-store.png" alt="" />
                                </a>
                            </div>
                            <span class="d-block fst-italic fw-light text-white mt-3">{{ __('landing.download.availability') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- ***** Download Area End ***** -->
         <!-- ***** FAQ Area Start ***** -->
		<section class="faq">
			<div class="container">
				<div class="row justify-content-center">
					<div class="col-12 col-md-10 col-lg-7">
						<!-- Intro -->
						<div class="intro text-center">
							<h2 class="title mt-0">{{ __('landing.faq.title') }}</h2>
							<p>{{ __('landing.faq.subtitle') }}</p>
						</div>
					</div>
				</div>

				<div class="row justify-content-center">
					<div class="col-12 col-md-10">
						<!-- Gold Station Accordion -->
						<div class="accordion accordion-flush" id="Gold Station-accordion">
							@foreach(__('landing.faq.items') as $index => $faq)
							<!-- Accordion Item -->
							<div class="accordion-item">
								<h4 class="accordion-header">
									<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index + 1 }}">
									{{ $faq['question'] }}
									</button>
								</h4>
								<div id="collapse{{ $index + 1 }}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" data-bs-parent="#Gold Station-accordion">
									<div class="accordion-body">{{ $faq['answer'] }}</div>
								</div>
							</div>
							@endforeach
						</div>
					</div>
				</div>
			</div>
		</section>
		<!-- ***** FAQ Area End ***** -->

        <!--====== Contact Area Start ======-->
        <section id="contact" class="contact-area primary-bg">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-md-10 col-lg-7">
                        <!-- Intro -->
                        <div class="intro text-center">
                            <h2 class="title mt-0">{{ __('landing.contact.title') }}</h2>
                            <p>{{ __('landing.contact.subtitle') }}</p>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-between">
                    <div class="col-12 col-md-5">
                        <div class="contact-info">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex align-items-center">
                                    <!-- Icon Box -->
                                    <div class="icon-box">
                                        <div class="icon">
                                            <span class="contact-icon material-symbols-outlined">call</span>
                                        </div>
                                    </div>
                                    <span><a href="tel:{{ __('landing.contact.phone') }}">{{ __('landing.contact.phone') }}</a></span>
                                </li>

                                <li class="list-group-item d-flex align-items-center">
                                    <!-- Icon Box -->
                                    <div class="icon-box">
                                        <div class="icon">
                                            <span class="contact-icon material-symbols-outlined">mark_email_unread</span>
                                        </div>
                                    </div>
                                    <span><a href="mailto:{{ __('landing.contact.email') }}">{{ __('landing.contact.email') }}</a></span>
                                </li>

                                <li class="list-group-item d-flex align-items-center">
                                    <!-- Icon Box -->
                                    <div class="icon-box">
                                        <div class="icon">
                                            <span class="contact-icon material-symbols-outlined">location_on</span>
                                        </div>
                                    </div>
                                    <span>{{ __('landing.contact.address') }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 mt-4 mt-md-0">
                        <form id="contact-form" class="contact-form outlined" method="POST">
                            @csrf
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" name="name" id="name" placeholder="{{ __('landing.contact.form.name') }}" required />
                                <label for="name">{{ __('landing.contact.form.name') }}</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="email" class="form-control" name="email" id="email" placeholder="{{ __('landing.contact.form.email') }}" required />
                                <label for="email">{{ __('landing.contact.form.email') }}</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" name="subject" id="subject" placeholder="{{ __('landing.contact.form.subject') }}" required />
                                <label for="subject">{{ __('landing.contact.form.subject') }}</label>
                            </div>
                            <div class="form-floating mb-4">
                                <textarea class="form-control" name="message" id="message" placeholder="{{ __('landing.contact.form.message') }}" required style="height: 100px"></textarea>
                                <label for="message">{{ __('landing.contact.form.message') }}</label>
                            </div>
                            <button type="submit" class="btn d-block w-100">{{ __('landing.contact.form.submit') }}</button>
                        </form>
                        <p class="form-message"></p>
                    </div>
                </div>
            </div>
        </section>
        <!--====== Contact Area End ======-->

        <!--====== Footer Area Start ======-->
        <footer class="footer-area p-0">
            <!-- Footer Top -->
            <div class="footer-top">
                <div class="container">
                    <div class="row items">
                        <div class="col-12 col-sm-6 col-lg-4 item">
                            <!-- Footer Items -->
                            <div class="footer-items">
                                <!-- Logo -->
                                <a class="navbar-brand mb-6" href="index.html">
                                    <img class="logo" src="assets/images/logo.svg" alt="{{ __('landing.site_name') }}" width="120" />
                                </a>
                                <p class="slug mt-3">{{ __('landing.footer.description') }}
                                    <hr />

                                    <!-- Social Icons -->
                                <div class="social-icons d-flex mt-3">
                                    <a class="icon has-overlay" href="#">
                                        <i class="fa-brands fa-facebook-f"></i>
                                        <i class="fa-brands fa-facebook-f"></i>
                                    </a>
                                    <a class="icon has-overlay" href="#">
                                        <i class="fa-brands fa-x-twitter"></i>
                                        <i class="fa-brands fa-x-twitter"></i>
                                    </a>
                                    <a class="icon has-overlay" href="#">
                                        <i class="fa-brands fa-linkedin-in"></i>
                                        <i class="fa-brands fa-linkedin-in"></i>
                                    </a>
                                    <a class="icon has-overlay" href="#">
                                        <i class="fa-brands fa-github"></i>
                                        <i class="fa-brands fa-github"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-4 item">
                            <!-- Footer Items -->
                            <div class="footer-items">
                                <h4 class="footer-title mt-0">{{ __('landing.footer.useful_links') }}</h4>

                                <!-- Navigation -->
                                <ul class="nav flex-column">
                                    <li class="nav-item">
                                        <a class="nav-link" href="index.html">{{ __('landing.footer.links.home') }}</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="index.html#about">{{ __('landing.footer.links.about') }}</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="privacy.html">{{ __('landing.footer.links.privacy') }}</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="terms.html">{{ __('landing.footer.links.terms') }}</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="index.html#contact">{{ __('landing.footer.links.contact') }}</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-4 item">
                            <!-- Footer Items -->
                            <div class="footer-items">
                                <h4 class="footer-title mt-0">{{ __('landing.footer.download_title') }}</h4>

                                <!-- Download Button -->
                                <div class="button-group download-button">
                                    <a href="#">
                                        <img src="assets/img/content/google-play-black.png" alt="" />
                                    </a>
                                    <a href="#">
                                        <img src="assets/img/content/app-store-black.png" alt="" />
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer Bottom -->
            <div class="footer-bottom">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <!-- Copyright Area -->
                            <div class="copyright-area d-flex flex-wrap justify-content-center align-items-center text-center py-4">
                                <span>{{ __('landing.footer.copyright') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!--====== Footer Area End ======-->

        <!--====== Modal Responsive Menu Area Start ======-->
        <div id="menu" class="modal fade p-0">
            <div class="modal-dialog modal-dialog-slideout">
                <div class="modal-content full">
                    <div class="modal-header" data-bs-dismiss="modal">{{ __('landing.menu.title') }} <i class="icon-close"></i></div>
                    <div class="menu modal-body">
                        <div class="row w-100">
                            <div class="items p-0 col-12 text-center">
                                <!-- Append [navbar] -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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

    <!-- Main js -->
    <script src="{{asset('assets/js/main.js')}}"></script>
</body>

</html>