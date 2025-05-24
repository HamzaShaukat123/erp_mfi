@include('../layouts.header')
	<body>
		<section class="body">
            @include('../layouts.pageheader')
            <div class="inner-wrapper cust-pad">
				<section role="main" class="content-body" style="margin:0px">
					<form method="post" id="myForm" action="{{ route('store-tstock-in-invoice') }}" enctype="multipart/form-data" onkeydown="return event.key != 'Enter';">
						@csrf
						<div class="row">
							<div class="col-12 mb-3">								
								<section class="card">
									<header class="card-header" style="display: flex;justify-content: space-between;">
										<h2 class="card-title">New Stock In Pipe/Garder</h2>
                                        <div class="card-actions">
											<button type="button" class="btn btn-danger modal-with-zoom-anim ws-normal mb-2" onclick="getPurchase2()" href="#getPurchase2"> Get Unclosed Purchase2 </button>
											<button type="button" class="btn btn-primary mb-2" onclick="addNewRow_btn()"> <i class="fas fa-plus"></i> Add New Row </button>
										</div>
									</header>

									<div class="card-body">
										<div class="row form-group mb-2">
											<div class="col-6 col-md-2 mb-2">
												<label class="col-form-label" >New ID</label>
												<input type="text" name="invoice_no" placeholder="(NEW ID)" class="form-control" disabled>
												<input type="hidden" id="itemCount" name="items" value="1" class="form-control" >
												<input type="hidden" id="printInvoice" name="printInvoice" value="0" class="form-control" >
                                                <input type="hidden" id="isInduced" name="isInduced" value="0" class="form-control" >
                                                <input type="hidden" id="sale_against" name="sale_against" value="0" class="form-control" >
											</div>

											<div class="col-6 col-md-2 mb-2">
												<label class="col-form-label" >Date</label>
												<input type="date" name="date" id="stck_in_date" required value="<?php echo date('Y-m-d'); ?>" class="form-control">
											</div>

											<div class="col-sm-12 col-md-4">
												<label class="col-form-label">Account Name<span style="color: red;"><strong>*</strong></span></label>
												<select data-plugin-selecttwo class="form-control select2-js" id="stck_in_coa_name" name="account_name" required>
													<option value="" disabled selected>Select Account</option>
													@foreach($coa as $key => $row)	
														<option value="{{$row->ac_code}}">{{$row->ac_name}}</option>
													@endforeach
												</select>
											</div>
											
											<div class="col-6 col-md-2">
												<label class="col-form-label" >Purchase Inv#</label>
												<input type="text" name="pur_inv" id="stock_in_pur_inv" placeholder="Purchase Inv#" class="form-control">
											</div>
											<div class="col-6 col-md-2">
												<label class="col-form-label" >Mill Inv/Gate#</label>
												<input type="text" name="mill_gate" placeholder="Mill Inv/Gate#" id="stock_in_mill_bill" class="form-control">
											</div>
											<div class="col-sm-12 col-md-3">
												<label class="col-form-label">File Attached</label>
												<input type="file" class="form-control" name="att[]" multiple accept=".zip, appliation/zip, application/pdf, image/png, image/jpeg">
											</div>

                                            <div class="col-sm-12 col-md-2">
												<label class="col-form-label">Item Type</label>
												<select data-plugin-selecttwo class="form-control select2-js mb-3" name="item_type">
													<option value="1">Pipes</option>
													<option value="2">Garder / TR</option>
												</select>												
											</div>

											<div class="col-sm-12 col-md-7 mb-2">
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
													<th width="10%">Weight</th>
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
                                                    <input type="number" id="qty1" name="qty[]" onchange="rowTotal(1)" placeholder="Qty" value="0" step="any" required class="form-control">
                                                    <input type="hidden" id="weight1" name="weight[]" placeholder="Weight" onchange="rowTotal(1)" value="0" step="any" required class="form-control">
                                                </td>
                                                <td>
                                                    <input type="number" id="row_total_weight1" placeholder="Weight" name="row_total_weight[]" disabled value="0" step="any"  class="form-control">
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
                                            <button type="button" class="btn btn-danger mt-2" onclick="window.location='{{ route('all-tstock-in') }}'"> <i class="fas fa-trash"></i> Discard Entry</button>
                                            <button type="submit" class="btn btn-primary mt-2" id="submitBtn"><i class="fas fa-save"></i> Add Entry</button>
                                        </div>
                                    </div>
                                </footer>
                            </section>
                        </div>
                    </div>
                </form>
                <div id="getPurchase2" class="zoom-anim-dialog modal-block modal-block-danger mfp-hide">
                    <section class="card">
                        <header class="card-header">
                            <h2 class="card-title">All Unclosed Purchases</h2>
                        </header>
                        <div class="card-body">
                            <div class="modal-wrapper">

                                <table class="table table-bordered table-striped mb-0" >
                                    <thead>
                                        <tr>
                                            <th>Inv #</th>
                                            <th>Company</th>
                                            <th>Date</th>
                                            <th>Mill Inv No.</th>
                                            <th>Dispatch To</th>
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
            newRow.append('<td><select data-plugin-selecttwo class="form-control select2-js" id="item_name'+index+'" name="item_name[]" onchange="getItemDetails(' + index + ', 2)"><option>Select Item</option>@foreach($items as $key => $row)<option value="{{ $row->it_cod }}">{{ $row->item_name }}</option>@endforeach</select></td>');
            newRow.append('<td><input type="text" id="remarks'+index+'" name="item_remarks[]" placeholder="Remarks" class="form-control"></td>');
            newRow.append('<td><input type="number" id="qty'+index+'" name="qty[]" placeholder="Qty" value="0" step="any" required class="form-control" onchange="rowTotal('+index+')"><input type="hidden" id="weight'+index+'" name="weight[]" placeholder="Weight" value="0" step="any" required class="form-control"></td>');
            newRow.append('<td><input type="number" id="row_total_weight'+index+'" name="row_total_weight[]" placeholder="weight" value="0" step="any" onchange="rowTotal('+index+')"  required class="form-control" disabled></td>');
            newRow.append('<td><button type="button" onclick="removeRow(this)" class="btn btn-danger"><i class="fas fa-times"></i></button></td>');

            table.append(newRow);
            index++;
            $('#itemCount').val(Number($('#itemCount').val()) + 1);
            $('#myTable select[data-plugin-selecttwo]').select2();


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
            url: "/item2/detail",
            data: {id: itemId},
            success: function(result) {
                if (result.length > 0) {
                    $('#item_code'+row_no).val(result[0]['it_cod']);
                    $('#item_name'+row_no).val(result[0]['it_cod']).select2();
                    $('#remarks'+row_no).val(result[0]['item_remark']);
                    $('#weight'+row_no).val(result[0]['weight']);
                    $('#weight'+row_no).trigger('change');

                    addNewRow();
                }
            },
            error: function() {
                alert("Error retrieving item details.");
            }
        });
    }

    function rowTotal(index){
        var qty = parseFloat($('#qty'+index+'').val());
        var weight = parseFloat($('#weight'+index+'').val());   
        var totalWeight = (qty*weight); 
        $('#row_total_weight'+index).val(totalWeight);

        tableTotal();
    }
    
    function tableTotal() {
        var totalqty = 0;
        var totalweight = 0;
        $('#tstock_inTable tr').each(function() {
            totalqty += Number($(this).find('input[name="qty[]"]').val());
            totalweight += Number($(this).find('input[name="row_total_weight[]"]').val());
        });

        $('#total_qty').val(totalqty.toFixed(0));
        $('#total_weight').val(totalweight.toFixed(0));
    }

    function getPurchase2(){
        var table = document.getElementById('unclosed_purchases_list');
        while (table.rows.length > 0) {
            table.deleteRow(0);
        }
        $.ajax({
            type: "GET",
            url: "/purchase2/getunclosedstockin/",
            success: function(result){
                $.each(result, function(k,v){
                    var html="<tr>";
                    html+= "<td>"+v['Sale_inv_no']+"</td>"
                    html+= "<td>"+v['acc_name']+"</td>"
                    html+= "<td>"+v['sa_date']+"</td>"
                    html+= "<td>"+v['pur_ord_no']+"</td>"
                    html+= "<td>"+v['disp_acc']+"</td>"
                    html+= "<td class='text-center'><a class='mb-1 mt-1 me-1 text-success' href='#' onclick='inducedItems("+v['Sale_inv_no']+")'><i class='fas fa-check'></i></a></td>"
                    html+="</tr>";
                    $('#unclosed_purchases_list').append(html);
                });
                        
            },
            error: function(){
                alert("error");
            }
        });
    }

    function inducedItems(id){
        var ind_total_qty=0, ind_total_weight=0;
        var table = document.getElementById('tstock_inTable');
        while (table.rows.length > 0) {
            table.deleteRow(0);
        }
        index=0;
        $('#itemCount').val(1);

        $.ajax({
            type: "GET",
            url: "/purchase2/getItems/"+id,
            success: function(result){
                $('#stck_in_date').val(result['pur1']['sa_date']);
                $('#stock_in_pur_inv').val(result['pur1']['Sale_inv_no']);
                $('#stock_in_mill_bill').val(result['pur1']['pur_ord_no']);
                $('#stock_in_pur_remarks').val(result['pur1']['Sales_Remarks']);
                $('#stck_in_coa_name').val(result['pur1']['account_name']).trigger('change');

                $.each(result['pur2'], function(k,v){
                    index++;
                    var table = $('#myTable').find('tbody');
                    var newRow = $('<tr>');
                    newRow.append('<td><input type="number" id="item_code'+index+'" value="'+v['item_cod']+'" name="item_code[]" placeholder="Code" class="form-control" required onchange="getItemDetails(' + index + ', 1)"></td>');
                    newRow.append('<td><select data-plugin-selecttwo class="form-control select2-js" id="item_name'+index+'" name="item_name[]" onchange="getItemDetails('+index+',2)"><option>Select Item</option>@foreach($items as $key => $row)+<option value="{{$row->it_cod}}" >{{ $row->item_name }}</option>@endforeach</select></td>');
                    newRow.append('<td><input type="text" id="remarks'+index+'" value="'+v['remarks']+'" name="item_remarks[]" placeholder="Remarks" class="form-control"></td>');
                    newRow.append('<td><input type="number" id="qty'+index+'" value="'+v['Sales_qty2']+'" name="qty[]" placeholder="Qty" step="any" required class="form-control" onchange="rowTotal('+index+')"><input type="hidden" id="weight'+index+'"  name="weight[]" placeholder="Weight" value="'+v['weight_pc']+'" step="any" required class="form-control"></td>');
                    newRow.append('<td><input type="number" id="row_total_weight'+index+'" name="row_total_weight[]" placeholder="weight"  value="'+v['Sales_qty2'] * v['weight_pc']+'" step="any" onchange="rowTotal('+index+')"  required class="form-control" disabled></td>');
                    newRow.append('<td><button type="button" onclick="removeRow(this)" class="btn btn-danger"><i class="fas fa-times"></i></button></td>');

                    table.append(newRow);
                    $('#item_name'+index).val(v['item_cod']);

                    ind_total_qty= ind_total_qty + v['Sales_qty2']
                    ind_total_weight= ind_total_weight + (v['Sales_qty2'] * v['weight_pc'])
                    $('#myTable select[data-plugin-selecttwo]').select2();
                }); 
                $("#total_qty").val(ind_total_qty);
                $("#total_weight").val(ind_total_weight);
                $("#isInduced").val(1);
                $("#sale_against").val(id);
                $('#itemCount').val(index);

                $("#closeModal").trigger('click');

            },
            error: function(){
                alert("error");
            }
        });
    }
</script>