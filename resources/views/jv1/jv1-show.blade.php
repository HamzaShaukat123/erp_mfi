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
											<h2 class="h2 mt-0 mb-1" style="color:#17365D">Voucher NO:</h2>
											<h4 class="h4 m-0 text-dark font-weight-bold">JV1-{{$jv1->auto_lager}}</h4>
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
													<span style="font-weight:400;color:black" class="value"> {{\Carbon\Carbon::parse($jv1->date)->format('d-m-y')}}</span>
												</h4>
											</div>
										</div>
										<div class="col-md-5">
											<div class="bill-data">
												<h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
													<span style="color:#17365D">Remarks: &nbsp</span>
													<span style="font-weight:400;color:black" class="value"> {{$jv1->remarks}}</span>
												</h4>
											</div>
										</div>
									</div>
								</div>

								<table class="table table-responsive-md table-striped invoice-items" style="font-size: 18px;">
									<thead>
										<tr class="text-dark">
											<th width="40%" class="text-center font-weight-semibold" style="color:#17365D;">Account Debit</th>
											<th width="40%" class="text-center font-weight-semibold" style="color:#17365D;">Account Credit</th>
											<th width="20%" class="text-center font-weight-semibold" style="color:#17365D;">Amount</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td class="font-weight-semibold text-dark text-center">{{$jv1->debit_account}}</td>
											<td class="font-weight-semibold text-dark text-center">{{$jv1->credit_account}}</td>
											<td class="font-weight-semibold text-dark text-center">{{number_format($jv1->amount)}}</td>
										</tr>
									</tbody>
								</table>
								
								<div class="row">
									<div class="col-12 col-md-8">
										<h3 style="color:#17365D; text-decoration: underline;" id="numberInWords"></h3>
									</div>
									<div class="col-12 col-md-4">
										<div class="text-end">
											<a onclick="window.location='{{ route('all-jv1') }}'" class="btn btn-primary mt-2"> <i class="fas fa-arrow-left"></i> Back</a>
											<a href="{{ route('print-jv1', $jv1->auto_lager) }}" class="btn btn-danger mt-2" target="_blank"> <i class="fas fa-print"></i> Print</a>
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


	<script>
		
		var netAmount = <?php echo json_encode($jv1->amount); ?>;
		var words = convertCurrencyToWords(netAmount);
		document.getElementById('numberInWords').innerHTML = words;

	</script>
	
	
</html>