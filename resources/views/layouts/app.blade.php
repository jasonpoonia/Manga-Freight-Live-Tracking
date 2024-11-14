<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Manga Freight Tracking</title>
    <!-- Stylesheets -->
    <link href="{{asset('css/bootstrap.css')}}" rel="stylesheet">
    <link href="{{asset('css/style.css')}}?v=1.2" rel="stylesheet">
    <link href="{{asset('css/responsive.css')}}" rel="stylesheet">
    <link rel="shortcut icon" href="{{asset('images/favicon.png')}}" type="image/x-icon">
    <link rel="icon" href="{{asset('images/favicon.png')}}" type="image/x-icon">

    <!-- Responsive -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">

    <!--[if lt IE 9]><script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.js"></script><![endif]-->
    <!--[if lt IE 9]><script src="{{asset('js/respond.js')}}"></script><![endif]-->

    @stack('head')

</head>

<body class="hidden-bar-wrapper">

<div class="page-wrapper">

    <!-- Preloader -->
    <div class="preloader"></div>
    <!--
    <header class="main-header header-style-two">
        <div class="auto-container">
            <div class="header-inner">
                
                <div class="header-top">
                    <div class="clearfix">

                        
                        <div class="top-right">

                            
                            <ul class="right-list">
                                <li><span class="icon flaticon-mail"></span>sales@tsbliving.co.nz</li>
                                <li><span class="icon flaticon-phone-contact"></span>09 274 1981</li>
                            </ul>

                            
                            <ul class="social-box">
                                <li><a href="https://www.facebook.com/profile.php?id=100094235177655"><span class="fa fa-facebook"></span></a></li>
                                <li><a href="https://www.instagram.com/tsb_living/"><span class="fa fa-instagram"></span></a></li>
                                <li><a href="https://www.pinterest.nz/tsbliving01/"><span class="fa fa-pinterest"></span></a></li>
                            </ul>

                        </div>

                    </div>

                </div>

                
                <div class="header-upper">

                    <div class="clearfix">

                        <div class="pull-left logo-box">
                            <div class="logo">
                                <a href="{{url('/')}}">
                                    <img src="{{asset('images/icons/Asset_1Red_400x.avif')}}" alt="" title="">
                                </a>
                            </div>
                        </div>

                        <div class="pull-right upper-right">

                            
                            <div class="header-lower">


                                <div class="nav-outer clearfix">
                                    
                                    <nav class="main-menu navbar-expand-md">
                                        <div class="navbar-header">
                                            
                                            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                                                <span class="icon-bar"></span>
                                                <span class="icon-bar"></span>
                                                <span class="icon-bar"></span>
                                            </button>
                                        </div>

                                        <div class="navbar-collapse collapse clearfix" id="navbarSupportedContent">
                                            <ul class="navigation clearfix">
                                                <li><a href="https://tsbliving.co.nz">Home</a></li>
                                                <li class="current"><a href="{{url('/')}}">Track & Trace</a></li>
                                            </ul>
                                        </div>
                                    </nav>


                                </div>

                            </div>
                            

                        </div>

                    </div>

                </div>
                
            </div>
        </div>
    
    </header>
    End Main Header -->

    @yield('content')

    <!--Main Footer
    <footer class="main-footer">

        
        <div class="footer-bottom">
            <div class="copyright">TSB Living &copy; 2024 / ALL RIGHTS RESERVED</div>
        </div>

    </footer>
-->
</div>
<!--End pagewrapper-->

<!--Scroll to top-->
<div class="scroll-to-top scroll-to-target" data-target="html"><span class="fa fa-arrow-up"></span></div>

<script src="{{asset('js/jquery.js')}}"></script>
<script src="{{asset('js/popper.min.js')}}"></script>
<script src="{{asset('js/bootstrap.min.js')}}"></script>
<script src="{{asset('js/jquery.mCustomScrollbar.concat.min.js')}}"></script>
<script src="{{asset('js/jquery.fancybox.js')}}"></script>
<script src="{{asset('js/appear.js')}}"></script>
<script src="{{asset('js/owl.js')}}"></script>
<script src="{{asset('js/wow.js')}}"></script>
<script src="{{asset('js/jquery-ui.js')}}"></script>
<script src="{{asset('js/script.js')}}"></script>

@stack('js')

</body>
</html>
