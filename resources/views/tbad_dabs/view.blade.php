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
											<h2 class="h2 mt-0 mb-1" style="color:#17365D">Bad Dabs Pipes/Garder ID:</h2>
											<h4 class="h4 m-0 text-dark font-weight-bold">{{$tbad_dabs->bad_dabs_id}}</h4>
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
													<span style="color:#17365D">Reason: &nbsp </span>
													<span style="font-weight:400;color:black" class="value"> {{$tbad_dabs->reason}}</span>
												</h4>
											</div>
										</div>
										<div class="col-md-5">
											<div class="bill-data">
												<h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
													<span style="color:#17365D">Date: &nbsp </span>
													<span style="font-weight:400;color:black" class="value">  {{\Carbon\Carbon::parse($tbad_dabs->sa_date)->format('d-m-y')}}</span>
												</h4>
												<h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
													<span style="color:#17365D">Item Type: &nbsp;</span>
												
													@if ($tbad_dabs->item_type == 1)
														<span style="font-weight:400;color:black" class="value">Pipes</span>
													@elseif ($tbad_dabs->item_type == 2)
														<span style="font-weight:400;color:black" class="value">Garder / TR</span>
													@else
														<span style="font-weight:400;color:black" class="value">Unknown</span>
													@endif
												</h4>
												
												
		
											</div>
										</div>
									</div>
								</div>
								<table class="table table-responsive-md invoice-items table-striped" style="overflow-x: auto;">
									<thead>
										<tr class="text-dark">
											<th width="5%" class="font-weight-semibold" style="color:#17365D">S.No</th>
											<th width="30%" class="font-weight-semibold" style="color:#17365D">Item Name</th>
											<th width="30%" class="font-weight-semibold" style="color:#17365D">Remarks</th>
											<th class="text-center font-weight-semibold" style="color:#17365D">Qty Add</th>
											<th class="text-center font-weight-semibold" style="color:#17365D">Qty Less</th>
										</tr>
									</thead>
									@php($qty_add = 0)
									@php($qty_less = 0)
									<tbody>
										@foreach($tbad_dabs2 as $key => $tbad_dabs_item)
										<tr>
											<td>{{$key+1}}</td>
											<td class="font-weight-semibold text-dark">{{$tbad_dabs_item->item_name}}</td>
											<td>{{$tbad_dabs_item->remarks}}</td>
											<td class="text-center">{{$tbad_dabs_item->pc_add}}</td>
											<td class="text-center">{{$tbad_dabs_item->pc_less}}</td>
										</tr>
										<?php $qty_less=$qty_less+$tbad_dabs_item->pc_less ?>
										<?php $qty_add=$qty_add+ $tbad_dabs_item->pc_add ?>

										@endforeach
									</tbody>
								</table>

								<div class="row" style="justify-content: space-between">
									<div class="col-12 col-md-4">
										<table class="table h6 text-dark">
											<tbody>
												<tr class="b-top-0">
													<td colspan="2" style="color:#17365D">Total Add</td>
													<td class="text-left">{{$qty_add}}</td>
												</tr>
												<tr>
													<td colspan="2" style="color:#17365D">Total Less</td>
													<td class="text-left">{{$qty_less}}</td>
												</tr>
	
											</tbody>
										</table>
									</div>
									<div class="col-12 col-md-4">
										<div class="text-end">
											<a onclick="window.location='{{ route('all-tbad-dabs') }}'" class="btn btn-primary mt-2 mb-2"> <i class="fas fa-arrow-left"></i> Back</a>
											<a href="{{ route('print-tbad-dabs-invoice', $tbad_dabs->bad_dabs_id) }}" class="btn btn-danger mt-2 mb-2" target="_blank"> <i class="fas fa-print"></i> Print</a>
										</div>
									</div>
								</div>
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