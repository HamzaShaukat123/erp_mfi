@include('../layouts.header')
	<body>
		<section class="body">
			@include('../layouts.pageheader')
			<div class="inner-wrapper cust-pad">
				<section role="main" class="content-body" style="margin:0px">
					<form method="post" action="{{ route('store-purchases2') }}" enctype="multipart/form-data" onkeydown="return event.key != 'Enter';" id="addForm">
						@csrf
						<div class="row">	
							<div class="col-12 col-md-4 mb-3">								
								<section class="card">
									<header class="card-header">
										<h2 class="card-title">Purchase Details</h2>
									</header>

									<div class="card-body">
										<div class="row form-group mb-2">
											<div class="col-6 mb-2">
												<label class="col-form-label" >Invoice No.</label>
												<input type="text" placeholder="(New Invoice)" class="form-control" disabled>
												<input type="hidden" id="itemCount" name="items" value="1" class="form-control">
											</div>
											<div class="col-6 mb-2">
												<label class="col-form-label" >Date</label>
												<input type="date" name="sa_date" autofocus value="<?php echo date('Y-m-d'); ?>" class="form-control">
											</div>
											<div class="col-6 mb-2">
												<label class="col-form-label" >Mill Inv. No.</label>
												<input type="text" placeholder="Mill Inv. No." name="pur_ord_no" class="form-control">
											</div>
											<div class="col-6 mb-3">
												<label class="col-form-label">Attachements</label>
												<input type="file" class="form-control" name="att[]" multiple accept=".zip, appliation/zip, application/pdf, image/png, image/jpeg">
											</div>
											<div class="col-sm-12 col-md-12 mb-3">
												<label class="col-form-label">Company Name <span style="color: red;">*</span></label>
												<select data-plugin-selecttwo class="form-control select2-js"  name="account_name" required>
													<option value="" disabled selected>Select Company Account</option>
													@foreach($coa as $key => $row)	
														<option value="{{$row->ac_code}}">{{$row->ac_name}}</option>
													@endforeach
												</select>
											</div>								
									  	</div>
									</div>
								</section>
							</div>

							<div class="col-12 col-md-4 mb-3">								
								<section class="card">
									<header class="card-header">
										<h2 class="card-title">Dispatch Details</h2>
									</header>

									<div class="card-body">
										<div class="row form-group mb-2">
											<div class="col-sm-12 col-md-6 mb-3">
												<label class="col-form-label">Customer Name<span style="color: red;"><strong>*</strong></span></label>
												<select data-plugin-selecttwo class="form-control select2-js"  name="disp_account_name" required>
													<option value="" disabled selected>Select Customer Account</option>
													@foreach($coa as $key => $row)	
														<option value="{{$row->ac_code}}">{{$row->ac_name}}</option>
													@endforeach
												</select>
											</div>
											<div class="col-sm-3 col-md-6 mb-2">
												<label class="col-form-label" >Name of Person</label>
												<input type="text" placeholder="Name of Person" name="Cash_pur_name" class="form-control">
											</div>
											<div class="col-sm-12 col-md-6 mb-2">
												<label class="col-form-label" >Sale Inv. No.</label>
												<input type="text" placeholder="Sale Inv. No." name="sal_inv_no" disabled class="form-control">
											</div>

											<div class="col-sm-3 col-md-6 mb-2">
												<label class="col-form-label" >Person Address</label>
												<input type="text" placeholder="Person Address" name="cash_Pur_address" class="form-control">
											</div>

											<div class="col-12 mb-12">
												<label class="col-form-label">Remarks</label>
												<textarea rows="2" cols="50" name="Sales_Remarks" id="Sales_Remarks" placeholder="Remarks" class="form-control cust-textarea"></textarea>
											</div>	

									  </div>
									</div>
								</section>
							</div>

							<div class="col-12 col-md-4 mb-3">
								<header class="card-header" style="display: flex;justify-content: space-between;">
									<h2 class="card-title">Commission Form</h2>
									<div class="form-check form-switch">
										<input class="form-check-input" type="checkbox" value="0" id="toggleSwitch" onchange="toggleInputs(this)">
										<input type="hidden" class="form-control" name="isCommissionForm" value="0" id="isCommissionForm">
									</div>
								</header>								
								<section class="card">
									<div class="card-body" style="background: #2023240f !important">
										<div class="row form-group mb-2">
											<div class="col-6 mb-2">
												<label class="col-form-label" >Basic Amount<span style="color: red;"><strong>*</strong></span></label>
												<input type="number" name="bamount" onchange="CalBillAfterDisc()"  autofocus id="basic_amount" required value="0" step="any" class="form-control comm-form-field" disabled>
											</div>
											<div class="col-2 mb-2">
												<label class="col-form-label" >%<span style="color: red;"><strong>*</strong></span></label>
												<input type="number" value="0" name="disc" id="basic_amount_disc" required onchange="CalBillAfterDisc()" step="any" class="form-control comm-form-field" disabled>
											</div>

											<div class="col-2 mb-2">
												<label class="col-form-label" >P.B<span style="color: red;"><strong>*</strong></span></label>
												<input type="number" value="0"  name="cd_disc" step="any" required class="form-control comm-form-field" disabled>
											</div>

											<div class="col-2 mb-2">
												<label class="col-form-label" >Target<span style="color: red;"><strong>*</strong></span></label>
												<input type="number" value="0" name="comm_disc" step="any" required class="form-control comm-form-field" disabled>
											</div>

											<div class="col-6 mb-2">
												<label class="col-form-label" >Bill Amount After Discount</label>
												<input type="number" value="0" id="BillAfterDisc" step="any" required disabled class="form-control comm-form-field">
											</div>

											<div class="col-6 mb-2">
												<label class="col-form-label" >Commission Amount<span style="color: red;"><strong>*</strong></span></label>
												<input type="number" value="0" name="comm_amount" step="any" required class="form-control comm-form-field" disabled>
											</div>

											<div class="col-3 mb-2">
												<label class="col-form-label" >GST<span style="color: red;"><strong>*</strong></span></label>
												<input type="number" value="18" name="gst" id="gst_id" onchange="CalGSTAmount()" step="any" required class="form-control comm-form-field" disabled>
											</div>
											<div class="col-3 mb-2">
												<label class="col-form-label" >GST Amount</label>
												<input type="number" value="0" id="GSTAmount"  step="any" required disabled class="form-control comm-form-field">
											</div>
											<div class="col-3 mb-2">
												<label class="col-form-label" >Income Tax<span style="color: red;"><strong>*</strong></span></label>
												<input type="number" value="0" name="income_tax" id="it_id" step="any" onchange="CalITAmount()" required class="form-control comm-form-field" disabled>
											</div>
											<div class="col-3 mb-2">
												<label class="col-form-label" >IT Amount</label>
												<input type="number" value="0" id="ITAmount"  step="any" required disabled class="form-control comm-form-field">
											</div>

											<div class="col-sm-12 col-md-6 mb-2">
												<label class="col-form-label" >Item Group<span style="color: red;"><strong>*</strong></span></label>
												<select data-plugin-selecttwo class="form-control comm-form-field select2-js"  name="tax_item_name" required disabled>
													<option value="" disabled selected>Select Account</option>
													@foreach($item_group as $key => $row)	
														<option value="{{$row->item_group_cod }}">{{$row->group_name}}</option>
													@endforeach
												</select>											
											</div>

											<div class="col-sm-12 col-md-6 mb-2">
												<label class="col-form-label" >Commission Remarks</label>
												<textarea rows="2" cols="50" name="tax_remarks" placeholder="Remarks" class="form-control comm-form-field cust-textarea" disabled></textarea>
											</div>
									  </div>
									</div>
								</section>
							</div>

							<div class="col-12 mb-3">
								<section class="card">
									<header class="card-header" style="display: flex;justify-content: space-between;">
										<h2 class="card-title">New Purchase Pipe</h2>

										<div class="card-actions">
											<button type="button" class="btn btn-primary" onclick="addNewRow_btn()"> <i class="fas fa-plus"></i> Add New Row </button>
										</div>
									</header>
									<div class="card-body" style="overflow-x:auto;min-height:450px;max-height:450px;overflow-y:auto">
										<table class="table table-bordered table-striped mb-0" id="myTable" >
											<thead>
												<tr>
													<th width="7%">Code<span style="color: red;"><strong>*</strong></span></th>
													<th width="20%">Item Name<span style="color: red;"><strong>*</strong></span></th>
													<th width="20%">Remarks</th>
													<th width="7%">Qty<span style="color: red;"><strong>*</strong></span></th>
													<th width="7,5%">Price/Unit<span style="color: red;"><strong>*</strong></span></th>
													<th width="7%">Len<span style="color: red;"><strong>*</strong></span></th>
													<th width="7%">%<span style="color: red;"><strong>*</strong></span></th>
													<!-- <th width="7%">Weight/Pc</th> -->
													<th width="7%">Weight</th>
													<th width="7%">Amount</th>
													<th width="7%">Price Date</th>
													<th width=""></th>
												</tr>
											</thead>
											<tbody id="Purchase2Table">
												<tr>
													<td>
														<input type="text" class="form-control" name="item_cod[]"  autofocus id="item_cod1" onchange="getItemDetails(1,1)" required>
													</td>
													<td>
														<select data-plugin-selecttwo class="form-control select2-js"  id="item_name1" name="item_name[]" onchange="getItemDetails(1,2)" required>
															<option value="" selected disabled>Select Item</option>
															@foreach($items as $key => $row)	
																<option value="{{$row->it_cod}}">{{$row->item_name}}</option>
															@endforeach
														</select>													
													</td>
													<td>
														<input type="text" class="form-control" id="remarks1" name="remarks[]">
													</td>	
													<td>
														<input type="number" class="form-control" name="pur2_qty2[]" id="pur2_qty2_1" onchange="CalculateRowWeight(1)" value="0" step="any" required>
													</td>
													<td>
														<input type="number" class="form-control" name="pur2_per_unit[]" onchange="rowTotal(1)" id="pur2_per_unit1" value="0" step="any" required>
                                                    </td>
													<td>
														<input type="number" class="form-control" name="pur2_len[]" id="pur2_len1" onchange="rowTotal(1)" value="20" step="any" required>
                                                    </td>
                                                    <td>
														<input type="number" class="form-control" name="pur2_percentage[]" id="pur2_percentage1" onchange="rowTotal(1)" value="0" step="any" required>
														<input type="hidden" class="form-control" name="weight_per_piece[]" id="weight_per_piece1" onchange="CalculateRowWeight(1)" value="0" step="any" required>
													</td>
													<td>
														<input type="number" class="form-control" id="pur2_qty1" value="0"  step="any" required disabled>
														<input type="hidden" class="form-control" name="pur2_qty[]" id="pur2_qty_show1" value="0"  step="any" required>
													</td>
													<td>
														<input type="number" class="form-control" id="amount1" onchange="tableTotal()" value="0" required step="any" disabled>
													</td>
													<td>
														<input type="date" class="form-control" disabled id="pur2_price_date1">
														<input type="hidden" class="form-control" name="pur2_price_date[]" id="pur2_price_date_show1">
													</td>

													<td style="vertical-align: middle;">
														<button type="button" onclick="removeRow(this)" class="btn btn-danger" tabindex="1"><i class="fas fa-times"></i></button>
													</td>
												</tr>
											</tbody>
										</table>
									</div>

									<footer class="card-footer" >
										<div class="row">
											<div class="row form-group mb-3">
												<div class="col-6 col-md-2 pb-sm-3 pb-md-0">
													<label class="col-form-label">Total Amount</label>
													<input type="number" id="totalAmount" name="totalAmount" placeholder="Total Amount" class="form-control" disabled>
												</div>

												<div class="col-6 col-md-2 pb-sm-3 pb-md-0">
													<label class="col-form-label">Total Weight</label>
													<input type="number" id="total_weight" placeholder="Total Weight" class="form-control" disabled>
												</div>

												<div class="col-6 col-md-2 pb-sm-3 pb-md-0">
													<label class="col-form-label">Total Quantity</label>
													<input type="number" id="total_quantity" placeholder="Total Quantity" class="form-control" disabled>
												</div>

												<div class="col-6 col-md-2 pb-sm-3 pb-md-0">
													<label class="col-form-label">Convance Charges</label>
													<input type="number" id="convance_charges" onchange="netTotal()" name="ConvanceCharges" placeholder="Convance Charges" class="form-control">
												</div>

												<div class="col-6 col-md-2 pb-sm-3 pb-md-0">
													<label class="col-form-label">Labour Charges</label>
													<input type="number" id="labour_charges"  onchange="netTotal()" name="LaborCharges" placeholder="Labour Charges" class="form-control">
												</div>

												<div class="col-12 col-md-2 pb-sm-3 pb-md-0">
													<label class="col-form-label">Bill Discount </label>
													<div class="row">
														<div class="col-8">
															<input type="number" id="bill_discount" onchange="netTotal()" name="Bill_discount" value="0" placeholder="Bill Discount" class="form-control">
														</div>
														<div class="col-4">
															<input type="text"  id="bill_perc" class="form-control" placeholder="0%" disabled>
														</div>
													</div>
												</div>

												<div class="col-12 pb-sm-3 pb-md-0 text-end">
													<h3 class="font-weight-bold mt-3 mb-0 text-5 text-primary">Net Amount</h3>
													<span>
														<strong class="text-4 text-primary">PKR <span id="netTotal" class="text-4 text-danger">0.00 </span></strong>
													</span>
												</div>
											</div>
										</div>
									</footer>
									<footer class="card-footer">
										<div class="row form-group mb-2">
											<div class="text-end">
												{{-- <button type="button" class="btn btn-danger mt-2"  onclick="window.location='{{ route('all-purchases2-paginate') }}'"> <i class="fas fa-trash"></i> Discard Invoice</button>
												--}}

												<button type="button" class="btn btn-danger mt-2" onclick="confirmDiscard()">
													<i class="fas fa-trash"></i> Discard Invoice
												</button>
												{{-- <button type="submit" class="btn btn-primary mt-2" id="submitBtn"><i class="fas fa-save"></i> Add Invoice</button> --}}

												<button type="submit" class="btn btn-primary mt-2" id="submitBtn"><i class="fas fa-save"></i> Add Invoice</button>

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
	var index=2;
	var itemCount = Number($('#itemCount').val());

	$(document).ready(function() {
		// Prevent default action of Enter key
		$(window).keydown(function(event) {
			if (event.keyCode === 13) {
				event.preventDefault();
				return false;
			}
		});

		
	});

	$('#addForm').on('submit', function(e){
		e.preventDefault(); // Prevent default submission initially

		const form = this;

		if (!form.checkValidity()) {
			// Let browser show HTML5 validation messages
			form.reportValidity();
			return;
		}

		// Perform your custom logic here...

		// If all conditions pass:
		$('#submitBtn').prop('disabled', true); // disable the button
		form.submit(); // now submit the form programmatically
	});


	function confirmDiscard() {
		if (confirm('Do you want to discard this invoice?')) {
		window.location = "{{ route('all-purchases2-paginate') }}";
		}
	}

	function removeRow(button) {
		var tableRows = $("#Purchase2Table tr").length;
		if(tableRows>1){
			var row = button.parentNode.parentNode;
			row.parentNode.removeChild(row);
			index--;	
			itemCount = Number($('#itemCount').val());
			itemCount = itemCount-1;
			$('#itemCount').val(itemCount);
		}  
		tableTotal(); 
	}

	function addNewRow(){
		var lastRow =  $('#myTable tr:last');
		latestValue=lastRow[0].cells[1].querySelector('select').value;
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
			var cell10 = newRow.insertCell(9);
			var cell11 = newRow.insertCell(10);


			cell1.innerHTML  = '<input type="text" class="form-control" name="item_cod[]" id="item_cod'+index+'" autofocus onchange="getItemDetails('+index+','+1+')" required>';
			cell2.innerHTML  = '<select data-plugin-selecttwo class="form-control select2-js" id="item_name'+index+'" onchange="getItemDetails('+index+','+2+')" name="item_name[]" required>'+
									'<option value="" disabled selected>Select Item</option>'+
									'@foreach($items as $key => $row)'+	
										'<option value="{{$row->it_cod}}">{{$row->item_name}}</option>'+
									'@endforeach'+
								'</select>';
			cell3.innerHTML  = '<input type="text" class="form-control" id="remarks'+index+'" name="remarks[]">';
			cell4.innerHTML  = '<input type="number" class="form-control" onchange="rowTotal('+index+')" id="pur2_qty2_'+index+'" value="0" name="pur2_qty2[]" step="any" required>';
			cell5.innerHTML  = '<input type="number" id="pur2_per_unit'+index+'" onchange="rowTotal('+index+')" class="form-control" name="pur2_per_unit[]" value="0" step="any" required>';
			cell6.innerHTML  = '<input type="number" id="pur2_len'+index+'" onchange="rowTotal('+index+')" class="form-control" name="pur2_len[]"  value="20" step="any" required>';
			cell7.innerHTML  = '<input type="number" class="form-control" name="pur2_percentage[]" onchange="rowTotal('+index+')" id="pur2_percentage'+index+'" value="0" step="any" required> <input type="hidden" class="form-control" id="weight_per_piece'+index+'" name="weight_per_piece[]" onchange="CalculateRowWeight('+index+')" value="0" step="any" required>';
			cell8.innerHTML  = '<input type="number" class="form-control" id="pur2_qty'+index+'" value="0" step="any" required disabled><input type="hidden" class="form-control" name="pur2_qty[]" id="pur2_qty_show1" value="0" step="any" required>';
			cell9.innerHTML  = '<input type="number" id="amount'+index+'" class="form-control"  value="0" step="any" disabled>';
			cell10.innerHTML = '<input type="date" disabled class="form-control" id="pur2_price_date'+index+'" required><input type="hidden" class="form-control" name="pur2_price_date[]" id="pur2_price_date_show'+index+'">';
			cell11.innerHTML = '<button type="button" onclick="removeRow(this)" class="btn btn-danger" tabindex="1"><i class="fas fa-times"></i></button>';

			index++;

			itemCount = Number($('#itemCount').val());
			itemCount = itemCount+1;
			$('#itemCount').val(itemCount);
			$('#myTable select[data-plugin-selecttwo]').select2();
			


		}
	}

	function addNewRow_btn() {

		addNewRow(); // Call the same function
		// Set focus on the new item_code input field
		document.getElementById('item_cod' + (index - 1)).focus();


	}

	function getItemDetails(row_no,option){
		var itemId;
		if(option==1){
			itemId = document.getElementById("item_cod"+row_no).value;
		}
		else if(option==2){
			itemId = document.getElementById("item_name"+row_no).value;
		}
		$.ajax({
			type: "GET",
			url: "/item2/detail",
			data: {id:itemId},
			success: function(result){
				$('#item_cod'+row_no).val(result[0]['it_cod']);
				$('#item_name' + row_no).val(result[0]['it_cod']).select2();
				$('#remarks'+row_no).val(result[0]['item_remark']);
				$('#pur2_per_unit'+row_no).val(result[0]['OPP_qty_cost']);
				$('#pur2_price_date'+row_no).val(result[0]['pur_rate_date']);
				$('#pur2_price_date_show'+row_no).val(result[0]['pur_rate_date']);
				$('#weight_per_piece'+row_no).val(result[0]['weight']).trigger('change');
				addNewRow();
			},
			error: function(){
				alert("error");
			}
		});
	}

	function CalBillAfterDisc(){
		var basic_amount = parseFloat($('#basic_amount').val());
		var basic_amount_disc = parseFloat($('#basic_amount_disc').val());

		sum= ((basic_amount * basic_amount_disc )/100)+basic_amount;
		$('#BillAfterDisc').val(sum.toFixed(0));

		// Update GST and IT amount based on the discounted bill
		CalGSTAmount();
		CalITAmount();
	}

	function CalGSTAmount() {
        var gst_id = parseFloat($('#gst_id').val());
        var BillAfterDisc = parseFloat($('#BillAfterDisc').val());

        if (!isNaN(gst_id) && !isNaN(BillAfterDisc)) {
            var sum = (BillAfterDisc * gst_id) / 100;
            $('#GSTAmount').val(sum.toFixed(0));
        }
    }

	function CalITAmount() {
		var it_id = parseFloat($('#it_id').val());
		var GSTAmount = parseFloat($('#GSTAmount').val());
		var BillAfterDisc = parseFloat($('#BillAfterDisc').val());

		if (!isNaN(it_id) && !isNaN(BillAfterDisc) && !isNaN(GSTAmount)) {
			// Calculate the income tax amount
			var sum = ((BillAfterDisc + GSTAmount) * it_id) / 100;
			$('#ITAmount').val(sum.toFixed(0));
		}
	}



	function getCOADetails(){
		var coaId = document.getElementById("coa_name").value;
		
		$.ajax({
			type: "GET",
			url: "/coa/detail",
			data: {id:coaId},
			success: function(result){
				$('#address').val(result[0]['address']);
				$('#cash_pur_phone').val(result[0]['phone_no']);
				$('#remarks').val(result[0]['remarks']);
			},
			error: function(){
				alert("error");
			}
		});
	}

	function rowTotal(index){

		var pur2_qty2 = parseFloat($('#pur2_qty2_'+index+'').val());
		var sales_price = parseFloat($('#pur2_per_unit'+index+'').val());   
		var discount = parseFloat($('#pur2_percentage'+index+'').val());
		var length = parseFloat($('#pur2_len'+index+'').val());
		var weight_per_piece = parseFloat($('#weight_per_piece'+index+'').val());

		var amount = ((pur2_qty2 * sales_price)+((pur2_qty2 * sales_price) * (discount/100))) * length;
		var weight = (pur2_qty2*weight_per_piece);

		$('#amount'+index+'').val(amount);
		$('#pur2_qty'+index+'').val(weight);
		$('#pur2_qty_show'+index+'').val(weight);

		tableTotal();
	}

	function tableTotal(){
		var totalAmount=0;
		var totalWeight=0;
		var totalQuantity=0;
		var tableRows = $("#Purchase2Table tr").length;
		var table = document.getElementById('myTable').getElementsByTagName('tbody')[0];

		for (var i = 0; i < tableRows; i++) {
			var currentRow =  table.rows[i];
			totalAmount = totalAmount + Number(currentRow.cells[8].querySelector('input').value);
			totalWeight = totalWeight + Number(currentRow.cells[7].querySelector('input').value);
			totalQuantity = totalQuantity + Number(currentRow.cells[3].querySelector('input').value);
		}
		
		$('#totalAmount').val(totalAmount.toFixed());
		$('#total_amount_show').val(totalAmount);
		$('#total_weight').val(totalWeight);
		$('#total_weight_show').val(totalWeight);
		$('#total_quantity').val(totalQuantity);
		$('#total_quantity_show').val(totalQuantity);
		netTotal();
	}

	function netTotal(){
		var netTotal = 0;
		var total = Number($('#totalAmount').val());
		var convance_charges = Number($('#convance_charges').val());
		var labour_charges = Number($('#labour_charges').val());
		var bill_discount = Number($('#bill_discount').val());

		netTotal = total + convance_charges + labour_charges - bill_discount;
		netTotal = netTotal.toFixed(0);
		FormattednetTotal = formatNumberWithCommas(netTotal);
		document.getElementById("netTotal").innerHTML = '<span class="text-4 text-danger">'+FormattednetTotal+'</span>';

		$('#net_amount').val(netTotal);

		var bill_perc = ((bill_discount/total)*100) + ' %';

		$('#bill_perc').val(bill_perc);

	}

	function formatNumberWithCommas(number) {
		// Convert number to string and add commas
		return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}

	function CalculateRowWeight(index){
		var pur2_qty = Number($('#pur2_qty2_'+index+'').val());
		var weight_per_piece = Number($('#weight_per_piece'+index+'').val());

		rowWeight= Number(pur2_qty*weight_per_piece);

		$('#pur2_qty'+index+'').val(rowWeight);

		rowTotal(index);
	}

	function toggleInputs(checkbox) {
		const inputGroups = document.querySelectorAll('.comm-form-field');
		const isChecked = checkbox.checked;

		inputGroups.forEach(input => {
			// Show or hide input groups based on the toggle switch state
			if (input.id !== 'BillAfterDisc' && input.id !== 'GSTAmount' && input.id !== 'ITAmount') {
				input.disabled = !isChecked;
			}
			
		});

		// Update hidden input field
		$('#isCommissionForm').val(isChecked ? 1 : 0);
	}


</script>