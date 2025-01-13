@include('../layouts.header')
	<body>
		<section class="body">
			@include('../layouts.pageheader')
			<div class="inner-wrapper cust-pad">
				<section role="main" class="content-body" style="margin:0px">
					<form method="post" action="{{ route('update-purchases1') }}" enctype="multipart/form-data" onkeydown="return event.key != 'Enter';" id="updateForm">
						@csrf
						<div class="row">
							<div class="col-12 mb-3">								
								<section class="card">
									<header class="card-header" style="display: flex;justify-content: space-between;">
										<h2 class="card-title">Edit Purchase 1</h2>
										<div class="card-actions">
											<button type="button" class="btn btn-primary" onclick="addNewRow_btn()"> <i class="fas fa-plus"></i> Add New Row </button>
										</div>
									</header>

									<div class="card-body">
										<div class="row form-group mb-2">
											<div class="col-6 col-md-2 mb-2">
												<label class="col-form-label" >Invoice No.</label>
												<input type="text" placeholder="Invoice #" class="form-control" value="{{$pur->prefix}}{{$pur->pur_id}}" disabled>
												<input type="hidden" placeholder="Invoice #" class="form-control" value="{{$pur->pur_id}}" name="pur_id">
												<input type="hidden" id="itemCount" name="items" value="1" class="form-control">
											</div>
											<div class="col-6 col-md-2 mb-2">
												<label class="col-form-label" >Date</label>
												<input type="date" name="pur_date" value="{{$pur->pur_date}}"  class="form-control">
											</div>
											<div class="col-6 col-md-2 mb-2">
												<label class="col-form-label" >Bill No.</label>
												<input type="text" placeholder="Bill No." name="pur_bill_no" value="{{$pur->pur_bill_no}}" class="form-control">
											</div>
											<div class="col-6 col-md-2 mb-2">
												<label class="col-form-label" >Sale Inv.</label>
												<input type="text" placeholder="Sale Inv." name="pur_sale_inv" value="{{$pur->sale_against}}" class="form-control">
											</div>
											<div class="col-sm-12 col-md-4 mb-3">
												<label class="col-form-label">Attachements</label>
												<input type="file" class="form-control" name="att[]" multiple accept=".zip, appliation/zip, application/pdf, image/png, image/jpeg">
											</div>
											<div class="col-sm-12 col-md-3 mb-3">
												<td>
													<label class="col-form-label">Account Name<span style="color: red;"><strong>*</strong></span></label>
														<select data-plugin-selecttwo class="form-control select2-js" id="coa_name" required name="ac_cod">
														<option value="" disabled selected>Select Account</option>
														@foreach($acc as $key => $row)	
															<option value="{{$row->ac_code}}" {{ $pur->ac_cod == $row->ac_code ? 'selected' : '' }}>{{$row->ac_name}}</option>
														@endforeach
													</select>
												</td>
											</div>
											<div class="col-sm-3 col-md-3 mb-2">
												<label class="col-form-label" >Name of Person</label>
												<input type="text" placeholder="Name of Person" name="cash_saler_name" value="{{$pur->cash_saler_name}}" class="form-control">
											</div>
											<div class="col-sm-3 col-md-6 mb-2">
												<label class="col-form-label" >Person Address</label>
												<input type="text" placeholder="Person Address" name="cash_saler_address" value="{{$pur->cash_saler_address}}" class="form-control">
											</div>
											<div class="col-12 mb-2">
												<label class="col-form-label">Remarks</label>
												<textarea rows="4" cols="50" name="pur_remarks" id="pur_remarks" placeholder="Remarks" class="form-control cust-textarea">{{$pur->pur_remarks}}</textarea>
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
											<tbody id="Purchase1Table">
                                            	@foreach ($pur_items as $pur1_key => $pur_items)
												<tr>
													<td>
														<input type="text" class="form-control" name="item_cod[]"  id="item_cod{{$pur1_key+1}}" value="{{$pur_items->item_cod}}" onchange="getItemDetails({{$pur1_key+1}},1)">
													</td>	
													<td>
														<input type="text" class="form-control" name="pur_qty2[]" id="pur_qty2_{{$pur1_key+1}}" onchange="tableTotal()"  value="{{$pur_items->pur_qty2}}">
													</td>
													<td>
														<select data-plugin-selecttwo class="form-control select2-js" name="item_name[]" id="item_name{{$pur1_key+1}}" onchange="getItemDetails({{$pur1_key+1}},2)" required>
															<option value="" selected disabled>Select Item</option>
															@foreach($items as $key => $row)	
																<option value="{{$row->it_cod}}" {{ $row->it_cod == $pur_items->item_cod ? 'selected' : '' }}>{{$row->item_name}}</option>
															@endforeach
														</select>													
													</td>
													<td>
														<input type="text" class="form-control"  name="remarks[]" id="remarks{{$pur1_key+1}}" value="{{$pur_items->remarks}}">
													</td>
                                                    <td>
														<input type="number" class="form-control" name="pur_qty[]" id="pur_qty{{$pur1_key+1}}"  onchange="rowTotal({{$pur1_key+1}})" step="any" value="{{$pur_items->pur_qty}}">
                                                    </td>
													<td>
														<input type="number" class="form-control" name="pur_price[]" id="pur_price{{$pur1_key+1}}" value="{{$pur_items->pur_price}}"  step="any" onchange="rowTotal({{$pur1_key+1}})" value="0">
													</td>
													<td>
														<input type="number" class="form-control" name="amount[]" id="amount{{$pur1_key+1}}" value="{{ $pur_items->pur_qty * $pur_items->pur_price }}" onchange="tableTotal()" value="0" disabled>
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
													<input type="text" id="total_weight" placeholder="Total Weight" value="{{$pur->total_weight}}" class="form-control" disabled>
												</div>

												<div class="col-6 col-md-2 pb-sm-3 pb-md-0">
													<label class="col-form-label">Total Quantity</label>
													<input type="text" id="total_quantity" name="total_quantity" value="{{$pur->total_quantity}}" placeholder="Total Quantity" class="form-control" disabled>
												</div>

												<div class="col-6 col-md-2 pb-sm-3 pb-md-0">
													<label class="col-form-label">Convance Charges</label>
													<input type="text" id="convance_charges" required onchange="netTotal()" name="pur_convance_char" value="{{$pur->pur_convance_char}}" placeholder="Convance Charges" class="form-control">
												</div>

												<div class="col-6 col-md-2 pb-sm-3 pb-md-0">
													<label class="col-form-label">Labour Charges</label>
													<input type="text" id="labour_charges" required  onchange="netTotal()" name="pur_labor_char" value="{{$pur->pur_labor_char}}" placeholder="Labour Charges" class="form-control">
												</div>
												<div class="col-6 col-md-2 pb-sm-3 pb-md-0">
													<label class="col-form-label">Bill Discount</label>
													<input type="text" id="bill_discount"  required onchange="netTotal()" name="bill_discount" value="{{$pur->pur_discount}}" placeholder="Bill Discount" class="form-control">
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
												<button type="button" class="btn btn-danger mt-2"  onclick="window.location='{{ route('all-purchases1') }}'"> <i class="fas fa-trash"></i> Discard Changes</button>
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


	var itemCount, index;

	$(document).ready(function() {
		$(window).keydown(function(event){
			if(event.keyCode == 13) {
				event.preventDefault();
				return false;
			}
		});

		var totalAmount=0, totalWeight=0, totalQuantity=0, netAmount=0, amount=0, weight=0, quantity=0;

		var table = document.getElementById("Purchase1Table"); // Get the table element
        var rowCount = table.rows.length; // Get the total number of rows

		itemCount = rowCount;
		index = rowCount+1;

		$('#itemCount').val(itemCount);

		for (var j=0;j<rowCount; j++){

			quantity = table.rows[j].cells[1].querySelector('input').value; // Get the value of the input field in the specified cell
			totalQuantity = totalQuantity + Number(quantity);

			weight = table.rows[j].cells[4].querySelector('input').value; // Get the value of the input field in the specified cell
			totalWeight = totalWeight + Number(weight);

			amount = table.rows[j].cells[6].querySelector('input').value; // Get the value of the input field in the specified cell
			totalAmount = totalAmount + Number(amount);
		}
		$('#total_quantity').val(totalQuantity);
		$('#total_weight').val(totalWeight);
		$('#totalAmount').val(totalAmount);

		var convance_charges = Number($('#convance_charges').val());
		var labour_charges = Number($('#labour_charges').val());
		var bill_discount = Number($('#bill_discount').val());

		netAmount = totalAmount + convance_charges +  labour_charges - bill_discount;
		FormattednetTotal = formatNumberWithCommas(netAmount);
		document.getElementById("netTotal").innerHTML = '<span class="text-4 text-danger">'+FormattednetTotal+'</span>';
	});

    function removeRow(button) {
		var confirmation = confirm("Are you sure you want to remove this row?");
		if (confirmation) {
			var tableRows = $("#Purchase1Table tr").length;
			if (tableRows > 1) {
				var row = button.parentNode.parentNode;
				row.parentNode.removeChild(row);
				var itemCount = Number($('#itemCount').val());
				itemCount = itemCount - 1;
				$('#itemCount').val(itemCount);
				index--;
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

	function addNewRow(){
		var lastRow =  $('#myTable tr:last');
		latestValue=lastRow[0].cells[2].querySelector('select').value;
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

			cell1.innerHTML  = '<input type="text" class="form-control" name="item_cod[]" id="item_cod'+index+'" onchange="getItemDetails('+index+','+1+')" required>';
			cell2.innerHTML  = '<input type="text" class="form-control" id="pur_qty2_'+index+'" name="pur_qty2[]" step="any" value="0" onchange="tableTotal()">';
			cell3.innerHTML  = '<select data-plugin-selecttwo class="form-control select2-js" id="item_name'+index+'"  onchange="getItemDetails('+index+','+2+')" name ="item_name[]" required>'+
									'<option value="" disabled selected>Select Account</option>'+
									@foreach($items as $key => $row)
                                        '<option value="{{$row->it_cod}}">{{$row->item_name}}</option>'+
                                    @endforeach
								'</select>';
			cell4.innerHTML  = '<input type="text" class="form-control" id="remarks'+index+'" name="remarks[]">';
			cell5.innerHTML  = '<input type="number" id="pur_qty'+index+'" class="form-control" name="pur_qty[]" onchange="rowTotal('+index+')" value="0" step="any" required>';
			cell6.innerHTML  = '<input type="number" id="pur_price'+index+'" class="form-control" name="pur_price[]"  value="0" onchange="rowTotal('+index+')" step="any" required>';
			cell7.innerHTML  = '<input type="number" id="amount'+index+'" class="form-control" name="amount[]" value="0" onchange="tableTotal()" step="any" disabled>';
			cell8.innerHTML = '<button type="button" onclick="removeRow(this)" class="btn btn-danger" tabindex="1"><i class="fas fa-times"></i></button>';
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
			url: "/items/detail",
			data: {id:itemId},
			success: function(result){
				$('#item_cod'+row_no).val(result[0]['it_cod']);
				$('#item_name'+row_no).val(result[0]['it_cod']).select2();
				// $('#remarks'+row_no).val(result[0]['item_remark']);
				// $('#pur_price'+row_no).val(result[0]['OPP_qty_cost']);
				addNewRow();
			},
			error: function(){
				alert("error");
			}
		});
		
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

	function rowTotal(index){
		var weight = $('#pur_qty'+index+'').val();
		var price = $('#pur_price'+index+'').val();
		var amount = weight * price;
		$('#amount'+index+'').val(amount);
		tableTotal();
	}

	function tableTotal(){
		var totalAmount=0;
		var totalWeight=0;
		var totalQuantity=0;
		var tableRows = $("#Purchase1Table tr").length;
		var table = document.getElementById('myTable').getElementsByTagName('tbody')[0];

		for (var i = 0; i < tableRows; i++) {
			var currentRow =  table.rows[i];
			totalAmount = totalAmount + Number(currentRow.cells[6].querySelector('input').value);
			totalWeight = totalWeight + Number(currentRow.cells[4].querySelector('input').value);
			totalQuantity = totalQuantity + Number(currentRow.cells[1].querySelector('input').value);
        }

		$('#totalAmount').val(totalAmount);
		$('#total_weight').val(totalWeight);
		$('#total_quantity').val(totalQuantity);		
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
	}
 
	function formatNumberWithCommas(number) {
    	// Convert number to string and add commas
    	return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}

	function goBack() {
		window.history.back();
	}

</script>