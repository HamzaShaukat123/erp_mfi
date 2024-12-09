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
											<h2 class="h2 mt-0 mb-1" style="color:#17365D">PURCHASE ORDER NO:</h2>
											<h4 class="h4 m-0 text-dark font-weight-bold">{{$pur->prefix}}{{$pur->pur_id}}</h4>
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
										<div class="col-12 col-md-7">
											<div class="bill-to">
												<h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
													<span style="color:#17365D">Date: &nbsp </span>
													<span style="font-weight:400;color:black" class="value"> {{\Carbon\Carbon::parse($pur->sa_date)->format('d-m-y')}}</span>
												</h4>

												<h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
													<span  style="color:#17365D">To: &nbsp </span>
													<span style="font-weight:400;color:black" class="value"> {{$pur->ac_name}}</span>
												</h4>

												<h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
													<span  style="color:#17365D">Address: &nbsp </span>
													<span style="font-weight:400;color:black" class="value"> {{$pur->address}}</span>
												</h4>

												<h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
													<span  style="color:#17365D">Phone No: &nbsp </span>
													<span style="font-weight:400;color:black" class="value"> {{$pur->phone_no}}</span>
												</h4>
												<h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
													<span  style="color:#17365D">Qoutation No: &nbsp </span>
													<span style="font-weight:400;color:black" class="value"> {{$pur->pur_bill_no}}</span>
												</h4>
											</div>
										</div>
										<div class="col-12 col-md-5">
											<div class="bill-data">

												<h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
													<span  style="color:#17365D">Name Of Person: &nbsp</span>
													<span style="font-weight:400;color:black" > {{$pur->cash_saler_name}}</span>
												</h4>
												
												<h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
													<span  style="color:#17365D">Person Address: &nbsp</span>
													<span style="font-weight:400;color:black" > {{$pur->cash_saler_address}}</span>
												</h4>

												<h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
													<span  style="color:#17365D">Purchase Inv No: &nbsp</span>
													<span style="font-weight:400;color:black"> {{$pur->sale_against}}</span>
												</h4>
												<h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
													<span style="color:#17365D">Remarks: &nbsp</span>
													<span style="font-weight:400;color:black"> {{$pur->pur_remarks}}</span>
												</h4>
											</div>
										</div>
									</div>
								</div>


								<table class="table table-responsive-md invoice-items table-striped">
									<thead>
										<tr class="text-dark">
											<th class="font-weight-semibold"  style="color:#17365D">S.No</th>
											<th class="text-center font-weight-semibold"  style="color:#17365D">Qty</th>
											<th class="font-weight-semibold"  style="color:#17365D">Item</th>
											<th class="font-weight-semibold"  style="color:#17365D">Remarks</th>
											<th class="text-center font-weight-semibold"  style="color:#17365D">Weight</th>
											<th class="text-center font-weight-semibold"  style="color:#17365D">Price</th>
											<th class="text-center font-weight-semibold"  style="color:#17365D">Amount</th>
										</tr>
									</thead>
									@php($subtotal = 0)
									@php($total_quantity = 0)
									@php($total_weight = 0)
									<tbody>
										@foreach($pur2 as $key => $pur_item)
										<tr>
											<td>{{$key+1}}</td>
											<td class="text-center">{{$pur_item->pur_qty2}}</td>
											<td class="font-weight-semibold text-dark">{{$pur_item->item_name}}</td>
											<td>{{$pur_item->remarks}}</td>
											<td class="text-center">{{$pur_item->pur_qty}}</td>
											<td class="text-center">{{$pur_item->pur_price}}</td>
											<td class="text-center">{{$pur_item->pur_price * $pur_item->pur_qty}}</td>
										</tr>
										<?php $subtotal=$subtotal+($pur_item->pur_price * $pur_item->pur_qty) ?>
										<?php $total_weight=$total_weight+ $pur_item->pur_qty ?>
										<?php $total_quantity=$total_quantity+ $pur_item->pur_qty2 ?>

										@endforeach
									</tbody>
								</table>

								<div class="row">
									<div class="col-12 col-md-6">
										<table class="table h6 text-dark">
											<tbody>
												<tr class="b-top-0">
													<td colspan="2"  style="color:#17365D">Total Quantity</td>
													<td class="text-left">{{$total_quantity}}</td>
												</tr>
												<tr>
													<td colspan="2"  style="color:#17365D">Total Weight(KGs)</td>
													<td class="text-left">{{$total_weight}}</td>
												</tr>
											</tbody>
										</table>
										<h3 style="color:#17365D; text-decoration: underline;" id="numberInWords"></h3>
											
										<div>
											<h2 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
											<span style="color:#17365D; font-size:20px; font-weight:bold; font-style:italic; text-decoration:underline;">
												Terms And Conditions:&nbsp
											</span><br><br>
											<span style="font-weight:400;color:rgb(224, 8, 8);white-space: pre-wrap;word-wrap: break-word;" class="value">{{ htmlspecialchars($pur->tc) }}</span>
											</h2>
										</div>
									</div>
									<div class="col-12 col-md-6 invoice-summary text-end ">
											<table class="table h6 text-dark">
												<tbody>
													<tr class="b-top-0">
														<td colspan="2"  style="color:#17365D" >Subtotal</td>
														<td class="text-left">{{$subtotal}}</td>
													</tr>
													<tr>
														<td colspan="2" style="color:#17365D">Labour Charges</td>
														<td class="text-left">{{$pur->pur_labor_char}} PKR</td>
													</tr>
														<td colspan="2"  style="color:#17365D">Convance Charges</td>
														<td class="text-left">{{$pur->pur_convance_char}} PKR</td>
													</tr>
													</tr>
														<td colspan="2"  style="color:#17365D">Discount</td>
														<td class="text-left">{{$pur->pur_discount}} PKR</td>
													</tr>
													<?php $netamount=round($subtotal + $pur->pur_labor_char + $pur->pur_convance_char - $pur->pur_discount) ?>
													<tr class="h5">
														<td colspan="2"  style="color:#17365D">Net Amount</td>
														<td class="text-left text-danger" style="font-weight:700">{{number_format($netamount)}} PKR</td>
													</tr>
												</tbody>
											</table>
									</div>
								<div>
							</div>

							<div class="text-end">
								<a onclick="window.location='{{ route('all-po') }}'" class="btn btn-primary mt-2 mb-2"> <i class="fas fa-arrow-left"></i> Back</a>
								<a href="{{ route('print-po-invoice', $pur->pur_id) }}" class="btn btn-danger mt-2 mb-2" target="_blank"> <i class="fas fa-print"></i> Print</a>
							</div>

						</div>

					</section>
				</section>
			</div>
			</div>
		</section>
        @include('../layouts.footerlinks')
	</body>
	<script>
		var netAmount = <?php echo json_encode($netamount); ?>;
		var words = convertCurrencyToWords(netAmount);
		document.getElementById('numberInWords').innerHTML = words;
	</script>
	
	
</html>