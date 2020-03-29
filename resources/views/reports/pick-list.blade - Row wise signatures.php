@extends('reports.common.template')

@section('title')
    Costing Details
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
				<div class="title">PICK LIST - MRN #{{ $header->mrn_no }} / ISSUE #{{ $header->issue_id }} </div> 
			</div>

			<div class="col-md-12">
				<table class="table table-borderless"> 
				  
				  <tr>
				    <th width="10%">CO #</th>
				    <th width="1%">:</th>
				    <td width="22%">{{ $header->cust_order }}</td>
				    <th width="1%">Line</th>
				    <th width="1%">:</th>
				    <td width="22%">{{ $header->line_no }}</td>
				    <th width="10%">Style</th>
				    <th width="1%">:</th>
				    <td width="23%">{{ $header->style_no }}</td>
				  </tr>

				  <tr>
				    <th>Customer PO #</th>
				    <th>:</th>
				    <td>{{ $header->po_nos }}</td>
				    <th>Cut Qty</th>
				    <th>:</th>
				    <td>{{ $header->cut_qty }}</td>				   
				    <th>FNG #</th>
				    <th>:</th>
				    <td>{{ $header->fg_code }}</td>
				  </tr>

				  <tr>
				    <th>Issue Date</th>
				    <th>:</th>
				    <td>{{ $header->created_date }}</td>
				    <th>Printed Date</th>
				    <th>:</th>
				    <td>{{ date('d-M-Y H:i:s') }}</td>
				    <th></th>
				    <th></th>
				    <td></td>
				  </tr>

				</table>
			</div>
			@endforeach



			<div class="col-md-12">
				
				<table class="table table-striped table-bordered table-no-padding">
					<thead>
					  <tr>
                        <th>&nbsp;#&nbsp;</th>
						<th>Code</th>
						<th>Description</th>
						<th>Colour</th>
						<th>UOM</th>
						<th>Size</th>
						<th>Store</th>
						<th>Sub&nbsp;Store</th>
						<th>Bin</th>
						<th>Shade</th>
						<th>Batch</th>
						<th>Roll/Box</th>
						<th>Lab Comment</th>
						<th>Yardage</th>					
						<th>Requirement</th>
						<th>Issued&nbsp;Qty</th>
						<th>Balance</th>
                        <th colspan="6"></th>
					  </tr>
					</thead>
					
					<tbody>
					  @php 
					   	$c=1;
					  @endphp
					  @foreach ($details as $detail)
					  <!-- Row begin -->
					  <tr>
					  	<td rowspan="4" style="vertical-align:middle;text-align:center;">{{ $c }}</td>
						<td rowspan="4" style="vertical-align:middle;">{{ $detail->master_code }}</td>
						<td rowspan="4" style="vertical-align:middle;">{{ $detail->master_description }}</td>
						<td rowspan="4" style="vertical-align:middle;">{{ $detail->color_name }}</td>
						<td rowspan="4" style="vertical-align:middle;">{{ $detail->uom_description }}</td>
						<td rowspan="4" style="vertical-align:middle;">{{ $detail->size_name }}</td>
						<td rowspan="4" style="vertical-align:middle;">{{ $detail->store_name }}</td>
						<td rowspan="4" style="vertical-align:middle;">{{ $detail->substore_name }}</td>
						<td rowspan="4" style="vertical-align:middle;">{{ $detail->store_bin_name }}</td>
						<td rowspan="4" style="vertical-align:middle;">{{ $detail->shade }}</td>
						<td rowspan="4" style="vertical-align:middle;">{{ $detail->batch_no }}</td>
						<td rowspan="4" style="vertical-align:middle;">{{ $detail->roll_box }}</td>
						<td rowspan="4" style="vertical-align:middle;">{{ $detail->lab_comment }}</td>
						<td rowspan="4" style="vertical-align:middle;text-align:right;">{{ $detail->yardage }}</td>
						<td rowspan="4" style="vertical-align:middle;text-align:right;">{{ number_format($detail->requested_qty,4) }}</td>
						<td rowspan="4" style="vertical-align:middle;text-align:right;">{{ number_format($detail->issue_qty,4) }}</td>
						<td rowspan="4" style="vertical-align:middle;text-align:right;">{{ number_format($detail->requested_qty-$detail->issue_qty,4) }}</td>
					  </tr>

					  <tr>
					  	<td>Issued&nbsp;Qty</td>
					  	<td width="20"></td>s
					  	<td width="20"></td>
					  	<td width="20"></td>
					  	<td width="20"></td>
					  	<td width="20"></td>
					  </tr>

					  <tr>
					  	<td>Signature</td>
					  	<td></td>
					  	<td></td>
					  	<td></td>
					  	<td></td>
					  	<td></td>
					  </tr>

					  <tr>
					  	<td>Signature</td>
					  	<td></td>
					  	<td></td>
					  	<td></td>
					  	<td></td>
					  	<td></td>
					  </tr>
					  <!-- Row end -->
					  @php 
						$c++;
					  @endphp
					  @endforeach

					</tbody> 	

				</table>

		</div>
	</div>
</div>

@stop

