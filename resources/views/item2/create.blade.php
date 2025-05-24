@include('../layouts.header')
	<body>
		<section class="body">
			@include('layouts.pageheader')
			<div class="inner-wrapper cust-pad">
				<section role="main" class="content-body" style="margin:0px">
					<form method="post" action="{{ route('store-item-2') }}" enctype="multipart/form-data" onkeydown="return event.key != 'Enter';">
						@csrf
						<div class="row">
							<div class="col-12 mb-3">
								<section class="card">
									<header class="card-header" style="display: flex;justify-content: space-between;">
										<h2 class="card-title">New Item Entry Pipes (Multiple)</h2>
										<div class="card-actions">
											<button type="button" class="btn btn-primary" onclick="addNewRow()"> <i class="fas fa-plus"></i> Add New Row </button>
										</div>
									</header>

									<div class="card-body" style="overflow-x:auto;min-height:450px;max-height:450px;overflow-y:auto">
										<input type="hidden" id="itemCount" name="items" value="1" placeholder="Code" class="form-control">
										<table class="table table-bordered table-striped mb-0" id="myTable" >
											<thead>
												<tr>
													<!-- <th width="4%">Code</th> -->
													<th width="">Group Name<span style="color: red;"><strong>*</strong></span></th>
													<th width="">Item Name<span style="color: red;"><strong>*</strong></span></th>
													<th width="">Remarks</th>
													<th width="6%">O.Stock<span style="color: red;"><strong>*</strong></span></th>
													<th width="6%">Weight<span style="color: red;"><strong>*</strong></span></th>
													<th width="6%">P.Price<span style="color: red;"><strong>*</strong></span></th>
													<th width="">PR.Date<span style="color: red;"><strong>*</strong></span></th>
													<th width="6%">S.Price<span style="color: red;"><strong>*</strong></span></th>
													<th width="">SR.Date<span style="color: red;"><strong>*</strong></span></th>
													<th width="">Date<span style="color: red;"><strong>*</strong></span></th>
													<th width="6%">S.Level<span style="color: red;"><strong>*</strong></span></th>
													<th width="6%">L.Price<span style="color: red;"><strong>*</strong></span></th>
													<th width=""></th>
												</tr>
											</thead>
											<tbody id="ItemsTable">
												<tr>
													<!-- <td>
														<input type="number" class="form-control" disabled>
													</td> -->
													<td>
														<select data-plugin-selecttwo class="form-control select2-js"  name ="item_group[]" onchange="addNewRow(1)" required>
															<option value="" disabled selected>Select Group</option>
															@foreach($item_groups as $key => $row)	
																<option value="{{$row->item_group_cod}}">{{$row->group_name}}</option>
															@endforeach
														</select>
													</td>
													<td>
														<input type="text" class="form-control" name="item_name[]" autofocus onchange="validateItemName(this)" required>
													</td>
													<td>
														<input type="text" class="form-control" name="item_remarks[]" value=" ">
													</td>
													<td>
														<input type="number" class="form-control" name="item_stock[]" required value="0" step=".00001">
													</td>
													<td>
														<input type="number" class="form-control" name="weight[]" required value="0" step=".00001">
													</td>
													<td>
														<input type="number" class="form-control" name="item_pur_cost[]" required value="0" step=".00001">
													</td>
													<td>
														<input type="date" class="form-control" style="max-width: 124px" name="purchase_rate_date[]" size=5 required value="<?php echo date('Y-m-d'); ?>" >
													</td>
													<td>
														<input type="number" class="form-control" name="item_s_price[]" required value="0" step=".00001">
													</td>
													<td>
														<input type="date" class="form-control" style="max-width: 124px" name="sale_rate_date[]" required value="<?php echo date('Y-m-d'); ?>" >
													</td>
													<td>
														<input type="date" class="form-control" style="max-width: 124px" name="item_date[]" required value="<?php echo date('Y-m-d'); ?>" >
													</td>
													<td>
														<input type="number" class="form-control" name="item_stock_level[]" required value="0" step=".00001">
													</td>
													<td>
														<input type="number" class="form-control" name="item_l_price[]" required value="0" step=".00001">
													</td>
													<td style="vertical-align: middle;">
														<button type="button" onclick="removeRow(this)" class="btn btn-danger" tabindex="1"><i class="fas fa-times"></i></button>
													</td>
												</tr>
											</tbody>
										</table>
									</div>

									<footer class="card-footer">
										<div class="row form-group mb-2">
											<div class="text-end">
												<button type="button" class="btn btn-danger mt-2" onclick="confirmDiscard()">
													<i class="fas fa-trash"></i> Discard
												</button>
												<button type="submit" class="btn btn-primary mt-2" id="submitBtn"><i class="fas fa-save"></i> Save All Items</button>
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
		if (confirm('Do you want to discard this page?')) {
		window.location = "{{ route('all-items-2') }}";
		}
	}

    function removeRow(button) {
		var tableRows = $("#ItemsTable tr").length;
		if(tableRows>1){
			var row = button.parentNode.parentNode;
			row.parentNode.removeChild(row);
			index--;	
			itemCount = Number($('#itemCount').val());
			itemCount = itemCount-1;
			$('#itemCount').val(itemCount);
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

	function addNewRow(id){
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
			var cell10 = newRow.insertCell(9);
			var cell11 = newRow.insertCell(10);
			var cell12 = newRow.insertCell(11);
			var cell13 = newRow.insertCell(12);

			// cell1.innerHTML  = '<input type="text" class="form-control" disabled>';
			cell1.innerHTML = '<select data-plugin-selecttwo class="form-control select2-js" onchange="addNewRow('+index+')" name="item_group[]" required>'+
									'<option value="" disabled selected>Select Group</option>'+
									@foreach($item_groups as $row)
										'<option value="{{ $row->item_group_cod }}">{{ $row->group_name }}</option>'+
									@endforeach
								'</select>';

			cell2.innerHTML  = '<input type="text" class="form-control" name="item_name[]" autofocus onchange="validateItemName(this)" required>';
			cell3.innerHTML  = '<input type="text"   class="form-control"  name="item_remarks[]">';
			cell4.innerHTML  = '<input type="number" class="form-control" name="item_stock[]" required value="0" step=".00001">';
			cell5.innerHTML  = '<input type="number" class="form-control" name="weight[]" required value="0" step=".00001">';
			cell6.innerHTML  = '<input type="number" class="form-control" name="item_pur_cost[]" required value="0" step=".00001">';
			cell7.innerHTML  = '<input type="date" class="form-control" style="max-width: 124px" name="purchase_rate_date[]" required value="<?php echo date('Y-m-d'); ?>" >';
			cell8.innerHTML  = '<input type="number" class="form-control" name="item_s_price[]" required value="0" step=".00001">';
			cell9.innerHTML  = '<input type="date" class="form-control" style="max-width: 124px" name="sale_rate_date[]" required value="<?php echo date('Y-m-d'); ?>" >';
			cell10.innerHTML  = '<input type="date" class="form-control" style="max-width: 124px" name="item_date[]" required value="<?php echo date('Y-m-d'); ?>" >';
			cell11.innerHTML  = '<input type="number" class="form-control" name="item_stock_level[]" required value="0" step=".00001">';
			cell12.innerHTML = '<input type="number" class="form-control" name="item_l_price[]" required value="0" step=".00001">';
			cell13.innerHTML = '<button type="button" onclick="removeRow(this)" class="btn btn-danger" tabindex="1"><i class="fas fa-times"></i></button>';

			index++;

			itemCount = Number($('#itemCount').val());
			itemCount = itemCount+1;
			$('#itemCount').val(itemCount);
			$('#myTable select[data-plugin-selecttwo]').select2();
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
			url: '/item2/new-item/validate',
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
</script>