/* ========================================================================

Data Table Init

=========================================================================
 */


"use strict";


/*======== Doucument Ready Function =========*/
jQuery(document).ready(function () {

    //CACHE JQUERY OBJECTS
    var $window = $(window);

    /*================================
            datatable active
    ==================================*/
    if ($('#dataTable').length) {
        $('#dataTable').DataTable();
    }
    if ($('#dataTable2').length) {
        $('#dataTable2').DataTable();
    }
    if ($('#dataTable3').length) {
        $('#dataTable3').DataTable();
    }
    if ($('#responsive-datatable').length) {
        $('#responsive-datatable').DataTable({
            pageLength: 10,     // عدد العناصر بالصفحة
            lengthChange: true, // Show entries
            searching: true,    // Search
            ordering: true,     // Sorting
            info: true,         // Showing x to y
            responsive: true,
        });
    }


});
/*======== End Doucument Ready Function =========*/