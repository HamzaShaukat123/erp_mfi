@include('../layouts.header')
	<body>
		<section class="body">
		@include('../layouts.pageheader')
		<div class="inner-wrapper cust-pad">
				<section role="main" class="content-body" style="margin:0px">
					<form method="post" action="{{ route('update-weight') }}" enctype="multipart/form-data" onkeydown="return event.key != 'Enter';" id="addForm">
						@csrf
						<div class="row">	
							<div class="col-12 mb-3">								
								<section class="card">
									<header class="card-header" style="display: flex;justify-content: space-between;">
										<h2 class="card-title">Edit Quotation Pipes/Garders</h2>
										<div class="card-actions">
											<button type="button" class="btn btn-primary" onclick="addNewRow_btn()"> <i class="fas fa-plus"></i> Add New Row </button>
										</div>
									</header>
									

									<div class="card-body">
										<div class="row form-group mb-2">
											<div class="col-6 col-md-2 mb-2">
												<label class="col-form-label" >Weight No.</label>
												<input type="text" placeholder="(Edit Quotation)" class="form-control" value="{{$pur2->prefix}}{{$pur2->Sale_inv_no}}" disabled>
												<input type="hidden" placeholder="Invoice #" class="form-control" value="{{$pur2->Sale_inv_no}}" name="pur2_id">
												<input type="hidden" id="itemCount" name="items" value="1" class="form-control">
											</div>
											<div class="col-6 col-md-2 mb-2">
												<label class="col-form-label" >Date</label>
												<input type="date" name="sa_date" value="{{$pur2->sa_date}}" autofocus class="form-control">
											</div>
											
											<div class="col-sm-12 col-md-3 mb-3">
												<label class="col-form-label">Customer Name<span style="color: red;"><strong>*</strong></span></label>
												<select data-plugin-selecttwo class="form-control select2-js"  name="account_name" required>
													<option value="" disabled selected>Select Customer Name</option>
													@foreach($coa as $key => $row)	
														<option value="{{$row->ac_code}}" {{ $pur2->account_name == $row->ac_code ? 'selected' : '' }}>{{$row->ac_name}}</option>
													@endforeach
												</select>
											</div>
											
											<div class="col-sm-12 col-md-3 mb-3">
												<label class="col-form-label">Dispatch From<span style="color: red;">*</span></label>
												<select data-plugin-selecttwo class="form-control select2-js"  name="disp_account_name" required>
													<option value="" disabled selected>Select Dispatch From</option>
													@foreach($coa as $key => $row)	
														<option value="{{$row->ac_code}}" {{ $pur2->Cash_pur_name_ac == $row->ac_code ? 'selected' : '' }}>{{$row->ac_name}}</option>
													@endforeach
												</select>
											</div>
											<div class="col-sm-3 col-md-2 mb-2">
												<label class="col-form-label" >Name of Person</label>
												<input type="text" placeholder="Name of Person" name="Cash_pur_name" value="{{$pur2->Cash_pur_name}}" class="form-control">
											</div>
											

											<div class="col-sm-3 col-md-2 mb-2">
												<label class="col-form-label" >Person Address</label>
												<input type="text" placeholder="Person Address" name="cash_Pur_address" value="{{$pur2->cash_Pur_address}}" class="form-control">
											</div>

											<div class="col-sm-12 col-md-4 mb-3">
												<label class="col-form-label">Attachements</label>
												<input type="file" class="form-control" name="att[]" multiple accept=".zip, appliation/zip, application/pdf, image/png, image/jpeg">
											</div>

											<div class="col-6 mb-12">
												<label class="col-form-label">Remarks</label>
												<textarea rows="2" cols="50" name="Sales_Remarks" id="Sales_Remarks"  placeholder="Remarks" class="form-control cust-textarea">{{$pur2->Sales_Remarks}}</textarea>
											</div>	

											
									  </div>
									</div>
									
									<div class="card-body" style="overflow-x:auto;min-height:450px;max-height:450px;overflow-y:auto">
										<table class="table table-bordered table-striped mb-0" id="myTable" >
											<thead>
												<tr>
													<th width="7%">Code<span style="color: red;"><strong>*</strong></span></th>
													<th width="20%">Item Name<span style="color: red;"><strong>*</strong></span></th>
													<th width="20%">Remarks</th>
													<th width="7%">Quantity<span style="color: red;"><strong>*</strong></span></th>
													<th width="7.5%">Price/Unit<span style="color: red;"><strong>*</strong></span></th>
													<th width="7%">Length<span style="color: red;"><strong>*</strong></span></th>
													<th width="7%">Percent<span style="color: red;"><strong>*</strong></span></th>
													<th width="7%">Weight</th>
													<th width="7%">Amount</th>
													<th width="7%">Price Date</th>
													<th width=""></th>
												</tr>
											</thead>
											<tbody id="Quotation2Table">
												@foreach ($pur2_item as $pur2_key => $pur2_items)
													<tr>
														<td>
															<input type="text" class="form-control" name="item_cod[]" id="item_cod{{$pur2_key+1}}" value="{{$pur2_items->item_cod}}" onchange="getItemDetails({{$pur2_key+1}},1)" required>
														</td>
														<td>
															<select data-plugin-selecttwo class="form-control select2-js" autofocus id="item_name{{$pur2_key+1}}" name="item_name[]" onchange="getItemDetails({{$pur2_key+1}},2)" required>
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
												<div class="col-6 col-md-2 pb-sm-3 pb-md-0">
													<label class="col-form-label">Total Amount</label>
													<input type="text" id="totalAmount" name="totalAmount" placeholder="Total Amount" class="form-control" disabled>
												</div>

												<div class="col-6 col-md-2 pb-sm-3 pb-md-0">
													<label class="col-form-label">Total Weight</label>
													<input type="text" id="total_weight" placeholder="Total Weight" class="form-control" disabled>
												</div>

												<div class="col-6 col-md-2 pb-sm-3 pb-md-0">
													<label class="col-form-label">Total Quantity</label>
													<input type="text" id="total_quantity" placeholder="Total Quantity" class="form-control" disabled>
												</div>

												

												
											</div>
										</div>
									</footer>
									<footer class="card-footer">
										<div class="row form-group mb-2">
											<div class="text-end">
												<button type="button" class="btn btn-danger mt-2"  onclick="window.location='{{ route('all-weight') }}'"> <i class="fas fa-trash"></i> Discard Changes</button>
												<button type="submit" class="btn btn-primary mt-2"> <i class="fas fa-save"></i> Update</button>
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

		var table = document.getElementById("Quotation2Table"); // Get the table element
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

		

		document.getElementById('toggleSwitch').addEventListener('change', toggleInputs);
		toggleInputs();
	});

    function removeRow(button) {
		var tableRows = $("#Quotation2Table tr").length;
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
			cell2.innerHTML  = '<select data-plugin-selecttwo class="form-control select-js" id="item_name'+index+'"  onchange="getItemDetails('+index+','+2+')" name="item_name[]" required>'+
									'<option value="" disabled selected>Select Account</option>'+
									'@foreach($items as $key => $row)'+	
                                        '<option value="{{$row->it_cod}}">{{$row->item_name}}</option>'+
                                    '@endforeach'+
								'</select>';
			cell3.innerHTML  = '<input type="text" class="form-control" id="remarks'+index+'" name="remarks[]">';
			cell4.innerHTML  = '<input type="text" class="form-control" onchange="rowTotal('+index+')" id="pur2_qty2_'+index+'" value="0" name="pur2_qty2[]" step="any" required>';
			cell5.innerHTML  = '<input type="number" id="pur2_per_unit'+index+'" class="form-control" name="pur2_per_unit[]" value="0" step="any" required>';
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
				$('#pur2_per_unit'+row_no).val(result[0]['sales_price']);
				$('#pur2_price_date'+row_no).val(result[0]['sale_rate_date']);
				$('#pur2_price_date_show'+row_no).val(result[0]['sale_rate_date']);
				$('#weight_per_piece'+row_no).val(result[0]['weight']);
				$('#weight_per_piece'+row_no+'').trigger('change')
				addNewRow();
			},
			error: function(){
				alert("error");
			}
		});
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
		var tableRows = $("#Quotation2Table tr").length;
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




											

</script>