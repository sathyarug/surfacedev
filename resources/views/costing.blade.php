
@extends('layout.main')

@section('title') Costing @endsection
@section('m_costing') class = 'active' @endsection

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
							<li><a href="#"><i class="icon-comment-discussion position-left"></i> Support</a></li>
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

					
					<div class="col-md-12">

						<!-- Basic layout-->
						<form action="#">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h5 class="panel-title">COSTING</h5>
									<div class="heading-elements">
										<ul class="icons-list">
											<li><a data-action="collapse"></a></li>
											<li><a data-action="reload"></a></li>
											<li><a data-action="close"></a></li>
										</ul>
									</div>
								</div>

								<div class="panel-body">

									<div class="row" >


										<div class="col-md-9">

											<div class="col-md-12" style="background:#F5F5F5;padding: 10px;border-radius:10px; ">

											<div class="col-md-4">
												<label>Style No:</label>
												<select class="select-search input-xxs" >

													<option value="">Select One ...</option>

												</select>
											</div>

										


											<div class=" col-md-2">
												<label>SC No:</label>
												<select class="select-search input-xxs" >

													<option value="">Select One ...</option>

												</select>
											</div>


											<div class="col-md-3">
												<label>Order No:</label>
												<select class="select-search input-xxs" >

													<option value="">Select One ...</option>

												</select>
											</div>

											<div class=" col-md-3">
												<label>Factory *:</label>
												<select class="select-search input-xxs" >

													<option value="">Select One ...</option>

												</select>
											</div>

											<div class="col-md-2">
												<label>Order No *:</label>
												<input type="text" class="form-control input-xxs" disabled="disabled">
											</div>

											<div class="col-md-2">
												<label></label>
												<select class="select-search input-xxs" >

													<option value="">Bulk</option>

												</select>

											</div>

											<div class=" col-md-2">
												<label>Style No *:</label>
												<input type="text" class="form-control input-xxs" disabled="disabled">
											</div>


											<div class="col-md-3">
												<label></label>
												<input type="text" class="form-control input-xxs" disabled="disabled">
											</div>

											<div class=" col-md-3">
												<label>Style Name *:</label>
												<input type="text" class="form-control input-xxs" >
											</div>

											<div class="col-md-2">
												<label>Qty *:</label>
												<input type="text" class="form-control input-xxs">
											</div>

											<div class="col-md-2">
												<label>Ex:Qty %:</label>
												<input type="text" class="form-control input-xxs">
											</div>

											<div class="col-md-2">
												<label>SMV *:</label>
												<input type="text" class="form-control input-xxs">
											</div>

											<div class="col-md-2">
												<label>Pack SMV:</label>
												<input type="text" class="form-control input-xxs">
											</div>

											<div class="col-md-4">
												<label>Buyer *</label>
												<select class="select-search input-xxs" >

													<option value="">Bulk</option>

												</select>
											</div>

											<div class="col-md-3">
												<label>Season</label>
												<select class="select-search input-xxs" >

													<option value="">Bulk</option>

												</select>
											</div>
											<div class="col-md-3">
												<label>Buying Office</label>
												<select class="select-search input-xxs" >

													<option value="">Bulk</option>

												</select>
											</div>
											<div class="col-md-3">
												<label>Schedule Method</label>
												<select class="select-search input-xxs" >

													<option value="">Bulk</option>

												</select>
											</div>
											<div class="col-md-3">
												<label>Division*</label>
												<select class="select-search input-xxs" >

													<option value="">Bulk</option>

												</select>
											</div>
											<div class="col-md-3">
												<label>Division *</label>
												<select class="select-search input-xxs" >

													<option value="">Bulk</option>

												</select>
											</div>

											<div class="col-md-2">
												<label>Eff. Level:</label>
												<input type="text" class="form-control input-xxs">
											</div>
											<div class="col-md-2">
												<label>Cost Per Min:</label>
												<input type="text" class="form-control input-xxs">
											</div>
											
											<div class="col-md-2">
												<label>ESC:</label>
												<input type="text" class="form-control input-xxs">
											</div>
											<div class="col-md-3">
												<label>Total Fabric ConPc:</label>
												<input type="text" class="form-control input-xxs">
											</div>

											<div class="col-md-3">
												<label>Ref. No.</label>
												<input type="text" class="form-control input-xxs">
											</div>

											<div class="col-md-3">
												<label>Category *</label>
												<select class="select-search input-xxs" >

													<option value="">Bulk</option>

												</select>
											</div>

											<div class="col-md-3">
												<label>Style Level *</label>
												<select class="select-search input-xxs" >

													<option value="">Bulk</option>

												</select>
											</div>


											<div class="col-md-3">
												<label>Sub Contract Qty</label>
												<input type="text" class="form-control input-xxs">
											</div>

</div>


											<div class="col-md-12">
												<legend class="text-bold">View Data</legend>

												<table class="table datatable-basic">
													<thead>
														<tr>
															<th>First Name</th>
															<th>Last Name</th>
															<th>Job Title</th>
															<th>DOB</th>
															<th>Status</th>
															<th class="text-center">Actions</th>
														</tr>
													</thead>
													<tbody>
														<tr>
															<td>Marth</td>
															<td><a href="#">Enright</a></td>
															<td>Traffic Court Referee</td>
															<td>22 Jun 1972</td>
															<td><span class="label label-success">Active</span></td>
															<td class="text-center">
																<ul class="icons-list">
																	<li class="dropdown">
																		<a href="#" class="dropdown-toggle" data-toggle="dropdown">
																			<i class="icon-menu9"></i>
																		</a>

																		<ul class="dropdown-menu dropdown-menu-right">
																			<li><a href="#"><i class="icon-file-pdf"></i> Export to .pdf</a></li>
																			<li><a href="#"><i class="icon-file-excel"></i> Export to .csv</a></li>
																			<li><a href="#"><i class="icon-file-word"></i> Export to .doc</a></li>
																		</ul>
																	</li>
																</ul>
															</td>
														</tr>
														<tr>
															<td>Jackelyn</td>
															<td>Weible</td>
															<td><a href="#">Airline Transport Pilot</a></td>
															<td>3 Oct 1981</td>
															<td><span class="label label-default">Inactive</span></td>
															<td class="text-center">
																<ul class="icons-list">
																	<li class="dropdown">
																		<a href="#" class="dropdown-toggle" data-toggle="dropdown">
																			<i class="icon-menu9"></i>
																		</a>

																		<ul class="dropdown-menu dropdown-menu-right">
																			<li><a href="#"><i class="icon-file-pdf"></i> Export to .pdf</a></li>
																			<li><a href="#"><i class="icon-file-excel"></i> Export to .csv</a></li>
																			<li><a href="#"><i class="icon-file-word"></i> Export to .doc</a></li>
																		</ul>
																	</li>
																</ul>
															</td>
														</tr>
														<tr>
															<td>Aura</td>
															<td>Hard</td>
															<td>Business Services Sales Representative</td>
															<td>19 Apr 1969</td>
															<td><span class="label label-danger">Suspended</span></td>
															<td class="text-center">
																<ul class="icons-list">
																	<li class="dropdown">
																		<a href="#" class="dropdown-toggle" data-toggle="dropdown">
																			<i class="icon-menu9"></i>
																		</a>

																		<ul class="dropdown-menu dropdown-menu-right">
																			<li><a href="#"><i class="icon-file-pdf"></i> Export to .pdf</a></li>
																			<li><a href="#"><i class="icon-file-excel"></i> Export to .csv</a></li>
																			<li><a href="#"><i class="icon-file-word"></i> Export to .doc</a></li>
																		</ul>
																	</li>
																</ul>
															</td>
														</tr>

													</tbody></table>

												</div>



											</div>


											<div class="form-group col-md-3" >

												<div class="col-md-12">
													<img src="assets\images\placeholder.jpg" width="120">
													<legend class="text-bold">Costing</legend>
												</div>

												<div class="col-md-5">
													<label>Finance %</label>
													<input type="text" class="form-control input-xxs ">

												</div>

												<div class="col-md-7">
													<label></label>
													<input type="text" class="form-control input-xxs" disabled="disabled">
												</div>

												<div class="col-md-12">
													<label>Material Cost</label>
													<input type="text" class="form-control input-xxs" disabled="disabled">
												</div>

												<div class="col-md-12">
													<label>SMV Rate</label>
													<input type="text" class="form-control input-xxs">
												</div>
												<div class="col-md-12">
													<label>CM Value</label>
													<input type="text" class="form-control input-xxs">
												</div>
												<div class="col-md-12">
													<label>Target FOB *</label>
													<input type="text" class="form-control input-xxs">
												</div>
												<div class="col-md-12">
													<label>Profit</label>
													<input type="text" class="form-control input-xxs" disabled="disabled">
												</div>
												<div class="col-md-12">
													<label>UP Charge</label>
													<input type="text" class="form-control input-xxs">
												</div>

												<div class="col-md-12">
													<label>Reason</label>
													<textarea class="form-control"></textarea>
												</div>







											</div>


































										</div>	



									</div>
								</div>
							</form>
							<!-- /basic layout -->

						</div>






					</div>
					<!-- /latest posts -->




					@endsection




					@section('javascripy') 


					<!-- Select with search -->
					<script type="text/javascript" src="assets/js/core/libraries/jquery_ui/interactions.min.js"></script>
					<script type="text/javascript" src="assets/js/plugins/forms/selects/select2.min.js"></script>
					<script type="text/javascript" src="assets/js/pages/form_select2.js"></script>

					<!-- Theme JS files -->
	<script type="text/javascript" src="assets/js/plugins/tables/datatables/datatables.min.js"></script>

	<script type="text/javascript" src="assets/js/pages/datatables_basic.js"></script>
	<!-- /theme JS files -->
					<!-- /Select with search -->

					@endsection
