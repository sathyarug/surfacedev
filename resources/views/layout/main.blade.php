<<<<<<< HEAD
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title')</title>
        <link rel="icon" href="assets\images\icon.jpg">
        <!-- Global stylesheets -->
        <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
        <link href="assets/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
        <link href="assets/css/bootstrap.css" rel="stylesheet" type="text/css">
        <link href="assets/css/core.css" rel="stylesheet" type="text/css">
        <link href="assets/css/components.css" rel="stylesheet" type="text/css">
        <link href="assets/css/colors.css" rel="stylesheet" type="text/css">
        <!-- /global stylesheets -->
        @yield('load_css')


    </head>

    <!-- <body  class="navbar-top"> -->
    <body  class="sidebar-xs navbar-top">
        

        @include('layout.top_menu')

        <!-- Page container -->
        <div class="page-container">

            <!-- Page content -->
            <div class="page-content">

                @include('layout.left_menu')


                <!-- Main content -->
                @yield('body')
                <!-- /main content -->

                @include('layout.footer')

            </div>
            <!-- /page content -->

        </div>
        <!-- /page container -->



        <!-- Core JS files -->
        <script type="text/javascript" src="assets/js/plugins/loaders/pace.min.js"></script>
        <script type="text/javascript" src="assets/js/core/libraries/jquery.min.js"></script>
        <script type="text/javascript" src="assets/js/core/libraries/bootstrap.min.js"></script>
        <script type="text/javascript" src="assets/js/plugins/loaders/blockui.min.js"></script>
        <!-- /core JS files -->

        <script type="text/javascript" src="assets/js/core/app.js"></script>
        <script type="text/javascript" src="assets/js/plugins/ui/nicescroll.min.js"></script>
        <script type="text/javascript" src="assets/js/pages/layout_fixed_custom.js"></script>
        <!-- /theme JS files -->       
        @yield('javascripy')

    </body>
</html>
=======
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title')</title>
        <link rel="icon" href="assets\images\icon.jpg">
        <!-- Global stylesheets -->
        <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
        <link href="{{ URL::asset('assets/css/icons/icomoon/styles.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ URL::asset('assets/css/bootstrap.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ URL::asset('assets/css/core.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ URL::asset('assets/css/components.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ URL::asset('assets/css/colors.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ URL::asset('assets/css/custom.css') }}" rel="stylesheet" type="text/css">
        <!-- /global stylesheets -->
        @yield('load_css')


    </head>

    <!-- <body  class="navbar-top"> -->
    <body  class="sidebar-xs navbar-top">
        

        @include('layout.top_menu')

        <!-- Page container -->
        <div class="page-container">

            <!-- Page content -->
            <div class="page-content">

                @include('layout.left_menu')


                <!-- Main content -->
                @yield('body')
                <!-- /main content -->

                @include('layout.footer')

            </div>
            <!-- /page content -->

        </div>
        <!-- /page container -->



        <!-- Core JS files -->
        <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/loaders/pace.min.js') }}"></script>
        <script type="text/javascript" src="{{ URL::asset('assets/js/core/libraries/jquery.min.js') }}"></script>
        <script type="text/javascript" src="{{ URL::asset('assets/js/core/libraries/bootstrap.min.js') }}"></script>
        <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/loaders/blockui.min.js') }}"></script>
        <!-- /core JS files -->

        <script type="text/javascript" src="{{ URL::asset('assets/js/core/app.js') }}"></script>
        <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/ui/nicescroll.min.js') }}"></script>
        <script type="text/javascript" src="{{ URL::asset('assets/js/pages/layout_fixed_custom.js') }}"></script>
        <!-- /theme JS files -->       
        @yield('javascripy')
        {{--test--}}

    </body>
</html>
>>>>>>> my-temp
