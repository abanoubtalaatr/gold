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
    
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
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
        
        /* Language Dropdown Styles */
        .dropdown-menu {
            border-radius: 8px;
            border: 1px solid #e9ecef;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            min-width: 150px;
        }
        
        .dropdown-item {
            padding: 8px 16px;
            transition: all 0.2s ease;
        }
        
        .dropdown-item:hover {
            background-color: #f8f9fa;
            color: #495057;
        }
        
        .dropdown-item.active {
            background-color: #007bff;
            color: white;
        }
        
        .dropdown-toggle::after {
            margin-right: 0.5rem;
        }
        
        /* RTL Support */
        [dir="rtl"] .dropdown-toggle::after {
            margin-left: 0.5rem;
            margin-right: 0;
        }
    </style>
    @else
    <style>
        /* Language Dropdown Styles */
        .dropdown-menu {
            border-radius: 8px;
            border: 1px solid #e9ecef;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            min-width: 150px;
        }
        
        .dropdown-item {
            padding: 8px 16px;
            transition: all 0.2s ease;
        }
        
        .dropdown-item:hover {
            background-color: #f8f9fa;
            color: #495057;
        }
        
        .dropdown-item.active {
            background-color: #007bff;
            color: white;
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
								<a href="{{url('download')}}">
									<img src="assets/img/content/google-play.png" alt="">
								</a>
								<a href="{{url('download')}}">
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
                        <form id="contact-form" class="contact-form outlined" method="POST" action="{{ route('landing.contact') }}">
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
                                <a class="navbar-brand mb-6" href="/">
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
                                        <a class="nav-link" href="/">{{ __('landing.footer.links.home') }}</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="/#about">{{ __('landing.footer.links.about') }}</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{url('privacy')}}">{{ __('landing.footer.links.privacy') }}</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{url('terms')}}">{{ __('landing.footer.links.terms') }}</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="/#contact">{{ __('landing.footer.links.contact') }}</a>
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
        @include('footer')
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
    
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    
    <!-- Contact Form Handler -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const contactForm = document.getElementById('contact-form');
            
            if (contactForm) {
                contactForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    // Get form data
                    const formData = new FormData(this);
                    const submitButton = this.querySelector('button[type="submit"]');
                    const originalButtonText = submitButton.textContent;
                    
                    // Disable submit button and show loading
                    submitButton.disabled = true;
                    submitButton.textContent = '{{ __("Sending...") }}';
                    
                    // Send AJAX request
                    fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Show success alert
                            Swal.fire({
                                icon: 'success',
                                title: '{{ __("Success!") }}',
                                text: data.message,
                                confirmButtonColor: '#006ce7',
                                confirmButtonText: '{{ __("OK") }}'
                            });
                            
                            // Reset form
                            contactForm.reset();
                        } else {
                            // Show error alert
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __("Error!") }}',
                                text: data.message || '{{ __("landing.contact.error_message") }}',
                                confirmButtonColor: '#006ce7',
                                confirmButtonText: '{{ __("OK") }}'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        // Show error alert
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("Error!") }}',
                            text: '{{ __("landing.contact.error_message") }}',
                            confirmButtonColor: '#006ce7',
                            confirmButtonText: '{{ __("OK") }}'
                        });
                    })
                    .finally(() => {
                        // Re-enable submit button
                        submitButton.disabled = false;
                        submitButton.textContent = originalButtonText;
                    });
                });
            }
        });
    </script>
    
    <!-- Initialize Bootstrap Dropdowns -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize all dropdowns
            const dropdownElementList = document.querySelectorAll('.dropdown-toggle');
            const dropdownList = [...dropdownElementList].map(dropdownToggleEl => new bootstrap.Dropdown(dropdownToggleEl));
            
            // Debug: Log when dropdown is clicked
            document.querySelectorAll('.dropdown-toggle').forEach(function(element) {
                element.addEventListener('click', function(e) {
                    console.log('Dropdown clicked');
                });
            });
        });
    </script>
</body>

</html>