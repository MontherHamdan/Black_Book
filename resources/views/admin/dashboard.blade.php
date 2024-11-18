@include('admin.head')
<body>
    @include('admin.switcher')
    @include('admin.loader')
    <!-- PAGE -->
    <div class="page">
        <!-- HEADER -->
        <header class="app-header">
            @include('admin.headerContentLeft')
            @include('admin.headerContentRight')
        </header>
        <!-- END HEADER -->

        <!-- SIDEBAR -->
        @include('admin.sideBar')
        <!-- END SIDEBAR -->

        <!-- MAIN-CONTENT -->
        <div class="main-content app-content">
            <div class="container-fluid">
                <!-- Page Header -->
                @include('admin.pageHeader')
                <!-- Page Header Close -->
                {{-- @include('admin.row') --}}

                @yield('content')
            </div>
        </div>
        <!-- END MAIN-CONTENT -->

        <!-- SEARCH-MODAL -->
        @include('admin.searchModal')
        <!-- END SEARCH-MODAL -->

        <!-- FOOTER -->
        @include('admin.footer')
        <!-- END FOOTER -->

    </div>
    <!-- END PAGE-->

    <!-- SCRIPTS -->
    @include('admin.scripts')
    <!-- END SCRIPTS -->

</body>

</html>
