@include('../layouts.header')
	<body>
		<section class="body">
			@include('../layouts.pageheader')
			<div class="inner-wrapper cust-pad">
				<section role="main" class="content-body" style="margin:0px">
					<form method="post" action="{{ route('store-sales2') }}" enctype="multipart/form-data" onkeydown="return event.key != 'Enter';" id="addForm">
						@csrf
						<div class="row">	
							<div class="col-md-6 col-sm-12  mb-3">								
								<section class="card">
									<header class="card-header" style="display: flex;justify-content: space-between;">
										<h2 class="card-title">Sale Pipe</h2>
										<div class="card-actions">
											<button type="button" class="btn btn-danger modal-with-zoom-anim ws-normal mb-2" onclick="getInvFromStockOut()" href="#getPur2FromStockOut" > From Stock Out </button>
											<button type="button" class="btn btn-danger modal-with-zoom-anim ws-normal mb-2" onclick="getFromPurchase2()" href="#getPurchase2" > From Purchase 2 </button>
										</div>
									</header>

									<div class="card-body">
										<div class="row form-group mb-2">
											<div class="col-6 mb-2">
												<label class="col-form-label" >Invoice No.</label>
												<input type="text" placeholder="(New Invoice)" class="form-control" disabled>
												<input type="hidden" id="itemCount" name="items" value="1" class="form-control">
												<input type="hidden" id="isInduced" name="isInduced" value="0" class="form-control" >
												<input type="hidden" id="inducedID" name="inducedID" class="form-control" >
												<input type="hidden" id="inducedPrefix" name="inducedPrefix" class="form-control" >
											</div>
											<div class="col-6 mb-2">
												<label class="col-form-label" >Date</label>
												<input type="date" name="sa_date" value="<?php echo date('Y-m-d'); ?>" id="stck_in_date" class="form-control">
											</div>
											<div class="col-6 mb-2">
												<label class="col-form-label" >Bill No.</label>
												<input type="text" placeholder="Bill No." name="pur_ord_no" id="stock_in_pur_inv" class="form-control">
											</div>
											<div class="col-6 mb-3">
												<label class="col-form-label">Attachements</label>
												<input type="file" class="form-control" name="att[]" multiple accept=".zip, appliation/zip, application/pdf, image/png, image/jpeg">
											</div>
											<div class="col-sm-12 col-md-12 mb-3">
												<label class="col-form-label">Account Name<span style="color: red;"><strong>*</strong></span></label>
												<select data-plugin-selecttwo class="form-control select2-js"  name="account_name" id="account_name" required>
													<option value="" disabled selected>Select Account</option>
													@foreach($coa as $key => $row)	
														<option value="{{$row->ac_code}}">{{$row->ac_name}}</option>
													@endforeach
												</select>
											</div>
									  </div>
									</div>
								</section>
							</div>

							<div class="col-md-6 col-sm-12 mb-3">								
								<section class="card">
									<header class="card-header">
										<h2 class="card-title">Company Details</h2>
									</header>

									<div class="card-body">
										<div class="row form-group mb-2">
											<div class="col-sm-12 col-md-6 mb-3">
												<label class="col-form-label">Company Name<span style="color: red;"><strong>*</strong></span></label>
												<select data-plugin-selecttwo class="form-control select2-js"  id="company_name" required disabled>
													<option value="" disabled selected>Select Account</option>
													@foreach($coa as $key => $row)	
														<option value="{{$row->ac_code}}">{{$row->ac_name}}</option>
													@endforeach
												</select>
												<input type="hidden" name="disp_account_name" id="disp_account_name" required class="form-control">
											</div>
											<div class="col-sm-3 col-md-6 mb-2">
												<label class="col-form-label" >Name of Person</label>
												<input type="text" placeholder="Name of Person" name="Cash_pur_name" id="Cash_pur_name" class="form-control">
											</div>
											<div class="col-sm-12 col-md-6 mb-2">
												<label class="col-form-label" >Pur Inv. No.</label>
												<input type="text" placeholder="Sale Inv. No." id="show_sal_inv_no" disabled class="form-control">
												<input type="hidden" placeholder="Sale Inv. No." name="sal_inv_no" id="sal_inv_no" class="form-control">
											</div>

											<div class="col-sm-3 col-md-6 mb-2">
												<label class="col-form-label" >Person Address</label>
												<input type="text" placeholder="Person Address" name="cash_Pur_address" id="cash_pur_address" class="form-control">
											</div>

											<div class="col-12 mb-12">
												<label class="col-form-label">Remarks</label>
												<textarea rows="2" cols="50" name="Sales_Remarks" id="Sales_Remarks" placeholder="Remarks" class="form-control cust-textarea"></textarea>
											</div>	

									  </div>
									</div>
								</section>
							</div>

							<div class="col-12 mb-3">
								<section class="card">
									<header class="card-header" style="display: flex;justify-content: space-between;">
										<h2 class="card-title">Sale Pipe Details</h2>
										<div class="card-actions">
											<button type="button" class="btn btn-primary" onclick="addNewRow_btn()"> <i class="fas fa-plus"></i> Add New Row </button>
										</div>
									</header>
									<div class="card-body" style="overflow-x:auto;max-height:450px;overflow-y:auto">
										<table class="table table-bordered table-striped mb-0" id="myTable" >
											<thead>
												<tr>
													<th width="7%">Code<span style="color: red;"><strong>*</strong></span></th>
													<th width="20%">Item Name<span style="color: red;"><strong>*</strong></span></th>
													<th width="20%">Remarks</th>
													<th width="7%">Qty<span style="color: red;"><strong>*</strong></span></th>
													<th width="7.5%">Price/Unit<span style="color: red;"><strong>*</strong></span></th>
													<th width="7%">Len<span style="color: red;"><strong>*</strong></span></th>
													<th width="7%">%<span style="color: red;"><strong>*</strong></span></th>
													<th width="7%">Weight</th>
													<th width="7%">Amount</th>
													<th width="7%">Sale Date</th>
													<th width=""></th>
												</tr>
											</thead>
											<tbody id="Purchase2Table">
												<tr>
													<td>
														<input type="text" class="form-control" name="item_cod[]"  id="item_cod1" onchange="getItemDetails(1,1)" required>
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


												<div class="col-6 col-md-2 pb-sm-3 pb-md-0">
													<label class="col-form-label">Convance Charges</label>
													<input type="text" id="convance_charges" onchange="netTotal()" name="ConvanceCharges" placeholder="Convance Charges" class="form-control">
												</div>

												<div class="col-6 col-md-2 pb-sm-3 pb-md-0">
													<label class="col-form-label">Labour Charges</label>
													<input type="text" id="labour_charges"  onchange="netTotal()" name="LaborCharges" placeholder="Labour Charges" class="form-control">
												</div>

												<div class="col-12 col-md-2 pb-sm-3 pb-md-0">
													<label class="col-form-label">Bill Discount </label>
													<div class="row">
														<div class="col-8">
															<input type="number" id="bill_discount" onchange="netTotal()" name="Bill_discount" placeholder="Bill Discount" class="form-control">
														</div>
														<div class="col-4">
															<input type="text"  id="bill_perc" class="form-control" placeholder="0%" disabled>
														</div>
													</div>
												</div>

												<div class="col-12 col-md-12 pb-sm-3 pb-md-0 text-end">
													<h3 class="font-weight-bold mt-3 mb-0 text-5 text-primary">Net Amount</h3>
													<span class="">
														<strong class="text-4 text-primary">PKR <span id="netTotal" class="text-4 text-danger">0.00 </span></strong>
													</span>
												</div>
											</div>
										</div>
									</footer>
									<footer class="card-footer">
										<div class="row form-group mb-2">
											<div class="text-end">
												<button type="button" class="btn btn-danger mt-2"  onclick="window.location='{{ route('all-sale2invoices-paginate') }}'"> <i class="fas fa-trash"></i> Discard Invoice</button>
												<button type="submit" class="btn btn-primary mt-2"> <i class="fas fa-save"></i> Add Invoice</button>
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

		<div id="getPurchase2" class="zoom-anim-dialog modal-block modal-block-danger mfp-hide">
			<section class="card">
				<header class="card-header">
					<h2 class="card-title">Induced From Purchase Pipe</h2>
				</header>
				<div class="card-body">
					<div class="modal-wrapper" style="overflow-x:auto">
						<table class="table table-bordered table-striped mb-0" >
							<thead>
								<tr>
									<th>ID</th>
									<th>Company Name</th>
									<th>Date</th>
									<th>Mill Inv #</th>
									<th>Customer Name</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody id="unclosed_purchases_list">

							</tbody>
						</table>
					</div>
				</div>
				<footer class="card-footer">
					<div class="row">
						<div class="col-md-12 text-end">
							<button class="btn btn-default modal-dismiss" id="closeModal">Cancel</button>
						</div>
					</div>
				</footer>
			</section>
		</div>

		<div id="getPur2FromStockOut" class="zoom-anim-dialog modal-block modal-block-danger mfp-hide">
			<section class="card">
				<header class="card-header">
					<h2 class="card-title">Induced From Godown</h2>
				</header>
				<div class="card-body">
					<div class="modal-wrapper" style="overflow-x:auto">
						<table class="table table-bordered table-striped mb-0" >
							<thead>
								<tr>
									<th>ID</th>
									<th>Customer Name</th>
									<th>Date</th>
									<th>Gate Pass #</th>
									<th>Person Name</th>
									{{-- <th>Item Type</th> --}}
									<th>Action</th>
								</tr>
							</thead>
							<tbody id="unclosed_purchases_list_from_stock_out">

							</tbody>
						</table>
					</div>
				</div>
				<footer class="card-footer">
					<div class="row">
						<div class="col-md-12 text-end">
							<button class="btn btn-default modal-dismiss" id="closeModal">Cancel</button>
						</div>
					</div>
				</footer>
			</section>
		</div>

		 
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

				// document.getElementById('toggleSwitch').addEventListener('change', toggleInputs);
				// toggleInputs();
			});




			function removeRow(button) {
				if (confirm("Are you sure you want to remove this row?")) {
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
					// Do nothing if the user clicks "Cancel"
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
					cell8.innerHTML  = '<input type="number" class="form-control" id="pur2_qty'+index+'" value="0" step="any" required disabled>';
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
						$('#weight_per_piece'+row_no).trigger('change');

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
					if (input.id !== 'BillAfterDisc') {
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

			function getInvFromStockOut(){
				var table = document.getElementById('unclosed_purchases_list_from_stock_out');
				while (table.rows.length > 0) {
					table.deleteRow(0);
				}
				$.ajax({
					type: "GET",
					url: "/tstock_out/getunclosed/",
					success: function(result){
						$.each(result, function(k,v){
							var html="<tr>";
							html+= "<td>"+v['prefix']+""+v['Sal_inv_no']+"</td>"
							html+= "<td>"+v['acc_name']+"</td>"
							html+= "<td>"+moment(v['sa_date']).format('DD-MM-YY')+"</td>"
							html+= "<td>"+v['mill_gate']+"</td>"
							html+= "<td>"+v['cash_pur_name']+"</td>"
							// html += "<td>";
							// if (v['item_type'] == 1) {
							// 	html += "<strong>Pipes</strong>";
							// } else if (v['item_type'] == 2) {
							// 	html += "<strong>Garder / TR</strong>";
							// }
							// html += "</td>";

							html+= "<td class='text-center'><a class='btn btn-danger mb-1 mt-1 me-1' href='#' onclick='inducedStockOutItems("+v['Sal_inv_no']+")'><i class='fas fa-check text-light'></i></a></td>"
							html+="</tr>";
							$('#unclosed_purchases_list_from_stock_out').append(html);
						});
								
					},
					error: function(){
						alert("error");
					}
				});
    		}

			function getFromPurchase2(){
				var table = document.getElementById('unclosed_purchases_list');
				
				while (table.rows.length > 0) {
					table.deleteRow(0);
				}
				$.ajax({
					type: "GET",
					url: "/purchase2/getunclosed/",
					success: function(result){
						$.each(result, function(k,v){
							var html="<tr>";
							html+= "<td>"+v['prefix']+""+v['Sale_inv_no']+"</td>"
							html+= "<td>"+v['acc_name']+"</td>"
							html+= "<td>"+moment(v['sa_date']).format('DD-MM-YY')+"</td>"
							html+= "<td>"+v['pur_ord_no']+"</td>"
							html+= "<td>"+v['disp_acc']+"</td>"
							html+= "<td class='text-center'><a class='btn btn-danger mb-1 mt-1 me-1' href='#' onclick='inducedPurchase2Items("+v['Sale_inv_no']+")'><i class='fas fa-check text-light'></i></a></td>"
							html+="</tr>";
							$('#unclosed_purchases_list').append(html);
						});
								
					},
					error: function(){
						alert("error");
					}
				});
    		}

			function inducedPurchase2Items(id){
				var ind_total_qty=0, ind_total_weight=0;
				var table = document.getElementById('Purchase2Table');
				while (table.rows.length > 0) {
					table.deleteRow(0);
				}
				index=1;
				$('#itemCount').val(1);

				$.ajax({
					type: "GET",
					url: "/purchase2/getItems/"+id,
					success: function(result){
						$('#stck_in_date').val(result['pur1']['sa_date']);
						$('#account_name').val(result['pur1']['Cash_pur_name_ac']).trigger('change');
						$('#company_name').val(result['pur1']['account_name']).trigger('change');
						$('#disp_account_name').val(result['pur1']['account_name']);
						$('#Cash_pur_name').val(result['pur1']['Cash_pur_name']);
						$('#sal_inv_no').val(result['pur1']['prefix']+""+result['pur1']['Sale_inv_no']);
						$('#show_sal_inv_no').val(result['pur1']['prefix']+""+result['pur1']['Sale_inv_no']);
						$('#inducedID').val(result['pur1']['Sale_inv_no']);
						$('#inducedPrefix').val(result['pur1']['prefix']);
						$('#cash_pur_address').val(result['pur1']['cash_Pur_address']);

						var table = $('#myTable').find('tbody');

						$.each(result['pur2'], function(k,v){
							var newRow = $('<tr>');
							newRow.append('<td><input type="number" id="item_cod'+index+'" value="'+v['item_cod']+'" name="item_cod[]" placeholder="Code" class="form-control" required onchange="getItemDetails(' + index + ', 1)"></td>');
							newRow.append('<td><select data-plugin-selecttwo class="form-control select2-js" id="item_name'+index+'" name="item_name[]" onchange="getItemDetails('+index+',2)"><option>Select Item</option>@foreach($items as $key => $row)+<option value="{{$row->it_cod}}" >{{ $row->item_name }}</option>@endforeach</select></td>');
							newRow.append('<td><input type="text" id="remarks'+index+'" value="'+v['remarks']+'" name="remarks[]" placeholder="Remarks" class="form-control"></td>');
							newRow.append('<td><input type="number" id="pur2_qty2_'+index+'" value="'+v['Sales_qty2']+'" name="pur2_qty2[]" placeholder="Qty" step="any" required class="form-control" onchange="rowTotal('+index+')"></td>');
							newRow.append('<td><input type="number" id="pur2_per_unit'+index+'" value="'+v['sales_price']+'" name="pur2_per_unit[]" onchange="rowTotal('+index+')" placeholder="Sales Price" step="any" required class="form-control" ></td>');
							newRow.append('<td><input type="number" id="pur2_len'+index+'" name="pur2_len[]" placeholder="Length" step="any" onchange="rowTotal('+index+')"  value="'+v['length']+'" required class="form-control" ></td>');
							newRow.append('<td><input type="number" id="pur2_percentage'+index+'" name="pur2_percentage[]" placeholder="%" step="any" onchange="rowTotal('+index+')" value="'+v['discount']+'"  required class="form-control" ><input type="hidden" id="weight_per_piece'+index+'"  name="weight_per_piece[]" placeholder="Weight" value="'+v['weight_pc']+'" step="any" required onchange="CalculateRowWeight('+index+')" class="form-control"></td>');
							newRow.append('<td><input type="number" id="pur2_qty'+index+'" disabled name="pur2_qty[]" placeholder="weight" value="0" step="any"  required class="form-control"></td>');
							newRow.append('<td><input type="number" id="amount'+index+'" name="amount[]" placeholder="Amount"  value="0" step="any" onchange="rowTotal('+index+')"  required class="form-control" disabled></td>');
							newRow.append('<td><input type="date" id="pur2_price_date'+index+'" name="pur2_price_date[]" value="'+v['rat_dat']+'" step="any" onchange="rowTotal('+index+')"  required class="form-control" disabled><input type="hidden"  value="'+v['rat_dat']+'" class="form-control" name="pur2_price_date[]" id="pur2_price_date_show'+index+'"></td>');
							newRow.append('<td><button type="button" onclick="removeRow(this)" class="btn btn-danger"><i class="fas fa-times"></i></button><button type="button" onclick="enablePrice('+index+')" class="btn btn-warning"><i class="bx bx-refresh" style="font-size:20px"></i></button></td>');

							table.append(newRow);
							$('#item_name'+index).val(v['item_cod']);
							$('#itemCount').val(index);
							rowTotal(index);							
							index++;

							ind_total_qty= ind_total_qty + v['Sales_qty2']
							ind_total_weight= ind_total_weight + (v['Sales_qty2'] * v['weight_pc'])
							$('#myTable select[data-plugin-selecttwo]').select2();

						}); 
						$("#total_qty").val(ind_total_qty);
						$("#total_weight").val(ind_total_weight);
						$("#isInduced").val(2);
						$("#sale_against").val(id);
						$("#closeModal").trigger('click');
					},
					error: function(){
						alert("error");
					}
				});
   			}

			function enablePrice(row_no) {
				var itemId = $('#item_cod' + row_no).val();

				$.ajax({
					type: "GET",
					url: "/item2/detail",
					data: { id: itemId },
					success: function(result) {
						var pur2PerUnit = $('#pur2_per_unit' + row_no);
						var pur2PriceDate = $('#pur2_price_date' + row_no);
						var pur2PriceDateShow = $('#pur2_price_date_show' + row_no);

						pur2PerUnit.val(result[0]['sales_price']).addClass('text-danger fw-bold');
						pur2PriceDate.val(result[0]['sale_rate_date']);
						pur2PriceDateShow.val(result[0]['sale_rate_date']);
					},
					error: function() {
						alert("error");
					}
				});
			}


			function inducedStockOutItems(id){
				var ind_total_qty=0, ind_total_weight=0;
				var table = document.getElementById('Purchase2Table');
				while (table.rows.length > 0) {
					table.deleteRow(0);
				}
				index=1;
				$('#itemCount').val(1);

				$.ajax({
					type: "GET",
					url: "/tstock_out/getItems/"+id,
					success: function(result){
						$('#stck_in_date').val(result['pur1']['sa_date']);
						$('#account_name').val(result['pur1']['account_name']).trigger('change');
						$('#company_name').val(24).trigger('change');
						$('#disp_account_name').val(24);
						$('#Cash_pur_name').val(result['pur1']['cash_pur_name']);
						$('#sal_inv_no').val(result['pur1']['prefix']+""+result['pur1']['Sal_inv_no']);
						$('#show_sal_inv_no').val(result['pur1']['prefix']+""+result['pur1']['Sal_inv_no']);
						$('#inducedID').val(result['pur1']['Sal_inv_no']);
						$('#inducedPrefix').val(result['pur1']['prefix']);
						$('#cash_pur_address').val(result['pur1']['cash_pur_address']);
						$('#Sales_Remarks').val(result['pur1']['sales_remarks']);

						$.each(result['pur2'], function(k,v){
							var table = $('#myTable').find('tbody');
							var newRow = $('<tr>');
							newRow.append('<td><input type="number" id="item_cod'+index+'" value="'+v['item_cod']+'" name="item_cod[]" placeholder="Code" class="form-control" required onchange="getItemDetails(' + index + ', 1)"></td>');
							newRow.append('<td><select data-plugin-selecttwo class="form-control select2-js" id="item_name'+index+'" name="item_name[]" onchange="getItemDetails('+index+',2)"><option>Select Item</option>@foreach($items as $key => $row)+<option value="{{$row->it_cod}}" >{{ $row->item_name }}</option>@endforeach</select></td>');
							newRow.append('<td><input type="text" id="remarks'+index+'" value="'+v['remarks']+'" name="remarks[]" placeholder="Remarks" class="form-control"></td>');
							newRow.append('<td><input type="number" id="pur2_qty2_'+index+'" value="'+v['sales_qty']+'" name="pur2_qty2[]" placeholder="Qty" step="any" required class="form-control" onchange="rowTotal('+index+')"></td>');
							newRow.append('<td><input type="number" id="pur2_per_unit'+index+'" value="'+v['sales_price']+'" name="pur2_per_unit[]" onchange="rowTotal('+index+')" placeholder="Sales Price" step="any" required class="form-control" ></td>');
							newRow.append('<td><input type="number" id="pur2_len'+index+'" name="pur2_len[]" placeholder="Length" step="any" onchange="rowTotal('+index+')" value="20" required class="form-control" ></td>');
							newRow.append('<td><input type="number" id="pur2_percentage'+index+'" name="pur2_percentage[]" placeholder="%" step="any" onchange="rowTotal('+index+')"  required class="form-control" ><input type="hidden" id="weight_per_piece'+index+'"  name="weight_per_piece[]" placeholder="Weight" value="'+v['weight_pc']+'" step="any" required onchange="CalculateRowWeight('+index+')" class="form-control"></td>');
							newRow.append('<td><input type="number" id="pur2_qty'+index+'" name="pur2_qty[]" placeholder="weight" value="0" step="any"  required class="form-control" disabled></td>');
							newRow.append('<td><input type="number" id="amount'+index+'" name="amount[]" placeholder="Amount"  value="0" step="any" onchange="rowTotal('+index+')"  required class="form-control" disabled></td>');
							newRow.append('<td><input type="date" id="pur2_price_date'+index+'" name="pur2_price_date[]" value="'+v['sale_rate_date']+'" step="any" onchange="rowTotal('+index+')"  required class="form-control" disabled><input type="hidden"  value="'+v['sale_rate_date']+'" class="form-control" name="pur2_price_date[]" id="pur2_price_date_show'+index+'"></td>');
							newRow.append('<td><button type="button" onclick="removeRow(this)" class="btn btn-danger"><i class="fas fa-times"></i></button></td>');

							table.append(newRow);
							$('#item_name'+index).val(v['item_cod']);
							$('#itemCount').val(index);
							rowTotal(index);
							index++;

							ind_total_qty= ind_total_qty + v['sales_qty']
							ind_total_weight= ind_total_weight + (v['sales_qty'] * v['weight_pc'])
							$('#myTable select[data-plugin-selecttwo]').select2();

						}); 
						$("#total_qty").val(ind_total_qty);
						$("#total_weight").val(ind_total_weight);
						$("#isInduced").val(1);
						$("#sale_against").val(id);

						$("#closeModal").trigger('click');
					},
					error: function(){
						alert("error");
					}
				});
   			}

		</script>
	@include('../layouts.footerlinks')
	</body>
</html>