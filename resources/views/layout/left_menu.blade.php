<<<<<<< HEAD
<!-- Main sidebar -->
<div class="sidebar sidebar-main sidebar-fixed">
    <div class="sidebar-content">

        <!-- User menu -->
        <div class="sidebar-user">
            <div class="category-content">
                <div class="media">
                    <a href="#" class="media-left"><img src="assets/images/placeholder.jpg" class="img-circle img-sm" alt=""></a>
                    <div class="media-body">
                        <span class="media-heading text-semibold">Victoria Baker</span>
                        <div class="text-size-mini text-muted">
                            <i class="icon-pin text-size-small"></i> &nbsp;Santa Ana, CA
                        </div>
                    </div>

                    <div class="media-right media-middle">
                        <ul class="icons-list">
                            <li>
                                <a href="#"><i class="icon-cog3"></i></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- /user menu -->


        <!-- Main navigation -->
        <div class="sidebar-category sidebar-category-visible">
            <div class="category-content no-padding">
                <ul class="navigation navigation-main navigation-accordion">

                    <!-- Main -->
                    <li class="navigation-header"><span>Main</span> <i class="icon-menu" title="Main pages"></i></li>
                    <li @yield('m_dashboard')><a href="dashboard"><i class="icon-home4"></i> <span>Dashboard</span></a></li>

                    <li>
                        <a href="#"><i class="icon-stack2"></i> <span>Administrator</span></a></a>
                        <ul>


                            <li>
                                <a href="#">Sample</a>
                                <ul>
                                    <li @yield('m_icon')><a href="icon">Form Details</a></li>
                                    <li @yield('m_alert')><a href="alert">Alert/Tooltip</a></li>
                                    <li @yield('m_costing')><a href="costing">Costing</a></li>

                                </ul>
                            </li>


                            <li>
                                <a href="#">Company</a>
                                <ul>

                                    <li @yield('m_add_location')><a href="add_location">Location</a></li>
                                    <!-- <li><a href="add_location">Main Sourse</a></li>
                                    <li><a href="layout_navbar_sidebar_fixed.html">Company Group</a></li>
                                    <li><a href="layout_navbar_sidebar_fixed.html">Main Cluster</a></li> -->
                                </ul>
                            </li>


                        </ul>
                    </li>


                </ul>
            </div>
        </div>
        <!-- /main navigation -->

    </div>
</div>
<!-- /main sidebar -->
=======
<!-- Main sidebar -->
<div class="sidebar sidebar-main sidebar-fixed">
    <div class="sidebar-content">

        <!-- User menu -->
        <div class="sidebar-user">
            <div class="category-content">
                <div class="media">
                    <a href="#" class="media-left"><img src="{{ URL::asset('assets/images/placeholder.jpg') }}" class="img-circle img-sm" alt=""></a>
                    <div class="media-body">
                        <span class="media-heading text-semibold">Victoria Baker</span>
                        <div class="text-size-mini text-muted">
                            <i class="icon-pin text-size-small"></i> &nbsp;Santa Ana, CA
                        </div>
                    </div>

                    <div class="media-right media-middle">
                        <ul class="icons-list">
                            <li>
                                <a href="#"><i class="icon-cog3"></i></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- /user menu -->


        <!-- Main navigation -->
        <div class="sidebar-category sidebar-category-visible">
            <div class="category-content no-padding">
                <ul class="navigation navigation-main navigation-accordion">

                    <!-- Main -->
                    <li class="navigation-header"><span>Main</span> <i class="icon-menu" title="Main pages"></i></li>
                    <li @yield('m_dashboard')><a href="dashboard"><i class="icon-home4"></i> <span>Dashboard</span></a></li>

                    <li>
                        <a href="#"><i class="icon-stack2"></i> <span>Administrator</span></a></a>
                        <ul>


                            <li>
                                <a href="#">Sample</a>
                                <ul>
                                    <li @yield('m_icon')><a href="icon">Form Details</a></li>
                                    <li @yield('m_alert')><a href="alert">Alert/Tooltip</a></li>
                                    <li @yield('m_costing')><a href="costing">Costing</a></li>

                                </ul>
                            </li>

                            <li>
                                <a href="#">Admin</a>
                                <ul>
                                    <li @yield('m_icon')><a href="register">User</a></li>
                                </ul>
                            </li>


                            <li>
                                <a href="#">Company</a>
                                <ul>

<<<<<<< HEAD
                                    <li @yield('m_add_location')><a href="{{url('/add_location')}}">Location</a></li>
<<<<<<< HEAD
=======
=======
                                    <li @yield('m_add_location')><a href="{{url('/location')}}">Location</a></li>
                                    <li @yield('m_add_dep')><a href="{{url('/department')}}">Department</a></li>
>>>>>>> origin/master
                                    <li @yield('m_add_stores')><a href="{{url('/add_stores')}}">Stores</a></li>
>>>>>>> origin/master
                                    <li @yield('m_add_country')><a href="{{url('/add_country')}}">Country</a></li>
                                    <li @yield('m_add_division')><a href="{{url('/add_division')}}">Division</a></li>
                                    <li @yield('m_add_season')><a href="{{url('/add_season')}}">Season</a></li>
                                    <li @yield('m_add_uom')><a href="{{url('/add_uom')}}">Unit of Measure</a></li>
                                    <li @yield('m_add_section')><a href="{{url('/add_section')}}">Section</a></li>
				    <li @yield('m_orgtype')><a href="origin-type-new">Orgin Type</a></li>
                                    <li @yield('m_currency')><a href="currency.new">Currency</a></li>
<<<<<<< HEAD
=======
                                    <li @yield('m_cancellation_category')><a href="add_category">Cancellation Category</a></li>
                                    <li @yield('m_cancellation_reason')><a href="add_reason">Cancellation Reason</a></li>
<<<<<<< HEAD
>>>>>>> origin/master
=======
                                    <li @yield('m_add_product_type')><a href="add_product_type">Product Type</a></li>
                                    <li @yield('m_add_sample_stage')><a href="add_sample_stage">Sample Stage</a></li>
                                    <li @yield('m_buyer_creation')><a href="create_customer">Customer Creation</a></li>
                                    <li @yield('m_add_custome_sizes')><a href="{{url('/customesizes/')}}">Custome Sizes</a></li>
>>>>>>> origin/master
                                    <!-- <li><a href="add_location">Main Sourse</a></li>
                                    <li><a href="layout_navbar_sidebar_fixed.html">Company Group</a></li>
                                    <li><a href="layout_navbar_sidebar_fixed.html">Main Cluster</a></li> -->
                                </ul>
                            </li>

<<<<<<< HEAD
                            
=======
                            <li>
                                <a href="#">User Management</a>
                                <ul>
                                    <li @yield('m_role')><a href="{{url('/admin/role')}}">Role</a></li>
                                    <li @yield('m_permission')><a href="{{url('/admin/permission')}}">Permission</a></li>
                                </ul>
                            </li>


>>>>>>> origin/master
                        </ul>
                    </li>


                    <li>
                        <a href="#"><i class="icon-coins"></i> <span>Finance</span></a></a>
                        <ul>


                            <li>
                                <a href="#">Accounting</a>
                                <ul>
                                  <li @yield('m_accrules')><a href="accounting-rules">Accounting Rules</a></li>
                                </ul>
                            </li>
                            <li><a href="{{url('/supplier')}}">supplier</a></li>
                            <li>
                                <a href="#">Style Item Creation</a>
                                <ul>
                                  <li @yield('m_item')><a href="item">Item Creation Wizard</a></li>   
                                  <li @yield('m_item')><a href="itemproperty">Item Property Assign</a></li>
                                  <li @yield('m_item')><a href="item">Sub Category</a></li>
                                </ul>
                                
                            </li>

<<<<<<< HEAD
                            <li>
                                <a href="#">Supplier</a>
                                <ul>
                                    <li><a href="{{url('/supplier')}}">supplier</a></li>

                                </ul>
                            </li>

=======
>>>>>>> origin/master



                        </ul>
                    </li>




                </ul>
            </div>
        </div>
        <!-- /main navigation -->

    </div>
</div>
<!-- /main sidebar -->
>>>>>>> my-temp
