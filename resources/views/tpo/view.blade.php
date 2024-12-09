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
											<h2 class="h2 mt-0 mb-1" style="color:#17365D">PURCHASE ORDER NO:</h2>
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
												
											</div>
										</div>
										<div class="col-md-5">
											<div class="bill-data">
												<h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
													<span  style="color:#17365D">Purchase Inv No: &nbsp</span>
													<span style="font-weight:400;color:black" class="value"> {{$pur->sales_against}}</span>
												</h4>
												<h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
													<span style="color:#17365D">Remarks: &nbsp</span>
													<span style="font-weight:400;color:black" class="value"> {{$pur->Sales_Remarks}}</span>
												</h4>
											</div>
										</div>
									</div>
								</div>

								<div>
									@php($grand_total_quantity = 0)
									@php($grand_total_weight = 0)
									@php($grand_subtotal = 0)
								
									@php($grouped_pur2 = $pur2->groupBy('dispatch_to'))
								
									@foreach($grouped_pur2 as $dispatch_to => $items)
									
									@php($subtotal_quantity = 0)
									@php($subtotal_weight = 0)
									@php($subtotal = 0)
								
									<div class="bill-data">
										<h4 style="color:#17365D; font-weight:bold; font-size:20px;">
											Ship to:  
											<span style="color:rgb(224, 8, 8); font-size:20px;">
												{{ $dispatch_to }}
											</span>
										</h4>
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
										<tbody>
											@foreach($items as $key => $pur_item)
											<tr>
												<td>{{$key+1}}</td>
												<td class="font-weight-semibold text-dark">{{$pur_item->item_name}}</td>
												<td>{{$pur_item->remarks}}</td>
												<td class="text-center">{{$pur_item->Sales_qty2}}</td>
												<td class="text-center">{{$pur_item->sales_price}}</td>
												<td class="text-center">{{$pur_item->length}}</td>
												<td class="text-center">{{$pur_item->discount}}</td>
												<td class="text-center">
													{{ (($pur_item->Sales_qty2 * $pur_item->sales_price) + (($pur_item->Sales_qty2 * $pur_item->sales_price) * ($pur_item->discount / 100))) * $pur_item->length }}
												</td>
											</tr>
									
											@php($subtotal += (($pur_item->Sales_qty2 * $pur_item->sales_price) + (($pur_item->Sales_qty2 * $pur_item->sales_price) * ($pur_item->discount / 100))) * $pur_item->length)
											@php($subtotal_weight += $pur_item->Sales_qty2 * $pur_item->weight_pc)
											@php($subtotal_quantity += $pur_item->Sales_qty2)
											@endforeach
											
											<!-- Subtotal row -->
											<tr>
												<td colspan="2"></td>
												<td colspan="1" class="font-weight-bold" style="color:#17365D">Subtotal====></td> <!-- Center-aligned Subtotal====> -->
												<td class="text-center text-danger font-weight-bold">{{ $subtotal_quantity }} ({{ $subtotal_weight }})KGs</td> <!-- Subtotal Quantity and Weight combined -->
												<td colspan="3"></td>
												<td class="text-center text-danger font-weight-bold" >{{ $subtotal }}</td> <!-- Subtotal Amount -->
											</tr>
										</tbody>
									</table>
									@php($grand_total_quantity += $subtotal_quantity)
									@php($grand_total_weight += $subtotal_weight)
									@php($grand_subtotal += $subtotal)
									@endforeach
								
									<div class="row" style="justify-content: space-between">
										<div class="col-12 col-md-6">
											<table class="table h6 text-dark">
												<tbody>
													<tr class="b-top-0">
														<td colspan="2"  style="color:#17365D">Total Quantity</td>
														<td class="text-left">{{$grand_total_quantity}}</td>
													</tr>
													<tr>
														<td colspan="2"  style="color:#17365D">Total Weight(KGs)</td>
														<td class="text-left">{{$grand_total_weight}}</td>
													</tr>
												</tbody>
											</table>
											<h3 class="d-none d-md-block" style="color:#17365D; text-decoration: underline;" id="numberInWords"></h3>
										
											<h2 class="mb-0 h6 mb-1 text-dark font-weight-semibold d-none d-md-block">
												<span style="color:#17365D; font-size:20px; font-weight:bold; font-style:italic; text-decoration:underline;">
													Terms And Conditions:&nbsp
												</span><br><br>
												<span style="font-weight:400;color:rgb(224, 8, 8);white-space: pre-wrap;word-wrap: break-word;" class="value">{{ htmlspecialchars($pur->tc) }}</span>
											</h2>
										</div>
										<div class="col-12 col-md-4">
											<table class="table h6 text-dark">
												<tbody>
													<tr class="b-top-0">
														<td colspan="2"  style="color:#17365D" >Bill total</td>
														<td class="text-left">{{$grand_subtotal}}</td>
													</tr>
													<tr>
														<td colspan="2"  style="color:#17365D">Labour Charges</td>
														<td class="text-left">{{$pur->LaborCharges}} PKR</td>
													</tr>
														<td colspan="2"  style="color:#17365D">Convance Charges</td>
														<td class="text-left">{{$pur->ConvanceCharges}} PKR</td>
													</tr>
													</tr>
														<td colspan="2"  style="color:#17365D">Discount</td>
														<td class="text-left">{{$pur->Bill_discount}} PKR</td>
													</tr>
													<?php $netamount=round($grand_subtotal + $pur->LaborCharges + $pur->ConvanceCharges - $pur->Bill_discount) ?>
													<tr class="h5">
														<td colspan="2"  style="color:#17365D">Net Amount</td>
														<td class="text-left text-danger" style="font-weight:700">{{number_format($netamount)}} PKR</td>
													</tr>
												</tbody>
											</table>
										</div>
										<div class="col-12">
											<h3 class="d-block d-md-none" style="color:#17365D; text-decoration: underline;" id="numberInWords"></h3>
											<h2 class="mb-0 h6 mb-1 text-dark font-weight-semibold d-block d-md-none">
												<span style="color:#17365D; font-size:20px; font-weight:bold; font-style:italic; text-decoration:underline;">
													Terms And Conditions:&nbsp
												</span><br><br>
												<span style="font-weight:400;color:rgb(224, 8, 8);white-space: pre-wrap;word-wrap: break-word;" class="value">{{ htmlspecialchars($pur->tc) }}</span>
											</h2>
											<div class="text-end">
												<a onclick="window.location='{{ route('all-tpo') }}'" class="btn btn-primary mt-2 mb-2"> <i class="fas fa-arrow-left"></i> Back</a>
												<a class="btn btn-danger mt-2 mb-2 mb-1 mt-1 me-1 modal-with-zoom-anim ws-normal" onclick="setPrintId({{$pur->Sale_inv_no}})" href="#printModal"><i class="fas fa-print"></i> Print</a>
											</div>
										</div>		
									</div>
								</div>
							</div>

							<div id="printModal" class="zoom-anim-dialog modal-block modal-block-danger mfp-hide" style="max-width: 350px;">
								<form method="get" action="{{ route('print-tpo-invoice') }}" target="_blank" enctype="multipart/form-data">
									@csrf
									<section class="card">
										<header class="card-header">
											<h2 class="card-title">Select Print Format</h2>
										</header>
										<div class="card-body">
											<div class="modal-wrapper">
												<select data-plugin-selecttwo class="form-control select2-js" autofocus name="print_type" required>
													<option value="" disabled selected>Select Print Format</option>
													<option value="1" >Show All</option>
													<option value="2" >Exclude Item Length</option>
													<option value="3" >Only Quantity & Price</option>
													<option value="4" >Weight Calculation</option>
												</select>
												<input type="hidden" name="print_sale2" id="printID" >
											</div>
										</div>
										<footer class="card-footer">
											<div class="row">
												<div class="col-md-12 text-end">
													<button type="submit" class="btn btn-danger">Print</button>
													<button class="btn btn-default modal-dismiss">Cancel</button>
												</div>
											</div>
										</footer>
									</section>
								</form>
							</div>
						</div>
					</section>
				</section>
			</div>
		</section>
        @include('../layouts.footerlinks')
	</body>
	<script>
		var netAmount = <?php echo json_encode($netamount); ?>;
		var words = convertCurrencyToWords(netAmount);
		document.getElementById('numberInWords').innerHTML = words;


		function setPrintId(id){
			$('#printID').val(id);
		}
	</script>
	
	
</html>