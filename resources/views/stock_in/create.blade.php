@include('../layouts.header')
	<body>
		<section class="body">
            @include('../layouts.pageheader')
            <div class="inner-wrapper cust-pad">
				<section role="main" class="content-body" style="margin:0px">
					<form method="post" id="myForm" action="{{ route('store-stock-in-invoice') }}" enctype="multipart/form-data" onkeydown="return event.key != 'Enter';">
						@csrf
						<div class="row">
							<div class="col-12 mb-3">								
								<section class="card">
									<header class="card-header" style="display: flex;justify-content: space-between;">
										<h2 class="card-title">New Stock In Doors</h2>
                                        <div class="card-actions">
                                            <button type="button" class="btn btn-primary" onclick="addNewRow_btn()"> <i class="fas fa-plus"></i> Add New Row </button>
                                        </div>
									</header>

									<div class="card-body">
										<div class="row form-group mb-2">
											<div class="col-6 col-md-2 mb-2">
												<label class="col-form-label" >New ID</label>
												<input type="text" name="invoice_no" placeholder="(NEW ID)" class="form-control" disabled>
												<input type="hidden" id="itemCount" name="items" value="1" class="form-control" >
												<input type="hidden" id="printInvoice" name="printInvoice" value="0" class="form-control" >
											</div>

											<div class="col-6 col-md-2 mb-2">
												<label class="col-form-label" >Date</label>
												<input type="date" name="date" id="stck_in_date" required value="<?php echo date('Y-m-d'); ?>" class="form-control">
											</div>

											<div class="col-sm-12 col-md-4">
												<label class="col-form-label">Karigar Name<span style="color: red;"><strong>*</strong></span></label>
												<select data-plugin-selecttwo class="form-control select2-js" id="stck_in_coa_name" name="account_name" required>
													<option value="" disabled selected>Select Karigar Name</option>
													@foreach($coa as $key => $row)	
														<option value="{{$row->ac_code}}">{{$row->ac_name}}</option>
													@endforeach
												</select>
											</div>
											
											<div class="col-sm-12 col-md-4">
												<label class="col-form-label">File Attached</label>
												<input type="file" class="form-control" name="att[]" multiple accept=".zip, appliation/zip, application/pdf, image/png, image/jpeg">
											</div>

											<div class="col-sm-12 col-md-8 mb-2">
												<label class="col-form-label">Remarks</label>
												<textarea rows="2" cols="50" name="remarks" id="stock_in_pur_remarks" placeholder="Remarks" class="form-control cust-textarea"></textarea>
											</div>
									  </div>
									</div>
							
									<div class="card-body" style="overflow-x:auto;max-height:450px;overflow-y:auto">
										<table class="table table-bordered table-striped mb-0" id="myTable" >
											<thead>
												<tr>
													<th width="10%">Item Code<span style="color: red;"><strong>*</strong></span></th>
													<th width="20%">Item Name<span style="color: red;"><strong>*</strong></span></th>
													<th width="20%">Remarks</th>
													<th width="15%">Qty<span style="color: red;"><strong>*</strong></span></th>
													<th width="10%">Weight<span style="color: red;"><strong>*</strong></span></th>
													<th width="10%"></th>
												</tr>
											</thead>
										    <tbody id="tstock_inTable">
											 <tr>
                                                <td>
                                                    <input type="number" id="item_code1" name="item_code[]" placeholder="Code" class="form-control" required onchange="getItemDetails(1,1)">
                                                </td>
                                                <td>
                                                    <select data-plugin-selecttwo class="form-control select2-js" id="item_name1" onchange="getItemDetails(1,2)" name="item_name[]" required>
                                                        <option selected>Select Item</option>
                                                        @foreach($items as $key => $row)
                                                            <option value="{{ $row->it_cod }}">{{ $row->item_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" id="remarks1" name="item_remarks[]" placeholder="Remarks" class="form-control">
                                                </td>
                                                <td>
                                                    <input type="number" id="qty1" onchange="tableTotal()" name="qty[]" placeholder="Qty" value="0" step="any" required class="form-control">
                                                </td>
                                                <td>
                                                    <input type="number" id="weight1" onchange="tableTotal()" name="weight[]" placeholder="Weight" value="0" step="any" required class="form-control">
                                                </td>
                                                <td>
                                                    <button type="button" onclick="removeRow(this)" class="btn btn-danger" tabindex="1"><i class="fas fa-times"></i></button>
                                                </td>
                                             </tr>
                                            </tbody>
                                    </table>
                                </div>
                                <footer class="card-footer">
                                    <div class="row mb-3" style="float:right; margin-right: 10%;">
                                        <div class="col-6 col-md-6 pb-sm-3 pb-md-0">
                                            <label class="col-form-label">Total Qty</label>
                                            <input type="number" id="total_qty" placeholder="Total Qty" class="form-control" step="any" disabled>
                                        </div>
                                        
                                        <div class="col-6 col-md-6 pb-sm-3 pb-md-0">
                                            <label class="col-form-label">Total Weight</label>
                                            <input type="number" id="total_weight" placeholder="Total weight" class="form-control" step="any" disabled>
                                        </div>
                                        
                                    </div>
                                </footer>
                                
                                <footer class="card-footer">
                                    <div class="row form-group mb-2">
                                        <div class="text-end">
                                            <button type="button" class="btn btn-danger mt-2" onclick="window.location='{{ route('all-stock-in') }}'"> <i class="fas fa-trash"></i> Discard Entry</button>
                                            <button type="submit" class="btn btn-primary mt-2" id="submitBtn"><i class="fas fa-save"></i> Add Entry</button>
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

    var index = 2;

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
        var tableRows = $("#tstock_inTable tr").length;
        if (tableRows > 1) {
            $(button).closest('tr').remove();
            index--;
            $('#itemCount').val(Number($('#itemCount').val()) - 1);
            tableTotal();
        }
    }

    function addNewRow() {
        var lastRow = $('#myTable tr:last');
        var latestValue = lastRow.find('select').val();

        if (latestValue !== "Select Item") {
            var table = $('#myTable').find('tbody');
            var newRow = $('<tr>');

            newRow.append('<td><input type="number" id="item_code'+index+'" name="item_code[]" placeholder="Code" class="form-control" required onchange="getItemDetails(' + index + ', 1)"></td>');
            newRow.append('<td><select data-plugin-selecttwo class="form-control select2-js" id="item_name'+index+'" name="item_name[]" required onchange="getItemDetails(' + index + ', 2)"><option>Select Item</option>@foreach($items as $key => $row)<option value="{{ $row->it_cod }}">{{ $row->item_name }}</option>@endforeach</select></td>');
            newRow.append('<td><input type="text" id="remarks'+index+'" name="item_remarks[]" placeholder="Remarks" class="form-control"></td>');
            newRow.append('<td><input type="number" id="qty'+index+'" onchange="tableTotal()" name="qty[]" placeholder="Qty" value="0" step="any" required class="form-control"></td>');
            newRow.append('<td><input type="number" id="weight'+index+'" onchange="tableTotal()" name="weight[]" placeholder="Weight" value="0" step="any" required class="form-control"></td>');
            newRow.append('<td><button type="button" onclick="removeRow(this)" class="btn btn-danger"><i class="fas fa-times"></i></button></td>');

            table.append(newRow);
            index++;
            $('#itemCount').val(Number($('#itemCount').val()) + 1);
            $('#myTable select[data-plugin-selecttwo]').select2();

            tableTotal();
        }
    }

    function addNewRow_btn() {

    addNewRow(); // Call the same function
    // Set focus on the new item_code input field
    document.getElementById('item_code' + (index - 1)).focus();


    }

    function getItemDetails(row_no, option) {
        var itemId = option === 1 ? $("#item_code" + row_no).val() : $("#item_name" + row_no).val();

        $.ajax({
            type: "GET",
            url: "/items/detail",
            data: {id: itemId},
            success: function(result) {
                if (result.length > 0) {
                    $('#item_code'+row_no).val(result[0]['it_cod']);
                    $('#item_name'+row_no).val(result[0]['it_cod']).select2();
                    $('#remarks'+row_no).val(result[0]['item_remark']);

                    addNewRow();
                }
            },
            error: function() {
                alert("Error retrieving item details.");
            }
        });
    }
    
    function tableTotal() {
        var totalqty = 0;
        var totalweight = 0;
        $('#tstock_inTable tr').each(function() {
            totalqty += Number($(this).find('input[name="qty[]"]').val());
            totalweight += Number($(this).find('input[name="weight[]"]').val());
        });

        $('#total_qty').val(totalqty.toFixed(0));
        $('#total_weight').val(totalweight.toFixed(0));
    }
</script>