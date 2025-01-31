@include('../layouts.header')
	<body>
		<section class="body">
			@include('layouts.pageheader')
			<div class="inner-wrapper cust-pad">
				<section role="main" class="content-body" style="margin:0px">
					<form method="post" action="{{ route('store-pdc-multiple') }}" enctype="multipart/form-data" onkeydown="return event.key != 'Enter';">
						@csrf
						<div class="row">
							<div class="col-12 mb-3">
								<section class="card">
									<header class="card-header" style="display: flex;justify-content: space-between;">
										<h2 class="card-title">New PDC (Multiple)</h2>
										<div class="card-actions">
											<button type="button" class="btn btn-primary" onclick="addNewRow_btn()"> <i class="fas fa-plus"></i> Add New Row </button>
										</div>
									</header>

									<div class="card-body" style="overflow-x:auto;min-height:450px;max-height:450px;overflow-y:auto">
										<input type="hidden" id="itemCount" name="items" value="1" placeholder="Code" class="form-control">
										<table class="table table-bordered table-striped mb-0" id="myTable" >
											<thead>
												<tr>
													<!-- <th width="4%">Code</th> -->
													<th width="">Date<span style="color: red;"><strong>*</strong></span></th>
													<th width="">Account Debit<span style="color: red;"><strong>*</strong></span></th>
													<th width="">Account Credit<span style="color: red;"><strong>*</strong></span></th>
													<th width="">Remarks</th>
													<th width="">Bank Name<span style="color: red;"><strong>*</strong></span></th>
													<th width="">Instrument#<span style="color: red;"><strong>*</strong></span></th>
													<th width="">Chq Date<span style="color: red;"><strong>*</strong></span></th>
													<th width="">Amount<span style="color: red;"><strong>*</strong></span></th>
													<th width=""></th>
												</tr>
											</thead>
											<tbody id="PDCTable">
												<tr>
													<!-- <td>
														<input type="number" class="form-control" disabled>
													</td> -->
													<td>
														<input type="date" class="form-control" style="max-width: 124px" name="date[]" required value="<?php echo date('Y-m-d'); ?>" >
													</td>
													<td>
														<select  data-plugin-selecttwo class="form-control select2-js" name ="ac_dr_sid" required>
															<option value="" disabled selected>Select Account</option>
															@foreach($acc as $key => $row)	
																<option value="{{$row->ac_code}}">{{$row->ac_name}}</option>
															@endforeach
														</select>
													</td>
													<td>
														<select  data-plugin-selecttwo class="form-control select2-js" name ="ac_cr_sid" required>
															<option value="" disabled selected>Select Account</option>
															@foreach($acc as $key => $row)	
																<option value="{{$row->ac_code}}">{{$row->ac_name}}</option>
															@endforeach
														</select>
													</td>
													<td>
														<input type="text" class="form-control" name="remarks[]" value=" ">
													</td>
													<td>
														<input type="text" class="form-control" name="bankname[]" required>
													</td>
													<td>
														<input type="text" class="form-control" name="instrumentnumber[]" required>
													</td>
													<td>
														<input type="date" class="form-control" style="max-width: 124px" name="chqdate[]" size=5 required value="<?php echo date('Y-m-d'); ?>" >
													</td>
													<td>
														<input type="number" class="form-control" name="amount[]" required value="0" step=".00001">
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
												<button type="submit" class="btn btn-primary mt-2"> <i class="fas fa-save"></i> Save All Items</button>
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

    function removeRow(button) {
		var tableRows = $("#PDCTable tr").length;
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