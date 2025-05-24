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
										<button type="button" class="btn btn-primary" onclick="addNewRow()"> <i class="fas fa-plus"></i> Add New Row </button>
									</div>
								</header>

								<div class="card-body" style="overflow-x:auto;min-height:450px;max-height:450px;overflow-y:auto">
									<input type="hidden" id="itemCount" name="items" value="1" class="form-control">
									<table class="table table-bordered table-striped mb-0" id="myTable">
										<thead>
											<tr>
												<th>Date<span style="color: red;"><strong>*</strong></span></th>
												<th>Account Debit<span style="color: red;"><strong>*</strong></span></th>
												<th>Account Credit<span style="color: red;"><strong>*</strong></span></th>
												<th>Remarks</th>
												<th>Bank Name<span style="color: red;"><strong>*</strong></span></th>
												<th>Instrument#<span style="color: red;"><strong>*</strong></span></th>
												<th>Chq Date<span style="color: red;"><strong>*</strong></span></th>
												<th>Amount<span style="color: red;"><strong>*</strong></span></th>
												<th>Attachments</th>
												<th></th>
											</tr>
										</thead>
										<tbody id="PDCTable">
											<tr>
												<td>
													<input type="date" class="form-control" style="max-width: 135px" name="date[]" required value="{{ date('Y-m-d') }}">
												</td>
												<td>
													<select class="form-control select2-js" name="ac_dr_sid[]" required>
														<option value="" disabled selected>Select Account</option>
														@foreach($acc as $row)	
															<option value="{{$row->ac_code}}">{{$row->ac_name}}</option>
														@endforeach
													</select>
												</td>
												<td>
													<select class="form-control select2-js" name="ac_cr_sid[]" required>
														<option value="" disabled selected>Select Account</option>
														@foreach($acc as $row)	
															<option value="{{$row->ac_code}}">{{$row->ac_name}}</option>
														@endforeach
													</select>
												</td>
												<td>
													<input type="text" class="form-control" name="remarks[]">
												</td>
												<td>
													<input type="text" class="form-control" name="bankname[]" required>
												</td>
												<td>
													<input type="text" class="form-control" name="instrumentnumber[]" required>
												</td>
												<td>
													<input type="date" class="form-control" style="max-width: 135px" name="chqdate[]" required value="{{ date('Y-m-d') }}">
												</td>
												<td>
													<input type="number" class="form-control amount-field" name="amount[]" required value="0" step=".00001" onchange="checkAndAddRow(this)">
												</td>
												<td>
													<input type="file" class="form-control" name="att[][]" multiple>
												</td>
												<td style="vertical-align: middle;">
													<button type="button" onclick="removeRow(this)" class="btn btn-danger"><i class="fas fa-times"></i></button>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
								<footer class="card-footer">
									<div class="d-flex justify-content-between align-items-center w-100">
										<h3 class="font-weight-bold mb-0 text-5 text-primary">
											Total Amount: <strong class="text-4 text-danger" id="totalAmount">0.00</strong> 
											<span class="text-4 text-primary">PKR</span>
										</h3>
										<div class="d-flex gap-2">
											<button type="button" class="btn btn-danger" onclick="window.location='{{ route('all-pdc') }}'">
												<i class="fas fa-trash"></i> Discard
											</button>
											<button type="submit" class="btn btn-primary mt-2" id="submitBtn"><i class="fas fa-save"></i> Save All PDCs
											</button>
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
	
	</div>
    @include('../layouts.footerlinks')
</body>
</html>

<script>
	var index = 2;
	var itemCount = Number($('#itemCount').val());

	function removeRow(button) {
		if ($("#PDCTable tr").length > 1) {
			$(button).closest('tr').remove();
			itemCount--;
			$('#itemCount').val(itemCount);

			calculateTotal(); 
		}
	}


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

	function addNewRow() {
		var lastRow = $('#PDCTable tr:last');
		var lastSelectValue = lastRow.find('select').first().val();

		if (lastSelectValue !== "") {
			var newRow = `<tr>
				<td><input type="date" class="form-control" style="max-width: 135px" name="date[]" required value="${new Date().toISOString().split('T')[0]}"></td>
				<td>${createSelect("ac_dr_sid[]")}</td>
				<td>${createSelect("ac_cr_sid[]")}</td>
				<td><input type="text" class="form-control" name="remarks[]"></td>
				<td><input type="text" class="form-control" name="bankname[]" required></td>
				<td><input type="text" class="form-control" name="instrumentnumber[]" required></td>
				<td><input type="date" class="form-control" style="max-width: 135px" name="chqdate[]" required value="${new Date().toISOString().split('T')[0]}"></td>
				<td><input type="number" class="form-control amount-field" name="amount[]" required value="0" step=".00001" onchange="checkAndAddRow(this)"></td>
				<td><input type="file" class="form-control" name="att[${itemCount}][]" multiple></td>
				<td style="vertical-align: middle;"><button type="button" onclick="removeRow(this)" class="btn btn-danger"><i class="fas fa-times"></i></button></td>
			</tr>`;

			$('#PDCTable').append(newRow);
			itemCount++;
			$('#itemCount').val(itemCount);
			$('.select2-js').select2();
		}
	}


	function createSelect(name) {
		var select = `<select class="form-control select2-js" name="${name}" required>
			<option value="" disabled selected>Select Account</option>`;
		@foreach($acc as $row)
			select += `<option value="{{ $row->ac_code }}">{{ $row->ac_name }}</option>`;
		@endforeach
		select += `</select>`;
		return select;
	}

	$(document).ready(function() {
		$('.select2-js').select2();
		
		// Ensure dynamic binding for newly added amount inputs
		$(document).on('change', '.amount-field', function() {
			checkAndAddRow(this);
		});
	});

	function checkAndAddRow(input) {
		var value = input.value.trim();
		
		// Only add a new row if the value is valid (greater than 0)
		if (value !== "" && parseFloat(value) > 0) {
			var lastRow = $('#PDCTable tr:last');
			
			// Add a new row if this is the last row with a valid amount value
			if (lastRow.find('.amount-field').is(input)) {
				addNewRow();
			}
		}
	}


	function calculateTotal() {
		var total = 0;
		$('.amount-field').each(function() {
			var value = parseFloat($(this).val()) || 0;
			total += value;
		});

		// Format number with commas as thousand separators
		var formattedTotal = total.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 });

		$('#totalAmount').text(formattedTotal); // Update total amount display
	}


	// Trigger calculation on input change
	$(document).on('input', '.amount-field', function() {
		calculateTotal();
	});




</script>
