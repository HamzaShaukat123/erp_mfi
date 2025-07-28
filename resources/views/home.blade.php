@include('layouts.header')
	<style>
		h3{
			margin-top: 7px;
			margin-bottom: 10px;
		}

		.icon-container {
			background-size: auto; /* Adjust the size of the icon to fit within the div */
			background-repeat: no-repeat; /* Ensure the icon doesn't repeat */
			background-position: right bottom; /* Align the icon to the center-right */
		}
		/* Initially hide the masked data */
		.masked-data {
			display: none;
		}

		/* When the switch is toggled ON, show the masked data and hide the actual data */
		.switch-off .actual-data {
			display: none;
		}

		.switch-off .masked-data {
			display: block;
		}
		.scrollable-div{
			height: 300px;
			overflow-y: auto;
			padding: 0px !important;
		}
		.scrollable-div2{
			height: 900px;
			overflow-y: auto;
			padding: 0px !important;
		}
		.sticky-tbl-header{
			position: sticky;
			top: 0;
			background-color: white;
		}
	</style>
	<body>
		<section class="body">
			<div class="inner-wrapper">
				@include('layouts.leftmenu')
				<section role="main" class="content-body">
					@include('layouts.homepageheader')
					<!-- start: page -->
					<div class="row home-cust-pad">
						<div style="display: flex;justify-content: space-between;">
							<h2 class="text-dark"><strong id="currentDate"></strong></h2>
							@if(session('user_role')==1 || session('user_role')==2)
								<div class="form-check form-switch">
									<input class="form-check-input" type="checkbox" id="ShowDatatoggleSwitch" onchange="handleToggleSwitch(this)" style="margin-top:30px">
								</div>
							@endif
						</div>
						@if(session('user_role')==1 || session('user_role')==2)
							<div class="col-12 col-md-3 mb-2">
								<section class="card card-featured-left card-featured-primary">
									<div class="card-body icon-container data-container" style="background-image: url('/assets/img/cheque-icon.png'); ">
										<h3 class="amount text-dark"><strong>Post Dated Cheques</strong></h3>
										@if(isset($pdc) && isset($pdc->Total_Balance))
											<h2 class="amount m-0 text-primary actual-data">
												<strong data-value="{{ $pdc->Total_Balance }}">0</strong>
												<span class="title text-end text-dark h6"> PKR</span>
											</h2>
											<h2 class="amount m-0 text-primary masked-data"><strong>******</strong></h2>
										@else
											<h2 class="amount m-0 text-primary actual-data">
												<strong data-value="0">0</strong>
												<span class="title text-end text-dark h6"> PKR</span>
											</h2>
											<h2 class="amount m-0 text-primary masked-data"><strong>******</strong></h2>
										@endif

										<div class="summary-footer">
											<a class="text-primary text-uppercase" href="#">View Details</a>
										</div>
									</div>
								</section>

								<section class="card card-featured-left card-featured-primary mt-3">
									<div class="card-body icon-container data-container" style="background-image: url('/assets/img/rec-icon.png'); ">
										<h3 class="amount text-dark"><strong>Total Receivables</strong></h3>
										@if(isset($receivables) && isset($receivables->total_balance))
											<h2 class="amount m-0 text-primary actual-data">
												<strong data-value="{{ $receivables->total_balance }}">0</strong>
												<span class="title text-end text-dark h6"> PKR</span>
											</h2>
											<h2 class="amount m-0 text-primary masked-data"><strong>******</strong></h2>
										@else
											<h2 class="amount m-0 text-primary actual-data">
												<strong data-value="0">0</strong>
												<span class="title text-end text-dark h6"> PKR</span>
											</h2>
											<h2 class="amount m-0 text-primary masked-data"><strong>******</strong></h2>
										@endif
										<div class="summary-footer">
											<a class="text-primary text-uppercase" href="#">View Details</a>
										</div>
									</div>
								</section>

								<section class="card card-featured-left card-featured-primary mt-3">
									<div class="card-body icon-container data-container" style="background-image: url('/assets/img/last-month-sale-icon.png'); ">
										<h3 class="amount text-dark"><strong>Last Month Sale</strong></h3>
										@if(isset($last_month_sale) && isset($last_month_sale->total_dr_amt) && isset($last_month_sale->total_weight))
											<h2 class="amount m-0 text-primary actual-data">
												<strong data-value="{{ $last_month_sale->total_dr_amt }}">0</strong>
												<span class="title text-end text-dark h6"> PKR</span></h2>
											<h2 class="amount m-0 text-primary masked-data"><strong>******</strong></h2>

											<h2 class="amount m-0 text-primary actual-data">
												<strong data-value="{{ $last_month_sale->total_weight }}">0</strong>
												<span class="title text-end text-dark h6"> M-Ton</span></h2>
											<h2 class="amount m-0 text-primary masked-data"><strong>******</strong></h2>
										@else
											<h2 class="amount m-0 text-primary actual-data">
												<strong data-value="0">0</strong>
												<span class="title text-end text-dark h6"> PKR</span></h2>
											<h2 class="amount m-0 text-primary masked-data">
												<strong>******</strong></h2>

											<h2 class="amount m-0 text-primary actual-data">
												<strong data-value="0">0</strong>
												<span class="title text-end text-dark h6"> M-Ton</span></h2>
											<h2 class="amount m-0 text-primary masked-data">
												<strong>******</strong>
											</h2>
										@endif

										<div class="summary-footer">
											<a class="text-primary text-uppercase" href="#">View Details</a>
										</div>
									</div>
								</section>

							</div>

							<div class="col-12 col-md-3 mb-2">
								<section class="card card-featured-left card-featured-danger">
									<div class="card-body icon-container data-container" style="background-image: url('/assets/img/bank-icon.png'); ">
										<h3 class="amount text-dark"><strong>Bank</strong></h3>
										@if(isset($banks) && isset($banks->Total_Balance))
											<h2 class="amount m-0 text-danger actual-data">
												<strong data-value="{{ $banks->Total_Balance }}">0</strong>
												<span class="title text-end text-dark h6"> PKR</span>
											</h2>
											<h2 class="amount m-0 text-danger masked-data"><strong>******</strong></h2>
										@else
											<h2 class="amount m-0 text-danger actual-data">
												<strong data-value="0">0</strong>
												<span class="title text-end text-dark h6"> PKR</span>
											</h2>
											<h2 class="amount m-0 text-danger masked-data"><strong>******</strong></h2>
										@endif

										<div class="summary-footer">
											<a class="text-primary text-uppercase" href="#">View Details</a>
										</div>
									</div>
								</section>

								<section class="card card-featured-left card-featured-danger mt-3">
									<div class="card-body icon-container data-container" style="background-image: url('/assets/img/pay-icon.png'); ">
										<h3 class="amount text-dark"><strong>Total Payables</strong></h3>
										@if(isset($payables) && isset($payables->total_balance))
											<h2 class="amount m-0 text-danger actual-data">
												<strong data-value="{{ $payables->total_balance }}">0</strong>
												<span class="title text-end text-dark h6"> PKR</span>
											</h2>
											<h2 class="amount m-0 text-danger masked-data"><strong>******</strong></h2>
										@else
											<h2 class="amount m-0 text-danger actual-data">
												<strong data-value="0">0</strong>
												<span class="title text-end text-dark h6"> PKR</span>
											</h2>
											<h2 class="amount m-0 text-danger masked-data"><strong>******</strong></h2>
										@endif
										<div class="summary-footer">
											<a class="text-primary text-uppercase" href="#">View Details</a>
										</div>
									</div>
								</section>

								<section class="card card-featured-left card-featured-danger mt-3">
									<div class="card-body icon-container data-container" style="background-image: url('/assets/img/last-month-pur-icon.png'); ">
										<h3 class="amount text-dark"><strong>Last Month Purchase</strong></h3>
										@if(isset($last_month_purchase) && isset($last_month_purchase->total_cr_amt) && isset($last_month_purchase->total_weight))
										<h2 class="amount m-0 text-danger actual-data">
											<strong data-value="{{ $last_month_purchase->total_cr_amt }}">0</strong>
											<span class="title text-end text-dark h6"> PKR</span></h2>
										<h2 class="amount m-0 text-danger masked-data"><strong>******</strong></h2>

										<h2 class="amount m-0 text-danger actual-data">
											<strong data-value="{{ $last_month_purchase->total_weight }}">0</strong>
											<span class="title text-end text-dark h6"> M-Ton</span></h2>
										<h2 class="amount m-0 text-danger masked-data"><strong>******</strong></h2>
										@else
											<h2 class="amount m-0 text-danger actual-data">
												<strong data-value="0">0</strong>
												<span class="title text-end text-dark h6"> PKR</span></h2>
											<h2 class="amount m-0 text-danger masked-data">
												<strong>******</strong></h2>

											<h2 class="amount m-0 text-danger actual-data">
												<strong data-value="0">0</strong>
												<span class="title text-end text-dark h6"> M-Ton</span></h2>
											<h2 class="amount m-0 text-danger masked-data">
												<strong>******</strong>
											</h2>
										@endif

										<div class="summary-footer">
											<a class="text-primary text-uppercase" href="#">View Details</a>
										</div>
									</div>
								</section>
							</div>

							<div class="col-12 col-md-2 mb-2">
								<section class="card card-featured-left card-featured-success">
									<div class="card-body icon-container data-container" style="background-image: url('/assets/img/cash-icon.png'); ">
										<h3 class="amount text-dark"><strong>Cash</strong></h3>
										@if(isset($cash) && isset($cash->Total_Balance))
										<h2 class="amount m-0 text-success actual-data">
											<strong data-value="{{ $cash->Total_Balance }}">0</strong>
											<span class="title text-end text-dark h6"> PKR</span>
										</h2>
										<h2 class="amount m-0 text-success masked-data"><strong>******</strong></h2>
										@else
											<h2 class="amount m-0 text-success actual-data">
												<strong data-value="0">0</strong>
												<span class="title text-end text-dark h6"> PKR</span>
											</h2>
											<h2 class="amount m-0 text-success masked-data"><strong>******</strong></h2>
										@endif
										<div class="summary-footer">
											<a class="text-primary text-uppercase" href="#">View Details</a>
										</div>
									</div>
								</section>
								
								<section class="card card-featured-left card-featured-success mb-2 mt-3">
									<div class="card-body icon-container data-container" style="background-image: url('/assets/img/long-term-loan-icon.png'); ">
										<h3 class="amount text-dark"><strong>Long Term Loan</strong></h3>

										@if(isset($long_term_loan) && isset($long_term_loan->total_balance))
										<h2 class="amount m-0 text-success actual-data">
											<strong data-value="{{ $long_term_loan->total_balance }}">0</strong>
											<span class="title text-end text-dark h6"> PKR</span>
										</h2>
										<h2 class="amount m-0 text-success masked-data"><strong>******</strong></h2>
										@else
											<h2 class="amount m-0 text-success actual-data">
												<strong data-value="0">0</strong>
												<span class="title text-end text-dark h6"> PKR</span>
											</h2>
											<h2 class="amount m-0 text-success masked-data"><strong>******</strong></h2>
										@endif

										<div class="summary-footer">
											<a class="text-primary text-uppercase" href="#">View Details</a>
										</div>
									</div>
								</section>

								<section class="card card-featured-left card-featured-success mt-3">
									<div class="card-body icon-container data-container" style="background-image: url('/assets/img/cash-in-icon.png'); ">
										<h3 class="amount text-dark"><strong>Last Month Cash In</strong></h3>
										@if(isset($long_term_loan) && isset($long_term_loan->total_balance))
										<h2 class="amount m-0 text-success actual-data">
											<strong data-value="{{ $long_term_loan->total_balance }}">0</strong>
											<span class="title text-end text-dark h6"> PKR</span>
										</h2>
										<h2 class="amount m-0 text-success masked-data"><strong>******</strong></h2>
										@else
											<h2 class="amount m-0 text-success actual-data">
												<strong data-value="0">0</strong>
												<span class="title text-end text-dark h6"> PKR</span>
											</h2>
											<h2 class="amount m-0 text-success masked-data"><strong>******</strong></h2>
										@endif
										<div class="summary-footer">
											<a class="text-primary text-uppercase" href="#">View Details</a>
										</div>
									</div>
								</section>
							</div>

							<div class="col-12 col-md-2 mb-2">
								<section class="card card-featured-left card-featured-tertiary mb-2">
									<div class="card-body icon-container data-container" style="background-image: url('/assets/img/fc-icon.png'); ">
										<h3 class="amount text-dark"><strong>Foreign Currency</strong></h3>

										@if(isset($foreign) && isset($foreign->Total_Balance))
										<h2 class="amount m-0 text-tertiary actual-data">
											<strong data-value="{{ $foreign->Total_Balance }}">0</strong>
											<span class="title text-end text-dark h6"> PKR</span>
										</h2>
										<h2 class="amount m-0 text-tertiary masked-data"><strong>******</strong></h2>
										@else
											<h2 class="amount m-0 text-tertiary actual-data">
												<strong data-value="0">0</strong>
												<span class="title text-end text-dark h6"> PKR</span>
											</h2>
											<h2 class="amount m-0 text-tertiary masked-data"><strong>******</strong></h2>
										@endif

										<div class="summary-footer">
											<a class="text-primary text-uppercase" href="#">View Details</a>
										</div>
									</div>
								</section>

								<section class="card card-featured-left card-featured-tertiary mt-3">
									<div class="card-body icon-container data-container" style="background-image: url('/assets/img/short-term-loan-icon.png'); ">
										<h3 class="amount text-dark"><strong>Short Term Loan</strong></h3>
										@if(isset($short_term_loan) && isset($short_term_loan->total_balance))
										<h2 class="amount m-0 text-tertiary actual-data">
											<strong data-value="{{ $short_term_loan->total_balance }}">0</strong>
											<span class="title text-end text-dark h6"> PKR</span>
										</h2>
										<h2 class="amount m-0 text-tertiary masked-data"><strong>******</strong></h2>
										@else
											<h2 class="amount m-0 text-tertiary actual-data">
												<strong data-value="0">0</strong>
												<span class="title text-end text-dark h6"> PKR</span>
											</h2>
											<h2 class="amount m-0 text-tertiary masked-data"><strong>******</strong></h2>
										@endif
										<div class="summary-footer">
											<a class="text-primary text-uppercase" href="#">View Details</a>
										</div>
									</div>
								</section>

								<section class="card card-featured-left card-featured-tertiary mt-3">
									<div class="card-body icon-container data-container" style="background-image: url('/assets/img/cash-out-icon.png'); ">
										<h3 class="amount text-dark"><strong>Last Month Cash Out</strong></h3>
										@if(isset($short_term_loan) && isset($short_term_loan->total_balance))
										<h2 class="amount m-0 text-tertiary actual-data">
											<strong data-value="{{ $short_term_loan->total_balance }}">0</strong>
											<span class="title text-end text-dark h6"> PKR</span>
										</h2>
										<h2 class="amount m-0 text-tertiary masked-data"><strong>******</strong></h2>
										@else
											<h2 class="amount m-0 text-tertiary actual-data">
												<strong data-value="0">0</strong>
												<span class="title text-end text-dark h6"> PKR</span>
											</h2>
											<h2 class="amount m-0 text-tertiary masked-data"><strong>******</strong></h2>
										@endif
										<div class="summary-footer">
											<a class="text-primary text-uppercase" href="#">View Details</a>
										</div>
									</div>
								</section>
							</div>

							<div class="col-12 col-md-2 mb-2">
								<section class="card card-featured-left card-featured-success">
									<div class="card-body icon-container data-container" style="background-image: url('/assets/img/all-user-icon.png');background-position: top right;background-size: 44%;">
										<h3 class="amount text-dark"><strong>Active Users</strong></h3>

										<table class="table table-responsive-md table-striped mb-0">
											<thead>
												<tr>
													<th>Name</th>
													<th>Role</th>
												</tr>
											</thead>
											<tbody>
												@foreach ($login_users as $key => $row)
												<tr>
													<td>{{$row->user_name}}</td>
													<td>{{$row->user_role}}</td>
												</tr>
												@endforeach
											</tbody>
										</table>
										<div class="summary-footer text-end mt-1">
											<a class="text-primary text-uppercase" href="{{ route('all-users')}}">View All</a>
										</div>
									</div>
								</section>
							</div>	
						@endif
					
						<!-- summaries ends here -->
						<div class="tabs mt-3">
							<ul class="nav nav-tabs">
								<li class="nav-item">
									<a class="nav-link nav-link-dashboard-tab" data-bs-target="#PENDING_INVOICES" href="#PENDING_INVOICES" data-bs-toggle="tab">Pending Invoices </a>
								</li>
								<li class="nav-item">
									<a class="nav-link nav-link-dashboard-tab" data-bs-target="#BILL_NOT_RECVD" href="#BILL_NOT_RECVD" data-bs-toggle="tab">Bill Not Recieved</a>
								</li>
								@if(session('user_role')==1 || session('user_role')==2)
								<li class="nav-item">
									<a class="nav-link nav-link-dashboard-tab" data-bs-target="#HR" href="#HR" data-bs-toggle="tab">HR</a>
								</li>
								<li class="nav-item">
									<a class="nav-link nav-link-dashboard-tab" data-bs-target="#IIL" href="#IIL" data-bs-toggle="tab">IIL Karachi</a>
								</li>
								<li class="nav-item">
									<a class="nav-link nav-link-dashboard-tab" data-bs-target="#IILSINDH" href="#IILSINDH" data-bs-toggle="tab">IIL Sindh</a>
								</li>
								<li class="nav-item">
									<a class="nav-link nav-link-dashboard-tab" data-bs-target="#GARDER" href="#GARDER" data-bs-toggle="tab">Garder</a>
								</li>
								<li class="nav-item">
									<a class="nav-link nav-link-dashboard-tab" data-bs-target="#ITEM_OF_MONTH" href="#ITEM_OF_MONTH" data-bs-toggle="tab">Item Of The Month</a>
								</li>
								<li class="nav-item">
									<a class="nav-link nav-link-dashboard-tab" data-bs-target="#ANNUAL" href="#ANNUAL" data-bs-toggle="tab">Annual</a>
								</li>
								@endif
								<li class="nav-item">
									<a class="nav-link nav-link-dashboard-tab" data-bs-target="#UV" href="#UV" data-bs-toggle="tab">Unadjusted Vouchers</a>
								</li>
								<li class="nav-item">
									<a class="nav-link nav-link-dashboard-tab" data-bs-target="#OVER_DUES" href="#OVER_DUES" data-bs-toggle="tab">Over Dues</a>
								</li>
								<li class="nav-item">
									<a class="nav-link nav-link-dashboard-tab" data-bs-target="#OVER_DAYS" href="#OVER_DAYS" data-bs-toggle="tab">Over Days</a>
								</li>
								<li class="nav-item">
									<a class="nav-link nav-link-dashboard-tab" data-bs-target="#PDC" href="#PDC" data-bs-toggle="tab">PDC</a>
								</li>
							</ul>
							<div class="tab-content">
								<div id="PENDING_INVOICES" class="tab-pane">
									<div class="row form-group pb-3">
										<!-- First Pair: Sale 1 Not Final and Purchase 1 Not Final -->
										<div class="col-12 col-md-6 mb-3 d-flex">
											<section class="card flex-fill">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>
													<h2 class="card-title">Sale 1 Not Final</h2>
												</header>
												<div class="card-body scrollable-div">
													<table class="table table-responsive-md table-striped mb-0">
														<thead class="sticky-tbl-header">
															<tr>
																<th>Invoice#</th>
																<th class="text-center">Date</th>
																<th>Bill#</th>
																<th>Account Name</th>
																<th>Name Of Person</th>
																<th>Remarks</th>
															</tr>
														</thead>
														<tbody id="Sale1NotTable" class="table-body-scroll">
															<!-- Table rows will be populated dynamically -->
														</tbody>
													</table>
												</div>
											</section>
										</div>
										<div class="col-12 col-md-6 mb-3 d-flex">
											<section class="card flex-fill">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>
													<h2 class="card-title">Purchase 1 Not Final</h2>
												</header>
												<div class="card-body scrollable-div">
													<table class="table table-responsive-md table-striped mb-0">
														<thead class="sticky-tbl-header">
															<tr>
																<th>Invoice#</th>
																<th class="text-center">Date</th>
																<th>Account Name</th>
																<th>Name Of Person</th>
																<th>Remarks</th>
															</tr>
														</thead>
														<tbody id="Pur1NotTable" class="table-body-scroll">
															<!-- Table rows will be populated dynamically -->
														</tbody>
													</table>
												</div>
											</section>
										</div>
										<!-- Second Pair:-->
										<div class="col-12 col-md-6 mb-3 d-flex">
											<section class="card flex-fill">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>
													<h2 class="card-title">Pending Sale Against Pur 2</h2>
												</header>
												<div class="card-body scrollable-div">
													<table class="table table-responsive-md table-striped mb-0">
														<thead class="sticky-tbl-header">
															<tr>
																<th>Invoice#</th>
																<th class="text-center">Date</th>
																<th>Company Name</th>
																<th>Customer Name</th>
																<th>Name Of Person</th>
															</tr>
														</thead>
														<tbody id="PendingSaleAgainstPur2Table" class="table-body-scroll">
															<!-- Table rows will be populated dynamically -->
														</tbody>
													</table>
												</div>
											</section>
										</div>
										<div class="col-12 col-md-6 mb-3 d-flex">
											<section class="card flex-fill">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>
													<h2 class="card-title">Pending Sale Against Godwon</h2>
												</header>
												<div class="card-body scrollable-div">
													<table class="table table-responsive-md table-striped mb-0">
														<thead class="sticky-tbl-header">
															<tr>
																<th>Stock Out#</th>
																<th class="text-center">Date</th>
																<th>Gate Pass#</th>
																<th>Account Name</th>
																<th>Name Of Person</th>
																<th>Item Type</th>
															</tr>
														</thead>
														<tbody id="PendingSaleAgainstGodwonTable" class="table-body-scroll">
															<!-- Table rows will be populated dynamically -->
														</tbody>
													</table>
												</div>
											</section>
										</div>
									</div>
									<!-- Third Pair:  -->
									<div class="row">
										<div class="col-12 col-md-6 mb-3 d-flex">
											<section class="card flex-fill">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>
													<h2 class="card-title">Sale 2 Not Final</h2>
												</header>
												<div class="card-body scrollable-div">
													<table class="table table-responsive-md table-striped mb-0">
														<thead class="sticky-tbl-header">
															<tr>
																<th>Invoice#</th>
																<th class="text-center">Date</th>
																<th>Pur Inv#</th>
																<th>Account Name</th>
																<th>Name Of Person</th>
																<th>Remarks</th>
															</tr>
														</thead>
														<tbody id="Sale2NotTable" class="table-body-scroll">
															<!-- Table rows will be populated dynamically -->
														</tbody>
													</table>
												</div>
											</section>
										</div>
									</div>
								</div>

								<div id="BILL_NOT_RECVD" class="tab-pane">
									<div class="row form-group pb-3">
										
										<div class="col-12 col-md-6 mb-3 d-flex">
											<section class="card flex-fill">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>
													<h2 class="card-title">Bill Not Received</h2>
												</header>
												<div class="card-body scrollable-div">
													<table class="table table-responsive-md table-striped mb-0">
														<thead class="sticky-tbl-header">
															<tr>
																<th>Invoice#</th>
																<th class="text-center">Date</th>
																<th>Bill#</th>
																<th>Name Of Person</th>
																<th>Bill Amount</th>
																<th>Received Amount</th>
																<th>Balance Amount</th>
															</tr>
														</thead>
														<tbody id="BillNotRECVDTable" class="table-body-scroll">
															<!-- Table rows will be populated dynamically -->
														</tbody>
													</table>
												</div>
											</section>
										</div>
									</div>
								</div>
								
								<div id="HR" class="tab-pane">
									<div class="row form-group pb-3">
										<!-- Category Sale -->
										<div class="col-12 col-md-5 mb-3">
											<section class="card">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>

													<h2 class="card-title">Mill Wise HR Pipe Purchase</h2>
												</header>
												<div class="card-body">
													<canvas id="top5CustomerPerformance" style="height: 353px;width: 600px;"></canvas>
												</div>
											</section>
										</div>

										<div class="col-12 col-md-3 mb-3">
											<section class="card">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>

													<h2 class="card-title">Monthly Tonage</h2>
												</header>
												<div class="card-body">
													<canvas id="MonthlyTonage"></canvas>
												</div>
											</section>
										</div>

										<div class="col-12 col-md-4 mb-3">
											<section class="card">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>

													<h2 class="card-title">Monthly Tonage Of Customer From Purchase 2</h2>
												</header>
												<div class="card-body">
													<div class="row mb-2">
														<div class="col-lg-10">
															<div class="form-group">
															
																<select data-plugin-selecttwo class="form-control select2-js" id="hr_monthly_tonage_of_coa" name="account_name" required onchange="getMonthlyTonageOfCustomer()">
																	<option value="" disabled selected>Select Account</option>
																	@foreach($coa as $key => $row)	
																		<option value="{{$row->ac_code}}">{{$row->ac_name}}</option>
																	@endforeach
																</select>
															</div>
														</div>
														<div class="col-lg-2">
															<a class="btn btn-primary" style="padding: 0.5rem 0.6rem;" onclick="getMonthlyTonageOfCustomer()"><i class="fa fa-filter"></i></a>
														</div>
													</div>
													<div class="scrollable-div">
														<table class="table table-responsive-md table-striped mb-0">
															<thead class="sticky-tbl-header">
																<tr>
																	<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Company Name</font></font></th>
																	<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Tonage</font></font></th>
																</tr>
															</thead>
															<tbody id="HRMonthlyTonageOfCust">
																
															</tbody>
														</table>
													</div>
												</div>
											</section>
										</div>

										<div class="mb-3 text-end">
											<div class="form-group" style="display: inline-block">
												<input type="month" class="form-control" id="filterHR" value="{{ date('Y-m') }}" onchange="getTabData()">
											</div>
											<a class="btn btn-primary" style="padding: 0.5rem 0.6rem;" onclick="getTabData()"><i class="fa fa-filter"></i></a>
										</div>

										<div class="col-12 col-md-3 mb-3">
											<section class="card">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>

													<h2 class="card-title">Steelex Purchase Summary</h2>
												</header>
												<div class="card-body scrollable-div">
													
													<table class="table table-responsive-md table-striped mb-0">
														<thead class="sticky-tbl-header">
															<tr>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Company Name</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Tonage</font></font></th>
															</tr>
														</thead>
														<tbody id="SteelexSaleTable">
															
														</tbody>
													</table>
												</div>
											</section>
										</div>

										<div class="col-12 col-md-3 mb-3">
											<section class="card">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>

													<h2 class="card-title">S.P.M Purchase Summary</h2>
												</header>
												<div class="card-body scrollable-div">
													
													<table class="table table-responsive-md table-striped mb-0">
														<thead class="sticky-tbl-header">
															<tr>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Company Name</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Tonage</font></font></th>
															</tr>
														</thead>
														<tbody id="SPMSaleTable">
															
														</tbody>
													</table>
												</div>
											</section>
										</div>

										<div class="col-12 col-md-3 mb-3">
											<section class="card">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>

													<h2 class="card-title">Mehboob Purchase Summary</h2>
												</header>
												<div class="card-body scrollable-div">
													
													<table class="table table-responsive-md table-striped mb-0">
														<thead class="sticky-tbl-header">
															<tr>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Company Name</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Tonage</font></font></th>
															</tr>
														</thead>
														<tbody id="MehboobSaleTable">
															
														</tbody>
													</table>
												</div>
											</section>
										</div>

										<div class="col-12 col-md-3 mb-3">
											<section class="card">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>

													<h2 class="card-title">GoDown Sale Summary</h2>
												</header>
												<div class="card-body scrollable-div">
													
													<table class="table table-responsive-md table-striped mb-0">
														<thead class="sticky-tbl-header">
															<tr>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Company Name</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Tonage</font></font></th>
															</tr>
														</thead>
														<tbody id="GodownSaleTable">
															
														</tbody>
													</table>
												</div>
											</section>
										</div>

										<div class="col-12 col-md-3 mb-3">
											<section class="card">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>

													<h2 class="card-title">Top 10 Customers Of Purchase 2</h2>
												</header>
												<div class="card-body scrollable-div">
													
													<table class="table table-responsive-md table-striped mb-0">
														<thead class="sticky-tbl-header">
															<tr>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Company Name</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Tonage</font></font></th>
															</tr>
														</thead>
														<tbody id="Top3Cus">
															
														</tbody>
													</table>
												</div>
											</section>
										</div>


										<div class="col-12 col-md-3 mb-3">
											<section class="card">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>

													<h2 class="card-title">Purchase 2 Summary</h2>
												</header>
												<div class="card-body scrollable-div">
													
													<table class="table table-responsive-md table-striped mb-0">
														<thead class="sticky-tbl-header">
															<tr>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Company Name</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Tonage</font></font></th>
															</tr>
														</thead>
														<tbody id="Pur2Summary">
															
														</tbody>
													</table>
												</div>
											</section>
										</div>
										
									</div>
								</div>
								<div id="IIL" class="tab-pane">
									<div class="row form-group pb-3">

										<div class="col-12 col-md-5 mb-3">
											<section class="card">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>

													<h2 class="card-title">Item Wise IIL Karachi Pipe Purchase</h2>
												</header>
												<div class="card-body">
													<canvas id="IILtop5CustomerPerformance" style="height: 353px;width: 600px;"></canvas>
												</div>
											</section>
										</div>

										<div class="col-12 col-md-3 mb-3">
											<section class="card">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>

													<h2 class="card-title">Monthly Tonage</h2>
												</header>
												<div class="card-body">
													<canvas id="IILMonthlyTonage"></canvas>
												</div>
											</section>
										</div>

										<div class="mb-3 text-end">
											<div class="form-group" style="display: inline-block">
												<input type="month" class="form-control" id="filterIIL" value="{{ date('Y-m') }}" onchange="getTabData()">
											</div>
											<a class="btn btn-primary" style="padding: 0.5rem 0.6rem;" onclick="getTabData()"><i class="fa fa-filter"></i></a>
										</div>

										<div class="col-12 col-md-3 mb-3">
											<section class="card">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>

													<h2 class="card-title">CRC Purchase Summary</h2>
												</header>
												<div class="card-body scrollable-div">
													
													<table class="table table-responsive-md table-striped mb-0">
														<thead class="sticky-tbl-header">
															<tr>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Company Name</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Tonage</font></font></th>
															</tr>
														</thead>
														<tbody id="CRCSaleTable">
															
														</tbody>
													</table>
												</div>
											</section>
										</div>

										<div class="col-12 col-md-3 mb-3">
											<section class="card">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>

													<h2 class="card-title">HRS Purchase Summary</h2>
												</header>
												<div class="card-body scrollable-div">
													
													<table class="table table-responsive-md table-striped mb-0">
														<thead class="sticky-tbl-header">
															<tr>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Company Name</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Tonage</font></font></th>
															</tr>
														</thead>
														<tbody id="HRSSaleTable">
															
														</tbody>
													</table>
												</div>
											</section>
										</div>

										<div class="col-12 col-md-3 mb-3">
											<section class="card">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>

													<h2 class="card-title">SS Eco 201 Purchase Summary</h2>
												</header>
												<div class="card-body scrollable-div">
													
													<table class="table table-responsive-md table-striped mb-0">
														<thead class="sticky-tbl-header">
															<tr>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Company Name</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Tonage</font></font></th>
															</tr>
														</thead>
														<tbody id="ECOSaleTable">
															
														</tbody>
													</table>
												</div>
											</section>
										</div>

										<div class="col-12 col-md-3 mb-3">
											<section class="card">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>

													<h2 class="card-title">SS Cosmo 304 Sale Summary</h2>
												</header>
												<div class="card-body scrollable-div">
													
													<table class="table table-responsive-md table-striped mb-0">
														<thead class="sticky-tbl-header">
															<tr>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Company Name</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Tonage</font></font></th>
															</tr>
														</thead>
														<tbody id="COSMOSaleTable">
															
														</tbody>
													</table>
												</div>
											</section>
										</div>

										
										
									</div>
								</div>

								<div id="IILSINDH" class="tab-pane">
									<div class="row form-group pb-3">

										<div class="col-12 col-md-5 mb-3">
											<section class="card">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>

													<h2 class="card-title">Item Wise IIL Sindh Pipe Purchase</h2>
												</header>
												<div class="card-body">
													<canvas id="IILSINDHtop5CustomerPerformance" style="height: 353px;width: 600px;"></canvas>
												</div>
											</section>
										</div>

										<div class="col-12 col-md-3 mb-3">
											<section class="card">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>

													<h2 class="card-title">Monthly Tonage</h2>
												</header>
												<div class="card-body">
													<canvas id="IILSINDHMonthlyTonage"></canvas>
												</div>
											</section>
										</div>

										<div class="mb-3 text-end">
											<div class="form-group" style="display: inline-block">
												<input type="month" class="form-control" id="filterIILSINDH" value="{{ date('Y-m') }}" onchange="getTabData()">
											</div>
											<a class="btn btn-primary" style="padding: 0.5rem 0.6rem;" onclick="getTabData()"><i class="fa fa-filter"></i></a>
										</div>

										<div class="col-12 col-md-3 mb-3">
											<section class="card">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>

													<h2 class="card-title">CRC Purchase Summary</h2>
												</header>
												<div class="card-body scrollable-div">
													
													<table class="table table-responsive-md table-striped mb-0">
														<thead class="sticky-tbl-header">
															<tr>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Company Name</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Tonage</font></font></th>
															</tr>
														</thead>
														<tbody id="CRCSaleSINDHTable">
															
														</tbody>
													</table>
												</div>
											</section>
										</div>

										<div class="col-12 col-md-3 mb-3">
											<section class="card">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>

													<h2 class="card-title">HRS Purchase Summary</h2>
												</header>
												<div class="card-body scrollable-div">
													
													<table class="table table-responsive-md table-striped mb-0">
														<thead class="sticky-tbl-header">
															<tr>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Company Name</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Tonage</font></font></th>
															</tr>
														</thead>
														<tbody id="HRSSaleSINDHTable">
															
														</tbody>
													</table>
												</div>
											</section>
										</div>

										<div class="col-12 col-md-3 mb-3">
											<section class="card">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>

													<h2 class="card-title">SS Eco 201 Purchase Summary</h2>
												</header>
												<div class="card-body scrollable-div">
													
													<table class="table table-responsive-md table-striped mb-0">
														<thead class="sticky-tbl-header">
															<tr>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Company Name</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Tonage</font></font></th>
															</tr>
														</thead>
														<tbody id="ECOSaleSINDHTable">
															
														</tbody>
													</table>
												</div>
											</section>
										</div>

										<div class="col-12 col-md-3 mb-3">
											<section class="card">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>

													<h2 class="card-title">SS Cosmo 304 Sale Summary</h2>
												</header>
												<div class="card-body scrollable-div">
													
													<table class="table table-responsive-md table-striped mb-0">
														<thead class="sticky-tbl-header">
															<tr>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Company Name</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Tonage</font></font></th>
															</tr>
														</thead>
														<tbody id="COSMOSaleSINDHTable">
															
														</tbody>
													</table>
												</div>
											</section>
										</div>

										
										
									</div>
								</div>

								<div id="GARDER" class="tab-pane">
									<div class="row form-group pb-3">

										<div class="mb-3 text-end">
											<div class="form-group" style="display: inline-block">
												<input type="month" class="form-control" id="filterGARDER" value="{{ date('Y-m') }}" onchange="getTabData()">
											</div>
											<a class="btn btn-primary" style="padding: 0.5rem 0.6rem;" onclick="getTabData()"><i class="fa fa-filter"></i></a>
										</div>

										<div class="col-12 col-md-4 mb-3">
											<section class="card">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>

													<h2 class="card-title">Garder / TR Purchase Summary</h2>
												</header>
												<div class="card-body scrollable-div">
													
													<table class="table table-responsive-md table-striped mb-0">
														<thead class="sticky-tbl-header">
															<tr>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Company Name</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Tonage</font></font></th>
															</tr>
														</thead>
														<tbody id="GarderPurTable">
															
														</tbody>
													</table>
												</div>
											</section>
										</div>

										<div class="col-12 col-md-4 mb-3">
											<section class="card">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>

													<h2 class="card-title">Garder / TR Sale Summary</h2>
												</header>
												<div class="card-body scrollable-div">
													
													<table class="table table-responsive-md table-striped mb-0">
														<thead class="sticky-tbl-header">
															<tr>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Customer Name</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Tonage</font></font></th>
															</tr>
														</thead>
														<tbody id="GarderSaleTable">
															
														</tbody>
													</table>
												</div>
											</section>
										</div>

										
										
										
									</div>
								</div>

								<div id="ITEM_OF_MONTH" class="tab-pane">
									<div class="row form-group pb-3">

										<div class="mb-3 text-end">
											<div class="form-group" style="display: inline-block">
												<input type="month" class="form-control" id="filterITEM_OF_MONTH" value="{{ date('Y-m') }}" onchange="getTabData()">
											</div>
											<a class="btn btn-primary" style="padding: 0.5rem 0.6rem;" onclick="getTabData()"><i class="fa fa-filter"></i></a>
										</div>

										<div class="col-12 col-md-3 mb-3">
											<section class="card">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>

													<h2 class="card-title">HR Item OF The Month By Weight</h2>
												</header>
												<div class="card-body scrollable-div">
													
													<table class="table table-responsive-md table-striped mb-0">
														<thead class="sticky-tbl-header">
															<tr>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Item Name</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Tonage</font></font></th>
															</tr>
														</thead>
														<tbody id="HRItemByWeightTable">
															
														</tbody>
													</table>
												</div>
											</section>
										</div>

										<div class="col-12 col-md-3 mb-3">
											<section class="card">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>

													<h2 class="card-title">HR Item Of The Month By Qty</h2>
												</header>
												<div class="card-body scrollable-div">
													
													<table class="table table-responsive-md table-striped mb-0">
														<thead class="sticky-tbl-header">
															<tr>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Item Name</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Quantity</font></font></th>
															</tr>
														</thead>
														<tbody id="HRItemByQtyTable">
															
														</tbody>
													</table>
												</div>
											</section>
										</div>
										<div class="col-12 col-md-3 mb-3">
											<section class="card">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>

													<h2 class="card-title">Water HR Item OF The Month By Weight</h2>
												</header>
												<div class="card-body scrollable-div">
													
													<table class="table table-responsive-md table-striped mb-0">
														<thead class="sticky-tbl-header">
															<tr>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Item Name</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Tonage</font></font></th>
															</tr>
														</thead>
														<tbody id="WTItemByWeightTable">
															
														</tbody>
													</table>
												</div>
											</section>
										</div>

										<div class="col-12 col-md-3 mb-3">
											<section class="card">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>

													<h2 class="card-title">Water HR Item Of The Month By Qty</h2>
												</header>
												<div class="card-body scrollable-div">
													
													<table class="table table-responsive-md table-striped mb-0">
														<thead class="sticky-tbl-header">
															<tr>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Item Name</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Quantity</font></font></th>
															</tr>
														</thead>
														<tbody id="WTItemByQtyTable">
															
														</tbody>
													</table>
												</div>
											</section>
										</div>
										<div class="col-12 col-md-3 mb-3">
											<section class="card">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>

													<h2 class="card-title">CRC Item OF The Month By Weight</h2>
												</header>
												<div class="card-body scrollable-div">
													
													<table class="table table-responsive-md table-striped mb-0">
														<thead class="sticky-tbl-header">
															<tr>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Item Name</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Tonage</font></font></th>
															</tr>
														</thead>
														<tbody id="CRCItemByWeightTable">
															
														</tbody>
													</table>
												</div>
											</section>
										</div>

										<div class="col-12 col-md-3 mb-3">
											<section class="card">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>

													<h2 class="card-title">CRC Item Of The Month By Qty</h2>
												</header>
												<div class="card-body scrollable-div">
													
													<table class="table table-responsive-md table-striped mb-0">
														<thead class="sticky-tbl-header">
															<tr>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Item Name</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Quantity</font></font></th>
															</tr>
														</thead>
														<tbody id="CRCItemByQtyTable">
															
														</tbody>
													</table>
												</div>
											</section>
										</div>
										<div class="col-12 col-md-3 mb-3">
											<section class="card">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>

													<h2 class="card-title">Eco 201 Item Of The Month By Weight</h2>
												</header>
												<div class="card-body scrollable-div">
													
													<table class="table table-responsive-md table-striped mb-0">
														<thead class="sticky-tbl-header">
															<tr>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Item Name</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Tonage</font></font></th>
															</tr>
														</thead>
														<tbody id="ECOItemByWeightTable">
															
														</tbody>
													</table>
												</div>
											</section>
										</div>

										<div class="col-12 col-md-3 mb-3">
											<section class="card">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>

													<h2 class="card-title">Eco 201 Item Of The Month By Qty</h2>
												</header>
												<div class="card-body scrollable-div">
													
													<table class="table table-responsive-md table-striped mb-0">
														<thead class="sticky-tbl-header">
															<tr>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Item Name</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Quantity</font></font></th>
															</tr>
														</thead>
														<tbody id="ECOItemByQtyTable">
															
														</tbody>
													</table>
												</div>
											</section>
										</div>
										<div class="col-12 col-md-3 mb-3">
											<section class="card">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>

													<h2 class="card-title">Cosmo 304 Item Of The Month By Weight</h2>
												</header>
												<div class="card-body scrollable-div">
													
													<table class="table table-responsive-md table-striped mb-0">
														<thead class="sticky-tbl-header">
															<tr>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Item Name</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Weight</font></font></th>
															</tr>
														</thead>
														<tbody id="COSMOItemByWeightTable">
															
														</tbody>
													</table>
												</div>
											</section>
										</div>
										<div class="col-12 col-md-3 mb-3">
											<section class="card">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>

													<h2 class="card-title">Cosmo 304 Item Of The Month By Qty</h2>
												</header>
												<div class="card-body scrollable-div">
													
													<table class="table table-responsive-md table-striped mb-0" >
														<thead class="sticky-tbl-header">
															<tr>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Item Name</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Quantity</font></font></th>
															</tr>
														</thead>
														<tbody id="COSMOItemByQtyTable">
															
														</tbody>
													</table>
												</div>
											</section>
										</div>

										
										
									</div>
								</div>

								<div id="ANNUAL" class="tab-pane">
									<div class="row form-group pb-3">
										
										<div class="mb-3 text-end">
											<div class="form-group" style="display: inline-block">
												<input type="month" class="form-control" id="filterANNUALFrom" value="{{ date('Y-01') }}" onchange="getTabData()">
											</div>
											
											<span> To </span>
											<div class="form-group" style="display: inline-block">
												<input type="month" class="form-control" id="filterANNUALTo" value="{{ date('Y-m') }}" onchange="getTabData()">
											</div>
											<a class="btn btn-primary" style="padding: 0.5rem 0.6rem;" onclick="getTabData()"><i class="fa fa-filter"></i></a>
										</div>
										

										<div class="col-12 col-md-6 mb-3">
											<section class="card">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>

													<h2 class="card-title">Annual Sale</h2>
												</header>
												<div class="card-body scrollable-div">
													
													<table class="table table-responsive-md table-striped mb-0">
														<thead class="sticky-tbl-header">
															<tr>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Sale Type</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Amount</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Tonage</font></font></th>
															</tr>
														</thead>
														<tbody id="AnnualSaleTable">
															
														</tbody>
													</table>
												</div>
											</section>
										</div>

										<div class="col-12 col-md-6 mb-3">
											<section class="card">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>

													<h2 class="card-title">Annual Purchase</h2>
												</header>
												<div class="card-body scrollable-div">
													
													<table class="table table-responsive-md table-striped mb-0">
														<thead class="sticky-tbl-header">
															<tr>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Pur Type</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Amount</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Tonage</font></font></th>
															</tr>
														</thead>
														<tbody id="AnnualPurTable">
															
														</tbody>
													</table>
												</div>
											</section>
										</div>
								
										
									</div>
								</div>

								<div id="UV" class="tab-pane">
									<div class="row form-group pb-3">

										<div class="col-12 col-md-6 mb-3">
											<section class="card">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>

													<h2 class="card-title">Unadjusted Sales Ageing Voucher</h2>
												</header>
												<div class="card-body scrollable-div">
													
													<table class="table table-responsive-md table-striped mb-0">
														<thead class="sticky-tbl-header">
															<tr>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">JV2-ID</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">JV2-Date</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Account Name</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Voucher Amount</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Adjusted Amount</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Unadjusted Amount</font></font></th>
															</tr>
														</thead>
														<tbody id="UVSaleTable">
															
														</tbody>
													</table>
												</div>
											</section>
										</div>

										<div class="col-12 col-md-6 mb-3">
											<section class="card">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>

													<h2 class="card-title">Unadjusted Purchase Ageing Voucher</h2>
												</header>
												<div class="card-body scrollable-div">
													
													<table class="table table-responsive-md table-striped mb-0">
														<thead class="sticky-tbl-header">
															<tr>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">JV2-ID</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">JV2-Date</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Account Name</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Voucher Amount</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Adjusted Amount</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Unadjusted Amount</font></font></th>
															</tr>
														</thead>
														<tbody id="UVPurTable">
															
														</tbody>
													</table>
												</div>
											</section>
										</div>

										<div class="col-12 col-md-6 mb-3">
											<section class="card">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>

													<h2 class="card-title">Wrong Sales Ageing Voucher</h2>
												</header>
												<div class="card-body scrollable-div">
													
													<table class="table table-responsive-md table-striped mb-0">
														<thead class="sticky-tbl-header">
															<tr>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">JV2-ID</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Sales-ID</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Account Name</font></font></th>
															</tr>
														</thead>
														<tbody id="WrongSaleTable">
															
														</tbody>
													</table>
												</div>
											</section>
										</div>

										<div class="col-12 col-md-6 mb-3">
											<section class="card">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>

													<h2 class="card-title">Wrong Purchase Ageing Voucher</h2>
												</header>
												<div class="card-body scrollable-div">
													
													<table class="table table-responsive-md table-striped mb-0">
														<thead class="sticky-tbl-header">
															<tr>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">JV2-ID</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Pur-ID</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Account Name</font></font></th>
															</tr>
														</thead>
														<tbody id="WrongPurTable">
															
														</tbody>
													</table>
												</div>
											</section>
										</div>
									</div>
								</div>

								<div id="OVER_DAYS" class="tab-pane">
									<div class="row form-group pb-3">

										<div class="col-12 col-md-6 mb-3">
											<section class="card">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>

													<h2 class="card-title">Over Days Sales Invoices</h2>
												</header>
												<div class="card-body scrollable-div2">
													
													<table class="table table-responsive-md table-striped mb-0">
														<thead class="sticky-tbl-header">
															<tr>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Account Name</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Invoice Id</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Invoice Date</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Bill Amount</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Recved Amount</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Remaing Amount</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Days Cross</font></font></th>
															</tr>
														</thead>
														<tbody id="ODSalesInvTable">
															
														</tbody>
													</table>
												</div>
											</section>
										</div>

										<div class="col-12 col-md-6 mb-3">
											<section class="card">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>

													<h2 class="card-title">Over Days Purchase Invoices</h2>
												</header>
												<div class="card-body scrollable-div2">
													
													<table class="table table-responsive-md table-striped mb-0">
														<thead class="sticky-tbl-header">
															<tr>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Account Name</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Invoice Id</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Invoice Date</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Bill Amount</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Recved Amount</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Remaing Amount</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Days Cross</font></font></th>
															</tr>
														</thead>
														<tbody id="ODPurInvTable">
															
														</tbody>
													</table>
												</div>
											</section>
										</div>

										
										
										
									</div>
								</div>

								<div id="OVER_DUES" class="tab-pane">
									<div class="row form-group pb-3">

										<div class="col-12 col-md-6 mb-3">
											<section class="card">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>

													<h2 class="card-title">Over Dues Account Receivables</h2>
												</header>
												<div class="card-body scrollable-div2">
													
													<table class="table table-responsive-md table-striped mb-0">
														<thead class="sticky-tbl-header">
															<tr>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Account Name</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Amount Limit</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Balance</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">UnPaid Amount</font></font></th>
															</tr>
														</thead>
														<tbody id="ODRecvAccTable">
															
														</tbody>
													</table>
												</div>
											</section>
										</div>

										<div class="col-12 col-md-6 mb-3">
											<section class="card">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>

													<h2 class="card-title">Over Dues Account Payables</h2>
												</header>
												<div class="card-body scrollable-div2">
													
													<table class="table table-responsive-md table-striped mb-0">
														<thead class="sticky-tbl-header">
															<tr>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Account Name</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Amount Limit</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Balance</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">UnPaid Amount</font></font></th>
															</tr>
														</thead>
														<tbody id="ODPayAccTable">
															
														</tbody>
													</table>
												</div>
											</section>
										</div>
									</div>
								</div>

								<div id="PDC" class="tab-pane">
									<div class="row form-group pb-3">

										<div class="col-12 col-md-6 mb-3">
											<section class="card">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>

													<h2 class="card-title">Received PDCs</h2>
												</header>
												<div class="card-body scrollable-div2">
													
													<table class="table table-responsive-md table-striped mb-0">
														<thead class="sticky-tbl-header">
															<tr>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">PDC ID</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Chq Date</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Account Name</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Detail</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Amount</font></font></th>
															</tr>
														</thead>
														<tbody id="RecPDCTable">
															
														</tbody>
													</table>
												</div>
											</section>
										</div>

										<div class="col-12 col-md-6 mb-3">
											<section class="card">
												<header class="card-header">
													<div class="card-actions">
														<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
													</div>

													<h2 class="card-title">Paid PDCs</h2>
												</header>
												<div class="card-body scrollable-div2">
													
													<table class="table table-responsive-md table-striped mb-0">
														<thead class="sticky-tbl-header">
															<tr>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">PDC ID</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Chq Date</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Account Name</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Detail</font></font></th>
																<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;text-align:center">Amount</font></font></th>
															</tr>
														</thead>
														<tbody id="PaidPDCTable">
															
														</tbody>
													</table>
													</table>
												</div>
											</section>
										</div>
									</div>
								</div>
								
							</div>
						</div>

					</div>
					<!-- end: page -->
				</section>
			</div>
		</section>
        @include('layouts.footerlinks')
	</body>
	<script>

		$(document).ready(function() {
			// Get current date and day
			const now = new Date();
			const day = getDaySuffix(now.getDate());
			const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
			const currentDate = now.toLocaleDateString(undefined, options);

			// Format the date as "Thursday, 5th December 2024"
			const formattedDate = `${now.toLocaleString('en-GB', { weekday: 'long' })}, ${day} ${now.toLocaleString('en-GB', { month: 'long' })} ${now.getFullYear()}`;

			// Update UI
			document.getElementById('currentDate').innerText = formattedDate;

			var toggleSwitch = document.getElementById('ShowDatatoggleSwitch');
            toggleSwitch.checked = true; // Set to "on" by default
            handleToggleSwitch(toggleSwitch); // Trigger the function
		});	

		function handleToggleSwitch(switchElement) {
			var dataContainers = document.querySelectorAll('.data-container');
			dataContainers.forEach(function(dataContainer) {
				if (!switchElement.checked) {
					dataContainer.classList.remove('switch-off');
					const elements = document.querySelectorAll(".actual-data strong");
					elements.forEach(element => {
						const totalBalance = parseFloat(element.dataset.value || 0); // Get value from data-value attribute or default to 0
						const duration = 2000; // Animation duration in milliseconds
						const frameRate = 60; // Frames per second
						const totalFrames = Math.round(duration / (1000 / frameRate));
						let frame = 0;
						
						if (totalBalance !== 0) {
							const counter = setInterval(() => {
								frame++;
								const progress = frame / totalFrames;
								const currentValue = Math.floor(progress * totalBalance);
								element.textContent = currentValue.toLocaleString();

								if (frame === totalFrames) {
									clearInterval(counter);
									element.textContent = totalBalance.toLocaleString();
								}
							}, 1000 / frameRate);
						} else {
							element.textContent = "0"; // Set to 0 if no value
						}
					});
				} else {
					dataContainer.classList.add('switch-off');
				}
			});
		}

		// Graph Chart for MILL WISE HR PIPE PURCHASE Started
		const mills = ['296', '82', '73','86'];
		const IILmills = ['1', '5', '6', '7', '8']; // Mill codes to include

		const colors = [
			'rgba(220, 53, 69, 1)',
			'rgba(0, 136, 204, 1)',
			'rgba(25, 135, 84, 1)',
			'rgba(219, 150, 81, 1)',
			'rgba(43, 170, 177, 1)',
			
		];

		function generateChartDatasets(data, mills, colors) {
			// Group data by 'dat3' field
			const groupedData = data.reduce((acc, item) => {
				if (!acc[item.dat3]) acc[item.dat3] = [];
				acc[item.dat3].push(item);
				return acc;
			}, {});

			// Get unique 'dat' values for chart labels
			const chartLabels = Object.keys(groupedData);

			// Initialize datasets
			const datasets = [];

			// Loop through each mill and create datasets
			mills.forEach((mill, index) => {
				const dataForMill = chartLabels.map(dat => {
					const millData = groupedData[dat]?.find(item => item.mill_code.toString() === mill);
					return millData ? millData.total_weight : 0;
				});

				// Get the mill name
				const millName = chartLabels
					.map(dat => groupedData[dat]?.find(item => item.mill_code.toString() === mill)?.mill_name)
					.find(name => name) || `Mill ${mill}`; // Default if not found

				// Add dataset for the mill
				datasets.push({
					label: millName,
					data: dataForMill,
					backgroundColor: colors[index] || 'rgba(0, 0, 0, 0.5)', // Default color if not enough colors provided
					stack: `Stack ${index}`,
				});
			});

			// Create dataset for "Others" (mills not in the mills array)
			const othersData = chartLabels.map(dat => {
				return groupedData[dat]?.reduce((acc, item) => {
					if (!mills.includes(item.mill_code.toString())) acc += item.total_weight;
					return acc;
				}, 0) || 0; // Default to 0 if no matching items
			});

			datasets.push({
				label: 'Others',
				data: othersData,
				backgroundColor: 'rgba(200, 200, 200, 1)', // Default color for "Others"
				stack: 'Stack Others',
			});

			return { datasets, chartLabels };
		}

		function generateChartDatasetsforIIL(data, mills, colors) {
			// Group data by 'dat' field
			const groupedData = data.reduce((acc, item) => {
				if (!acc[item.dat]) acc[item.dat] = [];
				acc[item.dat].push(item);
				return acc;
			}, {});

			// Get unique 'dat' values for chart labels
			const chartLabels = Object.keys(groupedData);

			// Initialize datasets
			const datasets = [];

			// Loop through each mill and create datasets
			mills.forEach((mill, index) => {
				const dataForMill = chartLabels.map(dat => {
					const millData = groupedData[dat]?.find(item => item.item_group_name.toString() === mill);
					return millData ? millData.total_weight : 0;
				});

				// Get the mill name
				const millName = chartLabels
					.map(dat => groupedData[dat]?.find(item => item.item_group_name.toString() === mill)?.mill_name)
					.find(name => name) || `${mill}`; // Default if not found

				// Add dataset for the mill
				datasets.push({
					label: millName,
					data: dataForMill,
					backgroundColor: colors[index] || 'rgba(0, 0, 0, 0.5)', // Default color if not enough colors provided
					stack: `Stack ${index}`,
				});
			});

			return { datasets, chartLabels };
		}
		// Graph Chart for MILL WISE HR PIPE PURCHASE Ended 

		// donut graph for Monthly Tonage Started 
		const MonthlyTonage = document.getElementById('MonthlyTonage');
		const IILMonthlyTonage = document.getElementById('IILMonthlyTonage');
		let monthlyTonageChart; // Declare a global variable to hold the chart instance
		let top5CustomerPerformanceChart;
		let IILmonthlyTonageChart; // Declare a global variable to hold the chart instance
		let IILtop5CustomerPerformanceChart;

		function groupByMillCode(mills, data) {
			const result = {
				labels: [], // To hold the labels for the chart
				data: [], // To hold the total_weight for each mill
				backgroundColor: [] // To hold the colors for the chart
			};

			// Initialize groups for each mill in mills array
			mills.forEach(mill => {
				result[mill] = { weight: 0, name: "", backgroundColor: "" };
			});

			// Add a group for "Others"
			result['Others'] = { weight: 0, name: "Others" };

			// Iterate through the data to group by mill_code and calculate total_weight
			data.forEach(item => {
				const millCode = item.mill_code.toString();
				const millName = mills.includes(millCode) ? item.mill_name : 'Others';

				// Aggregate the total_weight based on the mill_code or group it under "Others"
				if (millName === 'Others') {
					result['Others'].weight += item.total_weight;
					result['Others'].backgroundColor = 'rgba(200, 200, 200, 1)';

				} else {
					result[millCode].weight += item.total_weight;
					result[millCode].name = item.mill_name;
					if(item.mill_name=="STEELEX"){
						result[millCode].backgroundColor = 'rgba(220, 53, 69, 1)';
					}
					else if(item.mill_name=="S.P.M"){
						result[millCode].backgroundColor = 'rgba(0, 136, 204, 1)';
					}
					else if(item.mill_name=="MEHBOOB PIPE"){
						result[millCode].backgroundColor = 'rgba(25, 135, 84, 1)';
					}
				}
			});
			// Prepare the final chart data
			for (const key in result) {
				if (result[key].weight > 0) {
					result.labels.push(result[key].name);
					result.data.push(result[key].weight);
					result.backgroundColor.push(result[key].backgroundColor);
				}
			}

			return result;
		}

		function IILgroupByMillCode(itemGroups, colors, data) {
			const result = {
				labels: [], // To hold the labels for the chart
				data: [], // To hold the total_weight for each mill
				backgroundColor: [] // To hold the colors for the chart
			};

			// Initialize groups for each mill in mills array
			const groups = {};
			itemGroups.forEach(itemGroup => {
				groups[itemGroup] = { weight: 0, name: "", backgroundColor: "" };
			});

			// Iterate through the data to group by mill_code and calculate total_weight
			let index = 0;  // Initialize a counter for color assignment

			data.forEach(item => {
				const itemGroupName = item.item_group_name;
				
				groups[itemGroupName].weight += item.total_weight;
				groups[itemGroupName].name = item.item_group_name;
				groups[itemGroupName].backgroundColor = colors[index];  // Use the modulo operator to loop through the color array

				index++;  // Increment the counter for the next iteration				
			});

			// Prepare the final chart data
			for (const key in groups) {
				if (groups[key].weight > 0) {
					result.labels.push(groups[key].name);
					result.data.push(groups[key].weight);
					result.backgroundColor.push(groups[key].backgroundColor);
				}
			}

			return result;
		}

		// donut graph for Monthly Tonage Ended 

		// get Monthly Tonage Of Customer Started
		function getMonthlyTonageOfCustomer(){
			var month = document.getElementById('filterHR').value;
			var acc_name = document.getElementById('hr_monthly_tonage_of_coa').value;
			var table = document.getElementById('HRMonthlyTonageOfCust');

			while (table.rows.length > 0) {
                table.deleteRow(0);
            }

			$.ajax({
				type: "GET",
				url: '/dashboard-tabs/hr/monthlyTonageOfCustomer',
				data: {
					month: month,
					acc_name:acc_name,
				},
				beforeSend: function () {
                    $('#HRMonthlyTonageOfCust').html('<tr><td colspan="2" class="text-center">Loading Data Please Wait...</td></tr>');
                },
				success: function(result) {
					var rows = '';
					var totalWeight = 0; // Initialize total

					$.each(result, function (index, value) {
						var weight = value['weight'] ? parseFloat(value['weight']) : 0; // Convert to a number
    					totalWeight += weight; // Add to total
						rows += `<tr>
							<td>${value['company_name'] ? value['company_name'] : ''}</td>
                            <td>${weight ? weight : ''}</td>
 						</tr>`;
					});

					// Append a row for the total
					rows += `<tr>
						<td><strong>Total</strong></td>
						<td class="text-danger"><strong>${totalWeight.toFixed(2)}</strong></td> <!-- Format to 2 decimal places -->
					</tr>`;

					$('#HRMonthlyTonageOfCust').html(rows);
				},
				error: function() {
					alert("Error loading HR Monthly Tonage Of Customer Data");
				}
			});
		}

		// get Monthly Tonage Of Customer Ended

		// on tab changes flow
		document.querySelectorAll('.nav-link-dashboard-tab').forEach(tabLink => {
            tabLink.addEventListener('click', function() {
                tabId = this.getAttribute('data-bs-target');
                tabChanged(tabId);
            });
        });

		function tabChanged(tabId) {
			if (tabId === "#PENDING_INVOICES") {

				var table = document.getElementById('Sale1NotTable');
				while (table.rows.length > 0) {
					table.deleteRow(0);
				}

				var table = document.getElementById('Pur1NotTable');
				while (table.rows.length > 0) {
					table.deleteRow(0);
				}

				var table = document.getElementById('Sale2NotTable');
				while (table.rows.length > 0) {
					table.deleteRow(0);
				}

				var table = document.getElementById('PendingSaleAgainstPur2Table');
				while (table.rows.length > 0) {
					table.deleteRow(0);
				}

				var table = document.getElementById('PendingSaleAgainstGodwonTable');
				while (table.rows.length > 0) {
					table.deleteRow(0);
				}

				$.ajax({
					type: "GET",
					url: '/dashboard-tabs/pending-invoices',
					success: function(result) {
						var rows = '';

						$.each(result['sale1_not'], function (index, value) {
							rows += `<tr>
								<td>${value['prefix'] ? value['prefix'] : ''} ${value['Sal_inv_no'] ? value['Sal_inv_no'] : ''}</td>
								<td class="text-center">${value['sa_date'] ? moment(value['sa_date']).format('D-M-YY') : ''}</td>
								<td>${value['pur_ord_no'] ? value['pur_ord_no'] : ''}</td>
								<td>${value['account_name'] ? value['account_name'] : ''}</td>
								<td>${value['Cash_pur_name'] ? value['Cash_pur_name'] : ''}</td>
								<td>${value['Sales_remarks'] ? value['Sales_remarks'] : ''}</td>
							</tr>`;
						});

						$('#Sale1NotTable').html(rows);

						rows = '';

						$.each(result['pur1_not'], function (index, value) {
							rows += `<tr>
								<td>${value['prefix'] ? value['prefix'] : ''} ${value['pur_id'] ? value['pur_id'] : ''}</td>
								<td class="text-center">${value['pur_date'] ? moment(value['pur_date']).format('D-M-YY') : ''}</td>
								<td>${value['ac_name'] ? value['ac_name'] : ''}</td>
								<td>${value['cash_saler_name'] ? value['cash_saler_name'] : ''}</td>
								<td>${value['pur_remarks'] ? value['pur_remarks'] : ''}</td>
							</tr>`;
						});

						$('#Pur1NotTable').html(rows);

						rows = '';

						$.each(result['sale2_not'], function (index, value) {
							rows += `<tr>
								<td>${value['prefix'] ? value['prefix'] : ''} ${value['Sal_inv_no'] ? value['Sal_inv_no'] : ''}</td>
								<td class="text-center">${value['sa_date'] ? moment(value['sa_date']).format('D-M-YY') : ''}</td>
								<td>${value['pur_inv'] ? value['pur_inv'] : ''}</td>
								<td>${value['ac_name'] ? value['ac_name'] : ''}</td>
								<td>${value['name_of'] ? value['name_of'] : ''}</td>
								<td>${value['remarks'] ? value['remarks'] : ''}</td>
							</tr>`;
						});

						$('#Sale2NotTable').html(rows);


						rows = '';

						$.each(result['pending_sale_against_pur2'], function (index, value) {
							rows += `<tr>
								<td>${value['prefix'] ? value['prefix'] : ''} ${value['Sale_inv_no'] ? value['Sale_inv_no'] : ''}</td>
								<td class="text-center">${value['sa_date'] ? moment(value['sa_date']).format('D-M-YY') : ''}</td>
								<td>${value['ac_name'] ? value['ac_name'] : ''}</td>
								<td>${value['customer_name'] ? value['customer_name'] : ''}</td>
								<td>${value['Cash_pur_name'] ? value['Cash_pur_name'] : ''}</td>
							</tr>`;
						});

						$('#PendingSaleAgainstPur2Table').html(rows);

						rows = '';

						$.each(result['pending_sale_against_tstockout'], function (index, value) {
							rows += `<tr>
								<td>${value['prefix'] ? value['prefix'] : ''} ${value['Sal_inv_no'] ? value['Sal_inv_no'] : ''}</td>
								<td class="text-center">${value['sa_date'] ? moment(value['sa_date']).format('D-M-YY') : ''}</td>
								<td>${value['mill_gate'] ? value['mill_gate'] : ''}</td>
								<td>${value['ac_name'] ? value['ac_name'] : ''}</td>
								<td>${value['cash_pur_name'] ? value['cash_pur_name'] : ''}</td>
								<td>${value['item_type'] == 1 ? '<strong>Pipes</strong>' : (value['item_type'] == 2 ? '<strong>Garder / TR</strong>' : '')}</td>

							</tr>`;
						});

						$('#PendingSaleAgainstGodwonTable').html(rows);

						},
					error: function() {
						alert("Error loading Pending Invoices data");
					}
				});

			}
			else if (tabId === "#BILL_NOT_RECVD") {

				var table = document.getElementById('BillNotRECVDTable');
				while (table.rows.length > 0) {
					table.deleteRow(0);
				}

				$.ajax({
					type: "GET",
					url: '/dashboard-tabs/bill-not',
					success: function(result) {
						var rows = '';

						$.each(result['bill_not_recvd'], function (index, value) {
							rows += `<tr>
								<td>${value['sale_prefix'] ? value['sale_prefix'] : ''} ${value['Sal_inv_no'] ? value['Sal_inv_no'] : ''}</td>
								<td class="text-center">${value['bill_date'] ? moment(value['bill_date']).format('D-M-YY') : ''}</td>
								<td>${value['sales_pur_ord_no'] ? value['sales_pur_ord_no'] : ''} ${value['tsales_pur_ord_no'] ? value['tsales_pur_ord_no'] : ''}</td>
								<td>${value['Cash_pur_name'] ? value['Cash_pur_name'] : ''} ${value['Cash_name'] ? value['Cash_name'] : ''}</td>
								<td>${value['bill_amount'] ? value['bill_amount'] : ''}</td>
								<td>${value['ttl_jv_amt'] ? value['ttl_jv_amt'] : ''}</td>
								<td>${value['remaining_amount'] ? value['remaining_amount'] : ''}</td>
							</tr>`;
						});

						$('#BillNotRECVDTable').html(rows);

					

						},
					error: function() {
						alert("Error loading BILL NOT RECVD data");
					}
				});

			}
			else if(tabId=="#HR"){
				var table = document.getElementById('SteelexSaleTable');
				while (table.rows.length > 0) {
					table.deleteRow(0);
				}

				var table = document.getElementById('SPMSaleTable');
				while (table.rows.length > 0) {
					table.deleteRow(0);
				}

				var table = document.getElementById('MehboobSaleTable');
				while (table.rows.length > 0) {
					table.deleteRow(0);
				}

				var table = document.getElementById('GodownSaleTable');
				while (table.rows.length > 0) {
					table.deleteRow(0);
				}

				var table = document.getElementById('Top3Cus');
				while (table.rows.length > 0) {
					table.deleteRow(0);
				}

				var table = document.getElementById('Pur2Summary');
				while (table.rows.length > 0) {
					table.deleteRow(0);
				}

				var month = document.getElementById('filterHR').value;

				$.ajax({
					type: "GET",
					url: '/dashboard-tabs/hr',
					data: {
						month: month,
					},
					success: function(result) {

						const { datasets, chartLabels } = generateChartDatasets(result['dash_pur_2_summary_monthly_companywise'], mills, colors);

						if (top5CustomerPerformanceChart) {
							top5CustomerPerformanceChart.destroy();
						}

						const top5CustomerPerformance = document.getElementById('top5CustomerPerformance');

						top5CustomerPerformance.width = 600; // Set desired width
						top5CustomerPerformance.height = 353; // Set desired height

						// Create the new chart
						top5CustomerPerformanceChart = new Chart(top5CustomerPerformance, {
							type: 'bar',
							data: {
								labels: chartLabels, // 'dat' values as labels
								datasets: datasets,  // Dynamic datasets based on groupedData
							},
						});

						const groupedData = groupByMillCode(mills, result['dash_pur_2_summary_monthly_companywise_for_donut']);

						if (monthlyTonageChart) {
							monthlyTonageChart.destroy();
						}

						const chartData = {
							labels: groupedData.labels, // Set the labels directly here
							datasets: [
								{
									data: groupedData.data, // Extract total_weight values for each mill
									backgroundColor: groupedData.backgroundColor, // Assign background colors
								}
							]
						};
						
						// Create the doughnut chart
						monthlyTonageChart = new Chart(MonthlyTonage, {
							type: 'doughnut',
							data: chartData,
						});

						var rows = '';
						var totalWeight = 0; // Initialize total

						$.each(result['steelex'], function (index, value) {
							var weight = value['weight'] ? parseFloat(value['weight']) : 0; // Convert to a number
							totalWeight += weight; // Add to total
							rows += `<tr>
								<td>${value['ac_name'] ? value['ac_name'] : ''}</td>
								<td>${weight ? weight : ''}</td>
							</tr>`;
						});

						// Append a row for the total
						rows += `<tr>
							<td><strong>Total</strong></td>
							<td class="text-danger"><strong>${totalWeight.toFixed(2)}</strong></td> <!-- Format to 2 decimal places -->
						</tr>`;

						$('#SteelexSaleTable').html(rows);


						var rows = '';
						var totalWeight = 0; // Initialize total

						$.each(result['spm'], function (index, value) {
							var weight = value['weight'] ? parseFloat(value['weight']) : 0; // Convert to a number
							totalWeight += weight; // Add to total
							rows += `<tr>
								<td>${value['ac_name'] ? value['ac_name'] : ''}</td>
								<td>${weight ? weight : ''}</td>
							</tr>`;
						});

						// Append a row for the total
						rows += `<tr>
							<td><strong>Total</strong></td>
							<td class="text-danger"><strong>${totalWeight.toFixed(2)}</strong></td> <!-- Format to 2 decimal places -->
						</tr>`

						$('#SPMSaleTable').html(rows);

						var rows = '';
						var totalWeight = 0; // Initialize total

						$.each(result['mehboob'], function (index, value) {
							var weight = value['weight'] ? parseFloat(value['weight']) : 0; // Convert to a number
							totalWeight += weight; // Add to total
							rows += `<tr>
								<td>${value['ac_name'] ? value['ac_name'] : ''}</td>
								<td>${weight ? weight : ''}</td>
							</tr>`;
						});

						// Append a row for the total
						rows += `<tr>
							<td><strong>Total</strong></td>
							<td class="text-danger"><strong>${totalWeight.toFixed(2)}</strong></td> <!-- Format to 2 decimal places -->
						</tr>`

						$('#MehboobSaleTable').html(rows);

						var rows = '';
						var totalWeight = 0; // Initialize total

						$.each(result['godown'], function (index, value) {
							var weight = value['weight'] ? parseFloat(value['weight']) : 0; // Convert to a number
							totalWeight += weight; // Add to total
							rows += `<tr>
								<td>${value['ac_name'] ? value['ac_name'] : ''}</td>
								<td>${weight ? weight : ''}</td>
							</tr>`;
						});
						// Append a row for the total
						rows += `<tr>
							<td><strong>Total</strong></td>
							<td class="text-danger"><strong>${totalWeight.toFixed(2)}</strong></td> <!-- Format to 2 decimal places -->
						</tr>`
						$('#GodownSaleTable').html(rows);

						var rows = '';

						$.each(result['top_customers_of_pur2'], function (index, value) {
							rows += `<tr>
								<td>${value['ac_name'] ? value['ac_name'] : ''}</td>
								<td>${value['weight'] ? value['weight'] : ''}</td>
							</tr>`;
						});
						$('#Top3Cus').html(rows);

						var rows = '';
						var totalWeight = 0; // Initialize total

						$.each(result['pur2summary'], function (index, value) {
							var weight = value['weight'] ? parseFloat(value['weight']) : 0; // Convert to a number
							totalWeight += weight; // Add to total
							rows += `<tr>
								<td>${value['company_name'] ? value['company_name'] : ''}</td>
								<td>${weight ? weight : ''}</td>
							</tr>`;
						});
						// Append a row for the total
						rows += `<tr>
							<td><strong>Total</strong></td>
							<td class="text-danger"><strong>${totalWeight.toFixed(2)}</strong></td> <!-- Format to 2 decimal places -->
						</tr>`
						$('#Pur2Summary').html(rows);

						
					},
					error: function() {
						alert("Error loading HR data");
					}
				});
        	}
			else if(tabId=="#IIL"){
				var table = document.getElementById('CRCSaleTable');
				while (table.rows.length > 0) {
					table.deleteRow(0);
				}

				var table = document.getElementById('HRSSaleTable');
				while (table.rows.length > 0) {
					table.deleteRow(0);
				}

				var table = document.getElementById('ECOSaleTable');
				while (table.rows.length > 0) {
					table.deleteRow(0);
				}

				var table = document.getElementById('COSMOSaleTable');
				while (table.rows.length > 0) {
					table.deleteRow(0);
				}

				var month = document.getElementById('filterIIL').value;

				$.ajax({
					type: "GET",
					url: '/dashboard-tabs/iil',
					data: { month: month },
					success: function(result) {
						const itemGroupNames = result['item_group_name'].map(item => item.item_group_name);

						// Log the array with item_group_name values
						const { datasets, chartLabels } = generateChartDatasetsforIIL(result['dash_chart_for_item_group'], itemGroupNames, colors);

						if (IILtop5CustomerPerformanceChart) {
							IILtop5CustomerPerformanceChart.destroy();
						}

						const IILtop5CustomerPerformance = document.getElementById('IILtop5CustomerPerformance');

						IILtop5CustomerPerformance.width = 600; // Set desired width
						IILtop5CustomerPerformance.height = 353; // Set desired height

						// Create the new chart
						IILtop5CustomerPerformanceChart = new Chart(IILtop5CustomerPerformance, {
							type: 'bar',
							data: {
								labels: chartLabels, // 'dat' values as labels
								datasets: datasets,  // Dynamic datasets based on groupedData
							},
						});

						const groupedData = IILgroupByMillCode(itemGroupNames, colors , result['dash_chart_for_item_group_for_donut']);

						if (IILmonthlyTonageChart) {
							IILmonthlyTonageChart.destroy();
						}

						const IILchartData = {
							labels: groupedData.labels, // Set the labels directly here
							datasets: [
								{
									data: groupedData.data, // Extract total_weight values for each mill
									backgroundColor: groupedData.backgroundColor, // Assign background colors
								}
							]
						};

						// Create the doughnut chart
						IILmonthlyTonageChart = new Chart(IILMonthlyTonage, {
							type: 'doughnut',
							data: IILchartData,
						});						

						var rows = '';
						var totalWeight = 0; // Initialize total

						$.each(result['CRC'], function (index, value) {
							var weight = value['ttl_weight'] ? parseFloat(value['ttl_weight']) : 0; // Convert to a number
							totalWeight += weight; // Add to total
							rows += `<tr>
								<td>${value['company_name'] ? value['company_name'] : ''}</td>
								<td>${weight ? weight : ''}</td>
							</tr>`;
						});

						// Append a row for the total
						rows += `<tr>
							<td><strong>Total</strong></td>
							<td class="text-danger"><strong>${totalWeight.toFixed(2)}</strong></td> <!-- Format to 2 decimal places -->
						</tr>`;

						$('#CRCSaleTable').html(rows);

						rows = '';
						totalWeight = 0;

						$.each(result['HRS'], function (index, value) {
							var weight = value['ttl_weight'] ? parseFloat(value['ttl_weight']) : 0;
							totalWeight += weight;
							rows += `<tr>
								<td>${value['company_name'] ? value['company_name'] : ''}</td>
								<td>${weight ? weight : ''}</td>
							</tr>`;
						});

						rows += `<tr>
							<td><strong>Total</strong></td>
							<td class="text-danger"><strong>${totalWeight.toFixed(2)}</strong></td>
						</tr>`;

						$('#HRSSaleTable').html(rows);

						rows = '';
						totalWeight = 0;

						$.each(result['ECO'], function (index, value) {
							var weight = value['ttl_weight'] ? parseFloat(value['ttl_weight']) : 0;
							totalWeight += weight;
							rows += `<tr>
								<td>${value['company_name'] ? value['company_name'] : ''}</td>
								<td>${weight ? weight : ''}</td>
							</tr>`;
						});

						rows += `<tr>
							<td><strong>Total</strong></td>
							<td class="text-danger"><strong>${totalWeight.toFixed(2)}</strong></td>
						</tr>`;

						$('#ECOSaleTable').html(rows);

						rows = '';
						totalWeight = 0;

						$.each(result['COSMO'], function (index, value) {
							var weight = value['ttl_weight'] ? parseFloat(value['ttl_weight']) : 0;
							totalWeight += weight;
							rows += `<tr>
								<td>${value['company_name'] ? value['company_name'] : ''}</td>
								<td>${weight ? weight : ''}</td>
							</tr>`;
						});

						rows += `<tr>
							<td><strong>Total</strong></td>
							<td class="text-danger"><strong>${totalWeight.toFixed(2)}</strong></td>
						</tr>`;

						$('#COSMOSaleTable').html(rows);
					},
					error: function() {
						alert("Error loading IIL data");
					}
				});

        	}
			else if(tabId=="#GARDER"){

				var table = document.getElementById('GarderPurTable');
				while (table.rows.length > 0) {
					table.deleteRow(0);
				}

				var table = document.getElementById('GarderSaleTable');
				while (table.rows.length > 0) {
					table.deleteRow(0);
				}

				

				var month = document.getElementById('filterGARDER').value;

				$.ajax({
					type: "GET",
					url: '/dashboard-tabs/garder',
					data: { month: month },
					success: function(result) {

						var rows = '';
						var totalWeight = 0; // Initialize total

						$.each(result['garder_mill'], function (index, value) {
							var weight = value['weight'] ? parseFloat(value['weight']) : 0; // Convert to a number
							totalWeight += weight; // Add to total
							rows += `<tr>
								<td>${value['ac_name'] ? value['ac_name'] : ''}</td>
								<td>${weight ? weight : ''}</td>
							</tr>`;
						});

						// Append a row for the total
						rows += `<tr>
							<td><strong>Total</strong></td>
							<td class="text-danger"><strong>${totalWeight.toFixed(2)}</strong></td> <!-- Format to 2 decimal places -->
						</tr>`;

						$('#GarderPurTable').html(rows);

						rows = '';
						var totalWeight = 0; // Initialize total

						$.each(result['garder_customer'], function (index, value) {
							var weight = value['tt_weight'] ? parseFloat(value['tt_weight']) : 0; // Convert to a number
							totalWeight += weight; // Add to total
							rows += `<tr>
								<td>${value['ac_name'] ? value['ac_name'] : ''}</td>
								<td>${weight ? weight : ''}</td>
							</tr>`;
						});

						// Append a row for the total
						rows += `<tr>
							<td><strong>Total</strong></td>
							<td class="text-danger"><strong>${totalWeight.toFixed(2)}</strong></td> <!-- Format to 2 decimal places -->
						</tr>`;

						$('#GarderSaleTable').html(rows);


						
					},
					error: function() {
						alert("Error loading Garder data");
					}
				});

        	}
			else if (tabId === "#ITEM_OF_MONTH") {
				const clearTableRows = (tableIds) => {
					tableIds.forEach((tableId) => {
						const table = document.getElementById(tableId);
						while (table && table.rows.length > 0) {
							table.deleteRow(0);
						}
					});
				};

				clearTableRows([
					'HRItemByWeightTable',
					'HRItemByQtyTable',
					'CRCItemByWeightTable',
					'CRCItemByQtyTable',
					'ECOItemByWeightTable',
					'ECOItemByQtyTable',
					'COSMOItemByWeightTable',
					'COSMOItemByQtyTable'
				]);

				const month = document.getElementById('filterITEM_OF_MONTH').value;

				$.ajax({
					type: "GET",
					url: '/dashboard-tabs/item-of-the-month',
					data: { month: month },
					success: function (result) {
						const populateTable = (data, tableId, fields) => {
							let rows = '';
							data.forEach((item) => {
								rows += `<tr>
									<td>${item[fields[0]] || ''}</td>
									<td>${item[fields[1]] || ''}</td>
								</tr>`;
							});
							$(`#${tableId}`).html(rows);
						};

						populateTable(result.hrbyweight, 'HRItemByWeightTable', ['item_name', 'weight']);
						populateTable(result.hrbyqty, 'HRItemByQtyTable', ['item_name', 'qty']);
						populateTable(result.wtbyweight, 'WTItemByWeightTable', ['item_name', 'weight']);
						populateTable(result.wtbyqty, 'WTItemByQtyTable', ['item_name', 'qty']);
						populateTable(result.crcbyweight, 'CRCItemByWeightTable', ['item_name', 'weight']);
						populateTable(result.crcbyqty, 'CRCItemByQtyTable', ['item_name', 'qty']);
						populateTable(result.ecobyweight, 'ECOItemByWeightTable', ['item_name', 'weight']);
						populateTable(result.ecobyqty, 'ECOItemByQtyTable', ['item_name', 'qty']);
						populateTable(result.cosmobyweight, 'COSMOItemByWeightTable', ['item_name', 'weight']);
						populateTable(result.cosmobyqty, 'COSMOItemByQtyTable', ['item_name', 'qty']);
					},
					error: function () {
						alert("Error loading Item Of The Month data");
					}
				});
			}
			else if (tabId == "#ANNUAL") {
				// Clear previous rows from the tables
				var saleTable = document.getElementById('AnnualSaleTable');
				while (saleTable.rows.length > 0) {
					saleTable.deleteRow(0);
				}

				var purTable = document.getElementById('AnnualPurTable');
				while (purTable.rows.length > 0) {
					purTable.deleteRow(0);
				}

				var fromMonth = document.getElementById('filterANNUALFrom').value;
				var toMonth = document.getElementById('filterANNUALTo').value;

				$.ajax({
					type: "GET",
					url: '/dashboard-tabs/annual',
					data: { from: fromMonth, to: toMonth },
					success: function(result) {
						var rows = '';
						var netamount = 0; // Initialize total for sales
						var totalWeightSales = 0; // Rename to avoid conflict

						// Process sales data
						$.each(result['annual_sale'], function (index, value) {
							var amount = value['total_dr_amount'] ? parseFloat(parseFloat(value['total_dr_amount']).toFixed(0)) : 0;
							var weight = value['total_weight'] ? parseFloat(value['total_weight']) : 0;
							netamount += amount;
							totalWeightSales += weight;
							rows += `<tr>
								<td>${value['sale_type'] ? value['sale_type'] : ''}</td>
								<td>${amount ? amount.toFixed(0) : ''}</td>
								<td>${weight ? weight.toFixed(2) : ''}</td>
							</tr>`;
						});

						// Append the total row for sales
						rows += `<tr>
							<td><strong>Total</strong></td>
							<td class="text-danger"><strong>${netamount.toFixed(0)}</strong></td>
							<td class="text-danger"><strong>${totalWeightSales.toFixed(2)}</strong></td>
						</tr>
						<tr>
							<td colspan="3">
							<strong>
								<span id="numberInWordsSale" style="color:#17365D; text-decoration: underline; font-size: 20px;"></span>
							</strong>
							</td>
						</tr>`;
						$('#AnnualSaleTable').html(rows);

						// Convert netamount to words
						var words = convertCurrencyToWords(netamount);
						document.getElementById('numberInWordsSale').innerHTML = words;

						// Reset rows for purchases
						rows = '';
						var netamount = 0; // Initialize total for purchases
						var totalWeightPurchases = 0; // Rename to avoid conflict

						// Process purchases data
						$.each(result['annual_pur'], function (index, value) {
							var amount = value['total_cr_amount'] ? parseFloat(parseFloat(value['total_cr_amount']).toFixed(0)) : 0;
							var weight = value['total_weight'] ? parseFloat(value['total_weight']) : 0;
							netamount += amount;
							totalWeightPurchases += weight;
							rows += `<tr>
								<td>${value['pur_type'] ? value['pur_type'] : ''}</td>
								<td>${amount ? amount.toFixed(0) : ''}</td>
								<td>${weight ? weight.toFixed(2) : ''}</td>
							</tr>`;
						});

						// Append the total row for purchases
						rows += `<tr>
							<td><strong>Total</strong></td>
							<td class="text-danger"><strong>${netamount.toFixed(0)}</strong></td>
							<td class="text-danger"><strong>${totalWeightPurchases.toFixed(2)}</strong></td>
						</tr>
						<tr>
							<td colspan="3">
							<strong>
								<span id="numberInWords" style="color:#17365D; text-decoration: underline; font-size: 20px;"></span>
							</strong>
							</td>

						</tr>`;

						$('#AnnualPurTable').html(rows);

						// Convert netamount to words
						var words = convertCurrencyToWords(netamount);
						document.getElementById('numberInWords').innerHTML = words;

					},
					error: function() {
						alert("Error loading Annual data");
					}
				});
			}
			else if(tabId=="#UV"){

				var table = document.getElementById('UVSaleTable');
				while (table.rows.length > 0) {
					table.deleteRow(0);
				}

				var table = document.getElementById('UVPurTable');
				while (table.rows.length > 0) {
					table.deleteRow(0);
				}
				var table = document.getElementById('WrongSaleTable');
				while (table.rows.length > 0) {
					table.deleteRow(0);
				}

				var table = document.getElementById('WrongPurTable');
				while (table.rows.length > 0) {
					table.deleteRow(0);
				}

				$.ajax({
					type: "GET",
					url: '/dashboard-tabs/uv',
					success: function(result) {
						// For UV Sales Ageing
						var UVsalesRows = '';
						$.each(result['unadjusted_sales_ageing_jv2'], function (index, value) {
							UVsalesRows += `<tr>
								<td><a href='/vouchers2/edit/${value['jv2_id']}' target='_blank'>${value['prefix'] ? value['prefix'] : ''}${value['jv2_id'] ? value['jv2_id'] : ''}</a></td>
								<td>${value['jv_date'] ? moment(value['jv_date']).format('D-M-YYYY') : ''}</td>
								<td>${value['ac_name'] ? value['ac_name'] : ''}</td>
								<td>${value['SumCredit'] ? value['SumCredit'] : ''}</td>
								<td>${value['pur_age_amount'] ? value['pur_age_amount'] : ''}</td>
								<td>${value['remaining_amount'] ? value['remaining_amount'] : ''}</td>
							</tr>`;
						});
						$('#UVSaleTable').html(UVsalesRows);

						// For UV Purchase Ageing
						var UVpurchaseRows = '';
						$.each(result['unadjusted_purchase_ageing_jv2'], function (index, value) {
							UVpurchaseRows += `<tr>
								<td><a href='/vouchers2/edit/${value['jv2_id']}' target='_blank'>${value['prefix'] ? value['prefix'] : ''}${value['jv2_id'] ? value['jv2_id'] : ''}</a></td>
								<td>${value['jv_date'] ? moment(value['jv_date']).format('D-M-YYYY') : ''}</td>
								<td>${value['ac_name'] ? value['ac_name'] : ''}</td>
								<td>${value['SumDebit'] ? value['SumDebit'] : ''}</td>
								<td>${value['pur_age_amount'] ? value['pur_age_amount'] : ''}</td>
								<td>${value['remaining_amount'] ? value['remaining_amount'] : ''}</td>
							</tr>`;
						});
						$('#UVPurTable').html(UVpurchaseRows);

						var salesRows = '';
						$.each(result['sales_ageing'], function (index, value) {
							salesRows += `<tr>
								<td>${value['jv2_id'] ? value['jv2_id'] : ''}</td>
								<td>${value['sales_prefix'] ? value['sales_prefix'] : ''} ${value['sales_id'] ? value['sales_id'] : ''}</td>
								<td>${value['ac_name'] ? value['ac_name'] : ''}</td>
							</tr>`;
						});
						$('#WrongSaleTable').html(salesRows);

						// For Purchase Ageing
						var purchaseRows = '';
						$.each(result['purchase_ageing'], function (index, value) {
							purchaseRows += `<tr>
								<td>${value['jv2_id'] ? value['jv2_id'] : ''}</td>
								<td>${value['sales_prefix'] ? value['sales_prefix'] : ''} ${value['sales_id'] ? value['sales_id'] : ''}</td>
								<td>${value['ac_name'] ? value['ac_name'] : ''}</td>
							</tr>`;
						});
						$('#WrongPurTable').html(purchaseRows);
					},
					error: function() {
						alert("Error loading UV data");
					}
				});

			}
			else if(tabId=="#OVER_DAYS"){

				var table = document.getElementById('ODSalesInvTable');
				while (table.rows.length > 0) {
					table.deleteRow(0);
				}

				var table = document.getElementById('ODPurInvTable');
				while (table.rows.length > 0) {
					table.deleteRow(0);
				}

				$.ajax({
					type: "GET",
					url: '/dashboard-tabs/over-days',
					success: function(result) {
						// For Sales Ageing
						var salesRows = '';
						$.each(result['dash_over_days_sales'], function (index, value) {
							salesRows += `<tr>
								<td>${value['ac_name'] ? value['ac_name'] : ''}</td>
								<td>${value['sale_prefix'] ? value['sale_prefix'] : ''} ${value['Sal_inv_no'] ? value['Sal_inv_no'] : ''}</td>
								<td>${value['bill_date'] ? moment(value['bill_date']).format('D-M-YY') : ''}</td>
								<td>${value['bill_amount'] ? value['bill_amount'].toFixed(0) : ''}</td>
								<td>${value['ttl_jv_amt'] ? value['ttl_jv_amt'].toFixed(0) : ''}</td>
								<td>${value['remaining_amount'] ? value['remaining_amount'].toFixed(0) : ''}</td>
								<td>${value['days_cross'] ? value['days_cross'] : ''}</td>
							</tr>`;
						});
						$('#ODSalesInvTable').html(salesRows);

						// For Purchase Ageing
						var purchaseRows = '';
						$.each(result['dash_over_days_pur'], function (index, value) {
							purchaseRows += `<tr>
								<td>${value['ac_name'] ? value['ac_name'] : ''}</td>
								<td>${value['sale_prefix'] ? value['sale_prefix'] : ''} ${value['Sal_inv_no'] ? value['Sal_inv_no'] : ''}</td>
								<td>${value['bill_date'] ? moment(value['bill_date']).format('D-M-YY') : ''}</td>
								<td>${value['bill_amount'] ? value['bill_amount'].toFixed(0) : ''}</td>
								<td>${value['ttl_jv_amt'] ? value['ttl_jv_amt'].toFixed(0) : ''}</td>
								<td>${value['remaining_amount'] ? value['remaining_amount'].toFixed(0) : ''}</td>
								<td>${value['days_cross'] ? value['days_cross'] : ''}</td>
							</tr>`;
						});
						$('#ODPurInvTable').html(purchaseRows);
					},
					error: function() {
						alert("Error loading Over Days data");
					}
				});

			}
			else if(tabId=="#OVER_DUES"){

				var table = document.getElementById('ODRecvAccTable');
				while (table.rows.length > 0) {
					table.deleteRow(0);
				}

				var table = document.getElementById('ODPayAccTable');
				while (table.rows.length > 0) {
					table.deleteRow(0);
				}

				$.ajax({
					type: "GET",
					url: '/dashboard-tabs/over-dues',
					success: function(result) {
						// For Sales Ageing
						var salesRows = '';
						$.each(result['dash_over_dues_recv'], function (index, value) {
							salesRows += `<tr>
								<td>${value['ac_name'] ? value['ac_name'] : ''}</td>
								<td>${value['credit_limit'] ? value['credit_limit'].toFixed(0) : ''}</td>
								<td>${value['Bal'] ? value['Bal'].toFixed(0) : ''}</td>
								<td>${value['over_dues'] ? value['over_dues'].toFixed(0) : ''}</td>
							</tr>`;
						});
						$('#ODRecvAccTable').html(salesRows);

						// For Purchase Ageing
						var purchaseRows = '';
						$.each(result['dash_over_dues_pay'], function (index, value) {
							purchaseRows += `<tr>
								<td>${value['ac_name'] ? value['ac_name'] : ''}</td>
								<td>${value['credit_limit'] ? value['credit_limit'].toFixed(0) : ''}</td>
								<td>${value['Bal'] ? value['Bal'].toFixed(0) : ''}</td>
								<td>${value['over_dues'] ? value['over_dues'].toFixed(0) : ''}</td>
							</tr>`;
						});
						$('#ODPayAccTable').html(purchaseRows);
					},
					error: function() {
						alert("Error loading Over Dues data");
					}
				});

			}
			else if(tabId=="#PDC"){
				var table = document.getElementById('RecPDCTable');
				while (table.rows.length > 0) {
					table.deleteRow(0);
				}

				var table = document.getElementById('PaidPDCTable');
				while (table.rows.length > 0) {
					table.deleteRow(0);
				}

				$.ajax({
					type: "GET",
					url: '/dashboard-tabs/pdc',
					success: function(result) {
						// For Sales Ageing
						var salesRows = '';
						$.each(result['dash_pdc_recv'], function (index, value) {
							salesRows += `<tr>
								<td>${value['prefix'] ? value['prefix'] : ''} ${value['pdc_id'] ? value['pdc_id'] : ''}</td>
								<td>${value['chqdate'] ? moment(value['chqdate']).format('D-M-YY') : ''}</td>
								<td>${value['ac_name'] ? value['ac_name'] : ''}</td>
								<td>${value['remarks'] ? value['remarks'] : ''} ${value['bankname'] ? value['bankname'] : ''} ${value['instrumentnumber'] ? value['instrumentnumber'] : ''}</td>
								<td>${value['amount'] ? value['amount'].toFixed(0) : ''}</td>
							</tr>`;
						});
						$('#RecPDCTable').html(salesRows);

						// For Purchase Ageing
						var purchaseRows = '';
						$.each(result['dash_pdc_pay'], function (index, value) {
							purchaseRows += `<tr>
								<td>${value['prefix'] ? value['prefix'] : ''} ${value['pdc_id'] ? value['pdc_id'] : ''}</td>
								<td>${value['chqdate'] ? moment(value['chqdate']).format('D-M-YY') : ''}</td>
								<td>${value['ac_name'] ? value['ac_name'] : ''}</td>
								<td>${value['remarks'] ? value['remarks'] : ''} ${value['bankname'] ? value['bankname'] : ''} ${value['instrumentnumber'] ? value['instrumentnumber'] : ''}</td>
								<td>${value['amount'] ? value['amount'].toFixed(0) : ''}</td>
							</tr>`;
						});
						$('#PaidPDCTable').html(purchaseRows);
					},
					error: function() {
						alert("Error loading PDC data");
					}
				});

			}


		}

		function getTabData() {
            const activeTabLink = document.querySelector('.nav-link-dashboard-tab.active');
            if (activeTabLink) {
                activeTabLink.click();
            }
        }

		function getDaySuffix(day) {
			if (day >= 11 && day <= 13) {
			return day + 'th';
			}
			switch (day % 10) {
			case 1: return day + 'st';
			case 2: return day + 'nd';
			case 3: return day + 'rd';
			default: return day + 'th';
			}
		}

	</script>									
</html>