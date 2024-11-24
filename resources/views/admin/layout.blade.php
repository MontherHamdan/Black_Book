<!DOCTYPE html>
<html class="no-js" lang="zxx">


<!-- Mirrored from rtsolutz.com/raven/demo-gelr/gelr-html/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 20 Nov 2024 19:47:56 GMT -->
@include('admin.head')

<body>
    <!--[if lt IE 8]>
<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->


    <!--=========================*
         Page Content
*===========================-->
    <div class="vz_main_sec">
        <!--=========================*
               Sidebar
   *===========================-->
        @include('admin.sideBar')
        <!--=========================*
               End Sidebar
   *===========================-->

        <!--=========================*
               Header
   *===========================-->
        @include('admin.header')
        <!--=========================*
               End Header
   *===========================-->

        <!--=========================*
           Main Section
   *===========================-->
        <div class="vz_main_container">
            <div class="vz_main_content">
                {{--  --}}
                @yield('admin.content')
            </div>
            <!--=========================*
                    Footer
       *===========================-->
            @include('admin.footer')
            <!--=========================*
                End Footer
       *===========================-->
        </div>
        <!--=========================*
            End Main Section
   *===========================-->

    </div>
    <!--=========================*
        End Page Content
*===========================-->


    <!--=========================*
            Scripts
*===========================-->

    <!-- Jquery Js -->
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <!-- bootstrap 4 js -->
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <!-- Owl Carousel Js -->
    <script src="{{ asset('js/owl.carousel.min.js') }}"></script>
    <!-- Metis Menu Js -->
    <script src="{{ asset('js/metisMenu.min.js') }}"></script>
    <!-- SlimScroll Js -->
    <script src="{{ asset('js/jquery.slimscroll.min.js') }}"></script>
    <!-- Slick Nav -->
    <script src="{{ asset('js/jquery.slicknav.min.js') }}"></script>
    <!-- start amchart js -->
    <script src="{{ asset('vendors/am-charts/am4/core.js') }}"></script>
    <script src="{{ asset('vendors/am-charts/am4/charts.js') }}"></script>
    <script src="{{ asset('vendors/am-charts/am4/animated.js') }}"></script>

    <!-- flot chart -->
    <script src="{{ asset('vendors/flot/jquery.flot.min.js') }}"></script>
    <script src="{{ asset('vendors/flot/jquery.flot.pie.js') }}"></script>
    <script src="{{ asset('vendors/flot/jquery.flot.resize.min.js') }}"></script>

    <!--Morris Chart-->
    <script src="{{ asset('vendors/charts/morris-bundle/raphael.min.js') }}"></script>
    <script src="{{ asset('vendors/charts/morris-bundle/morris.js') }}"></script>

    <!--Chart Js-->
    <script src="{{ asset('vendors/charts/charts-bundle/Chart.bundle.js') }}"></script>

    <!--Apex Chart-->
    <script src="{{ asset('vendors/apex/js/apexcharts.min.js') }}"></script>

    <!--EChart-->
    <script src="{{ asset('vendors/charts/echarts/echarts-en.min.js') }}"></script>

    <!--Home Script-->
    <script src="{{ asset('js/home.js') }}"></script>

    <!--Perfect Scrollbar-->
    <script src="{{ asset('js/perfect-scrollbar.min.js') }}"></script>
    <!-- Main Js -->
    <script src="{{ asset('js/main.js') }}"></script>

</body>

<!-- Mirrored from rtsolutz.com/raven/demo-gelr/gelr-html/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 20 Nov 2024 19:47:56 GMT -->

</html>
