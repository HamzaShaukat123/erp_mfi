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
                                <header class="card-header" style="display: flex;justify-content: space-between;">
                                    <h2 class="card-title">Complains</h2>
                                    <div class="card-actions">
                                        <button type="button" class="modal-with-form btn btn-primary" href="#addModal"> <i class="fas fa-plus"></i> New Complain</button>
                                    </div>
                                    
                                </header>
                                <div class="card-body">
                                	<table class="table table-bordered table-striped mb-0" id="datatable-default">
                                        
                                        <thead>
                                            <tr>
                                                <th width="5%">Id</th>
                                                <th>Complain Date</th>
                                                <th>Company Name</th>
                                                <th>Custmer Name</th>
                                                <th>MFI Inv#</th>
                                                <th>Mill Inv#</th>
                                                <th>Complain Details</th>
                                                <th>Resolve Date</th>
                                                <th>Closing Remarks</th>
                                                <th>Status</th>
                                                <th>Att</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($complains as $key => $row)
                                                <tr>
                                                    <td>{{$row->id}}</td>
                                                    <td>{{ \Carbon\Carbon::parse($row->inv_dat)->format('d-m-y') }}</td>
                                                    <td>{{$row->company_name_display}}</td>
                                                    <td>{{$row->party_name_display}}</td>
                                                    <td>{{$row->mfi_pur_number}}</td>
                                                    <td>{{$row->mill_pur_number}}</td>
                                                    <td>{{$row->complain_detail}}</td>

                                                    <td>
                                                        @if($row->resolve_date)
                                                            {{ \Carbon\Carbon::parse($row->resolve_date)->format('d-m-y') }}
                                                        @endif
                                                    </td>
                                                    
                                                    <td>{{$row->resolve_remarks}}</td>
                                                    
                                                    @if ($row->clear==0)
                                                        <td> <i class="fas fa-circle" style="color:red;font-size:10px"></i> Open </td>
                                                    @elseif ($row->clear==1)
                                                        <td> <i class="fas fa-circle" style="color:green;font-size:10px"></i> Closed </td>
                                                    @endif
                                                    
                                                    <td><a class="mb-1 mt-1 me-1 modal-with-zoom-anim ws-normal" onclick="getAttachements({{$row->id}})" href="#attModal">View Att.</a></td>
                                                    <td class="actions">
                                                        <a class="text-danger" href="{{route('print-complain',$row->id)}}" target="_blank">
                                                          <i class="fas fa-print"></i>
                                                        </a>

                                                        <span class="separator"> | </span>

                                                        <a class="mb-1 mt-1 me-1 modal-with-zoom-anim ws-normal" onclick="getComplainsDetails({{$row->id}})" href="#updateModal">
                                                          <i class="fas fa-pencil-alt"></i>
                                                        </a>
                                                        @if(session('user_role')==1)
                                                        <span class="separator"> | </span>

                                                        <a class="mb-1 mt-1 me-1 modal-with-zoom-anim ws-normal" onclick="setId({{$row->id}})" href="#deleteModal">
                                                            <i class="far fa-trash-alt" style="color:red"></i>
                                                        </a>
                                                        @endif
                                                    </td>

                                                </tr>
                                            @endforeach
                                        </tbody>
									</table>
                                </div>
                            </section>
                        </div>
                    </div>
                </section>		
			</div>
		</section>

        <div id="deleteModal" class="zoom-anim-dialog modal-block modal-block-danger mfp-hide">
            <form method="post" action="{{ route('delete-complains') }}" enctype="multipart/form-data">
                @csrf
                <section class="card">
                    <header class="card-header">
                        <h2 class="card-title">Delete Complain</h2>
                    </header>
                    <div class="card-body">
                        <div class="modal-wrapper">
                            <div class="modal-icon">
                                <i class="fas fa-question-circle"></i>
                            </div>
                            <div class="modal-text">
                                <p class="mb-0">Are you sure that you want to delete this complain?</p>
                                <input name="complain_id" id="deleteID" hidden>
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
                <form method="post" action="{{ route('store-complains') }}" enctype="multipart/form-data" onkeydown="return event.key != 'Enter';">
                    @csrf
                    <header class="card-header">
                        <h2 class="card-title">New Complaint</h2>
                    </header>
                    <div class="card-body">
                        <div class="row form-group">
                            <div class="col-lg-6 mb-2">
                                <label for="complaint_id">ID</label>
                                <input type="number" id="complaint_id" class="form-control" placeholder="NEW ID" required disabled>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label for="complain_date">Complaint Date<span style="color: red;"><strong>*</strong></span></label>
                                <input type="date" id="complain_date" class="form-control" placeholder="Date" name="inv_dat" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label for="mfi_purchase_number">MFI Purchase Number</label>
                                <input type="text" id="mfi_purchase_number" class="form-control" placeholder="MFI Purchase Number" name="mfi_pur_number">
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label for="mill_purchase_number">Mill Purchase Number</label>
                                <input type="text" id="mill_purchase_number" class="form-control" placeholder="Mill Purchase Number" name="mill_pur_number">
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label for="company_name">Company Name<span style="color: red;"><strong>*</strong></span></label>
                                <select id="company_name" data-plugin-selecttwo class="form-control select2-js" name="company_name" required>
                                    <option value="" disabled selected>Select Company Name</option>
                                    @foreach($acc as $key => $row)    
                                        <option value="{{$row->ac_code}}">{{$row->ac_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label for="party_name">Party Name<span style="color: red;"><strong>*</strong></span></label>
                                <select id="party_name" data-plugin-selecttwo class="form-control select2-js" name="party_name" required>
                                    <option value="" disabled selected>Select Party Name</option>
                                    @foreach($acc as $key => $row)    
                                        <option value="{{$row->ac_code}}">{{$row->ac_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label for="complain_detail">Complain Details</label>
                                <textarea id="complain_detail" rows="4" class="form-control cust-textarea" placeholder="Complain Details" name="complain_detail"></textarea>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label for="resolve_remarks">Resolve Remarks</label>
                                <textarea id="resolve_remarks" rows="4" class="form-control cust-textarea" placeholder="Resolve Remarks" name="resolve_remarks" disabled></textarea>
                            </div>
                            
                            <div class="col-lg-6 mb-2">
                                <label for="resolve_date">Resolve Date</label>
                                <input type="date" id="resolve_date" class="form-control" placeholder="Resolve Date" name="resolve_date" disabled>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label for="complain_status">Complaint Status<span style="color: red;"><strong>*</strong></span></label>
                                <select id="complain_status" data-plugin-selecttwo class="form-control select2-js" name="clear" required disabled>
                                    <option value="" disabled>Select Status</option>
                                    <option value="0" selected>Open</option>
                                    <option value="1">Closed</option>
                                </select>
                            </div>
                            <div class="col-lg-12 mb-2">
                                <label for="attachments">Attachments</label>
                                <input type="file" id="attachments" class="form-control" name="att[]" multiple accept=".zip, application/zip, application/pdf, image/png, image/jpeg">
                            </div>
                        </div>
                    </div>
                    <footer class="card-footer">
                        <div class="row">
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-primary">Add Complain</button>
                                <button type="button" class="btn btn-default modal-dismiss">Cancel</button>
                            </div>
                        </div>
                    </footer>
                </form>
            </section>
        </div>
        <div id="updateModal" class="modal-block modal-block-primary mfp-hide">
            <section class="card">
                <form method="post" action="{{ route('update-complains') }}" enctype="multipart/form-data" onkeydown="return event.key != 'Enter';">
                    @csrf
                    <input type="hidden" id="update_complain_id_hidden" name="update_complain_id">
                    <header class="card-header">
                        <h2 class="card-title">Update Complain</h2>
                    </header>
                    <div class="card-body">
                        <div class="row form-group">
                            <div class="col-lg-6">
                                <label>JV1 Code</label>
                                <input type="number" class="form-control" placeholder="ID" id="update_complain_id" required disabled>
                                <input type="hidden" class="form-control" placeholder="ID" name="update_id" id="update_id_view" required>
                            </div>

                            <div class="col-lg-6 mb-2">
                                <label>Complain Date<span style="color: red;"><strong>*</strong></span></label>
                                <input type="date" id="update_complain_date" class="form-control" placeholder="Date" name="update_inv_dat" required>
                            </div>
                            
                            <div class="col-lg-6 mb-2">
                                <label for="update_mfi_purchase_number">MFI Purchase Number</label>
                                <input type="text" id="update_mfi_purchase_number" class="form-control" placeholder="MFI Purchase Number" name="update_mfi_pur_number">
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label for="update_mill_purchase_number">Mill Purchase Number</label>
                                <input type="text" id="update_mill_purchase_number" class="form-control" placeholder="Mill Purchase Number" name="update_mill_pur_number">
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label for="update_company_name">Company Name<span style="color: red;"><strong>*</strong></span></label>
                                <select id="update_company_name" data-plugin-selecttwo class="form-control select2-js"  name="update_company_name" required>
                                    <option value="" disabled>Select Company Name</option>
                                    @foreach($acc as $key => $row)
                                        <option value="{{$row->ac_code}}">{{$row->ac_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label for="update_party_name">Party Name<span style="color: red;"><strong>*</strong></span></label>
                                <select id="update_party_name" data-plugin-selecttwo class="form-control select2-js" name="pupdate_arty_name" required>
                                    <option value="" disabled>Select Party Name</option>
                                    @foreach($acc as $key => $row)
                                        <option value="{{$row->ac_code}}">{{$row->ac_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label for="update_complain_detail">Complain Details</label>
                                <textarea id="update_complain_detail" rows="4" class="form-control cust-textarea" placeholder="Complain Details" name="update_complain_detail"></textarea>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label for="update_resolve_remarks">Resolve Remarks</label>
                                <textarea id="update_resolve_remarks" rows="4" class="form-control cust-textarea" placeholder="Resolve Remarks" name="update_resolve_remarks"></textarea>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label for="update_resolve_date">Resolve Date</label>
                                <input type="date" id="update_resolve_date" class="form-control" placeholder="Resolve Date" name="update_resolve_date">
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label for="update_complain_status">Complain Status<span style="color: red;"><strong>*</strong></span></label>
                                <select id="update_complain_status" data-plugin-selecttwo class="form-control select2-js" name="update_complain_status" required>
                                    <option value="" disabled>Select Status</option>
                                    <option value="0">Open</option>
                                    <option value="1">Closed</option>
                                </select>
                            </div>
                            <div class="col-lg-12 mb-2">
                                <label for="update_attachments">Attachments</label>
                                <input type="file" id="update_attachments" class="form-control" name="att[]" multiple accept=".zip, application/zip, application/pdf, image/png, image/jpeg">
                            </div>
                        </div>
                    </div>
                    <footer class="card-footer">
                        <div class="row">
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-primary">Update Complain</button>
                                <button type="button" class="btn btn-default modal-dismiss">Cancel</button>
                            </div>
                        </div>
                    </footer>
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

                        <table class="table table-bordered table-striped mb-0" id="datatable-default">
                            <thead>
                                <tr>
                                    <th>Attachement Path</th>
                                    <th>Download</th>
                                    <th>View</th>
                                    <th>Delete</th>
                                </tr>
                            </thead>
                            <tbody id="complains_attachements">

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
        
        @include('../layouts.footerlinks')
	</body>
</html>
<script>
    function setId(id){
        $('#deleteID').val(id);
    }

    function getAttachements(id){

        var table = document.getElementById('complains_attachements');
            while (table.rows.length > 0) {
            table.deleteRow(0);
        }

        $.ajax({
            type: "GET",
            url: "/complains/attachements",
            data: {id:id},
            success: function(result){
                $.each(result, function(k,v){
                    var html="<tr>";
                    html+= "<td>"+v['att_path']+"</td>"
                    html+= "<td class='text-center'><a class='mb-1 mt-1 mr-2 me-1 text-danger' href='/complains/download/"+v['att_id']+"'><i class='fas fa-download'></i></a></td>"
                    html+= "<td class='text-center'><a class='mb-1 mt-1 me-1 text-primary' href='/complains/view/"+v['att_id']+"' target='_blank'><i class='fas fa-eye'></i></a></td>"
                    html+= "<td class='text-center'><a class='mb-1 mt-1 me-1 text-primary' href='#' onclick='deleteFile("+v['att_id']+")'><i class='fas fa-trash'></i></a></td>"
                    html+="</tr>";
                    $('#complains_attachements').append(html);
                });
            },
            error: function(){
                alert("error");
            }
        });
    }

    function getComplainsDetails(id){
        $.ajax({
            type: "GET",
            url: "/complains/detail",
            data: {id:id},
        success: function(result) {
            $('#update_complain_id').val(result.id);
            $('#update_id_view').val(result.id);
            $('#update_mfi_purchase_number').val(result.mfi_pur_number);
            $('#update_mill_purchase_number').val(result.mill_pur_number);
            $('#update_company_name').val(result.company_name).trigger('change');
            $('#update_party_name').val(result.party_name).trigger('change');
            $('#update_complain_detail').val(result.complain_detail);
            $('#update_resolve_date').val(result.resolve_date);
            $('#update_resolve_remarks').val(result.resolve_remarks);
            $('#update_complain_status').val(result.clear).trigger('change');
            $('#update_complain_date').val(result.inv_dat);
        },
        error: function(xhr, status, error) {
           
        }
    });
}

function deleteFile(fileId) {
        if (!confirm('Are you sure you want to delete this file?')) {
            return;
        }

        fetch('/complains/deleteAttachment/' + fileId, {
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
</script>