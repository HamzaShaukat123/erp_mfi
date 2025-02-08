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
											<h2 class="h2 mt-0 mb-1" style="color:#17365D">Sale INVOICE NO:</h2>
											<h4 class="h4 m-0 text-dark font-weight-bold">{{$pur->prefix}}{{$pur->Sal_inv_no}}</h4>
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
													<span style="color:#17365D">Purchase Inv No: &nbsp </span>
													<span style="font-weight:400;color:black" class="value"> {{$pur->pur_against}}</span>
												</h4>
											</div>
										</div>
										<div class="col-md-5">
											<div class="bill-data">
												<h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
													<span style="color:#17365D">Dispatch From: &nbsp </span>
													<span style="font-weight:400;color:black" class="value"> {{$pur->disp_to}}</span>
												</h4>
												<h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
													<span style="color:#17365D">Name Of Person: &nbsp </span>
													<span style="font-weight:400;color:black" class="value"> {{$pur->Cash_name}}</span>
												</h4>
												
												<h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
													<span style="color:#17365D">Person Address: &nbsp </span>
													<span style="font-weight:400;color:black" class="value"> {{$pur->cash_Pur_address}}</span>
												</h4>

												<h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
													<a href="#" style="color:#53b21c" data-bs-toggle="modal" data-bs-target="#editBillModal">
														Bill No: &nbsp;
													</a>
													<span style="font-weight:400;color:black" class="value" id="billNoDisplay">{{ $pur->pur_ord_no }}</span>
												</h4>

												<h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
													<span style="color:#17365D">Remarks: &nbsp </span>
													<span style="font-weight:400;color:black" class="value"> {{$pur->Sales_Remarks}}</span>
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
										@foreach($pur2 as $key => $pur2_item)
										<tr>
											<td>{{$key+1}}</td>
											<td class="font-weight-semibold text-dark">{{$pur2_item->item_name}}</td>
											<td>{{$pur2_item->remarks}}</td>
											<td class="text-center">{{$pur2_item->Sales_qty2}}</td>
											<td class="text-center">{{$pur2_item->sales_price}}</td>
											<td class="text-center">{{$pur2_item->length}}</td>
											<td class="text-center">{{$pur2_item->discount}}</td>
											<td class="text-center">{{(($pur2_item->Sales_qty2 * $pur2_item->sales_price)+( ($pur2_item->Sales_qty2 * $pur2_item->sales_price) * ($pur2_item->discount/100))) * $pur2_item->length}}</td>
										</tr>
										<?php $subtotal=$subtotal+((($pur2_item->Sales_qty2 * $pur2_item->sales_price)+( ($pur2_item->Sales_qty2 * $pur2_item->sales_price) * ($pur2_item->discount/100))) * $pur2_item->length) ?>
										<?php $total_weight=$total_weight+($pur2_item->Sales_qty2*$pur2_item->weight_pc) ?>
										<?php $total_quantity=$total_quantity+ $pur2_item->Sales_qty2 ?>

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
								<a onclick="window.location='{{ route('all-sale2invoices-paginate') }}'" class="btn btn-primary mt-2 mb-2"> <i class="fas fa-arrow-left"></i> Back</a>
							
								<a class="btn btn-danger mt-2 mb-2 mb-1 mt-1 me-1 modal-with-zoom-anim ws-normal" onclick="setPrintId({{$pur->Sal_inv_no}})" href="#printModal"><i class="fas fa-print"></i> Print</a>
							</div>

						</div>

						<div id="printModal" class="zoom-anim-dialog modal-block modal-block-danger mfp-hide" style="max-width: 350px;">
							<form method="get" action="{{ route('print-sales2-invoice') }}" target="_blank" enctype="multipart/form-data">
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
											</select>
											<input type="hidden" name="print_sale2" id="printID" >
										</div>
									</div>
									<footer class="card-footer">
										<div class="row">
											<div class="col-md-12 text-end">
												<button type="submit" class="btn btn-danger">Print Invoice</button>
												<button type="button" class="btn btn-default modal-dismiss">Cancel</button>
												<a href="{{ route('all-sale2invoices-paginate') }}" class="btn btn-primary mt-2 mb-2">
													<i class="fas fa-arrow-left"></i> Back
												</a>
											</div>
										</div>
									</footer>
									
								</section>
							</form>
						</div>
					</section>
				</section>
			</div>
			</div>
		</section>
		
		<!-- Edit Bill Modal -->
		<div class="modal fade" id="editBillModal" tabindex="-1" aria-labelledby="editBillModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<header class="card-header">
                        <h2 class="card-title">Edit Bill Number</h2>
                    </header>
					<form action="{{ route('update-bill-number') }}" method="POST">
						@csrf
						<div class="modal-body">
							<div class="mb-3">
								<label for="billNumberInput" class="form-label">Bill Number</label>
								<input type="text" class="form-control" id="billNumberInput" name="pur_ord_no" value="{{ $pur->pur_ord_no }}" required>
							</div>
							<input type="hidden" name="pur3_id" value="{{ $pur->Sal_inv_no }}">
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
							<button type="submit" class="btn btn-primary">Save Changes</button>
						</div>
					</form>
					
				</div>
			</div>
		</div>


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