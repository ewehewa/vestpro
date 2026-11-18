<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
    <title>The Trading and Investment Specialist |Strategic Investment Pro</title>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- Sites meta Data -->
    <meta name="application-name" content="Strategic Investment Pro" />
    <meta name="author" content="endycode" />
    <meta name="keywords" content="Strategic Investment Pro, Crypto, Forex, Indices, and Stocks Trading Business" />
    <meta name="description"
        content="Welcome to Strategic Investment Pro,Strategic Investment Pro is a fully licensed and regulated online with an online trading platform that empowers you to invest across global financial markets.">
    <meta name="robots" content="index, follow">
    <!-- OG meta data -->

    <meta property="og:site_name" content="Strategic Investment Pro" />

    <meta property="og:description"
        content="Welcome to Strategic Investment Pro,Strategic Investment Pro is a fully licensed and regulated online with an online trading platform that empowers you to invest across global financial markets." />
    <meta property="og:type" content="website" />
    <meta property="og:image" content="{{ asset('assets/img/logo-icon.png') }}" />

    <link rel="icon" href="{{ asset('assets/img/logo-icon.svg') }}" type="image/svg+xml" />
    <link rel="icon" href="{{ asset('assets/img/logo-icon.png') }}" type="image/png" />
    <link rel="apple-touch-icon" href="{{ asset('assets/img/logo-icon.png') }}" />

    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/aos.css" />
    <link rel="stylesheet" href="assets/css/all.min.css" />

    <link rel="stylesheet" href="assets/css/swiper-bundle.min.css" />

    <link rel="stylesheet" type="text/css" href="assets/plugins/owlcarousel/owl.carousel.min.css">
    <link rel="stylesheet" type="text/css" href="assets/plugins/owlcarousel/owl.theme.default.min.css">
    <link rel="stylesheet" type="text/css" href="assets/plugins/slick/slick.css">
    <link rel="stylesheet" type="text/css" href="assets/plugins/slick/slick-theme.css">

    <link href="https://fonts.googleapis.com/css2?family=Ubuntu&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu&amp;display=swap" rel="stylesheet">
    <!-- main css for template -->
    <link rel="stylesheet" href="assets/css/style16791679.css?6" />

    <style>
        .hero-wrapper h1.banner__content-heading {
            display: inline-block;
            margin: 0px;
            height: 35px;
            color: #34E834;
            font-size: 35px;
            font-weight: 600;
        }

        .typed-cursor {
            font-size: 30px;
            font-weight: 300;
            color: #000;
        }

        .heading-medium {
            font-size: 1.5em !important;
        }

        .banner:before {
            background-color: #34E834;
            block-size: 65%;
            -webkit-clip-path: polygon(0 60%, 100% 40%, 100% 100%, 0 100%);
            clip-path: polygon(0 60%, 100% 40%, 100% 100%, 0 100%);
            content: " ";
            inline-size: 100%;
            inset-block-end: 0;
            position: absolute;
        }

        @media only screen and (min-width: 992px) {
            .banner:before {
                block-size: 100%;
                -webkit-clip-path: polygon(25% 0, 100% 0, 100% 100%, 0 100%);
                clip-path: polygon(25% 0, 100% 0, 100% 100%, 0 100%);
                inline-size: 50%;
                inset-block-end: unset;
                inset-inline-end: 0;
            }
        }

        .banner--style1 {
            padding-block-start: 0px;
        }

        @media (min-width: 992px) {
            .banner--style1 {
                padding-block-start: 0px;
            }
        }

        @media (min-width: 768px) {
            .banner--style1 {
                padding-block-start: 0px;
            }
        }

        .owl-carousel .owl-item img,
        .slick-slide img {
            display: inline;
            /* width: 20px; */
        }

        .plan-card {
            margin: 5px !important;
        }

        #once-popup {
            position: fixed;
            top: 0;
            bottom: 0;
            right: 0;
            left: 0;
            background: rgba(0, 0, 0, 0.65);
            text-align: center;
            z-index: 10000;
        }

        #once-popup .inner {
            background: #ffffff;
            padding: 20px;
            width: 600px;
            max-width: 90%;
            margin: 50px auto;
        }

        #once-popup #popup-close {
            float: right;
            font-size: 30px;
            line-height: 10px;
            padding: 5px;
            cursor: pointer;
        }

        .slide-arrows {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            display: flex;
            justify-content: space-between;
            width: 100%;
            padding: 0 10px;
        }

        .slide-arrows .prev-arrow,
        .slide-arrows .next-arrow {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            /* background-color: var(--btn-color); */
            color: #ffffff;
            font-size: 24px;
            cursor: pointer;
        }

        .slide-arrows .next-arrow:hover,
        .slide-arrows .prev-arrow:hover {
            background-color: rgba(0, 0, 0, 0.8);
        }

        @media (max-width: 768px) {
            .slide-arrows {
                padding: 0px;
            }
        }
    </style>
    <style>
        body {
            font-family: 'Ubuntu', sans-serif;
        }

        .header-wrapper .logo a img {
            max-height: 40px;
        }

        h1,
        h2,
        h3,
        h4,
        h5 {
            font-family: 'Ubuntu', sans-serif
        }

        .menu>li>a {
            font-size: 16PX;
            padding: 15px 22px;
            text-transform: capitalize;
            font-family: "Ubuntu", sans-serif;
            font-weight: 500;
        }

        @media (min-width: 992px) {
            .header-wrapper {
                padding-block: 15px !important;
            }
        }

        /* Dark sections: re-scope the theme's dark-mode variables to a single section */
        .section-dark {
            --body-color: #000000;
            --title-color: #FFFFFF;
            --title-color-style2: #BBC2C7;
            --text-color: rgba(255, 255, 255, 0.7);
            --text-color-light: #FFFFFF;
            --border-color: rgba(230, 237, 251, 0.12);
            --border-color-2: rgba(255, 255, 255, 0.08);
            --border-color-4: #1B2D29;
            --border-color-5: rgba(255, 255, 255, 0.08);
            --border-color-6: rgba(255, 255, 255, 0.08);
            --border-color-7: #1B2D29;
            --header-border-color: rgba(255, 255, 255, 0.12);
            --social-border-color: rgba(255, 255, 255, 0.08);
            --social-border-color-2: rgba(255, 255, 255, 0.08);
            --social-border-color-3: rgba(255, 255, 255, 0.1);
            --bg-color: rgba(237, 242, 248, 0.08);
            --bg-color-2: #161616;
            --bg-color-3: #000000;
            --bg-color-6: #161616;
            --bg-color-7: rgba(255, 255, 255, 0.04);
            --bg-color-9: #000000;
            --bg-grad-color: #1A1A1A;
            --bg-grad-color-2: rgba(213, 229, 249, 0.16);
            --bg-grad-color-3: rgba(255, 255, 255, 0.06);
            --bg-team: #161616;
            --wh-color: #161616;
            --black-color: #FFFFFF;
            --floating-content-bg-color: rgba(30, 30, 30, 0.85);
            --floating-content-bg-color-2: rgba(255, 255, 255, 0.08);
            --service-item-border-color: #161616;
            --service-item-bg-color: #111111;
            --faq-item-btn-border-color: rgba(255, 255, 255, 0.08);
            --faq-bg-color: #111111;
            --extra-bg-color: #111111;
            --tag-bg-color: #0F2A0F;
            --bg-shape-color: rgba(213, 229, 249, 0.08);
            --bg-shape-color-2: rgba(213, 229, 249, 0.16);
            --arrow-color1: #2A2A2A;
            --arrow-icon-color: #FFFFFF;
            background-color: #000000 !important;
            color: var(--text-color);
        }

        .section-dark .typed-cursor {
            color: var(--title-color);
        }

        /* Header logo (markup and base styles live in components/brand-logo.blade.php) */
        .header-wrapper .logo {
            width: auto !important;
            max-width: none !important;
        }

        .header-wrapper .logo a.brand-logo {
            display: inline-flex;
            color: var(--title-color);
            font-family: 'Ubuntu', sans-serif;
        }

        @media (min-width: 992px) and (max-width: 1199px) {
            .header-wrapper .logo a.brand-logo {
                --logo-size: 34px !important;
            }
        }

        @media (max-width: 575px) {
            .header-wrapper .logo a.brand-logo {
                --logo-size: 30px !important;
            }
        }

        /* Green CTA card keeps dark text for contrast */
        .section-dark .cta__newsletter-inner {
            --title-color: #000000;
            --text-color: #0C263A;
        }

        /* Black text on the green primary buttons, as on bright green white text is unreadable */
        .trk-btn.trk-btn--primary,
        .trk-btn.trk-btn--primary span,
        .trk-btn.active.trk-btn--primary,
        .trk-btn:hover.trk-btn--primary {
            color: #000000;
        }

        .footer {
            --footer-bg-color: #000000;
        }

        /* Preloader: arrow draws itself inside the mark, a slow arc orbits it, name and bar fade up */
        .preloader {
            background: radial-gradient(circle at 50% 45%, rgba(52, 232, 52, 0.10) 0%, rgba(0, 0, 0, 0) 45%), #000000;
        }

        .preloader.is-leaving {
            pointer-events: none;
        }

        .preloader.is-leaving .loader {
            transform: translateY(-12px) scale(0.98);
            transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .loader {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 22px;
        }

        .loader__emblem {
            position: relative;
            width: 104px;
            height: 104px;
            display: grid;
            place-items: center;
        }

        .loader__orbit {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            animation: loader-spin 2.4s linear infinite;
        }

        .loader__orbit-track,
        .loader__orbit-arc {
            fill: none;
            stroke-width: 1.5;
        }

        .loader__orbit-track {
            stroke: rgba(255, 255, 255, 0.08);
        }

        .loader__orbit-arc {
            stroke: #34E834;
            stroke-linecap: round;
            stroke-dasharray: 22 78;
        }

        .loader__mark {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            animation: loader-breathe 2.4s ease-in-out infinite;
        }

        .loader__line,
        .loader__head {
            fill: none;
            stroke: #000000;
            stroke-width: 3.5;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-dasharray: 1;
            stroke-dashoffset: 1;
        }

        .loader__line {
            animation: loader-draw-line 2.4s cubic-bezier(0.65, 0, 0.35, 1) infinite;
        }

        .loader__head {
            animation: loader-draw-head 2.4s cubic-bezier(0.65, 0, 0.35, 1) infinite;
        }

        .loader__name {
            font-family: 'Ubuntu', sans-serif;
            font-size: 0.8rem;
            font-weight: 500;
            letter-spacing: 0.28em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.85);
            opacity: 0;
            animation: loader-rise 0.9s 0.3s cubic-bezier(0.2, 0.7, 0.2, 1) forwards;
        }

        .loader__name span {
            color: #34E834;
        }

        .loader__bar {
            position: relative;
            width: 140px;
            height: 2px;
            border-radius: 2px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.08);
            opacity: 0;
            animation: loader-rise 0.9s 0.5s cubic-bezier(0.2, 0.7, 0.2, 1) forwards;
        }

        .loader__bar span {
            position: absolute;
            inset: 0;
            width: 40%;
            border-radius: inherit;
            background: linear-gradient(90deg, rgba(52, 232, 52, 0), #34E834, rgba(52, 232, 52, 0));
            animation: loader-sweep 1.6s ease-in-out infinite;
        }

        @keyframes loader-spin {
            to { transform: rotate(360deg); }
        }

        @keyframes loader-breathe {
            0%, 100% { box-shadow: 0 0 0 0 rgba(52, 232, 52, 0); transform: scale(1); }
            50% { box-shadow: 0 0 32px 2px rgba(52, 232, 52, 0.35); transform: scale(1.03); }
        }

        /* line draws, arrowhead follows, both hold, then fade out before the next cycle */
        @keyframes loader-draw-line {
            0% { stroke-dashoffset: 1; opacity: 1; }
            40% { stroke-dashoffset: 0; opacity: 1; }
            80% { stroke-dashoffset: 0; opacity: 1; }
            95%, 100% { stroke-dashoffset: 0; opacity: 0; }
        }

        @keyframes loader-draw-head {
            0%, 32% { stroke-dashoffset: 1; opacity: 1; }
            52% { stroke-dashoffset: 0; opacity: 1; }
            80% { stroke-dashoffset: 0; opacity: 1; }
            95%, 100% { stroke-dashoffset: 0; opacity: 0; }
        }

        @keyframes loader-rise {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes loader-sweep {
            from { transform: translateX(-100%); }
            to { transform: translateX(250%); }
        }

        @media (prefers-reduced-motion: reduce) {
            .loader__orbit,
            .loader__mark,
            .loader__bar span {
                animation: none;
            }

            .loader__line,
            .loader__head {
                animation: none;
                stroke-dashoffset: 0;
            }
        }
    </style>
  <!-- Smartsupp Live Chat script -->
<script type="text/javascript">
var _smartsupp = _smartsupp || {};
_smartsupp.key = '0adbb0d1a6ee3841614754a4c2a11ddbd52f4ea5';
window.smartsupp||(function(d) {
  var s,c,o=smartsupp=function(){ o._.push(arguments)};o._=[];
  s=d.getElementsByTagName('script')[0];c=d.createElement('script');
  c.type='text/javascript';c.charset='utf-8';c.async=true;
  c.src='https://www.smartsuppchat.com/loader.js?';s.parentNode.insertBefore(c,s);
})(document);
</script>
<noscript>Powered by <a href="https://www.smartsupp.com" target="_blank">Smartsupp</a></noscript>
    


</head>
<body>
    <header class="header-section--style1 section-dark">
        <div class="header-bottom">
            <div class="container">
                <div class="header-wrapper">
                    <div class="logo">
                        <x-brand-logo :href="url('/')" />
                    </div>
                    <div class="menu-area">
                        <ul class="menu menu--style1">
                            <li>
                                <a href="{{ url('/about') }}">About us</a>
                            </li>
                            <li>
                                <a href="{{ url('/plans') }}">Pricing</a>
                            </li>
                            <li>
                                <a href="{{ url('/faqs') }}">FAQs</a>
                            </li>
                            <li>
                                <a href="{{ '/contact' }}">Contact Us</a>
                            </li>
                            <li>
                                <div id="google_translate_element"></div>
                            </li>

                        </ul>
                    </div>
                    <div class="header-action">
                        <div class="menu-area">
                            <div class="header-btn">
                                <a href="{{ url('/register') }}" class="trk-btn trk-btn--border trk-btn--primary">
                                    <span>
                                        Create a free account
                                    </span>
                                </a>
                            </div>

                            <div class="header-bar d-lg-none header-bar--style1">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{ $slot }}

<!-- ===============>> footer start here <<================= -->
<footer class="footer brand-1">
    <div class="container">
        <div class="footer__wrapper">
            <div class="footer__top footer__top--style2">
                <div class="row gy-5 gx-4">
                    <div class="col-md-6">
                        <div class="footer__about">
                            <a href="{{ url('/') }}" class="footer__about-logo fs-1" style="color: #34E834">Strategic Investment Pro</a>
                            <p class="footer__about-moto">
                                Welcome to Strategic Investment Pro. Cloud mining is super simple - Your mining
                                rigs are already set up and running. As soon as you’ve set up your account, you can
                                start to mine your first coins using our Bitcoin cloud mining service!.

                                <br>

                                Cloud mining is one of the most profitable ways of making profit in the crypto currency
                                ecosystem in the world.


                            </p>

                        </div>
                    </div>
                    <div class="col-md-2 col-sm-4 col-6">
                        <div class="footer__links">
                            <div class="footer__links-tittle">
                                <h6>Quick links</h6>
                            </div>
                            <div class="footer__links-content">
                                <ul class="footer__linklist">
                                    <li class="footer__linklist-item">
                                        <a href="{{ url('/') }}">Home</a>
                                    </li>
                                    <li class="footer__linklist-item">
                                        <a href="{{ url('/about') }}">About Us</a>
                                    </li>
                                    <li class="footer__linklist-item">
                                        <a href="{{ url('/login') }}">My Account</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-4 col-6">
                        <div class="footer__links">
                            <div class="footer__links-tittle">
                                <h6>Pages</h6>
                            </div>
                            <div class="footer__links-content">
                                <ul class="footer__linklist">
                                    <li class="footer__linklist-item">
                                        <a href="{{ url('/privacy-policy') }}">Privacy Policy</a>
                                    </li>
                                    <li class="footer__linklist-item">
                                        <a href="{{ url('/plans') }}">Pricing</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-4">
                        <div class="footer__links">
                            <div class="footer__links-tittle">
                                <h6>Support</h6>
                            </div>
                            <div class="footer__links-content">
                                <ul class="footer__linklist">
                                    <li class="footer__linklist-item">
                                        <a href="{{ url('/faqs') }}">FAQs</a>
                                    </li>
                                    <li class="footer__linklist-item">
                                        <a href="{{ url('/contact') }}">Support Center</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer__bottom">
                <div class="footer__end">
                    <div class="footer__end-copyright">
                        <p class="mb-0">
                            © {{ date('Y') }} All Rights Reserved By
                            Strategic Investment Pro
                        </p>
                    </div>
                    <div>
                        <ul class="social">
                            <li class="social__item">
                                <a href="#" class="social__link social__link--style22"><i
                                        class="fab fa-facebook-f"></i></a>
                            </li>

                            <li class="social__item">
                                <a href="#" class="social__link social__link--style22"><i
                                        class="fab fa-instagram"></i></a>
                            </li>

                            <li class="social__item">
                                <a href="#" class="social__link social__link--style22"><i
                                        class="fa-brands fa-linkedin-in"></i></a>
                            </li>

                            <li class="social__item">
                                <a href="#" class="social__link social__link--style22"><i
                                        class="fab fa-youtube"></i></a>
                            </li>

                            <li class="social__item">
                                <a href="#" class="social__link social__link--style22"><i
                                        class="fab fa-twitter"></i></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer__shape">
        <span class="footer__shape-item footer__shape-item--1"><img src="assets/images/footer/1.png"
                alt="shape icon" /></span>
        <span class="footer__shape-item footer__shape-item--2">
            <span></span>
        </span>
    </div>
</footer>
<!-- ===============>> footer end here <<================= -->

<!-- ===============>> scrollToTop start here <<================= -->
<a href="#" class="scrollToTop scrollToTop--home1"><i class="fa-solid fa-arrow-up-from-bracket"></i></a>
<!-- ===============>> scrollToTop ending here <<================= -->
<!-- vendor plugins -->

<script src="assets/js/bootstrap.bundle.min.js"></script>
<script src="assets/plugins/jquery/jquery-3.6.0.min.js"></script>
<script src="assets/js/all.min.js"></script>
<script src="assets/js/swiper-bundle.min.js"></script>
<script src="assets/js/aos.js"></script>
<script src="assets/js/fslightbox.js"></script>
<script src="assets/js/purecounter_vanilla.js"></script>
<script src="assets/plugins/owlcarousel/owl.carousel.min.js"></script>
<script src="assets/plugins/slick/slick.min.js"></script>
<script type="text/javascript" src="assets/js/typed.min.js"></script>
<script src="assets/js/customc4cac4ca.js?3"></script>


<script type="text/javascript">
    function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'en',
                layout: google.translate.TranslateElement.InlineLayout.SIMPLE
            }, 'google_translate_element');
        }
</script>

<script type="text/javascript" src="translate.google.com/translate_a/elementa0d8a0d8.html?cb=googleTranslateElementInit">
</script>

<script>
    $(document).ready(function() {
            // Typed Initiate

            if ($('.hero-wrapper').length == 1) {
                var typed_strings = $('.hero-wrapper .typed-text').text();
                var typed = new Typed('.hero-wrapper .typed-result', {
                    strings: typed_strings.split(', '),
                    typeSpeed: 100,
                    backSpeed: 20,
                    smartBackspace: false,
                    loop: true
                });
            }
            $('.hero-slider').slick({

                autoplay: true,
                speed: 2000,
                dots: false,
                arrows: false,
                fade: !0,
                cssEase: 'linear',
                slidesToShow: 1,
                responsive: [{
                        breakpoint: 1024,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1,
                            infinite: true,
                            dots: false
                        }
                    },
                    {
                        breakpoint: 900,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1
                        }
                    },
                    {
                        breakpoint: 480,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1
                        }
                    }
                ]
            });

            $('.plans-slider').slick({

                autoplay: true,
                speed: 2000,
                dots: false,
                arrows: false,
                slidesToShow: 3,
                responsive: [{
                        breakpoint: 1024,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 2,
                            infinite: true,
                            dots: false
                        }
                    },
                    {
                        breakpoint: 900,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1
                        }
                    },
                    {
                        breakpoint: 480,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1
                        }
                    }
                ]
            });
            $('.prev-arrow').click(function() {
                $('.plans-slider').slick('slickPrev');
            });

            $('.next-arrow').click(function() {
                $('.plans-slider').slick('slickNext');
            });



                            $("#once-popup").delay(2000).fadeIn();
                            
            $('#popup-close').click(function(e) {
                $('#once-popup').fadeOut();
            });
            $('#once-popup').click(function(e) {
                $('#once-popup').fadeOut();
            });
        });
</script>
<script>
    $(document).ready(function() {
                            $("#once-popup").delay(2000).fadeIn();
                            
            $('#popup-close').click(function(e) {
                $('#once-popup').fadeOut();
            });
            $('#once-popup').click(function(e) {
                $('#once-popup').fadeOut();
            });
        });
</script>


</body>
</html>