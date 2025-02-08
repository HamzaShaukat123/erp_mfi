@include('../layouts.header')
	<body>
		<section class="body">
			@include('../layouts.pageheader')
			<div class="inner-wrapper cust-pad">
				<section role="main" class="content-body" style="margin:0px">
					<form method="post" action="{{ route('update-purchases2') }}" enctype="multipart/form-data" onkeydown="return event.key != 'Enter';" id="addForm">
						@csrf
						<div class="row">	
							<div class="col-4 mb-3">								
								<section class="card">
									<header class="card-header">
										<h2 class="card-title">Edit Purchase Details</h2>
									</header>

									<div class="card-body">
										<div class="row form-group mb-2">
											<div class="col-sm-12 col-md-6 mb-2">
												<label class="col-form-label" >Invoice No.</label>
												<input type="text" placeholder="(Edit Invoice)" class="form-control" value="{{$pur2->prefix}}{{$pur2->Sale_inv_no}}" disabled>
												<input type="hidden" placeholder="Invoice #" class="form-control" value="{{$pur2->Sale_inv_no}}" name="pur2_id">
												<input type="hidden" id="itemCount" name="items" value="1" class="form-control">
											</div>
											<div class="col-sm-12 col-md-6 mb-2">
												<label class="col-form-label" >Date</label>
												<input type="date" name="sa_date" value="{{$pur2->sa_date}}" autofocus class="form-control">
											</div>
											<div class="col-sm-12 col-md-6 mb-2">
												<label class="col-form-label" >Mill Inv. No.</label>
												<input type="text" placeholder="Mill Inv. No." name="pur_ord_no" value="{{$pur2->pur_ord_no}}" class="form-control">
											</div>
											<div class="col-sm-12 col-md-6 mb-3">
												<label class="col-form-label">Attachements</label>
												<input type="file" class="form-control" name="att[]" multiple accept=".zip, appliation/zip, application/pdf, image/png, image/jpeg">
											</div>
											<div class="col-sm-12 col-md-12 mb-3">
												<label class="col-form-label">Company Name<span style="color: red;"><strong>*</strong></span></label>
												<select data-plugin-selecttwo class="form-control select2-js"  name="account_name" required>
													<option value="" disabled selected>Select Company Account</option>
													@foreach($coa as $key => $row)	
														<option value="{{$row->ac_code}}" {{ $pur2->account_name == $row->ac_code ? 'selected' : '' }}>{{$row->ac_name}}</option>
													@endforeach
												</select>
											</div>
									  </div>
									</div>
								</section>
							</div>

							<div class="col-4 mb-3">								
								<section class="card">
									<header class="card-header">
										<h2 class="card-title">Dispatch Details</h2>
									</header>

									<div class="card-body">
										<div class="row form-group mb-2">
											<div class="col-sm-12 col-md-6 mb-3">
												<label class="col-form-label">Customer Name<span style="color: red;"><strong>*</strong></span></label>
												<select data-plugin-selecttwo class="form-control select2-js" autofocus name="disp_account_name" required>
													<option value="" disabled selected>Select Customer Account</option>
													@foreach($coa as $key => $row)	
														<option value="{{$row->ac_code}}" {{ $pur2->Cash_pur_name_ac == $row->ac_code ? 'selected' : '' }}>{{$row->ac_name}}</option>
													@endforeach
												</select>
											</div>
											<div class="col-sm-3 col-md-6 mb-2">
												<label class="col-form-label" >Name of Person</label>
												<input type="text" placeholder="Name of Person" name="Cash_pur_name" value="{{$pur2->Cash_pur_name}}" class="form-control">
											</div>

											<div class="col-sm-12 col-md-6 mb-2">
												<label class="col-form-label">Sale Inv#</label>
												<label class="col-form-label" style="cursor: pointer; color: blue; text-decoration: underline; float: right;" id="edit-sale-inv">Enable</label>
												
												<!-- First Input Field (visible and disabled initially) -->
												<input type="text" placeholder="Sale Inv. No." name="sales_against" value="{{$pur2->sales_against}}" id="sale-inv-no" disabled class="form-control">
												
												<!-- Hidden Input Field -->
												<input type="hidden" placeholder="Sale Inv. No." class="form-control" value="{{$pur2->sales_against}}" name="hidden_sales_against" id="hidden-sale-inv-no">
											</div>
											
											

											<div class="col-sm-3 col-md-6 mb-2">
												<label class="col-form-label" >Person Address</label>
												<input type="text" placeholder="Person Address" name="cash_Pur_address" value="{{$pur2->cash_Pur_address}}" class="form-control">
											</div>

											<div class="col-12 mb-12">
												<label class="col-form-label">Remarks</label>
												<textarea rows="2" cols="50" name="Sales_Remarks" id="Sales_Remarks"  placeholder="Remarks" class="form-control cust-textarea">{{$pur2->Sales_Remarks}}</textarea>
											</div>	

									  </div>
									</div>
								</section>
							</div>

							<div class="col-4 mb-3">
								<header class="card-header" style="display: flex;justify-content: space-between;">
									<h2 class="card-title">Commission Form</h2>
									<div class="form-check form-switch">
										@if($pur2->tax_id!=null)
											<!-- <input class="form-check-input" type="checkbox" id="toggleSwitch" checked> -->
											<input type="hidden" class="form-control" name="isCommissionForm" value="1" id="isCommissionForm">
										@else
											<input class="form-check-input" type="checkbox" id="toggleSwitch" unchecked>
											<input type="hidden" class="form-control" name="isCommissionForm" value="0" id="isCommissionForm">
										@endif
									</div>
								</header>								
								<section class="card">
									<div class="card-body" style="background: #2023240f !important">
										<div class="row form-group mb-2">
											<div class="col-sm-6 col-md-6 mb-2">
												<label class="col-form-label" >Basic Amount<span style="color: red;"><strong>*</strong></span></label>
												<input type="number" name="bamount" onchange="CalBillAfterDisc()" id="basic_amount" autofocus value="{{$pur2->bamount}}" class="form-control comm-form-field" required step="any">
											</div>
											<div class="col-sm-2 col-md-2 mb-2">
												<label class="col-form-label" >%<span style="color: red;"><strong>*</strong></span></label>
												<input type="number" name="disc" id="basic_amount_disc" onchange="CalBillAfterDisc()" value="{{$pur2->disc}}" class="form-control comm-form-field" required step="any">
											</div>

											<div class="col-sm-2 col-md-2 mb-2">
												<label class="col-form-label" >P.B<span style="color: red;"><strong>*</strong></span></label>
												<input type="number"  name="cd_disc" value="{{$pur2->cd_disc}}" required class="form-control comm-form-field" step="any">
											</div>

											<div class="col-sm-2 col-md-2 mb-2">
												<label class="col-form-label" >Target<span style="color: red;"><strong>*</strong></span></label>
												<input type="number" name="comm_disc" value="{{$pur2->comm_disc}}" required class="form-control comm-form-field" step="any">
											</div>

											<div class="col-sm-12 col-md-6 mb-2">
												<label class="col-form-label" >Bill Amount After Discount</label>
												<input type="number" id="BillAfterDisc" disabled value="{{((($pur2->bamount * $pur2->disc )/100)+$pur2->bamount)}}" class="form-control comm-form-field">
											</div>

											<div class="col-sm-12 col-md-6 mb-2">
												<label class="col-form-label" >Commission Amount<span style="color: red;"><strong>*</strong></span></label>
												<input type="number" name="comm_amount" value="{{$pur2->comm_amount}}" required class="form-control comm-form-field" step="any">
											</div>
											<div class="col-sm-12 col-md-3 mb-2">
												<label class="col-form-label" >GST<span style="color: red;"><strong>*</strong></span></label>
												<input type="number"  name="gst" value="{{$pur2->gst}}" id="gst_id"  onchange="CalGSTAmount()" required class="form-control comm-form-field" step="any">
											</div>

											<div class="col-sm-12 col-md-3 mb-2">
												<label class="col-form-label" >GST Amount</label>
												<input type="number" id="GSTAmount" disabled
												 value="{{((($pur2->bamount * $pur2->disc )/100)+$pur2->bamount)*(($pur2->gst)/100)}}" 
												 class="form-control comm-form-field">
											</div>

											<div class="col-sm-12 col-md-3 mb-2">
												<label class="col-form-label" >Income Tax<span style="color: red;"><strong>*</strong></span></label>
												<input type="number"  name="income_tax" value="{{$pur2->income_tax}}" onchange="CalITAmount()" id="it_id" required class="form-control comm-form-field" step="any">
											</div>
											<div class="col-sm-12 col-md-3 mb-2">
												<label class="col-form-label" >IT Amount</label>
												<input type="number" id="ITAmount" disabled
												 value="{{(((($pur2->bamount * $pur2->disc )/100)+$pur2->bamount)*(($pur2->gst)/100)
												 +
												 ((($pur2->bamount * $pur2->disc )/100)+$pur2->bamount))*(($pur2->income_tax)/100)}}" 
												class="form-control comm-form-field">
											</div>
											

											<div class="col-sm-12 col-md-6 mb-2">
												<label class="col-form-label" >Item Group<span style="color: red;"><strong>*</strong></span></label>
												<select data-plugin-selecttwo class="form-control comm-form-field select2-js" required  name="tax_item_name">
													<option value="" disabled selected>Select Account</option>
													@foreach($item_group as $key => $row)	
														<option value="{{$row->item_group_cod}}" {{ $pur2->item == $row->item_group_cod ? 'selected' : '' }}>{{$row->group_name}}</option>
													@endforeach
												</select>											
											</div>

											<div class="col-sm-12 col-md-6 mb-2">
												<label class="col-form-label" >Commission Remarks</label>
												<textarea rows="2" cols="50" name="tax_remarks" placeholder="Remarks" class="form-control comm-form-field cust-textarea"> {{$pur2->tax_remarks}} </textarea>
											</div>
									  </div>
									</div>
								</section>
							</div>

							<div class="col-12 mb-3">
								<section class="card">
									<header class="card-header">
										<div class="card-actions">
											<button type="button" class="btn btn-primary" onclick="addNewRow_btn()"> <i class="fas fa-plus"></i> Add New Row </button>
										</div>
										<h2 class="card-title">Edit Purchase 2 Invoice Details</h2>
									</header>
									<div class="card-body" style="overflow-x:auto;min-height:450px;max-height:450px;overflow-y:auto">
										<table class="table table-bordered table-striped mb-0" id="myTable" >
											<thead>
												<tr>
													<th width="7%">Code<span style="color: red;"><strong>*</strong></span></th>
													<th width="20%">Item Name<span style="color: red;"><strong>*</strong></span></th>
													<th width="20%">Remarks</th>
													<th width="7%">Qty<span style="color: red;"><strong>*</strong></span></th>
													<th width="7.5%">Price/Unit<span style="color: red;"><strong>*</strong></span></th>
													<th width="7%">Len.<span style="color: red;"><strong>*</strong></span></th>
													<th width="7%">%<span style="color: red;"><strong>*</strong></span></th>
													<th width="7%">Weight</th>
													<th width="7%">Amount</th>
													<th width="7%">Price Date</th>
													<th width=""></th>
												</tr>
											</thead>
											<tbody id="Purchase2Table">
												@foreach ($pur2_item as $pur2_key => $pur2_items)
													<tr>
														<td>
															<input type="text" class="form-control" name="item_cod[]" autofocus id="item_cod{{$pur2_key+1}}" value="{{$pur2_items->item_cod}}" onchange="getItemDetails({{$pur2_key+1}},1)" required>
														</td>
														<td>
															<select data-plugin-selecttwo class="form-control select2-js"  id="item_name{{$pur2_key+1}}" name="item_name[]" onchange="getItemDetails({{$pur2_key+1}},2)" required>
																<option value="" selected disabled>Select Item</option>
																@foreach($items as $key => $row)	
																	<option value="{{$row->it_cod}}" {{ $pur2_items->item_cod == $row->it_cod ? 'selected' : '' }}>{{$row->item_name}}</option>
																@endforeach
															</select>													
														</td>
														<td>
															<input type="text" class="form-control" id="remarks{{$pur2_key+1}}" value="{{$pur2_items->remarks}}" name="remarks[]">
														</td>	
														<td>
															<input type="number" class="form-control" name="pur2_qty2[]" id="pur2_qty2_{{$pur2_key+1}}" onchange="CalculateRowWeight(1)" value="{{$pur2_items->Sales_qty2}}" step="any" required>
														</td>
														<td>
															<input type="number" class="form-control" name="pur2_per_unit[]" onchange="rowTotal({{$pur2_key+1}})" id="pur2_per_unit{{$pur2_key+1}}" value="{{$pur2_items->sales_price}}" step="any" required>
														</td>
														<td>
															<input type="number" class="form-control" name="pur2_len[]" id="pur2_len{{$pur2_key+1}}" onchange="rowTotal({{$pur2_key+1}})" value="{{$pur2_items->length}}" step="any" required>
														</td>
														<td>
															<input type="number" class="form-control" name="pur2_percentage[]" id="pur2_percentage{{$pur2_key+1}}" onchange="rowTotal({{$pur2_key+1}})" value="{{$pur2_items->discount}}" step="any" required>
															<input type="hidden" class="form-control" name="weight_per_piece[]" id="weight_per_piece{{$pur2_key+1}}" onchange="CalculateRowWeight({{$pur2_key+1}})" value="{{$pur2_items->weight_pc}}" step="any" required>
														</td>
														<td>
															<input type="number" class="form-control" id="pur2_qty{{$pur2_key+1}}" step="any" value="{{$pur2_items->weight_pc * $pur2_items->Sales_qty2}}" required disabled>
															<input type="hidden" class="form-control" name="pur2_qty[]" id="pur2_qty_show{{$pur2_key+1}}" value="{{$pur2_items->weight_pc * $pur2_items->Sales_qty2}}"  step="any" required>
														</td>
														<td>
															<input type="number" class="form-control" id="amount{{$pur2_key+1}}" onchange="tableTotal()" value="{{(($pur2_items->Sales_qty2 * $pur2_items->sales_price)+(($pur2_items->Sales_qty2 * $pur2_items->sales_price) * ($pur2_items->discount/100))) * $pur2_items->length}}" required step="any" disabled>
														</td>
														<td>
															<input type="date" class="form-control" disabled id="pur2_price_date{{$pur2_key+1}}" value="{{$pur2_items->rat_dat}}">
															<input type="hidden" class="form-control" name="pur2_price_date[]" id="pur2_price_date_show{{$pur2_key+1}}" value="{{$pur2_items->rat_dat}}">
														</td>

														<td style="vertical-align: middle;">
															<button type="button" onclick="removeRow(this)" class="btn btn-danger" tabindex="1"><i class="fas fa-times"></i></button>
														</td>
													</tr>
												@endforeach
											</tbody>
										</table>
									</div>

									<footer class="card-footer" >
										<div class="row">
											<div class="row form-group mb-3">
												<div class="col-sm-2 col-md-2 pb-sm-3 pb-md-0">
													<label class="col-form-label">Total Amount</label>
													<input type="text" id="totalAmount" name="totalAmount" placeholder="Total Amount" class="form-control" disabled>
												</div>

												<div class="col-sm-2 col-md-2 pb-sm-3 pb-md-0">
													<label class="col-form-label">Total Weight</label>
													<input type="text" id="total_weight" placeholder="Total Weight" class="form-control" disabled>
												</div>

												<div class="col-sm-2 col-md-2 pb-sm-3 pb-md-0">
													<label class="col-form-label">Total Quantity</label>
													<input type="text" id="total_quantity" placeholder="Total Quantity" class="form-control" disabled>
												</div>

												<div class="col-sm-2 col-md-2 pb-sm-3 pb-md-0">
													<label class="col-form-label">Convance Charges</label>
													<input type="text" required id="convance_charges" onchange="netTotal()" value="{{$pur2->ConvanceCharges}}"  name="ConvanceCharges"  placeholder="Convance Charges" class="form-control">
												</div>

												<div class="col-sm-2 col-md-2 pb-sm-3 pb-md-0">
													<label class="col-form-label">Labour Charges</label>
													<input type="text" required id="labour_charges"  onchange="netTotal()" value="{{$pur2->LaborCharges}}"  name="LaborCharges" placeholder="Labour Charges" class="form-control">
												</div>

												<div class="col-sm-2 col-md-2 pb-sm-3 pb-md-0">
													<label class="col-form-label">Bill Discount </label>
													<div class="row">
														<div class="col-8">
															<input type="number" required id="bill_discount" onchange="netTotal()" value="{{$pur2->Bill_discount}}"  name="Bill_discount" placeholder="Bill Discount" class="form-control">
														</div>
														<div class="col-4">
															<input type="text" id="bill_perc" class="form-control" placeholder="0%" disabled>
														</div>
													</div>
												</div>

												<div class="col-sm-2 col-md-12 pb-sm-3 pb-md-0">
													<h3 class="font-weight-bold mt-3 mb-0 text-5 text-end text-primary">Net Amount</h3>
													<span class="d-flex align-items-center justify-content-lg-end">
														<strong class="text-4 text-primary">PKR <span id="netTotal" class="text-4 text-danger">0.00 </span></strong>
													</span>
												</div>
											</div>
										</div>
									</footer>
									<footer class="card-footer">
										<div class="row form-group mb-2">
											<div class="text-end">
												<button type="button" class="btn btn-danger mt-2"  onclick="window.location='{{ route('all-purchases2-paginate') }}'"> <i class="fas fa-trash"></i> Discard Changes</button>
												<button type="submit" class="btn btn-primary mt-2"> <i class="fas fa-save"></i> Update Invoice</button>
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
		$(window).keydown(function(event){
			if(event.keyCode == 13) {
				event.preventDefault();
				return false;
			}
		});

		var totalAmount=0, totalWeight=0, totalQuantity=0, netAmount=0, amount=0, weight=0, quantity=0;

		var table = document.getElementById("Purchase2Table"); // Get the table element
        var rowCount = table.rows.length; // Get the total number of rows

		itemCount = rowCount;
		index = rowCount+1;

		$('#itemCount').val(itemCount);

		for (var j=0;j<rowCount; j++){

			quantity = table.rows[j].cells[3].querySelector('input').value; // Get the value of the input field in the specified cell
			totalQuantity = totalQuantity + Number(quantity);

			weight = table.rows[j].cells[7].querySelector('input').value; // Get the value of the input field in the specified cell
			totalWeight = totalWeight + Number(weight);

			amount = table.rows[j].cells[8].querySelector('input').value; // Get the value of the input field in the specified cell
			totalAmount = totalAmount + Number(amount);
		}
		FormattedTotalWeight = totalWeight.toFixed();
		FormattedTotalQuantity = totalQuantity.toFixed();
		FormattedTotalAmount = totalAmount.toFixed();

		$('#total_quantity').val(FormattedTotalQuantity);
		$('#total_weight').val(FormattedTotalWeight);
		$('#totalAmount').val(FormattedTotalAmount);

		var convance_charges = Number($('#convance_charges').val());
		var labour_charges = Number($('#labour_charges').val());
		var bill_discount = Number($('#bill_discount').val());

		netAmount = totalAmount + convance_charges +  labour_charges - bill_discount;
		FormattednetTotal = formatNumberWithCommas(netAmount.toFixed(0));
		document.getElementById("netTotal").innerHTML = '<span class="text-4 text-danger">'+FormattednetTotal+'</span>';

		document.getElementById('toggleSwitch').addEventListener('change', toggleInputs);
		toggleInputs();
	});

    function removeRow(button) {
		var confirmation = confirm("Are you sure you want to remove this row?");
		if (confirmation) {
			var tableRows = $("#Purchase2Table tr").length;
			if (tableRows > 1) {
				var row = button.parentNode.parentNode;
				row.parentNode.removeChild(row);
				index--;
				var itemCount = Number($('#itemCount').val());
				itemCount = itemCount - 1;
				$('#itemCount').val(itemCount);
			}
			tableTotal();
		} else {
			// Do nothing if the user selects "No"
			
		}
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
			cell2.innerHTML  = '<select data-plugin-selecttwo class="form-control select2-js" id="item_name'+index+'"  onchange="getItemDetails('+index+','+2+')" name="item_name[]" required>'+
									'<option value="" disabled selected>Select Account</option>'+
									'@foreach($items as $key => $row)'+	
                                        '<option value="{{$row->it_cod}}">{{$row->item_name}}</option>'+
                                    '@endforeach'+
								'</select>';
			cell3.innerHTML  = '<input type="text" class="form-control" id="remarks'+index+'" name="remarks[]">';
			cell4.innerHTML  = '<input type="text" class="form-control" onchange="rowTotal('+index+')" id="pur2_qty2_'+index+'" value="0" name="pur2_qty2[]" step="any" required>';
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
				$('#item_name'+row_no).val(result[0]['it_cod']).select2();
				$('#remarks'+row_no).val(result[0]['item_remark']);
				$('#pur2_per_unit'+row_no).val(result[0]['OPP_qty_cost']);
				$('#pur2_price_date'+row_no).val(result[0]['pur_rate_date']);
				$('#pur2_price_date_show'+row_no).val(result[0]['pur_rate_date']);
				$('#weight_per_piece'+row_no).val(result[0]['weight']);
				$('#weight_per_piece'+row_no+'').trigger('change')
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
		$('#BillAfterDisc').val(sum);

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
		FormattedTotalWeight = totalWeight.toFixed();

		$('#totalAmount').val(totalAmount.toFixed());
		$('#total_amount_show').val(totalAmount);
		$('#total_weight').val(FormattedTotalWeight);
		$('#total_weight_show').val(FormattedTotalWeight);
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

		var bill_perc = ((bill_discount/total)*100).toFixed() + ' %';
		
		$('#bill_perc').val(bill_perc);
	}
 
	function formatNumberWithCommas(number) {
    	// Convert number to string and add commas
    	return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}

	function CalculateRowWeight(index){
		var pur2_qty = $('#pur2_qty2_'+index+'').val();
		var weight_per_piece = $('#weight_per_piece'+index+'').val();

		rowWeight= pur2_qty*weight_per_piece;
		$('#pur2_qty'+index+'').val(rowWeight);
		rowTotal(index);
	}

	function toggleInputs() {
		const isChecked = document.getElementById('toggleSwitch').checked;
		const inputGroups = document.querySelectorAll('.comm-form-field');
		inputGroups.forEach(input => {
			// Show or hide input groups based on the toggle switch state
			if (input.id !== 'BillAfterDisc' && input.id !== 'GSTAmount' && input.id !== 'ITAmount') {
				input.disabled = !isChecked;
			}
		});
		
		var switchElement = document.getElementById('toggleSwitch');
		if(switchElement.checked){
			$('#isCommissionForm').val(1);
		}
		else{
			$('#isCommissionForm').val(0);
		}
	}

	document.getElementById('edit-sale-inv').addEventListener('click', function () {
        var inputField = document.getElementById('sale-inv-no');
        if (inputField.disabled) {
            inputField.disabled = false;
            inputField.focus(); // Focus on the input when enabled
            this.textContent = "Disable"; // Change the label to "Save" when editing
        } else {
            inputField.disabled = true;
            this.textContent = "Enable"; // Change the label back to "Edit" after saving
        }
    });

		 // Update the hidden input field on change of the first input field
		 document.getElementById('sale-inv-no').addEventListener('input', function() {
        var hiddenSaleInvInput = document.getElementById('hidden-sale-inv-no');
        hiddenSaleInvInput.value = this.value;  // Update the hidden input with the new value
    });
											

</script>