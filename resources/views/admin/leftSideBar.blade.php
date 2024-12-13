 <div class="left-side-menu">

     <div class="h-100" data-simplebar>

         <!-- User box -->
         <div class="user-box text-center">

             <img src="{{ asset('assets/images/users/user-1.jpg') }}" alt="user-img" title="Mat Helme"
                 class="rounded-circle img-thumbnail avatar-md">
             <div class="dropdown">
                 <a href="#" class="user-name dropdown-toggle h5 mt-2 mb-1 d-block" data-bs-toggle="dropdown"
                     aria-expanded="false">Nowak Helme</a>
                 <div class="dropdown-menu user-pro-dropdown">

                     <!-- item-->
                     <a href="javascript:void(0);" class="dropdown-item notify-item">
                         <i class="fe-user me-1"></i>
                         <span>My Account</span>
                     </a>

                     <!-- item-->
                     <a href="javascript:void(0);" class="dropdown-item notify-item">
                         <i class="fe-settings me-1"></i>
                         <span>Settings</span>
                     </a>

                     <!-- item-->
                     <a href="javascript:void(0);" class="dropdown-item notify-item">
                         <i class="fe-lock me-1"></i>
                         <span>Lock Screen</span>
                     </a>

                     <!-- item-->
                     <form action="{{ route('auth.logout') }}" method="POST">
                         @csrf
                         <button type="submit" class="dropdown-item notify-item">
                             <i class="fe-log-out me-1"></i>
                             <span>Logout</span>
                         </button>
                     </form>

                 </div>
             </div>

             <p class="text-muted left-user-info">Admin Head</p>

             <ul class="list-inline">
                 <li class="list-inline-item">
                     <a href="#" class="text-muted left-user-info">
                         <i class="mdi mdi-cog"></i>
                     </a>
                 </li>

                 <li class="list-inline-item">
                     <a href="#">
                         <i class="mdi mdi-power"></i>
                     </a>
                 </li>
             </ul>
         </div>

         <!--- Sidemenu -->
         <div id="sidebar-menu">

             <ul id="side-menu">

                 <li class="menu-title">Navigation</li>

                 <li>
                     <a href="{{ route('admin.dashboard') }}">
                         <i class="mdi mdi-view-dashboard-outline"></i>
                         <span class="badge bg-success rounded-pill float-end">9+</span>
                         <span> Dashboard </span>
                     </a>
                 </li>
                 <li class="menu-title mt-2">Orders</li>
                 <li>
                     <a href="{{ route('orders.index') }}">
                         <i class="mdi mdi-forum-outline"></i>
                         <span> Orders </span>
                     </a>
                 </li>

                 <li class="menu-title mt-2">Pages</li>

                 <li>
                     <a href="{{ route('book-types.index') }}">
                         <i class="mdi mdi-forum-outline"></i>
                         <span> Book type </span>
                     </a>
                 </li>

                 <li>
                     <a href="#email" data-bs-toggle="collapse">
                         <i class="mdi mdi-email-outline"></i>
                         <span> Book Design </span>
                         <span class="menu-arrow"></span>
                     </a>
                     <div class="collapse" id="email">
                         <ul class="nav-second-level">
                             <li>
                                 <a href="{{ route('book-designs.index') }}">
                                     Book Design
                                 </a>
                             </li>
                             <li>
                                 <a href="{{ route('categories.index') }}">
                                     Categories
                                 </a>
                             </li>
                             <li>
                                 <a href="{{ route('subcategories.index') }}">
                                     Sub Categories
                                 </a>
                             </li>
                         </ul>
                     </div>
                 </li>

                 <li>
                     <a href="{{ route('book-decorations.index') }}">
                         <i class="mdi mdi-forum-outline"></i>
                         <span> Book Decoration </span>
                     </a>
                 </li>

                 <li>
                     <a href="{{ route('governorates.index') }}">
                         <i class="mdi mdi-forum-outline"></i>
                         <span> Governorates </span>
                     </a>
                 </li>

                 <li>
                     <a href="{{ route('discount-codes.index') }}">
                         <i class="mdi mdi-forum-outline"></i>
                         <span> Discount Codes </span>
                     </a>
                 </li>

                 <li>
                     <a href="{{ route('svgs.index') }}">
                         <i class="mdi mdi-forum-outline"></i>
                         <span> SVG's </span>
                     </a>
                 </li>

                 <li>
                     <a href="{{ route('universities.index') }}">
                         <i class="mdi mdi-forum-outline"></i>
                         <span> Universities </span>
                     </a>
                 </li>

                 <li>
                     <a href="{{ route('diplomas.index') }}">
                         <i class="mdi mdi-forum-outline"></i>
                         <span> Colleges </span>
                     </a>
                 </li>

                 <li>
                     <a href="{{ route('phone-numbers.index') }}">
                         <i class="mdi mdi-forum-outline"></i>
                         <span> Phone numbers </span>
                     </a>
                 </li>


             </ul>
         </div>
         </li>
         </ul>

     </div>
     <!-- End Sidebar -->

     <div class="clearfix"></div>

 </div>
 <!-- Sidebar -left -->

 </div>
