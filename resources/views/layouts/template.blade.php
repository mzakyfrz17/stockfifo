<!DOCTYPE html>
<html lang="en">

    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <title>FIFO Stock Kedai</title>
        <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
        <link rel="icon" href="{{ asset('assets/img/kaiadmin/favicon.ico') }}" type="image/x-icon" />

        <!-- Fonts and icons -->
        <script src="{{ asset('assets/js/plugin/webfont/webfont.min.js') }}"></script>
        <script>
            WebFont.load({
                google: {
                    families: ["Public Sans:300,400,500,600,700"]
                },
                custom: {
                    families: [
                        "Font Awesome 5 Solid",
                        "Font Awesome 5 Regular",
                        "Font Awesome 5 Brands",
                        "simple-line-icons",
                    ],
                    urls: ["{{ asset('assets/css/fonts.min.css') }}"],
                },
                active: function() {
                    sessionStorage.fonts = true;
                },
            });
        </script>

        <!-- CSS Files -->
        <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/css/plugins.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/css/kaiadmin.min.css') }}" />

        <!-- CSS Just for demo purpose -->
        <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
    </head>

    <body>
        <div class="wrapper">
            <!-- Sidebar -->
            @include('layouts.sidebar')
            <!-- End Sidebar -->

            <div class="main-panel">
                <div class="main-header">
                    <div class="main-header-logo">
                        <!-- Logo Header -->
                        <div class="logo-header" data-background-color="dark">
                            <a href="{{ url('/') }}" class="logo">
                                <img src="{{ asset('assets/img/kaiadmin/logo_light.svg') }}" alt="navbar brand"
                                    class="navbar-brand" height="20" />
                            </a>
                            <div class="nav-toggle">
                                <button class="btn btn-toggle toggle-sidebar">
                                    <i class="gg-menu-right"></i>
                                </button>
                                <button class="btn btn-toggle sidenav-toggler">
                                    <i class="gg-menu-left"></i>
                                </button>
                            </div>
                            <button class="topbar-toggler more">
                                <i class="gg-more-vertical-alt"></i>
                            </button>
                        </div>
                        <!-- End Logo Header -->
                    </div>
                    <!-- Navbar Header -->
                    @include('layouts.navbar')
                    <!-- End Navbar -->
                </div>

                <div class="container ">
                    <div class="page-inner">
                        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
                            @yield('content')
                        </div>
                    </div>
                </div>

                <footer class="footer">
                    <div class="container-fluid d-flex justify-content-between">
                        <div class="copyright">
                            2025, made with <i class="fa fa-heart heart text-danger"></i> by
                            <a href="http://www.themekita.com">Zaky</a>
                        </div>
                        <div>
                            Distributed by
                            <a target="_blank" href="https://themewagon.com/">Guntur</a>.
                        </div>
                    </div>
                </footer>
            </div>
            <!-- End Custom template -->
        </div>

        <!--   Core JS Files   -->
        <script src="{{ asset('assets/js/core/jquery-3.7.1.min.js') }}"></script>
        <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
        <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>

        <!-- jQuery Scrollbar -->
        <script src="{{ asset('assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>

        <!-- Chart JS -->
        <script src="{{ asset('assets/js/plugin/chart.js/chart.min.js') }}"></script>

        <!-- jQuery Sparkline -->
        <script src="{{ asset('assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js') }}"></script>

        <!-- Chart Circle -->
        <script src="{{ asset('assets/js/plugin/chart-circle/circles.min.js') }}"></script>

        <!-- Datatables -->
        <script src="{{ asset('assets/js/plugin/datatables/datatables.min.js') }}"></script>

        <!-- Bootstrap Notify -->
        <script src="{{ asset('assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}"></script>

        <!-- jQuery Vector Maps -->
        <script src="{{ asset('assets/js/plugin/jsvectormap/jsvectormap.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugin/jsvectormap/world.js') }}"></script>

        <!-- Sweet Alert -->
        <script src="{{ asset('assets/js/plugin/sweetalert/sweetalert.min.js') }}"></script>

        <!-- Kaiadmin JS -->
        <script src="{{ asset('assets/js/kaiadmin.min.js') }}"></script>

        <!-- Kaiadmin DEMO methods -->
        <script src="{{ asset('assets/js/setting-demo.js') }}"></script>
        <script src="{{ asset('assets/js/demo.js') }}"></script>
        <script>
            $(document).ready(function() {
                $(".basic-datatables").DataTable({});

                $("#multi-filter-select").DataTable({
                    pageLength: 5,
                    initComplete: function() {
                        this.api()
                            .columns()
                            .every(function() {
                                var column = this;
                                var select = $(
                                        '<select class="form-select"><option value=""></option></select>'
                                    )
                                    .appendTo($(column.footer()).empty())
                                    .on("change", function() {
                                        var val = $.fn.dataTable.util.escapeRegex($(this).val());

                                        column
                                            .search(val ? "^" + val + "$" : "", true, false)
                                            .draw();
                                    });

                                column
                                    .data()
                                    .unique()
                                    .sort()
                                    .each(function(d, j) {
                                        select.append(
                                            '<option value="' + d + '">' + d + "</option>"
                                        );
                                    });
                            });
                    },
                });

                // Add Row
                $("#add-row").DataTable({
                    pageLength: 5,
                });

                var action =
                    '<td> <div class="form-button-action"> <button type="button" data-bs-toggle="tooltip" title="" class="btn btn-link btn-primary btn-lg" data-original-title="Edit Task"> <i class="fa fa-edit"></i> </button> <button type="button" data-bs-toggle="tooltip" title="" class="btn btn-link btn-danger" data-original-title="Remove"> <i class="fa fa-times"></i> </button> </div> </td>';

                $("#addRowButton").click(function() {
                    $("#add-row")
                        .dataTable()
                        .fnAddData([
                            $("#addName").val(),
                            $("#addPosition").val(),
                            $("#addOffice").val(),
                            action,
                        ]);
                    $("#addRowModal").modal("hide");
                });
            });
        </script>
        <script>
            $("#lineChart").sparkline([102, 109, 120, 99, 110, 105, 115], {
                type: "line",
                height: "70",
                width: "100%",
                lineWidth: "2",
                lineColor: "#177dff",
                fillColor: "rgba(23, 125, 255, 0.14)",
            });

            $("#lineChart2").sparkline([99, 125, 122, 105, 110, 124, 115], {
                type: "line",
                height: "70",
                width: "100%",
                lineWidth: "2",
                lineColor: "#f3545d",
                fillColor: "rgba(243, 84, 93, .14)",
            });

            $("#lineChart3").sparkline([105, 103, 123, 100, 95, 105, 115], {
                type: "line",
                height: "70",
                width: "100%",
                lineWidth: "2",
                lineColor: "#ffa534",
                fillColor: "rgba(255, 165, 52, .14)",
            });
        </script>
    </body>

</html>
