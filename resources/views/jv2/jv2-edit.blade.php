@include('../layouts.header')
	<body>
		<section class="body">
		@include('../layouts.pageheader')
			<div class="inner-wrapper cust-pad">
				<section role="main" class="content-body" style="margin:0px">
					<form method="post" action="{{ route('update-jv2') }}" enctype="multipart/form-data" onkeydown="return event.key != 'Enter';" id="updateForm">
						@csrf
						<div class="row">
							<div class="col-12 mb-3">								
								<section class="card">
									<header class="card-header">
										<h2 class="card-title">Edit Journal Voucher 2</h2>
										
										<div class="card-actions">
											<button type="button" class="btn btn-primary" onclick="addNewRow()"> <i class="fas fa-plus"></i> Add New Row </button>
										</div>
									</header>

									<div class="card-body">
										<div class="row form-group mb-2">
											<div class="col-6 col-md-1 mb-2">
												<label class="col-form-label" >RC. #</label>
												<input type="text" placeholder="Invoice No." value="{{$jv2->jv_no}}" class="form-control" disabled>
												<input type="hidden" name="jv_no" value="{{$jv2->jv_no}}" id="jv_no" class="form-control">
												<input type="hidden" id="itemCount" name="items" value="1" class="form-control">
												<input type="hidden" id="pur_prevInvoices" name="pur_prevInvoices" value="0" class="form-control">
												<input type="hidden" id="prevInvoices" name="prevInvoices" value="0" class="form-control">
											</div>

											<div class="col-6 col-md-2 mb-2">
												<label class="col-form-label" >Date</label>
												<input type="date" name="jv_date" value="{{$jv2->jv_date}}" class="form-control">
											</div>
											
											<div class="col-sm-12 col-md-5 mb-2">
												<label class="col-form-label">Narration</label>
												<textarea rows="1" cols="50" name="narration" id="narration" required placeholder="Narration" class="form-control cust-textarea">{{$jv2->narration}}</textarea>
											</div>
											<div class="col-sm-12 col-md-4 mb-3">
												<label class="col-form-label">Attachements</label>
												<input type="file" class="form-control" name="att[]" multiple accept=".zip, appliation/zip, application/pdf, image/png, image/jpeg">
											</div>

									  </div>
									
								
									<div class="card-body" style="overflow-x:auto;min-height:auto;max-height:450px;overflow-y:auto">
										<table class="table table-bordered table-striped mb-0" id="myTable" >
											<thead>
												<tr>
													<!-- <th width="4%">Code</th> -->
													<th width="">Account Name</th>
													<th width="">Remarks</th>
													<th width="">Bank Name</th>
													<th width="">Instr. #</th>
													<th width="">Slip #</th>
													<th width="">Chq Date</th>
													<th width="">Debit</th>
													<th width="">Credit</th>
													<th width=""></th>
												</tr>
											</thead>
											<tbody id="JV2Table">
                                            	@foreach ($jv2_items as $jv_key => $jv2_items)
													<tr>
														<td>
															<select data-plugin-selecttwo class="form-control select2-js"  name ="account_cod[]" onchange="addNewRow()" required>
																<option value="" disabled selected>Select Account</option>
																@foreach($acc as $key => $row)	
																	<option value="{{$row->ac_code}}" {{ $jv2_items->account_cod == $row->ac_code ? 'selected' : '' }}>{{$row->ac_name}}</option>
																@endforeach
															</select>
														</td>	
														<td>
															<input type="text" class="form-control" name="remarks[]" value="{{$jv2_items->remarks}}">
														</td>
														<td>
															<input type="text" class="form-control" name="bank_name[]" value="{{$jv2_items->bankname}}">
														</td>
														<td>
															<input type="text" class="form-control" name="instrumentnumber[]" value="{{$jv2_items->instrumentnumber}}">
														</td>
														<td>
															<input type="text" class="form-control" name="slip[]" value="{{$jv2_items->slip}}">
														</td>
														<td>
															<input type="date" class="form-control"  name="chq_date[]" size=5  value="{{$jv2_items->chqdate}}">
														</td>
														<td>
															<input type="number" class="form-control" name="debit[]" onchange="totalDebit(this)" required value="{{$jv2_items->debit}}" step=".00001">
														</td>

														<td>
															<input type="number" class="form-control" name="credit[]" onchange="totalCredit(this)" required value="{{$jv2_items->credit}}"  step=".00001">
														</td>
														<td style="vertical-align: middle;">
															<button type="button" onclick="removeRow(this)" class="btn btn-danger" tabindex="1"><i class="fas fa-times"></i></button>
														</td>
													</tr>
												@endforeach
											</tbody>
										</table>
									</div>
									</div>
									<footer class="card-footer" >
										<div class="row mb-3"  style="float:right">
											<div class="col-6 col-md-6 pb-sm-3 pb-md-0">
												<label class="col-form-label">Total Debit</label>
												<input type="number" id="total_debit" name="total_debit" placeholder="Total Debit" class="form-control" disabled>
											</div>
											<div class="col-6 col-md-6 pb-sm-3 pb-md-0">
												<label class="col-form-label">Total Credit</label>
												<input type="number" id="total_credit" name="total_credit" placeholder="Total Credit" class="form-control" disabled>
											</div>
										</div>
									</footer>
									
								</section>
							</div>
							<div class="col-sm-12 col-md-6 col-lg-6 mb-3">								
								<section class="card">
									<header class="card-header" style="display: flex;justify-content: space-between;">
										<h2 class="card-title">Sales Ageing <span id="sale_span" style="color:red;font-size: 16px;display:none">More than 1 credit not allowed</span><span id="sales_warning" style="color:red;font-size: 16px;display:none">All Previous Sales Ageing Record Against this JV2 will be replace by lastest</span></h2>
										<div class="form-check form-switch">
											<input class="form-check-input" type="checkbox" id="SaletoggleSwitch">
										</div>
									</header>
									
									@if(!empty($sales_ageing))
										<div class="card-body">
											<div class="row form-group mb-2">
												<div class="col-3 mb-2">
													<label class="col-form-label">Account Name <span><a onclick="refreshSalesAgeing()" id="refreshBtn" style="display:none"><i class="bx bx-refresh" style="font-size: 20px;color: red;"> </i></a></span></label>
													<select data-plugin-selecttwo class="form-control select2-js" id="customer_name" name="customer_name"   onchange="getPendingInvoices()" required disabled>
														<option value="0" selected>Select Account</option>
														@foreach($acc as $key1 => $row1)	
															<option value="{{$row1->ac_code}}" {{ $sales_ageing[0]->account_name == $row1->ac_code ? 'selected' : '' }}>{{$row1->ac_name}}</option>
														@endforeach
													</select>	
													
												</div>

												<div class="col-3 mb-2">
													<label class="col-form-label">Unadjusted Amount</label>
													<input type="number" id="sales_unadjusted_amount" name="sales_unadjusted_amount" value="0" class="form-control" disabled step="any">
												</div>

												<div class="col-3 mb-2">
													<label class="col-form-label">Total Amount</label>
													<input type="number" id="total_reci_amount" class="form-control" value="0" disabled step="any">
												</div>

												<div class="col-3 mb-2">
													<label class="col-form-label">Remaining Amount</label>
													<input type="number" id="sales_ageing_remaing_amt" class="form-control" value="0" disabled step="any">
												</div>
											
												<div class="col-12 mb-2" >
													<table id="sales_ageing" class="table table-bordered table-striped mb-0 mt-2">
														<thead>
															<tr>
																<th width="13%">Inv #</th>
																<th width="12%">Date</th>
																<th width="17%">Bill Amount</th>
																<th width="17%">Remaining</th>
																<th width="17%">Amount</th>
																<th width="14%">Name</th>
															</tr>
														</thead>
														<tbody id="pendingInvoices">
														@foreach ($sales_ageing as $key => $row)
															<tr>
																<td><input type='text' class='form-control' value="{{$row->prefix}}{{$row->Sal_inv_no}}" disabled><input type='hidden' name='invoice_nos[]' class='form-control' value="{{$row->Sal_inv_no}}"><input type='hidden' name='totalInvoices' class='form-control' value="{{$key}}"><input type='hidden' name='prefix[]' class='form-control' value="{{$row->prefix}}"></td>
																<td><input type='date' class='form-control' value="{{$row->sa_date}}" disabled></td>
																<td><input type='number' class='form-control' value="{{$row->b_amt}}" name='bill_amount[]' disabled></td>
																<td><input type='number' class='form-control text-danger' value="{{$row->balance}}" name='balance_amount[]' disabled></td>
																<td><input type='number' class='form-control' value="{{$row->amount}}" max="{{$row->amount}}" step='any' name='rec_amount[]' onchange='totalReci()' required disabled></td>
																<td><input type='text' class='form-control' value="{{$row->nop}}" disabled></td>
															</tr>
														@endforeach
														</tbody>
													</table>										
												</div>
											</div>
										</div>
									
									@else
										<div class="card-body">
											<div class="row form-group mb-2">

												<div class="col-3 mb-2">
													<label class="col-form-label">Account Name</label>
													<select data-plugin-selecttwo class="form-control select2-js" id="customer_name" name="customer_name"  onchange="getPendingInvoices()" required>
														<option value="0" selected>Select Account</option>
														@foreach($acc as $key1 => $row1)	
															<option value="{{$row1->ac_code}}">{{$row1->ac_name}}</option>
														@endforeach
													</select>	
													
												</div>

												<div class="col-3 mb-2">
													<label class="col-form-label">Unadjusted Amount</label>
													<input type="number" id="sales_unadjusted_amount" name="sales_unadjusted_amount" value="0" class="form-control" step="any">
												</div>

												<div class="col-3 mb-2">
													<label class="col-form-label">Total Amount</label>
													<input type="number" id="total_reci_amount" class="form-control" value="0" disabled step="any">
												</div>

												<div class="col-3 mb-2">
													<label class="col-form-label">Remaining Amount</label>
													<input type="number" id="sales_ageing_remaing_amt" class="form-control" value="0" disabled step="any">
												</div>

												<div class="col-12 mb-2" >
													<table id="sales_ageing" class="table table-bordered table-striped mb-0 mt-2">
														<thead>
															<tr>
																<th width="13%">Inv #</th>
																<th width="12%">Date</th>
																<th width="17%">Bill Amount</th>
																<th width="17%">Remaining</th>
																<th width="17%">Amount</th>
																<th width="14%">Name</th>
															</tr>
														</thead>
														<tbody id="pendingInvoices">
															<tr>

															</tr>
														</tbody>
													</table>										
												</div>
											</div>
										</div>
									
									@endif
								</section>
							</div>
							<div class="col-sm-12 col-md-6 col-lg-6 mb-3">								
								<section class="card">
									<header class="card-header"  style="display: flex;justify-content: space-between;">
										<h2 class="card-title">Purchase Ageing <span id="pur_span" style="color:red;font-size: 16px;display:none">More than 1 Debit not allowed</span><span id="pur_warning" style="color:red;font-size: 16px;display:none">All Previous Purchase Ageing Record Against this JV2 will be replace by lastest</span></h2>
										<div class="form-check form-switch">
											<input class="form-check-input" type="checkbox" value="0" id="PurtoggleSwitch">
										</div>
									</header>
									@if(!empty($purchase_ageing))
										<div class="card-body">
											<div class="row form-group mb-2">
											
												<div class="col-3 mb-2">
													<label class="col-form-label">Account Name <span><a onclick="refreshPurAgeing()" id="PurrefreshBtn" style="display:none"><i class="bx bx-refresh" style="font-size: 20px;color: red;"> </i></a></span> </label>
													<select data-plugin-selecttwo class="form-control select2-js" id="pur_customer_name" name="pur_customer_name" onchange="getPurPendingInvoices()" required disabled>
														<option value="0" disabled selected>Select Account</option>
														@foreach($acc as $key1 => $row1)
															<option value="{{$row1->ac_code}}" {{ $purchase_ageing[0]->acc_name == $row1->ac_code ? 'selected' : '' }}>{{$row1->ac_name}}</option>
														@endforeach
													</select>
												</div>

												<div class="col-3 mb-2">
													<label class="col-form-label">Unadjusted Amount</label>
													<input type="number" id="pur_unadjusted_amount" name="pur_unadjusted_amount" value="0" class="form-control" disabled step="any">
												</div>

												<div class="col-3 mb-2">
													<label class="col-form-label">Total Amount</label>
													<input type="number" id="total_pay_amount" value="0" class="form-control" disabled step="any">
												</div>

												<div class="col-3 mb-2">
													<label class="col-form-label">Remaining Amount</label>
													<input type="number" id="pur_ageing_remaing_amt" class="form-control" value="0" disabled step="any">
												</div>

												<div class="col-12 mb-2">
													<table class="table table-bordered table-striped mb-0 mt-2">
														<thead>
															<tr>
																<th width="">Inv #</th>
																<th width="">Date</th>
																<th width="">Bill Amount</th>
																<th width="">Remaining Amount</th>
																<th width="">Amount</th>
															</tr>
														</thead>
														<tbody id="purpendingInvoices">
															@foreach ($purchase_ageing as $key => $row)
																<tr>
																	<td><input type='text' class='form-control' value="{{$row->sales_prefix}}{{$row->sales_id}}" disabled><input type='hidden' name='invoice_nos[]' class='form-control' value="{{$row->sales_id}}"><input type='hidden' name='pur_totalInvoices' class='form-control' value="{{$key}}"><input type='hidden' name='sales_prefix[]' class='form-control' value="{{$row->prefix}}"></td>
																	<td><input type='date' class='form-control' value="{{$row->sa_date}}" disabled></td>
																	<td><input type='number' class='form-control' value="{{$row->b_amt}}" name='bill_amount[]' disabled></td>
																	<td><input type='number' class='form-control text-danger' value="{{$row->balance}}" name='balance_amount[]' disabled></td>
																	<td><input type='number' class='form-control' value="{{$row->amount}}" max="{{$row->amount}}" step='any' name='rec_amount[]' onchange='totalPay()' required disabled></td>
																</tr>
															@endforeach
														</tbody>
													</table>										
												</div>
											</div>
										</div>
									@else
										<div class="card-body">
											<div class="row form-group mb-2">
												<div class="col-3 mb-2">
													<label class="col-form-label">Account Name <span><a onclick="refreshPurAgeing()" id="PurrefreshBtn" name="pur_customer_name" style="display:none"><i class="bx bx-refresh" style="font-size: 20px;color: red;"> </i></a></span> </label>
													<select data-plugin-selecttwo class="form-control select2-js" name="pur_customer_name" id="pur_customer_name" onchange="getPurPendingInvoices()" required>
														<option value="0" disabled selected>Select Account</option>
														@foreach($acc as $key1 => $row1)	
															<option value="{{$row1->ac_code}}">{{$row1->ac_name}}</option>
														@endforeach
													</select>
												</div>

												<div class="col-3 mb-2">
													<label class="col-form-label">Unadjusted Amount</label>
													<input type="number" id="pur_unadjusted_amount" name="pur_unadjusted_amount" value="0" class="form-control" step="any">
												</div>

												<div class="col-3 mb-2">
													<label class="col-form-label">Total Amount</label>
													<input type="number" id="total_pay_amount" value="0" class="form-control" disabled step="any">
												</div>

												<div class="col-3 mb-2">
													<label class="col-form-label">Remaining Amount</label>
													<input type="number" id="pur_ageing_remaing_amt" class="form-control" value="0" disabled step="any">
												</div>

												<div class="col-12 mb-2">
													<table class="table table-bordered table-striped mb-0 mt-2">
														<thead>
															<tr>
																<th width="">Inv #</th>
																<th width="">Date</th>
																<th width="">Bill Amount</th>
																<th width="">Remaining Amount</th>
																<th width="">Amount</th>
															</tr>
														</thead>
														<tbody id="purpendingInvoices">
																<tr>
																	
																</tr>
														</tbody>
													</table>										
												</div>
											</div>
										</div>
									@endif
								</section>
							</div>
							<div class="col-12 mb-3">
								<section class="card">
									<footer class="card-footer">
										<div class="row form-group mb-2">
											<div class="text-end">
												<button type="button" class="btn btn-danger mt-2"  onclick="window.location='{{ route('all-jv2-paginate') }}'"> <i class="fas fa-trash"></i> Discard Changes</button>
												<button type="submit" class="btn btn-primary mt-2"> <i class="fas fa-save"></i> Save Voucher</button>
											</div>
										</div>
									</footer>
								</section>
							</div>
						</div>
					</form>
				</section>
			</div>
		</section>
        @include('../layouts.footerlinks')
	</body>
</html>
 
<script>

	window.addEventListener('beforeunload', function(event) {
		var isChecked = $('#SaletoggleSwitch').is(':checked');
		var isPurChecked = $('#PurtoggleSwitch').is(':checked');

		if (isChecked || isPurChecked) {
			event.preventDefault();
			event.returnValue = ''; // Show the default confirmation dialog
		}
  	});
	
	var itemCount;

	$(document).ready(function() {
		$(window).keydown(function(event){
			if(event.keyCode == 13) {
				event.preventDefault();
				return false;
			}
		});

		$('#updateForm').on('submit', function(e){
            e.preventDefault();
			var total_credit=$('#total_credit').val();
			var total_debit=$('#total_debit').val();
			var isChecked = $('#SaletoggleSwitch').is(':checked');
			var isPurChecked = $('#PurtoggleSwitch').is(':checked');

			if(isChecked && isPurChecked){
				var sales_unadjusted_amount=$('#sales_unadjusted_amount').val();
				var pur_unadjusted_amount=$('#pur_unadjusted_amount').val();

				var total_reci_amount=$('#total_reci_amount').val();
				var total_pay_amount=$('#total_pay_amount').val();

				if(total_debit==total_credit && sales_unadjusted_amount==total_reci_amount && pur_unadjusted_amount==total_pay_amount){
					var form = document.getElementById('updateForm');
					form.submit();
				}
				else if(total_debit!=total_credit) {
					alert("Total Debit & Credit Must be Equal")
				}
				else if(sales_unadjusted_amount!=total_reci_amount) {
					alert("Unadjusted amount is not completely adjusted In Sales Ageing")
				}
				else if(pur_unadjusted_amount!=total_pay_amount) {
					alert("Unadjusted amount is not completely adjusted In Purchase Ageing")
				}
			}

			else if(isChecked){
				var sales_unadjusted_amount=$('#sales_unadjusted_amount').val();
				var total_reci_amount=$('#total_reci_amount').val();

				if(total_debit==total_credit && sales_unadjusted_amount==total_reci_amount){
					var form = document.getElementById('updateForm');
					form.submit();
				}
				else if(total_debit!=total_credit) {
					alert("Total Debit & Credit Must be Equal")
				}
				else if(sales_unadjusted_amount!=total_reci_amount) {
					alert("Unadjusted amount is not completely adjusted In Sales Ageing")
				}
			}

			else if(isPurChecked){
				var pur_unadjusted_amount=$('#pur_unadjusted_amount').val();
				var total_pay_amount=$('#total_pay_amount').val();

				if(total_debit==total_credit && pur_unadjusted_amount==total_pay_amount){
					var form = document.getElementById('updateForm');
					form.submit();
				}
				else if(total_debit!=total_credit) {
					alert("Total Debit & Credit Must be Equal")
				}
				else if(pur_unadjusted_amount!=total_pay_amount) {
					alert("Unadjusted amount is not completely adjusted In Purchase Ageing")
				}
			}

			else if(total_debit==total_credit){
				var form = document.getElementById('updateForm');
				form.submit();
			}

			else{
				alert("Total Debit & Credit Must be Equal")
			}

		});

		var totalDebit=0, totalCredit=0, debit=0, credit=0;
		var table = document.getElementById("JV2Table"); // Get the table element
        var rowCount = table.rows.length; // Get the total number of rows

		var itemCount = rowCount;
		$('#itemCount').val(itemCount);

		for (var j=0;j<rowCount; j++){

			debit = table.rows[j].cells[6].querySelector('input').value; // Get the value of the input field in the specified cell
			totalDebit = totalDebit + Number(debit);

			credit = table.rows[j].cells[7].querySelector('input').value; // Get the value of the input field in the specified cell
			totalCredit = totalCredit + Number(credit);
		}
		$('#total_credit').val(totalCredit);
		$('#total_debit').val(totalDebit);

		document.getElementById('SaletoggleSwitch').addEventListener('change', SaletoggleInputs);
		document.getElementById('PurtoggleSwitch').addEventListener('change', PurtoggleInputs);
	});

	function removeRow(button) {
		var confirmation = confirm("Are you sure you want to remove this row?");
		if (confirmation) {
			var tableRows = $("#JV2Table tr").length;
			if (tableRows > 1) {
				var row = button.parentNode.parentNode;
				row.parentNode.removeChild(row);
				var itemCount = Number($('#itemCount').val());
				itemCount = itemCount - 1;
				$('#itemCount').val(itemCount);
			}
			totalDebit();
			totalCredit();
		} else {
			// Do nothing if the user selects "No"
		}
	}


    document.getElementById('removeRowBtn').addEventListener('click', function() {
        var table = document.getElementById('myTable').getElementsByTagName('tbody')[0];
        if (table.rows.length > 0) {
            table.deleteRow(table.rows.length - 1);
        } else {
            alert("No rows to delete!");
        }
    });

	function addNewRow(){
		var lastRow =  $('#myTable tr:last');
		latestValue=lastRow[0].cells[0].querySelector('select').value;

		if(latestValue!=""){
			var table = document.getElementById('myTable').getElementsByTagName('tbody')[0];
			var newRow = table.insertRow(table.rows.length);

			var cell1 = newRow.insertCell(0);
			var cell2 = newRow.insertCell(1);
			var cell3 = newRow.insertCell(2);
			var cell4 = newRow.insertCell(3);
			var cell5 = newRow.insertCell(4);
			var cell6 = newRow.insertCell(5);
			var cell7 = newRow.insertCell(6);
			var cell8 = newRow.insertCell(7);
			var cell9 = newRow.insertCell(8);

			cell1.innerHTML  = '<select data-plugin-selecttwo class="form-control select2-js"  onclick="addNewRow()" name ="account_cod[]" required>'+
									'<option value="" disabled selected>Select Account</option>'+
									@foreach($acc as $key => $row)	
                                        '<option value="{{$row->ac_code}}">{{$row->ac_name}}</option>'+
                                    @endforeach
								'</select>';
			cell2.innerHTML  = '<input type="text" class="form-control" name="remarks[]" >';
			cell3.innerHTML  = '<input type="text" class="form-control" name="bank_name[]" >';
			cell4.innerHTML  = '<input type="text" class="form-control" name="instrumentnumber[]">';
			cell5.innerHTML  = '<input type="text" class="form-control" name="slip[]">';
			cell6.innerHTML  = '<input type="date" class="form-control" size="5" name="chq_date[]"  >';
			cell7.innerHTML  = '<input type="number" class="form-control" name="debit[]"  required value="0" onchange="totalDebit()" step=".00001">';
			cell8.innerHTML  = '<input type="number" class="form-control" name="credit[]"  required value="0" onchange="totalCredit()" step=".00001">';
			cell9.innerHTML = '<button type="button" onclick="removeRow(this)" class="btn btn-danger" tabindex="1"><i class="fas fa-times"></i></button>';

			itemCount = Number($('#itemCount').val());
			itemCount = itemCount+1;
			$('#itemCount').val(itemCount);
			$('#myTable select[data-plugin-selecttwo]').select2();

			// Set focus on the new item_code input field
			document.getElementById('item_code' + (index - 1)).focus();

		}
	}

	function validateItemName(inputElement)
	{
		var item_name = inputElement.value;

		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

        $.ajax({
            type: 'POST',
			url: '/item/new-item/validate',
            data: {'item_name': item_name},
            success: function(response){
            },
            error: function(response){
                var errors = response.responseJSON.errors;
                var errorMessage = 'Item Already Exists';
                alert(errorMessage);
            }
        });
    }

	function totalDebit(){
		var totalDebit=0;
		var debit=0;
		var table = document.getElementById("JV2Table"); // Get the table element
        var rowCount = table.rows.length; // Get the total number of rows

		for (var j=0;j<rowCount; j++){
			debit = table.rows[j].cells[6].querySelector('input').value; // Get the value of the input field in the specified cell
			totalDebit = totalDebit + Number(debit);
		}
		$('#total_debit').val(totalDebit);

	}

	function totalCredit(){
		var totalCredit=0;
		var credit=0;
		var table = document.getElementById("JV2Table"); // Get the table element
        var rowCount = table.rows.length; // Get the total number of rows

		for (var i=0;i<rowCount; i++){
			credit = table.rows[i].cells[7].querySelector('input').value; // Get the value of the input field in the specified cell
			totalCredit = totalCredit + Number(credit);
		}
		$('#total_credit').val(totalCredit);

	}

	function goBack() {
		window.history.back();
	}

	function getPendingInvoices(){
		var cust_id=$('#customer_name').val();
		var table = document.getElementById('pendingInvoices');
		$('#pendingInvoices').html('');
		$('#pendingInvoices').find('tr').remove();

		if(cust_id!=0){
			var counter=1;
			$('#prevInvoices').val(1)
			
			$.ajax({
				type: "GET",
				url: "/vouchers2/pendingInvoice/"+cust_id,
				success: function(result){
					$.each(result, function(k,v){
						if(Math.round(v['balance'])>0){
							var html="<tr>";
							html+= "<td width='13%'><input type='text' class='form-control' value="+v['prefix']+""+v['Sal_inv_no']+" disabled><input type='hidden' name='invoice_nos[]' class='form-control' value="+v['Sal_inv_no']+"><input type='hidden' name='totalInvoices' class='form-control' value="+counter+"><input type='hidden' name='prefix[]' class='form-control' value="+v['prefix']+"></td>"
							html+= "<td width='12%'>"+v['sa_date']+"<input type='hidden' class='form-control' value="+v['sa_date']+"></td>"					
							html+= "<td width='17%'><input type='number' class='form-control' value="+Math.round(v['b_amt'])+" disabled><input type='hidden' name='balance_amount[]' class='form-control' value="+Math.round(v['b_amt'])+"></td>"
							html+= "<td width='17%'><input type='number' class='form-control text-danger' value="+Math.round(v['balance'])+" value='0' disabled><input type='hidden' name='bill_amount[]' class='form-control' value="+Math.round(v['bill_balance'])+"></td>"
							html+= "<td width='17%'><input type='number' class='form-control' value='0' max="+Math.round(v['balance'])+" step='any' name='rec_amount[]' onchange='totalReci()' required></td>"
							html += "<td width='14%'>" + (v['nop'] ? v['nop'] : "") + "</td>";
							html+="</tr>";
							$('#pendingInvoices').append(html);
							counter++;
						}
					});
				},
				error: function(){
					alert("error");
				}
			});
		}
	}

	function totalReci() {
		var totalRec = 0; // Initialize the total amount variable
		var table = document.getElementById("pendingInvoices"); // Get the table element
		var rowCount = table.rows.length; // Get the total number of rows

		// Loop through each row in the table
		for (var i = 0; i < rowCount; i++) {
			var input = table.rows[i].cells[4].querySelector('input'); // Get the input field in the specified cell
			if (input) { // Check if the input exists
				var rec = Number(input.value); // Convert the input value to a number
				totalRec += isNaN(rec) ? 0 : rec; // Add to totalRec, handle NaN cases
			}
		}
		
		var unadjusted_amt = $('#sales_unadjusted_amount').val();
		var RemainingRec = totalRec - unadjusted_amt;
		$('#total_reci_amount').val(totalRec); // Set the total in the corresponding input field
		$('#sales_ageing_remaing_amt').val(RemainingRec); // Set the total in the corresponding input field
	}

	function totalPay(){
		var totalPay = 0; // Initialize the total amount variable
		var table = document.getElementById("purpendingInvoices"); // Get the table element
		var rowCount = table.rows.length; // Get the total number of rows

		// Loop through each row in the table
		for (var i = 0; i < rowCount; i++) {
			var input = table.rows[i].cells[4].querySelector('input'); // Get the input field in the specified cell
			if (input) { // Check if the input exists
				var rec = Number(input.value); // Convert the input value to a number
				totalPay += isNaN(rec) ? 0 : rec; // Add to totalRec, handle NaN cases
			}
		}
		var pur_unadjusted_amt = $('#pur_unadjusted_amount').val();
		var pur_Remaining = totalPay - pur_unadjusted_amt;
		$('#total_pay_amount').val(totalPay); // Set the total in the corresponding input field
		$('#pur_ageing_remaing_amt').val(pur_Remaining)

	}

	function getPurPendingInvoices(){
		var cust_id=$('#pur_customer_name').val();
		var table = document.getElementById('purpendingInvoices');
		$('#purpendingInvoices').html('');
		$('#purpendingInvoices').find('tr').remove();

		if(cust_id!=0){
			var counter=1;
			$('#pur_prevInvoices').val(1)
			
			$.ajax({
				type: "GET",
				url: "/vouchers2/purpendingInvoice/"+cust_id,
				success: function(result){
					$.each(result, function(k,v){
						if(Math.round(v['balance'])>0){
							var html="<tr>";
							html+= "<td width='18%'><input type='text' class='form-control' value="+v['prefix']+""+v['Sal_inv_no']+" disabled><input type='hidden' name='pur_invoice_nos[]' class='form-control' value="+v['Sal_inv_no']+"><input type='hidden' name='pur_totalInvoices' class='form-control' value="+counter+"><input type='hidden' name='pur_prefix[]' class='form-control' value="+v['prefix']+"></td>"
							html+= "<td width='15%'>"+v['sa_date']+"<input type='hidden' class='form-control' value="+v['sa_date']+"></td>"					
							html+= "<td width='20%'><input type='number' class='form-control' value="+Math.round(v['b_amt'])+" disabled><input type='hidden' name='balance_amount[]' class='form-control' value="+Math.round(v['b_amt'])+"></td>"
							html+= "<td width='20%'><input type='number' class='form-control text-danger' value="+Math.round(v['balance'])+" value='0' disabled><input type='hidden' name='bill_amount[]' class='form-control' value="+Math.round(v['bill_balance'])+"></td>"
							html+= "<td width='20%'><input type='number' class='form-control' value='0' max="+Math.round(v['balance'])+" step='any' name='pur_rec_amount[]' onchange='totalPay()' required></td>"
							html+="</tr>";
							$('#purpendingInvoices').append(html);
							counter++;
						}
					});
				},
				error: function(){
					alert("error");
				}
			});
		}
	}

	function PurtoggleInputs() {
        const pur_customer_name = $('#pur_customer_name');
        const pur_unadjusted_amount = $('#pur_unadjusted_amount');
		const jv_no= $('#jv_no').val();
	
        if ($('#PurtoggleSwitch').is(':checked')) {
			document.getElementById('pur_warning').style.display = 'block';
			document.getElementById('PurrefreshBtn').style.display = 'inline';
			var table = document.getElementById('purpendingInvoices');
        	if (table.rows.length > 0) {
				
				$.ajax({
					type: "GET",
					url: "/vouchers2/deactive_pur_ageing/"+jv_no,
					success: function(result){

					},
					error: function(){
						alert("error");
					}
				});
			}
            pur_customer_name.prop('disabled', false);
            pur_unadjusted_amount.prop('disabled', false);
			$('#pur_prevInvoices').val(1);
        } else{
			document.getElementById('pur_warning').style.display = 'none';
			document.getElementById('PurrefreshBtn').style.display = 'none';

			var table = document.getElementById('pendingInvoices');
        	if (table.rows.length > 0) {
				$.ajax({
					type: "GET",
					url: "/vouchers2/active_pur_ageing/"+jv_no,
					success: function(result){
					},
					error: function(){
						alert("error");
					}
				});
			}
            pur_customer_name.prop('disabled', true);
            pur_unadjusted_amount.prop('disabled', true);
			$('#pur_prevInvoices').val(0);
        }
    }


	function SaletoggleInputs() {
        const customer_name = $('#customer_name');
        const sales_unadjusted_amount = $('#sales_unadjusted_amount');
		const jv_no= $('#jv_no').val();
	
        if ($('#SaletoggleSwitch').is(':checked')) {
			document.getElementById('sales_warning').style.display = 'block';
			document.getElementById('refreshBtn').style.display = 'inline';
			var table = document.getElementById('pendingInvoices');
        	if (table.rows.length > 0) {
				
				$.ajax({
					type: "GET",
					url: "/vouchers2/deactive_sales_ageing/"+jv_no,
					success: function(result){

					},
					error: function(){
						alert("error");
					}
				});
			}
            customer_name.prop('disabled', false);
            sales_unadjusted_amount.prop('disabled', false);
			$('#prevInvoices').val(1);
        } else{
			document.getElementById('sales_warning').style.display = 'none';
			document.getElementById('refreshBtn').style.display = 'none';

			var table = document.getElementById('pendingInvoices');
        	if (table.rows.length > 0) {
				$.ajax({
					type: "GET",
					url: "/vouchers2/active_sales_ageing/"+jv_no,
					success: function(result){
					},
					error: function(){
						alert("error");
					}
				});
			}
            customer_name.prop('disabled', true);
            sales_unadjusted_amount.prop('disabled', true);
			$('#prevInvoices').val(0);
        }
    }

	function refreshSalesAgeing(){
		$('#customer_name').trigger('change');
	}

	function refreshPurAgeing(){
		$('#pur_customer_name').trigger('change');
	}

</script>