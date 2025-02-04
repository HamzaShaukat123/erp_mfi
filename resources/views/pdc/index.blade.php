@include('../layouts.header')
	<body>
		<section class="body">
            @include('layouts.homepageheader')
			<div class="inner-wrapper cust-pad">
				@include('layouts.leftmenu')
				<section role="main" class="content-body">
                    <div class="row">
                        <div class="col">
                            <section class="card">
                                <header class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                                    <h2 class="card-title">PDC</h2>
                                    <div style="display: flex; gap: 10px;">
                                        <form class="text-end" action="{{ route('create-pdc-multiple') }}" method="GET">
                                            <button type="button" class="btn btn-primary mt-2 modal-with-zoom-anim ws-normal" href="#addModal">
                                                <i class="fas fa-plus"></i> New PDC (Single)
                                            </button>
                                            <button type="submit" class="btn btn-danger mt-2">
                                                <i class="fas fa-plus"></i> New PDC (Multiple)
                                            </button>
                                        </form>
                                        
                                        <!-- Separate Form for Print Selected -->
                                        <form id="print-form" class="text-end" action="{{ route('show-multiple-pdc') }}" method="GET">
                                            <input type="hidden" name="selected_pdc" id="selected_pdc_input">
                                            <button type="submit" class="btn btn-success mt-2">
                                                <i class="fas fa-print"></i> Print Selected
                                            </button>
                                        </form>
                                    </div>
                                </header>

                              
                                <div class="card-body" >
                                    <div>
                                        <div class="col-md-5" style="display:flex;">
                                            <select class="form-control" style="margin-right:10px" id="columnSelect">
                                                <option selected disabled>Search by</option>
                                                <option value="1">by ID</option>
                                                <option value="2">by Date</option>
                                                <option value="3">by Account Debit</option>
                                                <option value="4">by Account Credit</option>
                                                <option value="5">by Remarks</option>
                                                <option value="6">by Bank</option>
                                                <option value="7">by Instrument</option>
                                                <option value="8">by Chq Date</option>
                                                <option value="9">by Amount</option>
                                                <option value="10">by Vocher</option>
                                            </select>
                                            <input type="text" class="form-control" id="columnSearch" placeholder="Search By Column"/>
                                        </div>
                                    </div>
                                    <div class="modal-wrapper table-scroll">
                                        <table class="table table-bordered table-striped mb-0" id="cust-datatable-default">
                                            <thead>
                                                <tr>
                                                    <th></th> 
                                                    <th width="5%">ID#</th>
                                                    <th width="8%">Date</th>
                                                    <th width="15%">Account Debit</th>
                                                    <th width="15%">Account Credit</th>
                                                    <th width="20%">Remarks</th>
                                                    <th>Bank Name</th>
                                                    <th>Instrument</th>
                                                    <th>Chq Date</th>
                                                    <th>Amount</th>
                                                    <th>Voucher#</th>
                                                    <th>Att.</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($jv1 as $key => $row)
                                                    <tr>
                                                        <td><input type="checkbox" class="selectRow" value="{{$row->pdc_id}}" onclick="getSelectedIds()"></td> <!-- Checkbox per row -->
                                                        <td>{{$row->pdc_id}}</td>
                                                        <td>{{ \Carbon\Carbon::parse($row->date)->format('d-m-y') }}</td>
                                                        <td>{{$row->debit_account}}</td>
                                                        <td>{{$row->credit_account}}</td>
                                                        <td >{{$row->remarks}}</td>
                                                        <td >{{$row->bankname}}</td>
                                                        <td >{{$row->instrumentnumber}}</td>
                                                        <td>{{ \Carbon\Carbon::parse($row->chqdate)->format('d-m-y') }}</td>
                                                        <td>{{ number_format($row->amount, 0) }}</td>
                                                        <td>{{$row->voch_prefix . $row->voch_id}}</td>
                                                        <td style="vertical-align: middle;">
                                                            <a class="mb-1 mt-1 me-1 modal-with-zoom-anim ws-normal text-dark" onclick="getAttachements({{$row->pdc_id}})" href="#attModal"><i class="fa fa-eye"> </i></a>
                                                            <span class="separator"> | </span>
                                                            <a class="mb-1 mt-1 me-1 modal-with-zoom-anim ws-normal text-danger" onclick="setAttId({{$row->pdc_id}})" href="#addAttModal"> <i class="fas fa-paperclip"> </i></a>
                                                        </td>
                                                        <td class="actions">
                                                            {{-- <a class="mb-1 mt-1 me-1" href="{{ route('show-pdc', $row->pdc_id) }}">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <span class="separator"> | </span> --}}
                                                            <a class="mb-1 mt-1 me-1 modal-with-zoom-anim ws-normal modal-with-form" onclick="getJVSDetails({{$row->pdc_id}})" href="#updateModal">
                                                                <i class="fas fa-pencil-alt"></i>
                                                            </a>
                                                            @if(session('user_role')==1)
                                                            <span class="separator"> | </span>
                                                            <a class="mb-1 mt-1 me-1 modal-with-zoom-anim ws-normal" onclick="setId({{$row->pdc_id}})" href="#deleteModal">
                                                                <i class="far fa-trash-alt" style="color:red"></i>
                                                            </a>
                                                            @endif
                                                        </td>

                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </section>		
			</div>
		</section>

        <div id="updateModal" class="modal-block modal-block-primary mfp-hide" style="z-index: 1050">
            <section class="card">
                <form method="post" action="{{ route('update-pdc') }}" enctype="multipart/form-data" onkeydown="return event.key != 'Enter';">
                    @csrf
                    <header class="card-header">
                        <h2 class="card-title">Update PDC</h2>
                    </header>
                    <div class="card-body">
                        <div class="row form-group">
                            <div class="col-lg-6">
                                <label>JV1 Code</label>
                                <input type="number" class="form-control" placeholder="PDC Code" id="update_id" required disabled>
                                <input type="hidden" class="form-control" placeholder="PDC Code" name="update_pdc_id" id="update_id_view" required>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Date</label>
                                <input type="date" class="form-control" placeholder="Date" id="update_date" name="update_date" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Account Debit<span style="color: red;"><strong>*</strong></span></label>
                                <select data-plugin-selecttwo class="form-control select2-js"  name="update_ac_dr_sid" id="update_ac_dr_sid" required >
                                    <option value="" disabled selected>Select Account</option>
                                    @foreach($acc as $key => $row)	
                                        <option value="{{$row->ac_code}}">{{$row->ac_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Account Credit<span style="color: red;"><strong>*</strong></span></label>
                                <select data-plugin-selecttwo class="form-control select2-js"  name ="update_ac_cr_sid" required id="update_ac_cr_sid">
                                    <option disabled selected>Select Account</option>
                                    @foreach($acc as $key => $row)	
                                        <option value="{{$row->ac_code}}">{{$row->ac_name}}</option>
                                    @endforeach
                                </select>                            
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Amount<span style="color: red;"><strong>*</strong></span></label>
                                <input type="number" class="form-control" placeholder="Amount" id="update_amount" value="0" step="any" name="update_amount" required>
                            </div>

                            <div class="col-lg-6 mb-2">
                                <label>Attachments</label>
                                <input type="file" class="form-control" name="update_att[]" multiple accept=".zip, appliation/zip, application/pdf, image/png, image/jpeg">
                            </div> 
                            <div class="col-lg-6 mb-2">
                                <label>Bank Name</label>
                                <input type="text" class="form-control" placeholder="Bank Name" id="update_bankname" name="update_bankname" required>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Instrument#</label>
                                <input type="text" class="form-control" placeholder="Instrument#" id="update_instrumentnumber" name="update_instrumentnumber" required>
                            </div>
                            
                            
                            <div class="col-lg-6 mb-2">
                                <label>Chq Date</label>
                                <input type="date" class="form-control" placeholder="Chq Date" id="update_chqdate" name="update_chqdate" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            
                            <div class="col-lg-12 mb-2">
                                <label>Remarks</label>
                                <textarea rows="4" cols="50" class="form-control cust-textarea" placeholder="Remarks" id="update_remarks" name="update_remarks"> </textarea>                            </div>
                            </div>
                        </div>
                    
                    <footer class="card-footer">
                        <div class="row">
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-primary">Update PDC</button>
                                <button class="btn btn-default modal-dismiss">Cancel</button>
                            </div>
                        </div>
                    </footer>
                    </div>
                </form>
            </section>
        </div>

        <div id="deleteModal" class="zoom-anim-dialog modal-block modal-block-danger mfp-hide">
            <form method="post" action="{{ route('delete-jv1') }}" enctype="multipart/form-data">
                @csrf
                <section class="card">
                    <header class="card-header">
                        <h2 class="card-title">Delete Journal Voucher</h2>
                    </header>
                    <div class="card-body">
                        <div class="modal-wrapper">
                            <div class="modal-icon">
                                <i class="fas fa-question-circle"></i>
                            </div>
                            <div class="modal-text">
                                <p class="mb-0">Are you sure that you want to delete this Journal Voucher?</p>
                                <input name="delete_auto_lager" id="deleteID" hidden>
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

        <div id="addModal" class="modal-block modal-block-primary mfp-hide">
            <section class="card">
                <form method="post" action="{{ route('store-pdc') }}" enctype="multipart/form-data" onkeydown="return event.key != 'Enter';">
                    @csrf
                    <header class="card-header">
                        <h2 class="card-title">Add PDC</h2>
                    </header>
                    <div class="card-body">
                        <div class="row form-group">
                            <div class="col-lg-6">
                                <label>PDC Code</label>
                                <input type="number" class="form-control" placeholder="PDC Code" required disabled>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Date</label>
                                <input type="date" class="form-control" placeholder="Date" name="date" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Account Debit<span style="color: red;"><strong>*</strong></span></label>
                                <select data-plugin-selecttwo class="form-control select2-js" name ="ac_dr_sid" required>
                                    <option value="" disabled selected>Select Account</option>
                                    @foreach($acc as $key => $row)	
                                        <option value="{{$row->ac_code}}">{{$row->ac_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Account Credit<span style="color: red;"><strong>*</strong></span></label>

                                <select  data-plugin-selecttwo class="form-control select2-js" name ="ac_cr_sid" required>
                                    <option value="" disabled selected>Select Account</option>
                                    @foreach($acc as $key => $row)	
                                        <option value="{{$row->ac_code}}">{{$row->ac_name}}</option>
                                    @endforeach
                                </select>                            
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Amount<span style="color: red;"><strong>*</strong></span></label>
                                <input type="number" class="form-control" placeholder="Amount" value="0" step="any" name="amount" required>
                            </div>

                            <div class="col-lg-6 mb-2">
                                <label>Attachments</label>
                                <input type="file" class="form-control" name="att[]" multiple accept=".zip, appliation/zip, application/pdf, image/png, image/jpeg">
                            </div>  
                            <div class="col-lg-4 mb-2">
                                <label>Bank Name</label>
                                <input type="text" class="form-control" placeholder="Bank Name"  step="any" name="bankname" required>
                            </div>
                            <div class="col-lg-4 mb-2">
                                <label>Instrument#</label>
                                <input type="text" class="form-control" placeholder="Instrument#"  step="any" name="instrumentnumber" required>
                            </div>
                            <div class="col-lg-4 mb-2">
                                <label>Chq Date</label>
                                <input type="date" class="form-control" placeholder="Chq Date" name="chqdate" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="col-lg-12 mb-2">
                                <label>Remarks</label>
                                <textarea rows="4" cols="50" class="form-control cust-textarea" placeholder="Remarks" name="remarks"> </textarea>                            </div>
                            </div>
                        </div>
                        <footer class="card-footer">
                            <div class="row">
                                <div class="col-md-12 text-end">
                                    <button type="submit" class="btn btn-primary">Add PDC</button>
                                    <button class="btn btn-default modal-dismiss">Cancel</button>
                                </div>
                            </div>
                        </footer>
                    </div>
                </form>
            </section>
        </div>

        <div id="attModal" class="zoom-anim-dialog modal-block modal-block-danger mfp-hide">
            <section class="card">
                <header class="card-header">
                    <h2 class="card-title">All Attachements</h2>
                </header>
                <div class="card-body">
                    <div class="modal-wrapper">

                        <table class="table table-bordered table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Attachement Path</th>
                                    <th>Download</th>
                                    <th>View</th>
                                    <th>Delete</th>
                                </tr>
                            </thead>
                            <tbody id="pdc_attachements">

                            </tbody>
                        </table>
                    </div>
                </div>
                <footer class="card-footer">
                    <div class="row">
                        <div class="col-md-12 text-end">
                            <button class="btn btn-default modal-dismiss">Cancel</button>
                        </div>
                    </div>
                </footer>
            </section>
        </div>

        <div id="addAttModal" class="zoom-anim-dialog modal-block modal-block-danger mfp-hide">
            <form method="post" action="{{ route('pdc-att-add') }}" enctype="multipart/form-data" onkeydown="return event.key != 'Enter';">
                @csrf  
                <section class="card">
                    <header class="card-header">
                        <h2 class="card-title">Upload Attachements</h2>
                    </header>
                    <div class="card-body">
                        <div class="modal-wrapper">
                            <div class="col-lg-12 mb-2">
                                <input type="file" class="form-control" name="addAtt[]" multiple accept="application/pdf, image/png, image/jpeg">
                                <input type="hidden" class="form-control" name="att_id" id="att_id">
                            </div>
                        </div>
                    </div>
                    <footer class="card-footer">
                        <div class="row">
                            <div class="col-md-12 text-end">
                                <button type="sumit" class="btn btn-danger">Upload</button>
                                <button class="btn btn-default modal-dismiss">Cancel</button>
                            </div>
                        </div>
                    </footer>
                </section>
            </form>
        </div>
        
        @include('../layouts.footerlinks')
	</body>
</html>
<script>
    $(document).ready(function(){
        var table = $('#cust-datatable-default').DataTable();

        $('#columnSelect').on('change', function () {
            // Clear the previous search
            table.search('').columns().search('').draw(); // Reset global and column-specific filters
        });
        $('#columnSearch').on('keyup change', function () {
            var columnIndex = $('#columnSelect').val(); // Get selected column index
            table.column(columnIndex).search(this.value).draw(); // Apply search and redraw
        });
    });

    function setId(id){
        $('#deleteID').val(id);
    }

    function setAttId(id){
        $('#att_id').val(id);
    }

    function getAttachements(id){

        var table = document.getElementById('pdc_attachements');
        while (table.rows.length > 0) {
            table.deleteRow(0);
        }

        $.ajax({
            type: "GET",
            url: "/pdc/attachements",
            data: {id:id},
            success: function(result){
                $.each(result, function(k,v){
                    var html="<tr>";
                    html+= "<td>"+v['att_path']+"</td>"
                    html+= "<td class='text-center'><a class='mb-1 mt-1 mr-2 me-1 text-danger' href='/pdc/download/"+v['att_id']+"'><i class='fas fa-download'></i></a></td>"
                    html+= "<td class='text-center'><a class='mb-1 mt-1 me-1 text-primary' href='/pdc/view/"+v['att_id']+"' target='_blank'><i class='fas fa-eye'></i></a></td>"
                    html+= "<td class='text-center'><a class='mb-1 mt-1 me-1 text-primary' href='#' onclick='deleteFile("+v['att_id']+")'><i class='fas fa-trash'></i></a></td>"
                    html+="</tr>";
                    $('#pdc_attachements').append(html);
                });
                $('#download_id').val(result[0]['ac_code']);
            },
            error: function(){
                alert("error");
            }
        });
    }

    function getJVSDetails(id){
        $.ajax({
            type: "GET",
            url: "/pdc/detail",
            data: {id:id},
            success: function(result){
                $('#update_id').val(result['pdc_id']);
                $('#update_id_view').val(result['pdc_id']);
                $('#update_ac_cr_sid').val(result['ac_cr_sid']).trigger('change');
                $('#update_ac_dr_sid').val(result['ac_dr_sid']).trigger('change');
                $('#update_amount').val(result['amount']);
                $('#update_date').val(result['date']);
                $('#update_chqdate').val(result['chqdate']);
                $('#update_bankname').val(result['bankname']);
                $('#update_instrumentnumber').val(result['instrumentnumber']);
                $('#update_remarks').val(result['remarks']);
            },
            error: function(){
                alert("error");
            }
        });
	}

    function deleteFile(fileId) {
        if (!confirm('Are you sure you want to delete this file?')) {
            return;
        }

        fetch('/pdc/deleteAttachment/' + fileId, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (response.ok) {
                alert('File deleted successfully.');
                // Optionally, remove the element or reload the page
                location.reload();
            } else {
                return response.json().then(data => {
                    throw new Error(data.message || 'An error occurred.');
                });
            }
        })
        .catch(error => {
            alert(error.message);
        });
    }


    function getSelectedIds() {
        var selectedIds = [];
        var checkboxes = document.querySelectorAll('.selectRow:checked'); // Find selected checkboxes
        
        checkboxes.forEach(function(checkbox) {
            selectedIds.push(checkbox.value); // Add PDC ID to the array
        });

        // Check if any checkboxes are selected
        if (selectedIds.length > 0) {
            document.getElementById('selected_pdc_input').value = selectedIds.join(','); // Populate the hidden input
        } else {
            document.getElementById('selected_pdc_input').value = ''; // Clear the input if no checkboxes are selected
        }
    }
</script>