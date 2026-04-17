<!DOCTYPE html>
<html lang="en">
@include('admin.head')

<body>

    @if(auth()->check() && auth()->user()->isDesigner())
    <!-- Global Warning Banner for Penalized Designers -->
    <div id="global-penalty-banner" class="alert alert-danger text-center fw-bold mb-0 rounded-0 shadow-sm" style="display: {{ auth()->user()->isPenalized() ? 'block' : 'none' }}; position: sticky; top: 0; z-index: 99999; font-family: 'Cairo', sans-serif;">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <span>عذراً، لقد تجاوزت الحد الأقصى للتعديلات المسموحة أو تم إيقافك مؤقتاً. لا يمكنك استلام طلبات جديدة حالياً.</span>
        <span id="global-penalty-until" class="ms-2">
            {{ (auth()->user()->penalized_until && auth()->user()->penalized_until->isFuture()) ? '(موقوف حتى ' . auth()->user()->penalized_until->timezone('Asia/Amman')->format('h:i A') . ')' : '' }}
        </span>
    </div>
    @endif
    
    <!-- Begin page -->
    <div id="wrapper">


        <!-- Topbar Start -->
        @include('admin.topbar')

        <!-- end Topbar -->

        <!-- ========== Left Sidebar Start ========== -->
        @include('admin.leftSideBar')
        <!-- Left Sidebar End -->

        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->

        <div class="content-page">
            <div class="content">

                <!-- Start Content-->
                <div class="container-fluid">

                    @yield('content')

                </div> <!-- container-fluid -->

            </div> <!-- content -->

            <!-- Footer Start -->
            @include('admin.footer')
            <!-- end Footer -->

        </div>

        <!-- ============================================================== -->
        <!-- End Page content -->
        <!-- ============================================================== -->
    </div>
    <!-- END wrapper -->

    <!-- Right Sidebar -->
    @include('admin.rightSideBar')
    <!-- /Right-bar -->

    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>

    <!-- Vendor -->
    <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/libs/waypoints/lib/jquery.waypoints.min.js') }}"></script>
    <script src="{{ asset('assets/libs/jquery.counterup/jquery.counterup.min.js') }}"></script>
    <script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>

    <!-- knob plugin -->
    <script src="{{ asset('assets/libs/jquery-knob/jquery.knob.min.js') }}"></script>

    <!--Morris Chart-->
    <script src="{{ asset('assets/libs/morris.js06/morris.min.js') }}"></script>
    <script src="{{ asset('assets/libs/raphael/raphael.min.js') }}"></script>

    <!-- Dashboar init js-->
    <script src="{{ asset('assets/js/pages/dashboard.init.js') }}"></script>

    <!-- third party js -->
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/libs/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-buttons/js/buttons.flash.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-keytable/js/dataTables.keyTable.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-select/js/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('assets/libs/pdfmake/build/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/libs/pdfmake/build/vfs_fonts.js') }}"></script>
    <script src="{{ asset('https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js') }}"></script>
    <!-- third party js ends -->

    <!-- Datatables init -->
    <script src="{{ asset('assets/js/pages/datatables.init.js') }}"></script>

    <!-- Plugins js -->
    <script src="{{ asset('assets/libs/dropzone/min/dropzone.min.js') }}"></script>
    <script src="{{ asset('assets/libs/dropify/js/dropify.min.js') }}"></script>

    <!-- Init js-->
    <script src="{{ asset('assets/js/pages/form-fileuploads.init.js') }}"></script>

    <!-- Toastr js -->
    <script src="{{ asset('assets/libs/toastr/build/toastr.min.js') }}"></script>

    <script src="{{ asset('assets/js/pages/toastr.init.js') }}"></script>

    <!-- App js-->
    <script src="{{ asset('assets/js/app.js') }}"></script>

    <!-- Sweet Alerts js -->
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>

    <!-- Sweet alert init js-->
    <script src="{{ asset('assets/js/pages/sweet-alerts.init.js') }}"></script>

    <script>
        @if (session('success'))
            toastr.success("{{ session('success') }}", "Success", {
                closeButton: true,
                progressBar: true,
                timeOut: 5000
            });
        @endif

        @if (session('error'))
            toastr.error("{{ session('error') }}", "Error", {
                closeButton: true,
                progressBar: true,
                timeOut: 5000
            });
        @endif

        @if (session('warning'))
            toastr.warning("{{ session('warning') }}", "Warning", {
                closeButton: true,
                progressBar: true,
                timeOut: 5000
            });
        @endif

        @if (session('info'))
            toastr.info("{{ session('info') }}", "Info", {
                closeButton: true,
                progressBar: true,
                timeOut: 5000
            });
        @endif
    </script>

    @if(auth()->check() && auth()->user()->isDesigner())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof window.Echo !== 'undefined') {
                window.Echo.private('App.Models.User.{{ auth()->id() }}')
                    .listen('DesignerPenaltyStatusChanged', (e) => {
                        const banner = document.getElementById('global-penalty-banner');
                        const untilSpan = document.getElementById('global-penalty-until');
                        
                        if (e.isPenalized) {
                            // Show banner
                            banner.style.display = 'block';
                            if (e.penalizedUntil) {
                                let d = new Date(e.penalizedUntil);
                                untilSpan.innerText = '(موقوف حتى ' + d.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) + ')';
                            } else {
                                untilSpan.innerText = '';
                            }
                        } else {
                            // Hide banner
                            banner.style.display = 'none';
                            untilSpan.innerText = '';
                        }

                        // Refresh DataTables if it exists on the page
                        if (typeof table !== 'undefined' && table.ajax) {
                            table.ajax.reload(null, false);
                        }
                    });
            }
        });
    </script>
    @endif

</body>


</html>
