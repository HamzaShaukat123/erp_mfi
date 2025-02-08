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
											<h2 class="h2 mt-0 mb-1" style="color:#17365D">PURCHASE INVOICE NO:</h2>
											<h4 class="h4 m-0 text-dark font-weight-bold">{{$pur->prefix}}{{$pur->Sale_inv_no}}</h4>
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
													<span style="color:#17365D">Invoice Date: &nbsp </span>
													<span style="font-weight:400;color:black" class="value">  {{\Carbon\Carbon::parse($pur->sa_date)->format('d-m-y')}}</span>
												</h4>

												<h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
													<span style="color:#17365D">To: &nbsp </span>
													<span style="font-weight:400;color:black" class="value"> {{$pur->ac_name}}</span>
												</h4>

												<h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
													<span style="color:#17365D">Address: &nbsp </span>
													<span style="font-weight:400;color:black" class="value"> {{$pur->address}}</span>
												</h4>

												<h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
													<span style="color:#17365D">Phone No: &nbsp </span>
													<span style="font-weight:400;color:black" class="value"> {{$pur->phone_no}}</span>
												</h4>
												<h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
													<span style="color:#17365D">Mill Inv No: &nbsp </span>
													<span style="font-weight:400;color:black" class="value"> {{$pur->pur_ord_no}}</span>
												</h4>
											</div>
										</div>
										<div class="col-md-5">
											<div class="bill-data">
												<h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
													<span style="color:#17365D">Dispatch To: &nbsp </span>
													<span style="font-weight:400;color:black" class="value"> {{$pur->disp_to}}</span>
												</h4>
												<h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
													<span style="color:#17365D">Name Of Person: &nbsp </span>
													<span style="font-weight:400;color:black" class="value"> {{$pur->Cash_pur_name}}</span>
												</h4>
												
												<h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
													<span style="color:#17365D">Person Address: &nbsp </span>
													<span style="font-weight:400;color:black" class="value"> {{$pur->cash_Pur_address}}</span>
												</h4>

												<h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
													<span style="color:#17365D">Sale Inv No: &nbsp </span>
													<span style="font-weight:400;color:black" class="value"> {{$pur->sales_against}}</span>
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
											<th class="text-center font-weight-semibold" style="color:#17365D">Price/Unit</th>
											<th class="text-center font-weight-semibold" style="color:#17365D">Length</th>
											<th class="text-center font-weight-semibold" style="color:#17365D">%</th>
											<th class="text-center font-weight-semibold" style="color:#17365D">Amount</th>
										</tr>
									</thead>
									@php($subtotal = 0)
									@php($total_quantity = 0)
									@php($total_weight = 0)
									<tbody>
										@foreach($pur2 as $key => $pur_item)
										<tr>
											<td>{{$key+1}}</td>
											<td class="font-weight-semibold text-dark">{{$pur_item->item_name}}</td>
											<td>{{$pur_item->remarks}}</td>
											<td class="text-center">{{$pur_item->Sales_qty2}}</td>
											<td class="text-center">{{$pur_item->sales_price}}</td>
											<td class="text-center">{{$pur_item->length}}</td>
											<td class="text-center">{{$pur_item->discount}}</td>
											<td class="text-center">{{(($pur_item->Sales_qty2 * $pur_item->sales_price)+( ($pur_item->Sales_qty2 * $pur_item->sales_price) * ($pur_item->discount/100))) * $pur_item->length}}</td>
										</tr>
										<?php $subtotal=$subtotal+((($pur_item->Sales_qty2 * $pur_item->sales_price)+( ($pur_item->Sales_qty2 * $pur_item->sales_price) * ($pur_item->discount/100))) * $pur_item->length) ?>
										<?php $total_weight=$total_weight+($pur_item->Sales_qty2*$pur_item->weight_pc) ?>
										<?php $total_quantity=$total_quantity+ $pur_item->Sales_qty2 ?>

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
										<h3 style="color:#17365D; text-decoration: underline;" id="numberInWords"></h3>
									</div>

									<div class="col-12 col-md-4">
										<table class="table h6 text-dark">
											<tbody>
												<tr class="b-top-0">
													<td colspan="2" style="color:#17365D">Subtotal</td>
													<td class="text-left">{{$subtotal}}</td>
												</tr>
												<tr>
													<td colspan="2" style="color:#17365D">Labour Charges</td>
													<td class="text-left" style="color:#17365D">{{$pur->LaborCharges}} PKR</td>
												</tr>
													<td colspan="2" style="color:#17365D">Convance Charges</td>
													<td class="text-left">{{$pur->ConvanceCharges}} PKR</td>
												</tr>
												</tr>
													<td colspan="2" style="color:#17365D">Discount</td>
													<td class="text-left">{{$pur->Bill_discount}} PKR</td>
												</tr>
												<?php $netamount=round($subtotal + $pur->LaborCharges + $pur->ConvanceCharges - $pur->Bill_discount) ?>
												<tr class="h5">
													<td colspan="2" style="color:#17365D">Net Amount</td>
													<td class="text-left text-danger" style="font-weight:700">{{number_format($netamount)}} PKR</td>
												</tr>
											</tbody>
										</table>
									</div>
								<div>
							</div>

							<div class="text-end">
								<a onclick="window.location='{{ route('all-purchases2-paginate') }}'" class="btn btn-primary mt-2 mb-2"> <i class="fas fa-arrow-left"></i> Back</a>
								<a href="{{ route('print-purc2-invoice', $pur->Sale_inv_no) }}" class="btn btn-danger mt-2 mb-2" target="_blank"> <i class="fas fa-print"></i> Print</a>
							
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