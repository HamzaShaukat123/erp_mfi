@include('../layouts.header')
	<body>
		<section class="body">
			@include('../layouts.pageheader')
			<div class="inner-wrapper cust-pad">
				<section role="main" class="content-body" style="margin:0px">
					<form method="post" action="{{ route('update-sale-invoice') }}" enctype="multipart/form-data" onkeydown="return event.key != 'Enter';">
						@csrf
						<div class="row">
							<div class="col-12 mb-3">								
								<section class="card">
									<header class="card-header"  style="display: flex;justify-content: space-between;">
										<h2 class="card-title">Edit Sale Invoice</h2>
										<div class="card-actions">
											<button type="button" class="btn btn-primary" onclick="addNewRow_btn()"> <i class="fas fa-plus"></i> Add New Row </button>
										</div>
									</header>


									<div class="card-body">
										<div class="row form-group mb-2">
											<div class="col-6 col-md-2 mb-2">
												<label class="col-form-label" >Invoice no.</label>
												<input type="text" placeholder="Invoice No." class="form-control" disabled value="{{$sales->prefix}}{{$sales->Sal_inv_no}}">
												<input type="hidden" name="invoice_no" placeholder="Invoice No." class="form-control" value="{{$sales->Sal_inv_no}}">
												<input type="hidden" id="itemCount" name="items" class="form-control" >
											</div>

											<div class="col-6 col-md-2 mb-2">
												<label class="col-form-label" >Date</label>
												<input type="date" name="date" required class="form-control" value="{{$sales->sa_date}}">
											</div>

											<div class="col-6 col-md-2">
												<label class="col-form-label" >Bill No.</label>
												<input type="text" name="bill_no" placeholder="Bill No." class="form-control" value="{{$sales->pur_ord_no}}">
											</div>

											<div class="col-6 col-md-2">
												<label class="col-form-label">Status</label>
												<select class="form-control mb-3" id="bill_status" name="bill_status" required>
													<option value="0" {{ $sales->bill_not == 0 ? 'selected' : '' }}>Bill Not Final</option>
													<option value="1" {{ $sales->bill_not == 1 ? 'selected' : '' }}>Finalized</option>
												</select>
												
											</div>

											<div class="col-12 col-md-4">
												<label class="col-form-label">File Attached</label>
												<input type="file" class="form-control" name="att[]" multiple accept=".zip, appliation/zip, application/pdf, image/png, image/jpeg">
											</div>

											<div class="col-12 col-md-2 mb-3">
												<label class="col-form-label">Account Name<span style="color: red;"><strong>*</strong></span></label>
												<select data-plugin-selecttwo class="form-control select2-js" id="coa_name" required name="account_name">
													<option value="" disabled selected>Select Account</option>
													@foreach($coa as $key => $row)	
														<option value="{{$row->ac_code}}" {{ $row->ac_code == $sales->account_name ? 'selected' : '' }}>{{$row->ac_name}}</option>
													@endforeach
												</select>
											</div>

											<div class="col-12 col-md-2 mb-2">
												<label class="col-form-label">Name Of Person</label>
												<input type="text" name="nop" id="nop" placeholder="Name Of Person" class="form-control" value="{{$sales->Cash_pur_name}}">
											</div>

											<div class="col-12 col-md-4 mb-3">
												<label class="col-form-label">Person Address</label>
												<input type="text" name="address" id="address" placeholder="Person Address" class="form-control" value="{{$sales->cash_Pur_address}}">
											</div>

											<div class="col-12 col-md-4 mb-3">
												<label class="col-form-label">Person Phone Number</label>
												<input type="text" name="cash_pur_phone" id="cash_pur_phone" placeholder="Person Phone Number" class="form-control" value="{{$sales->cash_pur_phone}}">

											</div>

											<div class="col-12 mb-3">
												<label class="col-form-label">Remarks</label>
												<textarea rows="4" cols="50" name="remarks" id="remarks" placeholder="Remarks" class="form-control cust-textarea">{{$sales->Sales_remarks}}</textarea>
											</div>
									  </div>
									</div>
							
									<div class="card-body" style="overflow-x:auto;max-height:450px;overflow-y:auto">
										<table class="table table-bordered table-striped mb-0" id="myTable" >
											<thead>
												<tr>
													<th width="10%">Item Code<span style="color: red;"><strong>*</strong></span></th>
													<th width="10%">Qty<span style="color: red;"><strong>*</strong></span></th>
													<th width="20%">Item Name<span style="color: red;"><strong>*</strong></span></th>
													<th width="20%">Remarks</th>
													<th width="15%">Weight(kgs)<span style="color: red;"><strong>*</strong></span></th>
													<th width="10%">Price<span style="color: red;"><strong>*</strong></span></th>
													<th width="10%">Amount</th>
													<th width="10%"></th>
												</tr>
											</thead>
											<tbody id="saleInvoiceTable">
												@php
													$total_amount=0;
													$total_weight=0;
													$total_quantity=0;
													$net_amount=0;
												@endphp

												@foreach($sale_items as $key1 => $sale_item)
												<tr>
													<td>
														<input type="number" id="item_code{{$key1+1}}" name="item_code[]" placeholder="Code" class="form-control" value="{{$sale_item->item_cod}}" required onchange="getItemDetails({{$key1+1}},1)">
													</td>
													<td>
														<input type="number" id="item_qty_{{$key1+1}}" name="item_qty[]" placeholder="Qty" onchange="rowTotal({{$key1+1}})" class="form-control" required  step="any" value="{{$sale_item->Sales_qty2}}">
													</td>
													<td>
														<select data-plugin-selecttwo class="form-control select2-js" id="item_name{{$key1+1}}" onchange="getItemDetails({{$key1+1}},2)" required  name="item_name[]">
															<option>Select Item</option>
															@foreach($items as $key2 => $row)	
																<option value="{{$row->it_cod}}" {{ $row->it_cod == $sale_item->item_cod ? 'selected' : '' }}>{{$row->item_name}}</option>
															@endforeach
														</select>
													</td>
													<td>
														<input type="text" id="remarks{{$key1+1}}" name="item_remarks[]" placeholder="Remarks" class="form-control" value="{{$sale_item->remarks}}">
													</td>
													<td>
														<input type="number" id="weight_{{$key1+1}}" name="item_weight[]" onchange="rowTotal({{$key1+1}})" placeholder="Weight (kgs)" required step="any" class="form-control" value="{{$sale_item->Sales_qty}}">
														@php  $total_weight=$total_weight + $sale_item->Sales_qty  @endphp

													</td>
													<td>
														<input type="number" id="price{{$key1+1}}" name="item_price[]" onchange="rowTotal({{$key1+1}})" placeholder="Price" class="form-control" required step="any" value="{{$sale_item->sales_price}}">
													</td>
													<td>
														<input type="number" id="amount{{$key1+1}}" name="item_amount[]" placeholder="Amount" class="form-control" disabled step="any" required value="{{$sale_item->Sales_qty * $sale_item->sales_price}}"> 
														@php  $total_amount=$total_amount+ ($sale_item->Sales_qty * $sale_item->sales_price) @endphp
													</td>
													<td>
														<button type="button" onclick="removeRow(this)" class="btn btn-danger" tabindex="1"><i class="fas fa-times"></i></button>
													</td>
												</tr>
												@endforeach
											</tbody>
										</table>
									</div>
									<footer class="card-footer">
										<div class="row form-group mb-3">
											<div class="col-6 col-md-2 pb-sm-3 pb-md-0">
										 	    <label class="col-form-label">Total Amount</label>
										 		<input type="number" id="total_amount_show" step="any" placeholder="Total Amount" class="form-control" disabled step="any" value=@php echo $total_amount @endphp>
											</div>

											<div class="col-6 col-md-2 pb-sm-3 pb-md-0">
												<label class="col-form-label">Total Weight</label>
												<input type="number" id="total_weight_show" step="any" placeholder="Total Weight" class="form-control" disabled step="any" value=@php echo $total_weight @endphp >
												<input type="hidden" id="total_weight" name="total_weight" step="any" placeholder="Total Weight" class="form-control" value=@php echo $total_weight @endphp>
											</div>

											<div class="col-6 col-md-2 pb-sm-3 pb-md-0">
												<label class="col-form-label">Total Quantity</label>
												<input type="number" id="total_quantity" name="total_quantity" placeholder="Total Weight" class="form-control" disabled step="any" value=@php echo $total_quantity @endphp >
											</div>

											<div class="col-6 col-md-2 pb-sm-3 pb-md-0">
												<label class="col-form-label">Convance</label>
												<input type="text" id="convance_charges" onchange="netTotal()" name="convance_charges" placeholder="Convance Charges" class="form-control" step="any" value="{{$sales->ConvanceCharges}}">
											</div>

											<div class="col-6 col-md-2 pb-sm-3 pb-md-0">
												<label class="col-form-label">Labour Charges</label>
												<input type="number" id="labour_charges"  onchange="netTotal()" name="labour_charges" placeholder="Labour Charges" class="form-control" step="any" value="{{$sales->LaborCharges}}">
											</div>

											<div class="col-6 col-md-2 pb-sm-3 pb-md-0">
												<label class="col-form-label">Bill Discount</label>
												<input type="number" id="bill_discount"  onchange="netTotal()" name="bill_discount" placeholder="Bill Discount" class="form-control" step="any" value="{{$sales->Bill_discount}}">
											</div>

											@php $net_amount= round($total_amount + $sales->ConvanceCharges + $sales->LaborCharges - $sales->Bill_discount) @endphp
											<div class="col-12 pb-sm-3 pb-md-0 text-end">
												<h3 class="font-weight-bold mt-3 mb-0 text-5 text-primary">Net Amount</h3>
												<span>
													<strong class="text-4 text-primary">PKR <span id="netTotal" class="text-4 text-danger">@php echo $net_amount @endphp</span></strong>
												</span>
											</div>
										</div>
									</footer>
									<footer class="card-footer">
										<div class="row form-group mb-2">
											<div class="text-end">
												<button type="button" class="btn btn-danger mt-2"  onclick="window.location='{{ route('all-saleinvoices-paginate') }}'"> <i class="fas fa-trash"></i> Discard Changes</button>
												<button type="submit" class="btn btn-primary mt-2"> <i class="fas fa-save"></i> Save Invoice</button>
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

	var itemCount=0, index;
	var totalAmount=0, totalWeight=0, totalQuantity=0, netAmount=0, amount=0, weight=0, quantity=0;

	var table = document.getElementById("saleInvoiceTable"); // Get the table element
	var rowCount = table.rows.length; // Get the total number of rows

	itemCount = rowCount;	
	document.getElementById("itemCount").value = itemCount;

	// $('#itemCount').val(itemCount);
	index = rowCount+1;

	for (var j=0;j<rowCount; j++){

		quantity = table.rows[j].cells[1].querySelector('input').value; // Get the value of the input field in the specified cell
		totalQuantity = totalQuantity + Number(quantity);

		weight = table.rows[j].cells[4].querySelector('input').value; // Get the value of the input field in the specified cell
		totalWeight = totalWeight + Number(weight);

		amount = table.rows[j].cells[6].querySelector('input').value; // Get the value of the input field in the specified cell
		totalAmount = totalAmount + Number(amount);
	}
	$('#total_quantity').val(totalQuantity);
	$('#total_weight_show').val(totalWeight);
	$('#total_amount_show').val(totalAmount);


	$(document).ready(function() {
		$(window).keydown(function(event){
			if(event.keyCode == 13) {
				event.preventDefault();
				return false;
			}
		});
	});

    function removeRow(button) {
		var confirmation = confirm("Are you sure you want to remove this row?");
		if (confirmation) {
			var tableRows = $("#saleInvoiceTable tr").length;
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


    document.getElementById('removeRowBtn').addEventListener('click', function() {
        var table = document.getElementById('myTable').getElementsByTagName('tbody')[0];
        if (table.rows.length > 0) {
            table.deleteRow(table.rows.length - 1);
        } else {
            alert("No rows to delete!");
        }
    });

	


	function addNewRow() {
		var lastRow = $('#myTable tbody tr:last');
		var canAdd = true;

		if (lastRow.length > 0) {
			var lastSelect = lastRow.find('select');
			if (lastSelect.length > 0 && lastSelect.val() === "Select Item") {
				canAdd = false;
			}
		}

		if (canAdd) {
			var tableBody = $('#myTable tbody');
			var newRow = $('<tr></tr>');

			newRow.append('<td><input type="text" id="item_code' + index + '" name="item_code[]" placeholder="Code" required onchange="getItemDetails(' + index + ',1)" class="form-control"></td>');
			newRow.append('<td><input type="number" id="item_qty_' + index + '" onchange="rowTotal(' + index + ')" name="item_qty[]" placeholder="Qty" value="0" step="any" required class="form-control"></td>');
			newRow.append('<td><select data-plugin-selecttwo class="form-control select2-js" id="item_name' + index + '" onchange="getItemDetails(' + index + ',2)" name="item_name[]">' +
						'<option>Select Item</option>' +
						@foreach($items as $key => $row)
							'<option value="{{ $row->it_cod }}">{{ $row->item_name }}</option>' +
						@endforeach
						'</select></td>');
			newRow.append('<td><input type="text" id="remarks' + index + '" name="item_remarks[]" placeholder="Remarks" class="form-control"></td>');
			newRow.append('<td><input type="number" id="weight_' + index + '" onchange="rowTotal(' + index + ')" name="item_weight[]" placeholder="Weight (kgs)" value="0" step="any" required class="form-control"></td>');
			newRow.append('<td><input type="number" id="price' + index + '" onchange="rowTotal(' + index + ')" name="item_price[]" placeholder="Price" value="0" step="any" required class="form-control"></td>');
			newRow.append('<td><input type="number" id="amount' + index + '" name="item_amount[]" placeholder="Amount" class="form-control" disabled></td>');
			newRow.append('<td><button type="button" onclick="removeRow(this)" class="btn btn-danger" tabindex="1"><i class="fas fa-times"></i></button></td>');

			tableBody.append(newRow);

			index++;
			var itemCount = Number($('#itemCount').val()) || 0;
			$('#itemCount').val(itemCount + 1);

			// Re-initialize Select2
			$('#myTable select[data-plugin-selecttwo]').select2();
		}
	}



	function addNewRow_btn() {

	addNewRow(); // Call the same function
	// Set focus on the new item_code input field
	document.getElementById('item_code' + (index - 1)).focus();


	}


	function getItemDetails(row_no,option){
		var itemId;
		if(option==1){
			itemId = document.getElementById("item_code"+row_no).value;
		}
		else if(option==2){
			itemId = document.getElementById("item_name"+row_no).value;
		}
		$.ajax({
			type: "GET",
			url: "/items/detail",
			data: {id:itemId},
			success: function(result){
				$('#item_code'+row_no).val(result[0]['it_cod']);
				$('#item_name'+row_no).val(result[0]['it_cod']).select2();
				// $('#remarks'+row_no).val(result[0]['item_remark']);
				// $('#price'+row_no).val(result[0]['sales_price']);

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
		var weight = $('#weight_'+index+'').val();
		var price = $('#price'+index+'').val();
		var amount = weight * price;
		$('#amount'+index+'').val(amount);
		tableTotal();
	}

	function tableTotal(){
		var totalAmount=0;
		var totalWeight=0;
		var totalQuantity=0;
		var tableRows = $("#saleInvoiceTable tr").length;
		var table = document.getElementById('myTable').getElementsByTagName('tbody')[0];

		for (var i = 0; i < tableRows; i++) {
			var currentRow =  table.rows[i];
			totalAmount = totalAmount + Number(currentRow.cells[6].querySelector('input').value);
			totalWeight = totalWeight + Number(currentRow.cells[4].querySelector('input').value);
			totalQuantity = totalQuantity + Number(currentRow.cells[1].querySelector('input').value);
        }

		$('#total_amount_show').val(totalAmount);
		$('#total_weight_show').val(totalWeight);
		$('#total_quantity').val(totalQuantity);

		netTotal();
	}


	function netTotal(){
		var netTotal = 0;
		var total = Number($('#total_amount_show').val());
		var convance_charges = Number($('#convance_charges').val());
		var labour_charges = Number($('#labour_charges').val());
		var bill_discount = Number($('#bill_discount').val());

		netTotal = total + convance_charges + labour_charges - bill_discount;
		netTotal = netTotal.toFixed(0);
		FormattednetTotal = formatNumberWithCommas(netTotal);
		document.getElementById("netTotal").innerHTML = '<span class="text-4 text-danger">'+FormattednetTotal+'</span>';
	}

	function formatNumberWithCommas(number) {
    	// Convert number to string and add commas
    	return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}

	function goBack() {
		window.history.back();
	}

</script>