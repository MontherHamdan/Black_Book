@extends('admin.layout')
@section('content')
    <div class="row">
        <!-- Pending Order Card -->
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="dropdown float-end">
                        <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="mdi mdi-dots-vertical"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="{{ route('orders.index') }}" class="dropdown-item">Orders Page</a>
                        </div>
                    </div>
                    <h4 class="header-title mt-0 mb-4">Pending Order</h4>
                    <div class="d-flex align-items-center">
                        <!-- Circular Icon Container -->
                        <div class="icon-circle bg-warning text-white me-3" style="width:70px; height:70px; border-radius:50%; display:flex; align-items:center; justify-content:center;">
                            <i class="mdi mdi-clock-outline" style="font-size:28px;"></i>
                        </div>
                        <!-- Order Count and Label -->
                        <div>
                            <h2 class="fw-normal pt-2 mb-1">{{ $pendingCount }}</h2>
                            <p class="text-muted mb-1">Pending Order</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Preparing Order Card -->
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="dropdown float-end">
                        <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="mdi mdi-dots-vertical"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="{{ route('orders.index') }}" class="dropdown-item">Orders Page</a>
                        </div>
                    </div>
                    <h4 class="header-title mt-0 mb-4">Preparing Order</h4>
                    <div class="d-flex align-items-center">
                        <div class="icon-circle bg-purple text-white me-3" style="width:70px; height:70px; border-radius:50%; display:flex; align-items:center; justify-content:center;">
                            <i class="mdi mdi-cog-outline" style="font-size:28px;"></i>
                        </div>
                        <div>
                            <h2 class="fw-normal pt-2 mb-1">{{ $preparingCount }}</h2>
                            <p class="text-muted mb-1">Preparing Order</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Out for Delivery Card -->
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="dropdown float-end">
                        <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="mdi mdi-dots-vertical"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="{{ route('orders.index') }}" class="dropdown-item">Orders Page</a>
                        </div>
                    </div>
                    <h4 class="header-title mt-0 mb-4">Out for Delivery</h4>
                    <div class="d-flex align-items-center">
                        <div class="icon-circle bg-pink text-white me-3" style="width:70px; height:70px; border-radius:50%; display:flex; align-items:center; justify-content:center;">
                            <i class="mdi mdi-truck-delivery-outline" style="font-size:28px;"></i>
                        </div>
                        <div>
                            <h2 class="fw-normal pt-2 mb-1">{{ $outForDeliveryCount }}</h2>
                            <p class="text-muted mb-1">Out for Delivery</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completed Order Card -->
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="dropdown float-end">
                        <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="mdi mdi-dots-vertical"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="{{ route('orders.index') }}" class="dropdown-item">Orders Page</a>
                        </div>
                    </div>
                    <h4 class="header-title mt-0 mb-4">Completed Order</h4>
                    <div class="d-flex align-items-center">
                        <div class="icon-circle bg-success text-white me-3" style="width:70px; height:70px; border-radius:50%; display:flex; align-items:center; justify-content:center;">
                            <i class="mdi mdi-check-circle-outline" style="font-size:28px;"></i>
                        </div>
                        <div>
                            <h2 class="fw-normal pt-2 mb-1">{{ $completedCount }}</h2>
                            <p class="text-muted mb-1">Completed Order</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Canceled Order Card -->
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="dropdown float-end">
                        <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="mdi mdi-dots-vertical"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="{{ route('orders.index') }}" class="dropdown-item">Orders Page</a>
                        </div>
                    </div>
                    <h4 class="header-title mt-0 mb-4">Canceled Order</h4>
                    <div class="d-flex align-items-center">
                        <div class="icon-circle bg-danger text-white me-3" style="width:70px; height:70px; border-radius:50%; display:flex; align-items:center; justify-content:center;">
                            <i class="mdi mdi-close-circle-outline" style="font-size:28px;"></i>
                        </div>
                        <div>
                            <h2 class="fw-normal pt-2 mb-1">{{ $canceledCount }}</h2>
                            <p class="text-muted mb-1">Canceled Order</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end row -->

    <div class="row">
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <div class="dropdown float-end">
                        <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="mdi mdi-dots-vertical"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item">Action</a>
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item">Another action</a>
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item">Something else</a>
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item">Separated link</a>
                        </div>
                    </div>

                    <h4 class="header-title mt-0">Daily Sales</h4>

                    <div class="widget-chart text-center">
                        <div id="morris-donut-example" dir="ltr" style="height: 245px;" class="morris-chart"></div>
                        <ul class="list-inline chart-detail-list mb-0">
                            <li class="list-inline-item">
                                <h5 style="color: #ff8acc;"><i class="fa fa-circle me-1"></i>Series A
                                </h5>
                            </li>
                            <li class="list-inline-item">
                                <h5 style="color: #5b69bc;"><i class="fa fa-circle me-1"></i>Series B
                                </h5>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div><!-- end col -->

        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <div class="dropdown float-end">
                        <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="mdi mdi-dots-vertical"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item">Action</a>
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item">Another action</a>
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item">Something else</a>
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item">Separated link</a>
                        </div>
                    </div>
                    <h4 class="header-title mt-0">Statistics</h4>
                    <div id="morris-bar-example" dir="ltr" style="height: 280px;" class="morris-chart">
                    </div>
                </div>
            </div>
        </div><!-- end col -->

        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <div class="dropdown float-end">
                        <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="mdi mdi-dots-vertical"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item">Action</a>
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item">Another action</a>
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item">Something else</a>
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item">Separated link</a>
                        </div>
                    </div>
                    <h4 class="header-title mt-0">Total Revenue</h4>
                    <div id="morris-line-example" dir="ltr" style="height: 280px;" class="morris-chart">
                    </div>
                </div>
            </div>
        </div><!-- end col -->
    </div>
    <!-- end row -->


    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body widget-user">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 avatar-lg me-3">
                            <img src="{{ asset('assets/images/users/user-3.jpg') }}" class="img-fluid rounded-circle"
                                alt="user">
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <h5 class="mt-0 mb-1">Chadengle</h5>
                            <p class="text-muted mb-2 font-13 text-truncate">coderthemes@gmail.com</p>
                            <small class="text-warning"><b>Admin</b></small>
                        </div>
                    </div>
                </div>
            </div>

        </div><!-- end col -->

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body widget-user">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 avatar-lg me-3">
                            <img src="{{ asset('assets/images/users/user-2.jpg') }}" class="img-fluid rounded-circle"
                                alt="user">
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <h5 class="mt-0 mb-1"> Michael Zenaty</h5>
                            <p class="text-muted mb-2 font-13 text-truncate">coderthemes@gmail.com</p>
                            <small class="text-pink"><b>Support Lead</b></small>
                        </div>
                    </div>
                </div>
            </div>

        </div><!-- end col -->

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body widget-user">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 avatar-lg me-3">
                            <img src="{{ asset('assets/images/users/user-1.jpg') }}" class="img-fluid rounded-circle"
                                alt="user">
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <h5 class="mt-0 mb-1">Stillnotdavid</h5>
                            <p class="text-muted mb-2 font-13 text-truncate">coderthemes@gmail.com</p>
                            <small class="text-success"><b>Designer</b></small>
                        </div>
                    </div>
                </div>
            </div>

        </div><!-- end col -->

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body widget-user">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 avatar-lg me-3">
                            <img src="{{ asset('assets/images/users/user-10.jpg') }}" class="img-fluid rounded-circle"
                                alt="user">
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <h5 class="mt-0 mb-1">Tomaslau</h5>
                            <p class="text-muted mb-2 font-13 text-truncate">coderthemes@gmail.com</p>
                            <small class="text-info"><b>Developer</b></small>
                        </div>
                    </div>
                </div>
            </div>

        </div><!-- end col -->

    </div>
    <!-- end row -->


    <div class="row">
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <div class="dropdown float-end">
                        <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="mdi mdi-dots-vertical"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item">Action</a>
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item">Another action</a>
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item">Something else</a>
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item">Separated link</a>
                        </div>
                    </div>

                    <h4 class="header-title mb-3">Inbox</h4>

                    <div class="inbox-widget">

                        <div class="inbox-item">
                            <a href="#">
                                <div class="inbox-item-img"><img src="{{ asset('assets/images/users/user-1.jpg') }}"
                                        class="rounded-circle" alt=""></div>
                                <h5 class="inbox-item-author mt-0 mb-1">Chadengle</h5>
                                <p class="inbox-item-text">Hey! there I'm available...</p>
                                <p class="inbox-item-date">13:40 PM</p>
                            </a>
                        </div>

                        <div class="inbox-item">
                            <a href="#">
                                <div class="inbox-item-img"><img src="{{ asset('assets/images/users/user-2.jpg') }}"
                                        class="rounded-circle" alt=""></div>
                                <h5 class="inbox-item-author mt-0 mb-1">Tomaslau</h5>
                                <p class="inbox-item-text">I've finished it! See you so...</p>
                                <p class="inbox-item-date">13:34 PM</p>
                            </a>
                        </div>

                        <div class="inbox-item">
                            <a href="#">
                                <div class="inbox-item-img"><img src="{{ asset('assets/images/users/user-3.jpg') }}"
                                        class="rounded-circle" alt=""></div>
                                <h5 class="inbox-item-author mt-0 mb-1">Stillnotdavid</h5>
                                <p class="inbox-item-text">This theme is awesome!</p>
                                <p class="inbox-item-date">13:17 PM</p>
                            </a>
                        </div>

                        <div class="inbox-item">
                            <a href="#">
                                <div class="inbox-item-img"><img src="{{ asset('assets/images/users/user-4.jpg') }}"
                                        class="rounded-circle" alt=""></div>
                                <h5 class="inbox-item-author mt-0 mb-1">Kurafire</h5>
                                <p class="inbox-item-text">Nice to meet you</p>
                                <p class="inbox-item-date">12:20 PM</p>
                            </a>
                        </div>

                        <div class="inbox-item">
                            <a href="#">
                                <div class="inbox-item-img"><img src="{{ asset('assets/images/users/user-5.jpg') }}"
                                        class="rounded-circle" alt=""></div>
                                <h5 class="inbox-item-author mt-0 mb-1">Shahedk</h5>
                                <p class="inbox-item-text">Hey! there I'm available...</p>
                                <p class="inbox-item-date">10:15 AM</p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div><!-- end col -->

        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <div class="dropdown float-end">
                        <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="mdi mdi-dots-vertical"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item">Action</a>
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item">Another action</a>
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item">Something else</a>
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item">Separated link</a>
                        </div>
                    </div>

                    <h4 class="header-title mt-0 mb-3">Latest Projects</h4>

                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Project Name</th>
                                    <th>Start Date</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th>Assign</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Adminto Admin v1</td>
                                    <td>01/01/2017</td>
                                    <td>26/04/2017</td>
                                    <td><span class="badge bg-danger">Released</span></td>
                                    <td>Coderthemes</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Adminto Frontend v1</td>
                                    <td>01/01/2017</td>
                                    <td>26/04/2017</td>
                                    <td><span class="badge bg-success">Released</span></td>
                                    <td>Adminto admin</td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>Adminto Admin v1.1</td>
                                    <td>01/05/2017</td>
                                    <td>10/05/2017</td>
                                    <td><span class="badge bg-pink">Pending</span></td>
                                    <td>Coderthemes</td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>Adminto Frontend v1.1</td>
                                    <td>01/01/2017</td>
                                    <td>31/05/2017</td>
                                    <td><span class="badge bg-purple">Work in Progress</span>
                                    </td>
                                    <td>Adminto admin</td>
                                </tr>
                                <tr>
                                    <td>5</td>
                                    <td>Adminto Admin v1.3</td>
                                    <td>01/01/2017</td>
                                    <td>31/05/2017</td>
                                    <td><span class="badge bg-warning">Coming soon</span></td>
                                    <td>Coderthemes</td>
                                </tr>

                                <tr>
                                    <td>6</td>
                                    <td>Adminto Admin v1.3</td>
                                    <td>01/01/2017</td>
                                    <td>31/05/2017</td>
                                    <td><span class="badge bg-primary">Coming soon</span></td>
                                    <td>Adminto admin</td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div><!-- end col -->

    </div>
    <!-- end row -->
@endsection
