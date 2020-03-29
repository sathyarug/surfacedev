@extends('reports.common.template')

@section('title')
    BOM Report
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
				<div class="title">BOM {{ $header->bom_id }} (COST ID - {{ $header->costing_id }})</div> 
			</div>

			<div class="col-md-12">
				<table class="table table-borderless"> 
				  <tr>
				    <th width="10%">SC</th>
				    <th width="1%">:</th>
				    <td width="22%">{{ $header->sc_no }}</td>
				    <th width="1%">Status</th>
				    <th width="1%">:</th>
				    <td width="22%">{{ $header->status }}</td>
				    <th width="10%">FNG</th>
				    <th width="1%">:</th>
				    <td width="23%">{{ $header->master_code }}</td>
				  </tr>

				  <tr>
				    <th>Style</th>
				    <th>:</th>
				    <td>{{ $header->style_no }}</td>
				    <th>Country</th>
				    <th>:</th>
				    <td>{{ $header->country_description }}</td>				   
				    <th>BOM Stage</th>
				    <th>:</th>
				    <td>{{ $header->bom_stage_description }}</td>
				  </tr>

				  <tr>
				    <th>Season</th>
				    <th>:</th>
				    <td>{{ $header->season_name }}</td>
				    <th>Color Type</th>
				    <th>:</th>
				    <td>{{ $header->color_option }}</td>				   
				    <th>Color</th>
				    <th>:</th>
				    <td>{{ $header->color_name }}</td>
				  </tr>

				  <tr>
				    <th>Buy</th>
				    <th>:</th>
				    <td>{{ $header->buy_name }}</td>
				    <th>Created By</th>
				    <th>:</th>
				    <td>{{ $header->user_name }}</td>				   
				    <th>Created Date</th>
				    <th>:</th>
				    <td>{{ $header->created_date }}</td>
				  </tr>

				</table>
			</div>

			<div class="col-md-12">
				<table class="table table-bordered details">
					<tr class="green-row">
						<th width="20%">Total SMV</th>
						<th width="20%">Finance Charges</th>
						<th width="20%">FOB</th>
						<th width="20%">EPM</th>
						<th width="20%">NP Margin</th>					
					</tr>

					<tr>
						<td>{{ $header->total_smv }}</td>
						<td>{{ $header->finance_charges }}</td>
						<td>{{ $header->fob }}</td>
						<td>{{ $header->epm }}</td>
						<td>{{ $header->np_margin }}</td>
					</tr>

					<tr class="green-row">
						<th>Finance Cost</th>
						<th>Fabric Cost</th>
						<th>Elastic Cost</th>
						<th>Trim Cost</th>
						<th>Packing Cost</th>
					</tr>

					<tr>
						<td>{{ $header->finance_cost }}</td>
						<td>{{ $header->fabric_cost }}</td>
						<td>{{ $header->elastic_cost }}</td>
						<td>{{ $header->trim_cost }}</td>
						<td>{{ $header->packing_cost }}</td>
					</tr>

					<tr class="green-row">
						<th>Other Cost</th>
						<th>Total RM Cost</th>
						<th>Labour Cost</th>					
						<th>Coperate Cost</th>
						<th>Total Cost</th>
					</tr>

					<tr>
						<td>{{ $header->other_cost }}</td>
						<td>{{ $header->total_rm_cost }}</td>
						<td>{{ $header->labour_cost }}</td>
						<td>{{ $header->coperate_cost }}</td>
						<td>{{ $header->total_cost }}</td>						
					</tr>		
				
				</table>						
			</div>
			@endforeach
			

			<div class="col-md-12">
				<table class="table table-striped table-bordered table-no-padding">
				  <thead>
				    <tr>
				      <th width="20px">#</th>
				      <th>Item</th>
				      <th>Item Description</th>
				      <th>Origin</th>
				      <th>Unit</th>
				      <th>Net&nbsp;Con</th>
				      <th>Gross&nbsp;Con</th>
				      <th>Wastage</th>
				      <th>Freight</th>
				      <th>Surcharge</th>
				      <th>Unit Price</th>
				      <th>Total Cost</th>
				    </tr>
				  </thead>
				  <tbody>

				  	@if($header->comp_count==1)
						<!-- Single Style  -->
					  	@foreach($categories as $category)
						  <tr>
						    <td colspan="12" style="background-color:#cccccc;" class="main-category">{{ $category->category_name }}</td>
						  </tr>

						  @php 
							$c=1;
						  @endphp

						  @foreach($details as $detail)
						  	
						  	@if($category->category_id==$detail->category_id)
						  	<tr>
						      <td class="text-center">{{ $c }}</td>
						      <td>{{ $detail->master_code }}</td>
						      <td>{{ $detail->master_description }}</td>
						      <td>{{ $detail->origin_type }}</td>
						      <td>{{ $detail->uom_code }}</td>
						      <td class="text-right">{{ $detail->net_consumption }}</td>
						      <td class="text-right">{{ $detail->gross_consumption }}</td>
						      <td class="text-right">{{ $detail->wastage }}%</td>
						      <td class="text-right">{{ $detail->freight_charges }}</td>
						      <td class="text-right">{{ $detail->surcharge }}</td>
						      <td class="text-right">{{ $detail->bom_unit_price }}</td>
						      <td class="text-right">{{ $detail->total_cost }}</td>
						    </tr>
						    @endif

						  @endforeach

						  @php 
							$c++;
						  @endphp

						@endforeach
						<!-- Single style end -->
					@else

						<!-- Pack style -->
						@foreach($sfgs as $sfg)

							<tr>
								<td colspan="12" style="background-color:#cccccc;" class="main-category">{{ $sfg->sfg_code }} - {{ $sfg->product_component_description }}</td>
							</tr>

							@foreach($categories as $category)
								
								@if($category->sfg_id==$sfg->sfg_id)
									<tr>
										<td colspan="12" class="sub-category">{{ $category->category_name }}</td>
									</tr>
									@php 
										$c=1;
									@endphp
									@foreach($details as $detail)
										
									  	@if($category->category_id==$detail->category_id && $sfg->sfg_id==$detail->sfg_id)
									  	<tr>
									      <td class="text-center">{{ $c }}</td>
									      <td>{{ $detail->master_code }}</td>
									      <td>{{ $detail->master_description }}</td>
									      <td>{{ $detail->origin_type }}</td>
									      <td>{{ $detail->uom_code }}</td>
									      <td class="text-right">{{ $detail->net_consumption }}</td>
									      <td class="text-right">{{ $detail->gross_consumption }}</td>
									      <td class="text-right">{{ $detail->wastage }}%</td>
									      <td class="text-right">{{ $detail->freight_charges }}</td>
									      <td class="text-right">{{ $detail->surcharge }}</td>
									      <td class="text-right">{{ $detail->bom_unit_price }}</td>
									      <td class="text-right">{{ $detail->total_cost }}</td>
									    </tr>
									    @php 
											$c++;
									  	@endphp
									    @endif
										
									@endforeach

								@endif

								
							@endforeach
							

						@endforeach
						<!-- Pack style end -->
					@endif
			
				  </tbody>
				</table>	
			</div>



		</div>
	</div>
</div>

@stop

