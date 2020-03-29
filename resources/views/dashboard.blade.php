<<<<<<< HEAD

@extends('layout.main')
<<<<<<< HEAD
<head>
@section('title') Surface  @endsection
<link rel="shortcut icon" href="{{{asset('assets/images/ccc.jpg') }}}" />
</head>
=======

@section('title') Surface - Dashboard @endsection
@section('m_dashboard') class = 'active' @endsection
>>>>>>> aebef2397c356d441736bc665eff2f07cca7a003


@section('body')
<!-- Page header -->
				<div class="page-header page-header-default ">
					<!-- <div class="page-header-content">
						<div class="page-title">
							<h4><i class="icon-arrow-left52 position-left"></i> <span class="text-semibold">Home</span> - Dashboard</h4>
						</div>

						<div class="heading-elements">
							<div class="heading-btn-group">
								<a href="#" class="btn btn-link btn-float has-text"><i class="icon-bars-alt text-primary"></i><span>Statistics</span></a>
								<a href="#" class="btn btn-link btn-float has-text"><i class="icon-calculator text-primary"></i> <span>Invoices</span></a>
								<a href="#" class="btn btn-link btn-float has-text"><i class="icon-calendar5 text-primary"></i> <span>Schedule</span></a>
							</div>
						</div>
					</div> -->

					<div class="breadcrumb-line">
						<ul class="breadcrumb">
							<li><a href="index.html"><i class="icon-home2 position-left"></i> Home</a></li>
							<li class="active">@yield('title')</li>
						</ul>

						<ul class="breadcrumb-elements">
							<li><a href="#"><i class="icon-comment-discussion position-left"></i> Dashboard</a></li>
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">
									<i class="icon-gear position-left"></i>
									Settings
									<span class="caret"></span>
								</a>

								<ul class="dropdown-menu dropdown-menu-right">
									<li><a href="#"><i class="icon-user-lock"></i> Account security</a></li>
									<li><a href="#"><i class="icon-statistics"></i> Analytics</a></li>
									<li><a href="#"><i class="icon-accessibility"></i> Accessibility</a></li>
									<li class="divider"></li>
									<li><a href="#"><i class="icon-gear"></i> All settings</a></li>
								</ul>
							</li>
						</ul>
					</div>
				</div>
				<!-- /page header -->


<!-- Content area -->
				<div class="content">

					<!-- Latest posts -->
					<div class="panel panel-flat">
						<div class="panel-heading">
							<h6 class="panel-title">Latest posts</h6>
							<div class="heading-elements">
								<ul class="icons-list">
									<li><a data-action="collapse"></a></li>
									<li><a data-action="reload"></a></li>
									<li><a data-action="close"></a></li>
								</ul>
							</div>
						</div>

						<div class="panel-body">
							<div class="row">
								<div class="col-lg-6">
									<ul class="media-list content-group">
										<li class="media stack-media-on-mobile">
											<div class="media-left">
												<div class="thumb">
													<a href="#">
														<img src="assets/images/placeholder.jpg" class="img-responsive img-rounded media-preview" alt="">
														<span class="zoom-image"><i class="icon-play3"></i></span>
													</a>
												</div>
											</div>

											<div class="media-body">
												<h6 class="media-heading"><a href="#">Up unpacked friendly</a></h6>
												<ul class="list-inline list-inline-separate text-muted mb-5">
													<li><i class="icon-book-play position-left"></i> Video tutorials</li>
													<li>14 minutes ago</li>
												</ul>
												The him father parish looked has sooner. Attachment frequently gay terminated son...
											</div>
										</li>

										<li class="media stack-media-on-mobile">
											<div class="media-left">
												<div class="thumb">
													<a href="#">
														<img src="assets/images/placeholder.jpg" class="img-responsive img-rounded media-preview" alt="">
														<span class="zoom-image"><i class="icon-play3"></i></span>
													</a>
												</div>
											</div>

											<div class="media-body">
												<h6 class="media-heading"><a href="#">It allowance prevailed</a></h6>
												<ul class="list-inline list-inline-separate text-muted mb-5">
													<li><i class="icon-book-play position-left"></i> Video tutorials</li>
													<li>12 days ago</li>
												</ul>
												Alteration literature to or an sympathize mr imprudence. Of is ferrars subject as enjoyed...
											</div>
										</li>
									</ul>
								</div>

								<div class="col-lg-6">
									<ul class="media-list content-group">
										<li class="media stack-media-on-mobile">
											<div class="media-left">
												<div class="thumb">
													<a href="#">
														<img src="assets/images/placeholder.jpg" class="img-responsive img-rounded media-preview" alt="">
														<span class="zoom-image"><i class="icon-play3"></i></span>
													</a>
												</div>
											</div>

											<div class="media-body">
												<h6 class="media-heading"><a href="#">Case read they must</a></h6>
												<ul class="list-inline list-inline-separate text-muted mb-5">
													<li><i class="icon-book-play position-left"></i> Video tutorials</li>
													<li>20 hours ago</li>
												</ul>
												On it differed repeated wandered required in. Then girl neat why yet knew rose spot...
											</div>
										</li>

										<li class="media stack-media-on-mobile">
											<div class="media-left">
												<div class="thumb">
													<a href="#">
														<img src="assets/images/placeholder.jpg" class="img-responsive img-rounded media-preview" alt="">
														<span class="zoom-image"><i class="icon-play3"></i></span>
													</a>
												</div>
											</div>

											<div class="media-body">
												<h6 class="media-heading"><a href="#">Too carriage attended</a></h6>
												<ul class="list-inline list-inline-separate text-muted mb-5">
													<li><i class="icon-book-play position-left"></i> FAQ section</li>
													<li>2 days ago</li>
												</ul>
												Marianne or husbands if at stronger ye. Considered is as middletons uncommonly...
											</div>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
					<!-- /latest posts -->
=======

@extends('layout.main')

@section('title') Surface  @endsection


@section('title') Surface - Dashboard @endsection
@section('m_dashboard') class = 'active' @endsection



@section('body')
<!-- Page header -->
				<div class="page-header page-header-default ">
					<!-- <div class="page-header-content">
						<div class="page-title">
							<h4><i class="icon-arrow-left52 position-left"></i> <span class="text-semibold">Home</span> - Dashboard</h4>
						</div>

						<div class="heading-elements">
							<div class="heading-btn-group">
								<a href="#" class="btn btn-link btn-float has-text"><i class="icon-bars-alt text-primary"></i><span>Statistics</span></a>
								<a href="#" class="btn btn-link btn-float has-text"><i class="icon-calculator text-primary"></i> <span>Invoices</span></a>
								<a href="#" class="btn btn-link btn-float has-text"><i class="icon-calendar5 text-primary"></i> <span>Schedule</span></a>
							</div>
						</div>
					</div> -->

					<div class="breadcrumb-line">
						<ul class="breadcrumb">
							<li><a href="index.html"><i class="icon-home2 position-left"></i> Home</a></li>
							<li class="active">@yield('title')</li>
						</ul>

						<ul class="breadcrumb-elements">
							<li><a href="#"><i class="icon-comment-discussion position-left"></i> Dashboard</a></li>
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">
									<i class="icon-gear position-left"></i>
									Settings
									<span class="caret"></span>
								</a>

								<ul class="dropdown-menu dropdown-menu-right">
									<li><a href="#"><i class="icon-user-lock"></i> Account security</a></li>
									<li><a href="#"><i class="icon-statistics"></i> Analytics</a></li>
									<li><a href="#"><i class="icon-accessibility"></i> Accessibility</a></li>
									<li class="divider"></li>
									<li><a href="#"><i class="icon-gear"></i> All settings</a></li>
								</ul>
							</li>
						</ul>
					</div>
				</div>
				<!-- /page header -->


<!-- Content area -->
				<div class="content">

					<!-- Latest posts -->
					<div class="panel panel-flat">
						<div class="panel-heading">
							<h6 class="panel-title">Latest posts</h6>
							<div class="heading-elements">
								<ul class="icons-list">
									<li><a data-action="collapse"></a></li>
									<li><a data-action="reload"></a></li>
									<li><a data-action="close"></a></li>
								</ul>
							</div>
						</div>

						<div class="panel-body">
							<div class="row">
								<div class="col-lg-6">
									<ul class="media-list content-group">
										<li class="media stack-media-on-mobile">
											<div class="media-left">
												<div class="thumb">
													<a href="#">
														<img src="assets/images/placeholder.jpg" class="img-responsive img-rounded media-preview" alt="">
														<span class="zoom-image"><i class="icon-play3"></i></span>
													</a>
												</div>
											</div>

											<div class="media-body">
												<h6 class="media-heading"><a href="#">Up unpacked friendly</a></h6>
												<ul class="list-inline list-inline-separate text-muted mb-5">
													<li><i class="icon-book-play position-left"></i> Video tutorials</li>
													<li>14 minutes ago</li>
												</ul>
												The him father parish looked has sooner. Attachment frequently gay terminated son...
											</div>
										</li>

										<li class="media stack-media-on-mobile">
											<div class="media-left">
												<div class="thumb">
													<a href="#">
														<img src="assets/images/placeholder.jpg" class="img-responsive img-rounded media-preview" alt="">
														<span class="zoom-image"><i class="icon-play3"></i></span>
													</a>
												</div>
											</div>

											<div class="media-body">
												<h6 class="media-heading"><a href="#">It allowance prevailed</a></h6>
												<ul class="list-inline list-inline-separate text-muted mb-5">
													<li><i class="icon-book-play position-left"></i> Video tutorials</li>
													<li>12 days ago</li>
												</ul>
												Alteration literature to or an sympathize mr imprudence. Of is ferrars subject as enjoyed...
											</div>
										</li>
									</ul>
								</div>

								<div class="col-lg-6">
									<ul class="media-list content-group">
										<li class="media stack-media-on-mobile">
											<div class="media-left">
												<div class="thumb">
													<a href="#">
														<img src="assets/images/placeholder.jpg" class="img-responsive img-rounded media-preview" alt="">
														<span class="zoom-image"><i class="icon-play3"></i></span>
													</a>
												</div>
											</div>

											<div class="media-body">
												<h6 class="media-heading"><a href="#">Case read they must</a></h6>
												<ul class="list-inline list-inline-separate text-muted mb-5">
													<li><i class="icon-book-play position-left"></i> Video tutorials</li>
													<li>20 hours ago</li>
												</ul>
												On it differed repeated wandered required in. Then girl neat why yet knew rose spot...
											</div>
										</li>

										<li class="media stack-media-on-mobile">
											<div class="media-left">
												<div class="thumb">
													<a href="#">
														<img src="assets/images/placeholder.jpg" class="img-responsive img-rounded media-preview" alt="">
														<span class="zoom-image"><i class="icon-play3"></i></span>
													</a>
												</div>
											</div>

											<div class="media-body">
												<h6 class="media-heading"><a href="#">Too carriage attended</a></h6>
												<ul class="list-inline list-inline-separate text-muted mb-5">
													<li><i class="icon-book-play position-left"></i> FAQ section</li>
													<li>2 days ago</li>
												</ul>
												Marianne or husbands if at stronger ye. Considered is as middletons uncommonly...
											</div>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
					<!-- /latest posts -->
>>>>>>> my-temp
@endsection