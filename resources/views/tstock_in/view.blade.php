@include('../layouts.header')
	<body>
		<section class="body">
			<div class="inner-wrapper">
				<section role="main" class="content-body" style="margin:0px;padding:75px 10px !important">
				@include('../layouts.pageheader')

                    <section class="card">

						<div class="card-body">

							<div class="invoice">

								<header class="clearfix">
									<div class="row">
										<div class="col-8 mt-3 mb-3">
											<h2 class="h2 mt-0 mb-1" style="color:#17365D">Stock In NO:</h2>
											<h4 class="h4 m-0 text-dark font-weight-bold">{{$tstock_in->prefix}}{{$tstock_in->Sal_inv_no}}</h4>
										</div>
										<div class="col-4 text-end mt-3 mb-3">
											<div class="ib">
												<img width="100px" src="/assets/img/logo.png" alt="MFI Logo" />
											</div>
										</div>
									</div>
								</header>
								<div class="bill-info">
									<div class="row">
										<div class="col-md-7">
											<div class="bill-to">
												<h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
													<span style="color:#17365D">Date: &nbsp </span>
													<span style="font-weight:400;color:black" class="value">  {{\Carbon\Carbon::parse($tstock_in->sa_date)->format('d-m-y')}}</span>
												</h4>

												<h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
													<span style="color:#17365D">Company Name: &nbsp </span>
													<span style="font-weight:400;color:black" class="value"> {{$tstock_in->ac_name}}</span>
												</h4>

												<h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
													<span style="color:#17365D">Address: &nbsp </span>
													<span style="font-weight:400;color:black" class="value"> {{$tstock_in->address}}</span>
												</h4>

												<h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
													<span style="color:#17365D">Phone No: &nbsp </span>
													<span style="font-weight:400;color:black" class="value"> {{$tstock_in->phone_no}}</span>
												</h4>
												<h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
													<span style="color:#17365D">Mill Inv No: &nbsp </span>
													<span style="font-weight:400;color:black" class="value"> {{$tstock_in->mill_gate}}</span>
												</h4>
											</div>
										</div>
										<div class="col-md-5">
											<div class="bill-data">

												<h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
													<span style="color:#17365D">Item Type: &nbsp;</span>
												
													@if ($tstock_in->item_type == 1)
														<span style="font-weight:400;color:black" class="value">Pipes</span>
													@elseif ($tstock_in->item_type == 2)
														<span style="font-weight:400;color:black" class="value">Garder / TR</span>
													@else
														<span style="font-weight:400;color:black" class="value">Unknown</span>
													@endif
												</h4>
												<h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
													<span style="color:#17365D">Purchase Invoice#: &nbsp </span>
													<span style="font-weight:400;color:black" class="value"> {{$tstock_in->pur_inv}}</span>
												</h4>
												<h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
													<span style="color:#17365D">Remarks: &nbsp </span>
													<span style="font-weight:400;color:black" class="value"> {{$tstock_in->Sales_remarks}}</span>
												</h4>
											</div>
										</div>
									</div>
								</div>

								<table class="table table-responsive-md invoice-items table-striped" style="overflow-x: auto;">
									<thead>
										<tr class="text-dark">
											<th width="4%" class="font-weight-semibold" style="color:#17365D">S.No</th>
											<th width="24%" class="font-weight-semibold" style="color:#17365D">Item Name</th>
											<th width="22%" class="font-weight-semibold" style="color:#17365D">Remarks</th>
											<th class="text-center font-weight-semibold" style="color:#17365D">Qty</th>
											<th class="text-center font-weight-semibold" style="color:#17365D">Weight</th>
										</tr>
									</thead>
									@php($total_quantity = 0)
									@php($total_weight = 0)
									<tbody>
										@foreach($tstock_in_items as $key => $tstock_in_items)
										<tr>
											<td>{{$key+1}}</td>
											<td class="font-weight-semibold text-dark">{{$tstock_in_items->item_name}}</td>
											<td>{{$tstock_in_items->remarks}}</td>
											<td class="text-center">{{$tstock_in_items->Sales_qty}}</td>
											<td class="text-center">{{($tstock_in_items->Sales_qty*$tstock_in_items->weight_pc)}}</td>
										</tr>
										<?php $total_weight=$total_weight+($tstock_in_items->Sales_qty*$tstock_in_items->weight_pc) ?>
										<?php $total_quantity=$total_quantity+ $tstock_in_items->Sales_qty ?>

										@endforeach
									</tbody>
								</table>

								<div class="row" style="justify-content: space-between">
									<div class="col-12 col-md-4">
										<table class="table h6 text-dark">
											<tbody>
												<tr class="b-top-0">
													<td colspan="2" style="color:#17365D">Total Quantity</td>
													<td class="text-left">{{$total_quantity}}</td>
												</tr>
												<tr>
													<td colspan="2" style="color:#17365D">Total Weight(KGs)</td>
													<td class="text-left">{{$total_weight}}</td>
												</tr>
	
											</tbody>
										</table>
									</div>
									<div class="col-12 col-md-4">
										<div class="text-end">
											<a onclick="window.location='{{ route('all-tstock-in') }}'" class="btn btn-primary mt-2 mb-2"> <i class="fas fa-arrow-left"></i> Back</a>
											<a href="{{ route('print-tstock-in-invoice', $tstock_in->Sal_inv_no) }}" class="btn btn-danger mt-2 mb-2" target="_blank"> <i class="fas fa-print"></i> Print</a>
										</div>
									</div>
								<div>
							</div>
						</div>
					</section>
				</section>
			</div>
			</div>
		</section>
        @include('../layouts.footerlinks')
	</body>

	
</html>