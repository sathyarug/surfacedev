@extends('reports.common.template')

@section('title')
    Costing Variance Details
@stop

@section('content')   

<div class="container">
	<div class="row">
		<div class="col-md-8 col-sm-offset-2 form-holder">
					
			<div class="col-md-12">
				<div class="logo">
					<img src="{{ URL::asset('assets\images\logo.jpg') }}"/>
				</div>
				<div class="address"> 
					@foreach ($company as $row)
					<div class="company-name">{{ $row->company_name }}</div>
					<div class="company-address">{{ $row->loc_name }}</div>
					<div class="company-address">{{ $row->loc_address_1 }},{{ $row->loc_address_2 }},{{ $row->country_description }}</div>
					<div class="company-contact"><strong>Tel :</strong> {{ $row->loc_phone }} / <strong>Fax :</strong> {{ $row->loc_fax }}</div>
					<div class="company-contact"><strong>Email :</strong> {{ $row->loc_email }} / <strong>Web :</strong> {{ $row->loc_web }}</div>
					@endforeach
				</div>
			</div>

			@foreach ($headers as $header)
			<div class="col-md-12">
				<div class="title">COST SHEET VARIANCE (COST ID -{{ $header->id }})</div> 
			</div>

			<div class="col-md-12">
				<div class="style-details">
					<table class="table table-borderless">
					  <tr>
					    <th width="17%">Customer</th>
					    <th width="2%">:</th>
					    <td width="31%">{{ $header->customer_name }}</td>
					    <th width="17%">BOM Stage</th>
					    <th width="2%">:</th>
					    <td width="31%">{{ $header->bom_stage_description }}</td>
					  </tr>
					  <tr>
					    <th>Costing by</th>
					    <th>:</th>
					    <td>{{ $header->user_name }}</td>
					    <th>Costing Date</th>
					    <th>:</th>
					    <td>{{ $header->created_date2 }}</td>
					  </tr>
					  <tr>
					    <th>Color Type</th>
					    <th>:</th>
					    <td>{{ $header->color_option }}</td>
					    <th>Division</th>
					    <th>:</th>
					    <td>{{ $header->division_description }}</td>
					  </tr>
					  <tr>
					    <th>Style No</th>
					    <th>:</th>
					    <td>{{ $header->style_no }}</td>
					    <th>Style Name</th>
					    <th>:</th>
					    <td>{{ $header->style_description }}</td>
					  </tr>
					  <tr>
					    <th>Season</th>
					    <th>:</th>
					    <td>{{ $header->season_name }}</td>
					    <th></th>
					    <th></th>
					    <td></td>
					  </tr>
					</table>
				</div>

				<div class="style-image">
					<div class="text-right">
						@if($header->image!='')
							<img src="{{ URL::to('/assets/styleImage/') }}/{{ $header->image }}" alt="" class="img-thumbnail image-holder">
						@else
							<img alt="" class="img-thumbnail image-holder">
						@endif
					</div>
				</div>
	
			</div>


			<div class="col-md-12">
				<table class="table table-bordered details">
					<tr>
						<th colspan="5" class="text-center">Header Version - {{ $new_version = $header->revision_no }} </th>
					</tr>
					<tr class="green-row">
						<th width="20%">Approval No</th>
						<th width="20%">Approval Date</th>
						<th width="20%">Buy</th>
						<th width="20%">Status</th>
						<th width="20%">Design Source</th>
					</tr>
					<tr>
						<td>{{ $header->revision_no }}</td>
						<td>{{ $header->last_app_by }}</td>
						<td>{{ $header->buy_name }}</td>
						<td>{{ $header->status }}</td>
						<td>{{ $header->design_source_name }}</td>
					</tr>
					<tr class="green-row">
						<th>FOB</th>
						<th>Planned Efficiency</th>
						<th>Upcharge</th>
						<th>CPM</th>
						<th>CPSM</th>
					</tr>
					<tr>
						<td>{{ $header->fob }}</td>
						<td>{{ $header->planned_efficiency }}</td>
						<td>{{ $header->upcharge }}</td>
						<td>{{ $header->cost_per_min }}</td>
						<td>{{ $header->cost_per_std_min }}</td>
					</tr>
					<tr class="green-row">
						<th>EPM</th>
						<th>NPM</th>
						<th>CPUM</th>
						<th>CPM Factory</th>
						<th>Corporate Cost</th>
					</tr>
					<tr>
						<td>{{ $header->epm }}</td>
						<td>{{ $header->np_margine }}</td>
						<td>{{ $header->cost_per_utilised_min }}</td>
						<td>{{ $header->cpm_factory }}</td>
						<td>{{ $header->coperate_cost }}</td>
					</tr>
					<tr class="green-row">
						<th>Total RM Cost</th>
						<th>Labour Cost</th>
						<th>Finance Cost</th>					
						<th>Finance Charges</th>
						<th>CPM Front End</th>
					</tr>
					<tr>
						<td>{{ $header->total_rm_cost }}</td>
						<td>{{ $header->labour_cost }}</td>
						<td>{{ $header->finance_cost }}</td>
						<td>{{ $header->finance_charges }}</td>
						<td>{{ $header->cpm_front_end }}</td>						
					</tr>
				
				</table>				
				@endforeach
			</div>

			<div class="col-md-12">
				@foreach ($pre_headers as $pre_header)
				<table class="table table-bordered details">
					<tr>
						<th colspan="5" class="text-center">Header Version - {{ $pre_version = $pre_header->revision_no }} </th>
					</tr>
					<tr class="green-row">
						<th width="20%">Approval No</th>
						<th width="20%">Approval Date</th>
						<th width="20%">Buy</th>
						<th width="20%">Status</th>
						<th width="20%">Design Source</th>
					</tr>
					<tr>
						<td>{{ $pre_header->revision_no }}</td>
						<td>{{ $pre_header->last_app_by }}</td>
						<td>{{ $pre_header->buy_name }}</td>
						<td>{{ $pre_header->status }}</td>
						<td>{{ $pre_header->design_source_name }}</td>
					</tr>
					<tr class="green-row">
						<th>FOB</th>
						<th>Planned Efficiency</th>
						<th>Upcharge</th>
						<th>CPM</th>
						<th>CPSM</th>
					</tr>
					<tr>
						<td>{{ $pre_header->fob }}</td>
						<td>{{ $pre_header->planned_efficiency }}</td>
						<td>{{ $pre_header->upcharge }}</td>
						<td>{{ $pre_header->cost_per_min }}</td>
						<td>{{ $pre_header->cost_per_std_min }}</td>
					</tr>
					<tr class="green-row">
						<th>EPM</th>
						<th>NPM</th>
						<th>CPUM</th>
						<th>CPM Factory</th>
						<th>Corporate Cost</th>
					</tr>
					<tr>
						<td>{{ $pre_header->epm }}</td>
						<td>{{ $pre_header->np_margine }}</td>
						<td>{{ $pre_header->cost_per_utilised_min }}</td>
						<td>{{ $pre_header->cpm_factory }}</td>
						<td>{{ $pre_header->coperate_cost }}</td>
					</tr>
					<tr class="green-row">
						<th>Total RM Cost</th>
						<th>Labour Cost</th>
						<th>Finance Cost</th>					
						<th>Finance Charges</th>
						<th>CPM Front End</th>
					</tr>
					<tr>
						<td>{{ $pre_header->total_rm_cost }}</td>
						<td>{{ $pre_header->labour_cost }}</td>
						<td>{{ $pre_header->finance_cost }}</td>
						<td>{{ $pre_header->finance_charges }}</td>
						<td>{{ $pre_header->cpm_front_end }}</td>						
					</tr>			
				</table>				
				@endforeach
				<hr>
			</div>

			

			<div class="col-md-12">
				<table class="table table-striped table-bordered table-no-padding">
				  <thead>
				    <tr>
				      <th width="20px">#</th>
				      <th>Item Description</th>
				      <th>Comp</th>
				      <th>Origin</th>
				      <th>Unit</th>
				      <th>Net&nbsp;Con</th>
				      <th>Gross&nbsp;Con</th>
				      <th>Wastage</th>
				      <th>Freight</th>
				      <th>Surcharge</th>
				      <th>UP</th>
				      <th>TC PC</th>
				    </tr>
				  </thead>
				  <tbody>
				  	@foreach($categories as $category)
					  	<tr>
					       <td colspan="12" class="main-category">{{ $category->category_name }}</td>
					    </tr>
					    @php 
					    	$c=1;
					    	$cat_sum=0;
					    @endphp
					    @foreach($details as $detail)
					    	@if($category->category_id==$detail->category_id)
							    
							    <tr>
							      <td class="text-center">{{ $new_version }}</td>
							      <td>{{ $detail->master_description }}</td>
							      <td>{{ $detail->product_component_description }}</td>
							      <td>{{ $detail->origin_type }}</td>
							      <td>{{ $detail->uom_description }}</td>
							      <td class="text-right">{{ $detail->net_consumption }}</td>
							      <td class="text-right">{{ $detail->gross_consumption }}</td>
							      <td class="text-right">{{ $detail->wastage }}%</td>
							      <td class="text-right">{{ $detail->freight_charges }}</td>
							      <td class="text-right">{{ $detail->surcharge }}</td>
							      <td class="text-right">{{ $detail->unit_price }}</td>
							      <td class="text-right">{{ $detail->total_cost }}</td>
							    </tr>

							    @php $cat_sum += $detail->total_cost; @endphp

							    @foreach($pre_details as $pre_detail)
							    @if($detail->item_id==$pre_detail->item_id)

									@php 
									$bg_net='';
									$bg_gross='';
									$bg_wast='';
									$bg_fer='';
									$bg_sur='';
									$bg_up='';
									$bg_tot='';

									if($detail->net_consumption!=$pre_detail->net_consumption){
										$bg_net="color: #e60000;";	
									}
									if($detail->gross_consumption!=$pre_detail->gross_consumption){
										$bg_gross="color: #e60000;";	
									}
									if($detail->wastage!=$pre_detail->wastage){
										$bg_wast="color: #e60000;";	
									}
									if($detail->freight_charges!=$pre_detail->freight_charges){
										$bg_fer="color: #e60000;";	
									}
									if($detail->surcharge!=$pre_detail->surcharge){
										$bg_sur="color: #e60000;";	
									}
									if($detail->unit_price!=$pre_detail->unit_price){
										$bg_up="color: #e60000;";	
									}
									if($detail->total_cost!=$pre_detail->total_cost){
										$bg_tot="color: #e60000;";	
									}
									@endphp

							    	<tr class="bg-yellow">
								      <td style="background-color: #f2f2f2;color:#e60000;" class="text-center">{{ $pre_detail->revision_no }}</td>
								      <td style="background-color: #f2f2f2;">{{ $pre_detail->master_description }}</td>
								      <td style="background-color: #f2f2f2;">{{ $pre_detail->origin_type }}</td>
								      <td style="background-color: #f2f2f2;">{{ $pre_detail->uom_description }}</td>
								      <td style="background-color: #f2f2f2;{{ $bg_net }}" class="text-right">{{ $pre_detail->net_consumption }}</td>
								      <td style="background-color: #f2f2f2;{{ $bg_gross }}" class="text-right">{{ $pre_detail->gross_consumption }}</td>
								      <td style="background-color: #f2f2f2;{{ $bg_wast }}" class="text-right">{{ $pre_detail->wastage }}%</td>
								      <td style="background-color: #f2f2f2;{{ $bg_fer }}" class="text-right">{{ $pre_detail->freight_charges }}</td>
								      <td style="background-color: #f2f2f2;{{ $bg_sur }}" class="text-right">{{ $pre_detail->surcharge }}</td>
								      <td style="background-color: #f2f2f2;{{ $bg_up }}" class="text-right">{{ $pre_detail->unit_price }}</td>
								      <td style="background-color: #f2f2f2;{{ $bg_tot }}" class="text-right">{{ $pre_detail->total_cost }}</td>
								    </tr>

							    @endif
							    @endforeach

							@endif

						@php $c++; @endphp

						@endforeach

						@foreach($removed_rows as $removed_row)
					    @if($category->category_id==$removed_row->category_id)
					    <tr>
					      <td style="color:#e60000;" class="text-center"><strike>{{ $pre_detail->revision_no }}</strike></td>
					      <td><strike>{{ $removed_row->master_description }}</strike></td>
					      <td><strike>{{ $removed_row->origin_type }}</strike></td>
					      <td><strike>{{ $removed_row->uom_description }}</strike></td>
					      <td class="text-right"><strike>{{ $removed_row->net_consumption }}</strike></td>
					      <td class="text-right"><strike>{{ $removed_row->gross_consumption }}</strike></td>
					      <td class="text-right"><strike>{{ $removed_row->wastage }}%</strike></td>
					      <td class="text-right"><strike>{{ $removed_row->freight_charges }}</strike></td>
					      <td class="text-right"><strike>{{ $removed_row->surcharge }}</strike></td>
					      <td class="text-right"><strike>{{ $removed_row->unit_price }}</strike></td>
					      <td class="text-right"><strike>{{ $removed_row->total_cost }}</strike></td>
					    </tr>
					    @endif
					    @endforeach

						<tr>
					      <td colspan="11" class="cat-total">TOTAL {{ $category->category_name }} COST</td>
					      <td class="text-bold cat-total text-right bottom-border">{{ number_format($cat_sum, 4, '.', '') }}</td>					   
					    </tr>	
					@endforeach

				  </tbody>
				</table>


				<table class="table table-striped table-bordered table-no-padding">
				  <thead>
				    <tr>
				      <th>Item Description</th>
				      <th>Revision {{ $pre_version }}</th>
				      <th>Revision {{ $new_version }}</th>
					</tr>
				  </thead>
				  <tbody>
				  	@foreach($headers as $header)
					  	@foreach($pre_headers as $pre_header)
					  	<tr>
					  		<td width="70%" style="background-color: #e6e6e6;">Total RM Cost</td>
					  		<td class="text-right" style="background-color: #e6e6e6;">{{ $pre_header->total_rm_cost }}</td>
					  		<td class="text-right" style="background-color: #e6e6e6;">{{ $header->total_rm_cost }}</td>
					  	</tr>
					  	<tr>
					  		<td>Labour / Sub Contracting Cost</td>
					  		<td class="text-right">{{ $pre_header->labour_cost }}</td>
					  		<td class="text-right">{{ $header->labour_cost }}</td>
					  	</tr>
					  	<tr>
					  		<td style="background-color: #e6e6e6;">Total Manufacturing Cost</td>
					  		<td class="text-right" style="background-color: #e6e6e6;">{{ $pre_header->total_rm_cost+$pre_header->labour_cost }}</td>
					  		<td class="text-right" style="background-color: #e6e6e6;">{{ $header->total_rm_cost+$header->labour_cost }}</td>
					  	</tr>
					  	<tr>
					  		<td>Finance Cost</td>
					  		<td class="text-right">{{ $pre_header->finance_cost }}</td>
					  		<td class="text-right">{{ $header->finance_cost }}</td>
					  	</tr>
					  	<tr>
					  		<td>Corporate Cost</td>
					  		<td class="text-right">{{ $pre_header->coperate_cost }}</td>
					  		<td class="text-right">{{ $header->coperate_cost }}</td>
					  	</tr>
					  	<tr>
					  		<td>Upcharge</td>
					  		<td class="text-right">{{ $pre_header->upcharge }}</td>
					  		<td class="text-right">{{ $header->upcharge }}</td>
					  	</tr>
					  	<tr>
					  		<td style="background-color: #e6e6e6;">Total Cost</td>
					  		<td class="text-right" style="background-color: #e6e6e6;">{{ $pre_header->total_cost }}</td>
					  		<td class="text-right" style="background-color: #e6e6e6;">{{ $header->total_cost }}</td>
					  	</tr>
					  	<tr>
					  		<td>Total FOB</td>
					  		<td class="text-right">{{ $pre_header->fob }}</td>
					  		<td class="text-right">{{ $header->fob }}</td>
					  	</tr>
					  	<tr>
					  		<td>SMV</td>
					  		<td class="text-right">{{ $pre_header->total_smv }}</td>
					  		<td class="text-right">{{ $header->total_smv }}</td>
					  	</tr>
					  	<tr>
					  		<td>EPM</td>
					  		<td class="text-right">{{ $pre_header->epm }}</td>
					  		<td class="text-right">{{ $header->epm }}</td>
					  	</tr>
					  	<tr>
					  		<td>NP</td>
					  		<td class="text-right">{{ $pre_header->np_margine }}</td>
					  		<td class="text-right">{{ $header->np_margine }}</td>
					  	</tr>
					  	<tr>
					  		<td>Planned Efficiency</td>
					  		<td class="text-right">{{ $pre_header->planned_efficiency }}</td>
					  		<td class="text-right">{{ $header->planned_efficiency }}</td>
					  	</tr>
					  	<tr>
					  		<td>CPM Factory</td>
					  		<td class="text-right">{{ $pre_header->cpm_factory }}</td>
					  		<td class="text-right">{{ $header->cpm_factory }}</td>
					  	</tr>
					  	@endforeach
				  	@endforeach
				  </tbody>		
				</table>

			</div>

			<div class="col-md-6 pre-ratio">			
				<div class="ratio-title">Percentage Ratio Revision - {{ $pre_version }}</div>
				<table class="table table-striped table-bordered table-no-padding">
				  <thead>
				    <tr>
				      <th>RM Cost</th>
				      <th>%</th>
				      <th>Cost / PC</th>
				    </tr>
				  </thead>
				  <tbody>
				  	@foreach($pre_ratios as $pre_ratio)
				    <tr>
				      <td>{{ $pre_ratio->category_name }}</td>
				      <td class="text-right">{{ number_format($pre_ratio->cat_sum*100/$pre_ratio->tot_sum, 4, '.', '') }}</td>
				      <td class="text-right">{{ $pre_ratio->cat_sum }}</td>
				    </tr>
				    @endforeach
				  </tbody>
				</table>
			</div>

			<div class="col-md-6 ratio">			
				<div class="ratio-title">Percentage Ratio Revision - {{ $new_version }}</div>
				<table class="table table-striped table-bordered table-no-padding">
				  <thead>
				    <tr>
				      <th>RM Cost</th>
				      <th>%</th>
				      <th>Cost / PC</th>
				    </tr>
				  </thead>
				  <tbody>
				  	@foreach($ratios as $ratio)
				    <tr>
				      <td>{{ $ratio->category_name }}</td>
				      <td class="text-right">{{ number_format($ratio->cat_sum*100/$ratio->tot_sum, 4, '.', '') }}</td>
				      <td class="text-right">{{ $ratio->cat_sum }}</td>
				    </tr>
				    @endforeach
				  </tbody>
				</table>
			</div>
			

		</div>
	</div>
</div>

@stop

