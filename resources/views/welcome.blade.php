<!DOCTYPE html>
<html class="no-js" lang="en" dir="ltr">
<!-- change from 'rtl' to 'ltr' to switch direction -->

<head>
    <meta charset="UTF-8" />
    <meta name="description" content="" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <!-- The above 4 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <!-- Title  -->
    <title>Gold Station - Homepage</title>

    <!-- Favicon  -->
    <link rel="icon" href="{{asset('assets/img/favicon.png')}}" />

    <!-- ***** All CSS Files ***** -->

    <!-- Style css -->
    <link rel="stylesheet" href="{{asset('assets/css/style.css')}}" />
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
                        <img src="assets/images/logo.svg" alt="Gold Station" width="100" />
                    </a>

                    <div class="ms-auto"></div>

                    <!-- Navbar Nav -->
                    <ul class="navbar-nav items ms-auto">
                        <li class="nav-item">
                            <a class="nav-link smooth-anchor" href="#home">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link smooth-anchor" href="#about">About Us</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="terms.html">Terms & Conditions</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="privacy.html">Privacy Policy</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link smooth-anchor" href="#contact">Contact us</a>
                        </li>
                        <li class="nav-item d-flex align-items-center">
                            <a class="nav-link smooth-anchor" href="#contact">EN</a>
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
							<h3 class="text-white text-uppercase">Gold Station</h3>
							<p class="sub-heading text-white my-4">Gold Station is the first innovative and secure digital experience connecting gold traders with individuals seeking to benefit from rental and purchase services through the platform.</p>

							<!-- Download Button -->
							<div class="button-group download-button d-flex align-items-center justify-content-center">
								<a href="download.html">
									<img src="assets/img/content/google-play.png" alt="">
								</a>
								<a href="download.html">
									<img src="assets/img/content/app-store.png" alt="">
								</a>
							</div>
							<span class="d-block fst-italic fw-light text-white mt-3">* Available on iPhone, iPad and all Android devices</span>
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
								<span>Premium</span>
								<span>Features</span>
							</span>
							<h2 class="title">What Makes Gold Station Different?</h2>
							<p>Discover the unique features of Gold Station that set it apart from the competition, designed to deliver unmatched performance and seamless user experiences.</p>
						</div>
					</div>
				</div>

				<div class="row items">
					<div class="col-12 col-md-6 col-lg-4 item">
						<!-- Image Box -->
						<div class="image-box text-center wow fadeInLeft" data-wow-delay="0.4s">
							<!-- Content -->
							<div class="content">
								<h4 class="mb-3">Ease of use</h4>
								<p class="mt-3">Simple interface and smooth design for all categories</p>
							</div>
						</div>
					</div>
					<div class="col-12 col-md-6 col-lg-4 item">
						<!-- Image Box -->
						<div class="image-box text-center wow fadeInUp" data-wow-delay="0.2s">
							<!-- Content -->
							<div class="content">
								<h4 class="mb-3">Data security</h4>
								<p class="mt-3">Your privacy is a priority, and all your transactions are protected with modern encryption technologies.</p>
							</div>
						</div>
					</div>
					<div class="col-12 col-md-6 col-lg-4 item">
						<!-- Image Box -->
						<div class="image-box text-center wow fadeInRight" data-wow-delay="0.4s">
							<!-- Content -->
							<div class="content">
								<h4 class="mb-3">Geographic location</h4>
								<p class="mt-3">Easily browse the offers closest to you.</p>
							</div>
						</div>
					</div>
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
                            <h2 class="mt-0">About Gold Station</h2>

                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex border-0 p-0">
                                    <div class="icon layout-2 align-items-start mt-1 me-2">
                                        <span class="material-symbols-outlined">task_alt</span>
                                    </div>
                                    <span>Gold Station is an innovative digital platform that connects gold traders with customers looking to buy or rent gold easily and securely.</span>
                                </li>

                                <li class="list-group-item d-flex border-0 p-0">
                                    <div class="icon layout-2 align-items-start mt-1 me-2">
                                        <span class="material-symbols-outlined">task_alt</span>
                                    </div>
                                    <span>We believe technology can revolutionize the way gold is accessed by offering a smooth and smart user experience.</span>
                                </li>

                                <li class="list-group-item d-flex border-0 p-0">
                                    <div class="icon layout-2 align-items-start mt-1 me-2">
                                        <span class="material-symbols-outlined">task_alt</span>
                                    </div>
                                    <span>To become the #1 platform in the Arab world for buying and renting gold online with trust and professionalism.</span>
                                </li>

                                <li class="list-group-item d-flex border-0 p-0">
                                    <div class="icon layout-2 align-items-start mt-1 me-2">
                                        <span class="material-symbols-outlined">task_alt</span>
                                    </div>
                                    <span>To provide a modern and secure digital experience that empowers users to browse gold, view offers, and make informed purchase or rental decisions.</span>
                                </li>
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
                            <h5 class="mt-1 mb-0">Users</h5>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 item">
                        <div class="counter-item text-center">
                            <span class="counter">23</span><span>K</span>
                            <h5 class="mt-1 mb-0">Download</h5>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 item">
                        <div class="counter-item text-center">
                            <span class="counter">9</span><span>M</span>
                            <h5 class="mt-1 mb-0">Customer</h5>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 item">
                        <div class="counter-item text-center">
                            <span class="counter">12</span><span>K</span>
                            <h5 class="mt-1 mb-0">Developer</h5>
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
                            <h2 class="title">Modern Design For A Modern Experience.</h2>
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
                            <h2 class="text-white">Gold Station is available for all devices</h2>
                            <p class="text-white">
                                Enjoy the versatility of Gold Station, designed to seamlessly function on all devices.
                                Whether you're using a
                                smartphone, tablet, or desktop, download the app now and experience its powerful
                                features anytime, anywhere!
                            </p>

                            <!-- Download Button -->
                            <div class="button-group download-button justify-content-center">
                                <a href="#">
                                    <img src="assets/img/content/google-play.png" alt="" />
                                </a>
                                <a href="#">
                                    <img src="assets/img/content/app-store.png" alt="" />
                                </a>
                            </div>
                            <span class="d-block fst-italic fw-light text-white mt-3">* Available on iPhone, iPad and
                                all Android devices</span>
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
							<h2 class="title mt-0">Frequently Asked Questions</h2>
							<p>Everything you need to know about selling and renting gold via goldstation, from the method of use, to payment methods, delivery, and security</p>
						</div>
					</div>
				</div>

				<div class="row justify-content-center">
					<div class="col-12 col-md-10">
						<!-- Gold Station Accordion -->
						<div class="accordion accordion-flush" id="Gold Station-accordion">
							<!-- Accordion Item -->
							<div class="accordion-item">
								<h4 class="accordion-header">
									<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
									How to install Gold Station?
									</button>
								</h4>
								<div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#Gold Station-accordion">
									<div class="accordion-body">To install Gold Station, follow the <a href="#">step-by-step instructions</a> in the provided documentation. It covers everything you need to set up and customize the theme effortlessly.</div>
								</div>
							</div>

							<!-- Accordion Item -->
							<div class="accordion-item">
								<h4 class="accordion-header">
									<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
									How can I edit my personal information?
									</button>
								</h4>
								<div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#Gold Station-accordion">
									<div class="accordion-body">You can easily edit your personal information by <a href="#">navigating</a> to the profile settings within the app. From there, update your details and save the changes instantly.</div>
								</div>
							</div>

							<!-- Accordion Item -->
							<div class="accordion-item">
								<h4 class="accordion-header">
									<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">
									Do you have a free trail?
									</button>
								</h4>
								<div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#Gold Station-accordion">
									<div class="accordion-body">Absolutely! Gold Station offers a free trial that gives you full access to its <a href="#">core features</a>. This allows you to explore the theme, test its capabilities, and determine if it fits your needs before making a purchase decision.</div>
								</div>
							</div>

							<!-- Accordion Item -->
							<div class="accordion-item">
								<h4 class="accordion-header">
									<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour">
									Can I get support from the Author?
									</button>
								</h4>
								<div id="collapseFour" class="accordion-collapse collapse" data-bs-parent="#Gold Station-accordion">
									<div class="accordion-body">Yes, you can get dedicated support from the author. If you have any questions or encounter issues, simply reach out through the support channels provided in the documentation or marketplace. The author is available to assist with any queries related to installation, customization, or troubleshooting.</div>
								</div>
							</div>

							<!-- Accordion Item -->
							<div class="accordion-item">
								<h4 class="accordion-header">
									<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive">
									Contact form isn't working?
									</button>
								</h4>
								<div id="collapseFive" class="accordion-collapse collapse" data-bs-parent="#Gold Station-accordion">
									<div class="accordion-body">If the contact form isn't working, first ensure that all required fields are filled out correctly. If the issue persists, please reach out to <a href="#">support</a> for assistance, and provide details about the issue for a quicker resolution.</div>
								</div>
							</div>
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
                            <h2 class="title mt-0">Stay Tuned</h2>
                            <p>
                                Keep an eye out for exciting news and updates from the app, as we continue to enhance
                                your experience and
                                introduce new features.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-between">
                    <div class="col-12 col-md-5">
                        <div class="contact-info">
                            <!-- <h3 class="mt-0">Schedule a call with us to see if we can help</h3> -->
                            <!-- <p>Whether you’re looking to start a new project or simply want to chat, feel free to reach out to us!</p> -->

                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex align-items-center">
                                    <!-- Icon Box -->
                                    <div class="icon-box">
                                        <div class="icon">
                                            <span class="contact-icon material-symbols-outlined">call</span>
                                        </div>
                                    </div>
                                    <span><a href="tel:+18904735102">+1.890.473.5102</a></span>
                                </li>

                                <li class="list-group-item d-flex align-items-center">
                                    <!-- Icon Box -->
                                    <div class="icon-box">
                                        <div class="icon">
                                            <span
                                                class="contact-icon material-symbols-outlined">mark_email_unread</span>
                                        </div>
                                    </div>
                                    <span><a href="mailto:hello@yourmail.com">hello@yourmail.com</a></span>
                                </li>

                                <li class="list-group-item d-flex align-items-center">
                                    <!-- Icon Box -->
                                    <div class="icon-box">
                                        <div class="icon">
                                            <span class="contact-icon material-symbols-outlined">location_on</span>
                                        </div>
                                    </div>
                                    <span>912 Park Ave, Ketchikan, Alaska 99901, USA</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 mt-4 mt-md-0">
                        <form id="contact-form" class="contact-form outlined" method="POST">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" name="name" id="name" placeholder="Name"
                                    required />
                                <label for="name">Name</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="email" class="form-control" name="email" id="email"
                                    placeholder="name@example.com" required />
                                <label for="email">Email address</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" name="subject" id="subject"
                                    placeholder="Subject" required />
                                <label for="subject">Subject</label>
                            </div>
                            <div class="form-floating mb-4">
                                <textarea class="form-control" name="message" id="message"
                                    placeholder="Leave a comment here" required style="height: 100px"></textarea>
                                <label for="message">Message</label>
                            </div>
                            <button type="submit" class="btn d-block w-100">Submit Message</button>
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
                                    <img class="logo" src="assets/images/logo.svg" alt="" width="120" />
                                </a>
                                <p class="slug mt-3">Gold Station - a safe and reliable platform for selling and renting gold
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
                                <h4 class="footer-title mt-0">Useful Links</h4>

                                <!-- Navigation -->
                                <ul class="nav flex-column">
                                    <li class="nav-item">
                                        <a class="nav-link" href="index.html">Home</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="index.html#about">About Us</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="privacy.html">Privacy Policy</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="terms.html">Terms &amp; Conditions</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="index.html#contact">Contact</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-4 item">
                            <!-- Footer Items -->
                            <div class="footer-items">
                                <h4 class="footer-title mt-0">Download</h4>

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
                            <div
                                class="copyright-area d-flex flex-wrap justify-content-center align-items-center text-center py-4">
                                <span>&copy; 2020-2024 Gold Station | All rights reserved.</span>
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
                    <div class="modal-header" data-bs-dismiss="modal">Menu <i class="icon-close"></i></div>
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