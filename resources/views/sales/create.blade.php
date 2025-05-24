@include('../layouts.header')
	<body>
		<section class="body">
			@include('../layouts.pageheader')
			<div class="inner-wrapper cust-pad">
				<section role="main" class="content-body" style="margin:0px">
					<form method="post" id="myForm" action="{{ route('store-sale-invoice') }}" enctype="multipart/form-data" onkeydown="return event.key != 'Enter';">
						@csrf
						<div class="row">
							<div class="col-12 mb-3">								
								<section class="card">
									<header class="card-header" style="display: flex;justify-content: space-between;">
										<h2 class="card-title">New Sale Invoice</h2>
										<div class="card-actions">
											<button type="button" class="btn btn-primary" onclick="addNewRow_btn()"> <i class="fas fa-plus"></i> Add New Row </button>
										</div>
									</header>

									<div class="card-body">
										<div class="row form-group mb-2">
											<div class="col-6 col-md-2 mb-2">
												<label class="col-form-label" >Invoice no.</label>
												<input type="text" name="invoice_no" placeholder="(New Invoice)" class="form-control" disabled>
												<input type="hidden" id="itemCount" name="items" value="1" class="form-control" >
												<input type="hidden" id="printInvoice" name="printInvoice" value="0" class="form-control" >
											</div>

											<div class="col-6 col-md-2 mb-2">
												<label class="col-form-label" >Date</label>
												<input type="date" name="date" required value="<?php echo date('Y-m-d'); ?>" class="form-control">
											</div>

											<div class="col-6 col-md-2">
												<label class="col-form-label" >Bill No.</label>
												<input type="text" name="bill_no" placeholder="Bill No." class="form-control">
											</div>

											<div class="col-6 col-md-2">
												<label class="col-form-label">Status</label>
												<select data-plugin-selecttwo class="form-control select2-js mb-3" name="bill_status">
													<option value="0">Bill Not Final</option>
													<option value="1">Finalized</option>
												</select>												
											</div>
											<div class="col-sm-12 col-md-4">
												<label class="col-form-label">File Attached</label>
												<input type="file" class="form-control" name="att[]" multiple accept=".zip, appliation/zip, application/pdf, image/png, image/jpeg">
											</div>
											<div class="col-12 col-md-2 mb-3">
												<label class="col-form-label">Account Name<span style="color: red;"><strong>*</strong></span></label>
												<select data-plugin-selecttwo class="form-control select2-js" id="coa_name" name="account_name" required>
													<option value="" disabled selected>Select Account</option>
													@foreach($coa as $key => $row)	
														<option value="{{$row->ac_code}}">{{$row->ac_name}}</option>
													@endforeach
												</select>
											</div>

											<div class="col-sm-12 col-md-2 mb-2">
												<label class="col-form-label">Name Of Person</label>
												<input type="text" name="nop" id="nop" placeholder="Name Of Person" class="form-control">
											</div>

											<div class="col-12 col-md-4 mb-3">
												<label class="col-form-label">Person Address</label>
												<input type="text" name="address" id="address" placeholder="Person Address" class="form-control">
											</div>

											<div class="col-12 col-md-4 mb-3">
												<label class="col-form-label">Person Phone Number</label>
												<input type="text" name="cash_pur_phone" id="cash_pur_phone" placeholder="Person Phone Number" class="form-control">
											</div>

											<div class="col-12 mb-3">
												<label class="col-form-label">Remarks</label>
												<textarea rows="4" cols="50" name="remarks" id="remarks" placeholder="Remarks" class="form-control cust-textarea"></textarea>
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
												<tr>
													<td>
														<input type="number" id="item_code1" name="item_code[]" placeholder="Code" class="form-control" required onchange="getItemDetails(1,1)">
													</td>
													<td>
														<input type="number" id="item_qty_1" name="item_qty[]" onchange="rowTotal(0)" placeholder="Qty" value="0" step="any" required class="form-control">
													</td>
													<td>
														<select data-plugin-selecttwo class="form-control select2-js" id="item_name1" onchange="getItemDetails(1,2)" name="item_name[]" required>
														<option selected>Select Item</option>
															@foreach($items as $key => $row)	
																<option value="{{$row->it_cod}}">{{$row->item_name}}</option>
															@endforeach
														</select>
													</td>
													<td>
														<input type="text" id="remarks1" name="item_remarks[]" placeholder="Remarks" class="form-control">
													</td>
													<td>
														<input type="number" id="weight_1" name="item_weight[]" onchange="rowTotal(1)" placeholder="Weight (kgs)" value="0" step="any" required class="form-control">
													</td>
													<td>
														<input type="number" id="price1" name="item_price[]" onchange="rowTotal(1)" placeholder="Price" value="0" step="any" required class="form-control">
													</td>
													<td>
														<input type="number" id="amount1" name="item_amount[]" placeholder="Amount" class="form-control" value="0" step="any" required disabled>
													</td>
													<td>
														<button type="button" onclick="removeRow(this)" class="btn btn-danger" tabindex="1"><i class="fas fa-times"></i></button>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
									<footer class="card-footer">
										<div class="row form-group mb-3">
											<div class="col-6 col-md-2 pb-sm-3 pb-md-0">
										 	    <label class="col-form-label">Total Amount</label>
										 		<input type="number" id="total_amount_show" placeholder="Total Amount" class="form-control" step="any" disabled>
												<input type="hidden" id="totalAmount" name="totalAmount" step="any" placeholder="Total Amount" class="form-control">
											</div>

											<div class="col-6 col-md-2 pb-sm-3 pb-md-0">
												<label class="col-form-label">Total Weight</label>
												<input type="number" id="total_weight_show"  placeholder="Total Weight" class="form-control" step="any" disabled>
												<input type="hidden" id="total_weight" name="total_weight" step="any" placeholder="Total Weight" class="form-control">
											</div>

											<div class="col-6 col-md-2 pb-sm-3 pb-md-0">
												<label class="col-form-label">Total Quantity</label>
												<input type="number" id="total_quantity" name="total_quantity" placeholder="Total Weight" class="form-control" step="any" disabled>
											</div>

										

											<div class="col-6 col-md-2 pb-sm-3 pb-md-0">
												<label class="col-form-label">Convance Charges</label>
												<input type="number" id="convance_charges" onchange="netTotal()" name="convance_charges" placeholder="Convance Charges" step="any" value="0" class="form-control">
											</div>

											<div class="col-6 col-md-2 pb-sm-3 pb-md-0">
												<label class="col-form-label">Labour Charges</label>
												<input type="number" id="labour_charges"  onchange="netTotal()" name="labour_charges" placeholder="Labour Charges" step="any" value="0" class="form-control">
											</div>

											<div class="col-6 col-md-2 pb-sm-3 pb-md-0">
												<label class="col-form-label">Bill Discount</label>
												<input type="number" id="bill_discount"  onchange="netTotal()" name="bill_discount" placeholder="Bill Discount" step="any" value="0" class="form-control">
											</div>

										</div>
										<div class="col-sm-12 col-md-12 pb-sm-3 pb-md-0 text-end">
											<h3 class="font-weight-bold mt-3 mb-0 text-5 text-primary">Net Amount</h3>
											<span>
												<strong class="text-4 text-primary">PKR <span id="netTotal" class="text-4 text-danger">0.00 </span></strong>
											</span>
										</div>
									</footer>
									<footer class="card-footer">
										<div class="row form-group mb-2">
											<div class="text-end">
												<button type="button" class="btn btn-danger mt-2"  onclick="window.location='{{ route('all-saleinvoices-paginate') }}'"> <i class="fas fa-trash"></i> Discard Invoice</button>
												<button type="submit" class="btn btn-primary mt-2" id="submitBtn"><i class="fas fa-save"></i> Add Invoice</button>
											</div>
										</div>
									</footer>
									<div id="deleteModal" class="zoom-anim-dialog modal-block modal-block-danger mfp-hide">
											<section class="card">
												<header class="card-header">
													<h2 class="card-title">Save Invoice</h2>
												</header>
												<div class="card-body">
													<div class="modal-wrapper">
														<div class="modal-icon">
															<i class="fas fa-question-circle"></i>
														</div>
														<div class="modal-text">
															<p class="mb-0">Are you sure that you want to delete this invoice?</p>
															<input name="invoice_id" id="deleteID" hidden>
														</div>
													</div>
												</div>
												<footer class="card-footer">
													<div class="row">
														<div class="col-md-12 text-end">
															<button type="submit" class="btn btn-danger">Delete</button>
															<button class="btn btn-default modal-dismiss">Cancel</button>
														</div>
													</div>
												</footer>
											</section>
										</form>
									</div>
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

	$(document).ready(function() {
		$(window).keydown(function(event){
			if(event.keyCode == 13) {
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

    function removeRow(button) {
		var tableRows = $("#saleInvoiceTable tr").length;
		if(tableRows>1){
			var row = button.parentNode.parentNode;
			row.parentNode.removeChild(row);
			index--;
			var itemCount = Number($('#itemCount').val());
			itemCount = itemCount-1;
			$('#itemCount').val(itemCount);
		}   
		tableTotal();
    }

    document.getElementById('removeRowBtn').addEventListener('click', function() {
        var table = document.getElementById('myTable').getElementsByTagName('tbody')[0];
        if (table.rows.length > 0) {
            table.deleteRow(table.rows.length - 1);
        } else {
            alert("No rows to delete!");
        }
    });

	function addNewRow(id){		
		var lastRow =  $('#myTable tr:last');
		latestValue=lastRow[0].cells[2].querySelector('select').value;

		if(latestValue!="Select Item"){
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

			cell1.innerHTML = '<input type="text" id="item_code'+index+'" name="item_code[]" onchange="getItemDetails('+index+','+1+')" placeholder="Code" class="form-control" required>';
			cell2.innerHTML = '<input type="number" id="item_qty_'+index+'"  onchange="rowTotal('+index+')" name="item_qty[]" placeholder="Qty" value="0" step="any" required class="form-control">';
			cell3.innerHTML = '<select data-plugin-selecttwo class="form-control select2-js" id="item_name'+index+'" required onchange="getItemDetails('+index+','+2+')" name="item_name">'+
									'<option>Select Item</option>'+
									@foreach($items as $key => $row)	
										'<option value="{{$row->it_cod}}">{{$row->item_name}}</option>'+
									@endforeach
								'</select>';
			cell4.innerHTML = '<input type="text" id="remarks'+index+'" name="item_remarks[]" placeholder="Remarks" class="form-control">';
			cell5.innerHTML = '<input type="number" id="weight_'+index+'" onchange="rowTotal('+index+')" name="item_weight[]"  placeholder="Weight (kgs)" value="0" step="any" required class="form-control">';
			cell6.innerHTML = '<input type="number" id="price'+index+'" onchange="rowTotal('+index+')" name="item_price[]"  placeholder="Price" value="0" step="any" required class="form-control">';
			cell7.innerHTML = '<input type="number" id="amount'+index+'" name="item_amount[]" placeholder="Amount" class="form-control" value="0" step="any" required disabled>';
			cell8.innerHTML = '<button type="button" onclick="removeRow(this)" class="btn btn-danger" tabindex="1"><i class="fas fa-times"></i></button>';

			index++;

			var itemCount = Number($('#itemCount').val());
			itemCount = itemCount+1;
			$('#itemCount').val(itemCount);
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
				$('#remarks'+row_no).val(result[0]['item_remark']);
				$('#price'+row_no).val(result[0]['sales_price']);

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
				// $('#address').val(result[0]['address']);
				// $('#cash_pur_phone').val(result[0]['phone_no']);
				// $('#remarks').val(result[0]['remarks']);
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

		$('#totalAmount').val(totalAmount);
		$('#total_amount_show').val(totalAmount);
		$('#total_weight').val(totalWeight);
		$('#total_weight_show').val(totalWeight);
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

</script>