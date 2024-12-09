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
										<div class="col-6 mt-3">
											<h2 class="h2 mt-0 mb-1" style="color:#17365D">Bad Dabs Doors ID:</h2>
											<h4 class="h4 m-0 text-dark font-weight-bold">{{$bad_dabs->bad_dabs_id}}</h4>
										</div>
										<div class="col-6 text-end mt-3 mb-3">
											<div class="ib">
												<img width="100px" src="/assets/img/logo.png" alt="MFI Logo" />
											</div>
										</div>
									</div>
								</header>

								<div class="bill-info">
									<div class="row">
										<div class="col-6">
											<div class="bill-to">
												<h4 class="text-dark font-weight-semibold">
													<span style="color:#17365D">Reason: &nbsp </span>
													<span style="font-weight:400;color:black" class="value"> {{$bad_dabs->reason}}</span>
												</h4>
											</div>
										</div>
										<div class="col-6">
											<div class="bill-data">
												<h4 class="text-dark font-weight-semibold">
													<span style="color:#17365D">Date: &nbsp </span>
													<span style="font-weight:400;color:black" class="value">  {{\Carbon\Carbon::parse($bad_dabs->sa_date)->format('d-m-y')}}</span>
												</h4>
								
											</div>
										</div>
									</div>
								</div>
								<table class="table table-responsive-md invoice-items table-striped" style="overflow-x: auto;">
									<thead>
										<tr class="text-dark">
											<th  class="font-weight-semibold" style="color:#17365D">S.No</th>
											<th  class="font-weight-semibold" style="color:#17365D">Item Name</th>
											<th  class="font-weight-semibold" style="color:#17365D">Remarks</th>
											<th class="text-center font-weight-semibold" style="color:#17365D">Qty Add</th>
											<th class="text-center font-weight-semibold" style="color:#17365D">Qty Less</th>
										</tr>
									</thead>
									@php($qty_add = 0)
									@php($qty_less = 0)
									<tbody>
										@foreach($bad_dabs_2 as $key => $bad_dabs_item)
										<tr>
											<td>{{$key+1}}</td>
											<td class="font-weight-semibold text-dark">{{$bad_dabs_item->item_name}}</td>
											<td>{{$bad_dabs_item->remarks}}</td>
											<td class="text-center">{{$bad_dabs_item->pc_add}}</td>
											<td class="text-center">{{$bad_dabs_item->pc_less}}</td>
										</tr>
										<?php $qty_less=$qty_less+$bad_dabs_item->pc_less ?>
										<?php $qty_add=$qty_add+ $bad_dabs_item->pc_add ?>

										@endforeach
									</tbody>
								</table>

								<div class="row">
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
								</div>

								<div class="text-end">
									<a onclick="window.location='{{ route('all-bad-dabs') }}'" class="btn btn-primary mt-2 mb-2"> <i class="fas fa-arrow-left"></i> Back</a>
									<a href="{{ route('print-bad-dabs-invoice', $bad_dabs->bad_dabs_id) }}" class="btn btn-danger mt-2 mb-2" target="_blank"> <i class="fas fa-print"></i> Print</a>
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