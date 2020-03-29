{{--<!doctype html>--}}
{{--<html>--}}
{{--<head>--}}
	{{--<title>Look at me Login</title>--}}
{{--</head>--}}
{{--<body>--}}

{{--{{ Form::open(array('url' => 'login')) }}--}}
{{--<h1>Login</h1>--}}

{{--<!-- if there are login errors, show them here -->--}}
{{--@if (Session::get('loginError'))--}}
	{{--<div class="alert alert-danger">{{ Session::get('loginError') }}</div>--}}
{{--@endif--}}

{{--<p>--}}
	{{--{{ $errors->first('email') }}--}}
	{{--{{ $errors->first('password') }}--}}
{{--</p>--}}

{{--<p>--}}
	{{--{{ Form::label('email', 'Email Address') }}--}}
	{{--{{ Form::text('email', Input::old('email'), array('placeholder' => 'awesome@awesome.com')) }}--}}
{{--</p>--}}

{{--<p>--}}
	{{--{{ Form::label('password', 'Password') }}--}}
	{{--{{ Form::password('password') }}--}}
{{--</p>--}}
{{--<input type="checkbox" name="remember" class="styled" >--}}
{{--<p>{{ Form::submit('Submit!') }}</p>--}}
{{--{{ Form::close() }}--}}

{{--</body>--}}
{{--</html>--}}

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Limitless - Responsive Web Application Kit by Eugene Kopyov</title>

	<!-- Global stylesheets -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
	<link href="assets/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
	<link href="assets/css/bootstrap.css" rel="stylesheet" type="text/css">
	<link href="assets/css/core.css" rel="stylesheet" type="text/css">
	<link href="assets/css/components.css" rel="stylesheet" type="text/css">
	<link href="assets/css/colors.css" rel="stylesheet" type="text/css">
	<!-- /global stylesheets -->

	<!-- Core JS files -->
	<script type="text/javascript" src="assets/js/plugins/loaders/pace.min.js"></script>
	<script type="text/javascript" src="assets/js/core/libraries/jquery.min.js"></script>
	<script type="text/javascript" src="assets/js/core/libraries/bootstrap.min.js"></script>
	<script type="text/javascript" src="assets/js/plugins/loaders/blockui.min.js"></script>
	<!-- /core JS files -->

	<!-- Theme JS files -->
	<script type="text/javascript" src="assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script type="text/javascript" src="assets/js/core/app.js"></script>
	<script type="text/javascript" src="assets/js/pages/login.js"></script>
	<!-- /theme JS files -->

</head>

<body class="login-container bg-slate-800">

<!-- Page container -->
<div class="page-container">

	<!-- Page content -->
	<div class="page-content">

		<!-- Main content -->
		<div class="content-wrapper">

			<!-- Content area -->
			<div class="content">

				<!-- Advanced login -->
				{{ Form::open(array('url' => 'login','id'=> 'login')) }}
				<div class="panel panel-body login-form">
					<div class="text-center">
						<div class="icon-object " style="color:#FFF;margin-bottom:-20px">
							<!--<i class="icon-people"></i>-->
							<img style="width:110%;margin-left:-10px" src="{{ URL::asset('assets/images/logod004.png') }}">
						</div>
						<h5 class="content-group-lg">Login to your account <small class="display-block">Enter your credentials</small></h5>
					</div>
					<!-- if there are login errors, show them here -->
					@if (Session::get('loginError'))
						<div class="alert alert-danger">{{ Session::get('loginError') }}</div>
					@endif

					<div class="form-group has-feedback has-feedback-left">
						{{ Form::text('user-name',Cookie::get('user-name'),array('class' => 'form-control','id'=> 'user-name')) }}
						<div class="form-control-feedback">
							<i class="icon-user text-muted"></i>
						</div>
					</div>

					<div class="form-group has-feedback has-feedback-left">
						{{--{{ Form::password('password',array('class' => 'form-control','id'=> 'password')) }}--}}
						<input class="form-control" name="password" type="password" value="{{Cookie::get('password')}}">
						<div class="form-control-feedback">
							<i class="icon-lock2 text-muted"></i>
						</div>
					</div>

					<div class="form-group login-options">
						<div class="row">
							<div class="col-sm-6">
								<label class="checkbox-inline">
									<input type="checkbox" @if (Cookie::get('user-name')) checked="checked" @endif name="remember" class="styled" >
									Remember
								</label>
							</div>

							<div class="col-sm-6 text-right">
								<a href="recover">Forgot password?</a>
							</div>
						</div>
					</div>

					<div class="form-group">
					<!--	{{ Form::submit('Login',array('class' => 'btn bg-blue btn-block')) }}
						{{--<button type="submit" class="btn bg-blue btn-block">Login <i class="icon-circle-right2 position-right"></i></button>--}}
					-->
					<a href="http://localhost:4200" class="btn bg-blue btn-block" >Submit</a>
					</div>


				{{ Form::close() }}
				<!-- /advanced login -->

				</div>
				<!-- /content area -->

			</div>
			<!-- /main content -->

		</div>
		<!-- /page content -->

	</div>
	<!-- /page container -->
</div>
<script>

    //    $(document).ready(function() {
    //
    ////        var remember = $.cookie('remember');
    ////        if (remember == 'true')
    ////        {
    ////            var email = $.cookie('username');
    ////            var password = $.cookie('password');
    ////            // autofill the fields
    ////            $('#user-name').val(email);
    ////            $('#password').val(password);
    ////        }
    //
    //
    //        $("#login").submit(function() {
    //
    //            if ($('#remember').is(':checked')) {
    //                var username = $('#user-name').val();
    //                var password = $('#password').val();
    //                alert(username );
    //                // set cookies to expire in 14 days
    //                $.cookie('username', username, { expires: 14 });
    //                $.cookie('password', password, { expires: 14 });
    //                $.cookie('remember', true, { expires: 14 });
    //            }
    //            else
    //            {alert('sss');
    //                // reset cookies
    //                $.cookie('username', null);
    //                $.cookie('password', null);
    //                $.cookie('remember', null);
    //            }
    //        });
    //    });
</script>
</body>
</html>
