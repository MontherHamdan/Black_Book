<header class="vz_main_header flex-grow-1 top_nav">
    <div class="container-fluid d-flex flex-row h-100 align-items-center">
        <!--=========================*
                          Logo
            *===========================-->
        <div class="text-center rt_nav_wrapper d-flex align-items-center">
            <a class="nav_logo rt_logo" href="index.html"><img src="imagesTemp/logo-dark.svg" alt="logo"/></a>
            <a class="nav_logo nav_logo_mob" href="index.html"><img src="images/mobile-logo.svg" alt="logo"/></a>

        </div>
        <!--=========================*
                   End Logo
       *===========================-->
        <div class="nav_wrapper_main d-flex align-items-center justify-content-between flex-grow-1">
            <a class="vz_mobile_menu_icon mr-3 d-md-flex d_none_sm" id="vz_mobileCollapseIcon" href="javascript:void(0)"><span></span></a>
            <form class="search-field d-md-flex d_none_sm" action="#">
                <div class="form-group mb-0">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="feather ft-search"></i></span>
                        </div>
                        <input type="text" class="form-control" placeholder="search here...">
                    </div>
                </div>
            </form>
            <ul class="navbar-nav navbar-nav-right mr-0 ml-auto">
                <li class="nav-item dropdown language_dropdown d_none_sm">
                    <div class="nav-link">
                        <span class="dropdown-toggle btn btn-sm" id="languageDropdown" data-toggle="dropdown">English
                            <i class="flag-icon flag-icon-us ml-2"></i>
                        </span>
                        <div class="dropdown-menu navbar-dropdown">
                            <a class="dropdown-item font-weight-medium">
                                Italy
                                <i class="flag-icon flag-icon-it ml-2"></i>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item font-weight-medium">
                                Russia
                                <i class="flag-icon flag-icon-ru ml-2"></i>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item font-weight-medium">
                                France
                                <i class="flag-icon flag-icon-fr ml-2"></i>
                            </a>
                        </div>
                    </div>
                </li>
                <!--==================================*
                         Notification Section
                *====================================-->
                <li class="nav-item dropdown">
                    <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#"
                       data-toggle="dropdown">
                        <i class="feather ft-bell"></i>
                        <span class="count"></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right navbar-dropdown rt-notification-list"
                         aria-labelledby="notificationDropdown">
                        <div class="dropdown-item">
                            <p class="mb-0 font-weight-normal float-left">You have 3 new notifications</p>
                            <a href="#" class="view_btn">view all</a>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item rt-notification-item">
                            <div class="rt-notification-thumbnail">
                                <div class="rt-notification-icon">
                                    <i class="ti-map-alt text-info mx-0"></i>
                                </div>
                            </div>
                            <div class="rt-notification-item-content">
                                <h6 class="rt-notification-subject text-info font-weight-normal mb-1">You added your Location</h6>
                                <p class="font-weight-light small-text mb-0">
                                    Just now
                                </p>
                            </div>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item rt-notification-item">
                            <div class="rt-notification-thumbnail">
                                <div class="rt-notification-icon">
                                    <i class="ti-bolt-alt text-primary mx-0"></i>
                                </div>
                            </div>
                            <div class="rt-notification-item-content">
                                <h6 class="rt-notification-subject font-weight-normal text-primary mb-1">Your Subscription Expired</h6>
                                <p class="font-weight-light small-text mb-0">
                                    30 Seconds ago
                                </p>
                            </div>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item rt-notification-item">
                            <div class="rt-notification-thumbnail">
                                <div class="rt-notification-icon">
                                    <i class="ti-heart text-secondary mx-0"></i>
                                </div>
                            </div>
                            <div class="rt-notification-item-content">
                                <h6 class="rt-notification-subject text-secondary font-weight-normal mb-1">Some special like you</h6>
                                <p class="font-weight-light small-text mb-0">
                                    Just Now
                                </p>
                            </div>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item rt-notification-item">
                            <div class="rt-notification-thumbnail">
                                <div class="rt-notification-icon">
                                    <i class="ti-comments text-danger mx-0"></i>
                                </div>
                            </div>
                            <div class="rt-notification-item-content">
                                <h6 class="rt-notification-subject text-danger font-weight-normal mb-1">New Commetns On Post</h6>
                                <p class="font-weight-light small-text mb-0">
                                    Just Now
                                </p>
                            </div>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item rt-notification-item">
                            <div class="rt-notification-thumbnail">
                                <div class="rt-notification-icon">
                                    <i class="ti-settings text-success mx-0"></i>
                                </div>
                            </div>
                            <div class="rt-notification-item-content">
                                <h6 class="rt-notification-subject text-success font-weight-normal mb-1">You changed your Settings</h6>
                                <p class="font-weight-light small-text mb-0">
                                    Just Now
                                </p>
                            </div>
                        </a>
                    </div>
                </li>
                <!--==================================*
                         End Notification Section
                *====================================-->
                <!--==================================*
                         Message Section
                *====================================-->
                <li class="nav-item dropdown">
                    <a class="nav-link count-indicator dropdown-toggle" id="messageDropdown" href="#"
                       data-toggle="dropdown" aria-expanded="false">
                        <i class="feather ft-mail mx-0"></i>
                        <span class="count"></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right navbar-dropdown rt-notification-list"
                         aria-labelledby="messageDropdown">
                        <div class="dropdown-item">
                            <p class="mb-0 font-weight-normal float-left">You have 3 New Messages</p>
                            <a href="#" class="view_btn">view all</a>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item rt-notification-item">
                            <div class="rt-notification-thumbnail">
                                <img src="imagesTemp/author/author-img1.jpg" class="profile-pic" alt="image">
                            </div>
                            <div class="rt-notification-item-content flex-grow">
                                <h6 class="rt-notification-subject ellipsis font-weight-medium">Jhon Doe
                                    <span class="float-right font-weight-light small-text">3:15 PM</span>
                                </h6>
                                <p class="font-weight-light small-text">
                                    Hello are you there?
                                </p>
                            </div>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item rt-notification-item">
                            <div class="rt-notification-thumbnail">
                                <img src="imagesTemp/author/author-img2.jpg" class="profile-pic" alt="image">
                            </div>
                            <div class="rt-notification-item-content flex-grow">
                                <h6 class="rt-notification-subject ellipsis font-weight-medium">David Boos
                                    <span class="float-right font-weight-light small-text">1:25 PM</span>
                                </h6>
                                <p class="font-weight-light small-text">
                                    Waiting for your Response...
                                </p>
                            </div>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item rt-notification-item">
                            <div class="rt-notification-thumbnail">
                                <img src="imagesTemp/user.jpg" class="profile-pic" alt="image">
                            </div>
                            <div class="rt-notification-item-content flex-grow">
                                <h6 class="rt-notification-subject ellipsis font-weight-medium"> Jason Roy
                                    <span class="float-right font-weight-light small-text">5:21 PM</span>
                                </h6>
                                <p class="font-weight-light small-text">
                                    Hi there, Hope you are well
                                </p>
                            </div>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item rt-notification-item">
                            <div class="rt-notification-thumbnail">
                                <img src="imagesTemp/author/author-img3.jpg" class="profile-pic" alt="image">
                            </div>
                            <div class="rt-notification-item-content flex-grow">
                                <h6 class="rt-notification-subject ellipsis font-weight-medium"> Malika Roy
                                    <span class="float-right font-weight-light small-text">2:30 PM</span>
                                </h6>
                                <p class="font-weight-light small-text">
                                    Your Product Dispatched ...
                                </p>
                            </div>
                        </a>
                    </div>
                </li>
                <!--==================================*
                         End Message Section
                *====================================-->
                <!--==================================*
                         Profile Menu
                *====================================-->
                <li class="nav-item nav-profile dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
                        <span class="profile_sec">

                            <span class="profile_name">
                                <span class="hi_name">Hello,</span>
                                Brian C. <i class="feather ft-chevron-down"></i>
                            </span>
                            <img src="imagesTemp/user.jpg" alt="profile"/>
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right navbar-dropdown pt-2"
                         aria-labelledby="profileDropdown">
                        <a class="dropdown-item">
                            <i class="ti-user text-dark mr-3"></i> Profile
                        </a>
                        <a class="dropdown-item">
                            <i class="ti-settings text-dark mr-3"></i> Account Settings
                        </a>
                        <a class="dropdown-item">
                            <i class="feather ft-lock text-dark mr-3"></i>
                            Update Password
                        </a>
                    </div>
                </li>
                <!--==================================*
                         End Profile Menu
                *====================================-->
                <li class="nav-item d_none_sm">
                    <a class="nav-link logout_link" href="{{ route('auth.login') }}">
                        Logout <i class="feather ft-power"></i>
                    </a>
                </li>
            </ul>
            <!--=========================*
                       Mobile Menu
           *===========================-->
            <span class="d-lg-none">
                <a class="vz_mobile_menu_icon ml-3" id="vz_mobileCollapseIconMobile" href="javascript:"><span></span></a>
            </span>
            <!--=========================*
                   End Mobile Menu
           *===========================-->
        </div>
    </div>
</header>