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
                                    <h2 class="card-title mb-2">COA</h2>
                                    <div class="card-actions">
                                        <button type="button" class="modal-with-form btn btn-primary" href="#addModal"> <i class="fas fa-plus">  </i>  New Account</button>
                                        <button type="button" class="btn btn-danger" onclick="printReport()"> <i class="fas fa-print">  </i>  Report</button>
                                    </div>
                                </header>
                                <div class="card-body">
                                    <div>
                                        <div class="col-md-5" style="display:flex;">
                                            <select class="form-control" style="margin-right:10px" id="columnSelect">
                                                <option selected disabled>Search by</option>
                                                <option value="0">by Code</option>
                                                <option value="1">by Account</option>
                                                <option value="2">by Receivable</option>
                                                <option value="3">by Payable</option>
                                                <option value="4">by Date</option>
                                                <option value="5">by Remarks</option>
                                                <option value="6">by Address</option>
                                                <option value="7">by Credit Limit</option>
                                                <option value="8">by Days Limit</option>
                                                <option value="9">by Group</option>
                                                <option value="10">by Account Type</option>
                                            </select>
                                            <input type="text" class="form-control" id="columnSearch" placeholder="Search By Column"/>
                                        </div>
                                    </div>
                                    <div class="modal-wrapper table-scroll">
                                        <table class="table table-bordered table-striped mb-0" id="cust-datatable-default">
                                            <thead>
                                                <tr>
                                                    <th>Code</th>
                                                    <th>Account Name</th>
                                                    <th>Receivable</th>
                                                    <th>Payable</th>
                                                    <th>Date</th>
                                                    <th>Remarks</th>
                                                    <th>Address</th>
                                                    <th>Credit Limit</th>
                                                    <th>Days Limit</th>
                                                    <th>Group</th>
                                                    <th>Account Type</th>
                                                    <th>Att.</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($acc as $key => $row)
                                                    <tr>
                                                        <td>{{$row->ac_code}}</td>
                                                        <td><strong>{{$row->ac_name}}</strong></td>
                                                        @if(substr(strval($row->rec_able), strpos(strval($row->rec_able), '.') + 1) >0)
                                                            <td>{{ rtrim(rtrim(number_format($row->rec_able, 10, '.', ','), '0'), '.') }}</td>
                                                        @else
                                                            <td>{{ number_format(intval($row->rec_able))}}</td>
                                                        @endif
                                                        @if(substr(strval($row->pay_able), strpos(strval($row->pay_able), '.') + 1)>0)
                                                            <td>{{ rtrim(rtrim(number_format($row->pay_able, 10, '.', ','), '0'), '.') }}</td>
                                                        @else
                                                            <td>{{ number_format(intval($row->pay_able))}}</td>
                                                        @endif
                                                        <td>{{ \Carbon\Carbon::parse($row->opp_date)->format('d-m-y') }}</td>
                                                        <td>{{$row->remarks}}</td>
                                                        <td>{{$row->address}}  {{$row->area}} {{$row->city}} {{$row->phone_no}}</td>
                                                        @if(substr(strval($row->credit_limit), strpos(strval($row->credit_limit), '.') + 1) > 0)
                                                            <td style="color: rgb(156, 32, 32);"><strong>{{ rtrim(rtrim(number_format($row->credit_limit, 10, '.', ','), '0'), '.') }}</strong></td>
                                                        @else
                                                            <td style="color: rgb(156, 32, 32);"><strong>{{ number_format(intval($row->credit_limit))}}</strong></td>
                                                        @endif

                                                        <td style="color: rgb(156, 32, 32);"><strong>{{$row->days_limit}}-Days</strong></td>
                                                        <td>{{$row->group_name}}</td>
                                                        <td><strong>{{$row->sub}}</strong></td>
                                                        <td>
                                                            <a class="mb-1 mt-1 me-1 modal-with-zoom-anim ws-normal text-dark" onclick="getAttachements({{$row->ac_code}})" href="#attModal"><i class="fa fa-eye"> </i></a>
                                                            <span class="separator"> | </span>
                                                            <a class="mb-1 mt-1 me-1 modal-with-zoom-anim ws-normal text-danger" onclick="setAttId({{$row->ac_code}})" href="#addAttModal"> <i class="fas fa-paperclip"> </i></a>
                                                        </td>
                                                        @if($row->status==1)
                                                            <td class="actions">
                                                                <a class="mb-1 mt-1 me-1 modal-with-zoom-anim ws-normal text-dark modal-with-form" onclick="getAccountDetails({{$row->ac_code}})" href="#updateModal"><i class="fas fa-pencil-alt"></i></a>
                                                                @if(session('user_role')==1)
                                                                <span class="separator"> | </span>
                                                                <a class="mb-1 mt-1 me-1 modal-with-zoom-anim ws-normal text-danger" onclick="setId({{$row->ac_code}})" href="#deleteModal"><i class="fa fa-times" style="color:red"></i></a>
                                                                @endif
                                                            </td>
                                                        @else
                                                            <td><a href="{{ route('activate-acc',$row->ac_code)}}"><i style="color:green" class="fas fa-check"></i></a></td>
                                                        @endif
                                                    
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

        <div id="deleteModal" class="zoom-anim-dialog modal-block modal-block-danger mfp-hide">
            <form method="post" action="{{ route('delete-acc') }}" enctype="multipart/form-data">
                @csrf
                <section class="card">
                    <header class="card-header">
                        <h2 class="card-title">Delete Account</h2>
                    </header>
                    <div class="card-body">
                        <div class="modal-wrapper">
                            <div class="modal-icon">
                                <i class="fas fa-question-circle"></i>
                            </div>
                            <div class="modal-text">
                                <p class="mb-0">Are you sure that you want to delete this account?</p>
                                <input name="acc_id" id="deleteID" hidden>
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
                                <tbody id="acc_attachements">

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
            <form method="post" action="{{ route('coa-att-add') }}" enctype="multipart/form-data" onkeydown="return event.key != 'Enter';">
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

        <div id="printModal" class="zoom-anim-dialog modal-block modal-block-danger mfp-hide">
            <form method="post" action="{{ route('print-acc') }}" enctype="multipart/form-data">
                @csrf
                <section class="card">
                    <header class="card-header">
                        <h2 class="card-title">Print COA Report Account</h2>
                    </header>
                    <div class="card-body">
                        <div class="row form-group">
                            <div class="col-lg-6">
                                <label>From Date:</label>
                                <input type="date" class="form-control" name="print_from_date" value="<?php echo date('Y-m-d',strtotime("yesterday")); ?>">
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>To Date:</label>
                                <input type="date" class="form-control" name="print_to_date" value="<?php echo date('Y-m-d'); ?>">
                            </div>
                        </div>
                    </div>
                    <footer class="card-footer">
                        <div class="row">
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-danger">Print</button>
                                <button class="btn btn-default modal-dismiss">Cancel</button>
                            </div>
                        </div>
                    </footer>
                </section>
            </form>
        </div>

        <div id="addModal" class="modal-block modal-block-primary mfp-hide">
            <section class="card">
                <form method="post" id="addForm" action="{{ route('store-acc') }}" enctype="multipart/form-data" onkeydown="return event.key != 'Enter';">
                    @csrf
                    <header class="card-header">
                        <h2 class="card-title">Add New Account</h2>
                    </header>
                    <div class="card-body">
                        <div class="row form-group">
                            <div class="col-lg-6">
                                <label>Account Code</label>
                                <input type="number" class="form-control" placeholder="Account Code" name="ac_cod" required disabled>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Account Name<span style="color: red;"><strong>*</strong></span></label>
                                <input type="text" class="form-control" placeholder="Account Name" name="ac_name" required>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Receivables<span style="color: red;"><strong>*</strong></span></label>
                                <input type="number" class="form-control" placeholder="Receivables" value="0" name="rec_able" step=".00001" required>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Payables<span style="color: red;"><strong>*</strong></span></label>
                                <input type="number" class="form-control" placeholder="Payables" value="0" name="pay_able" step=".00001" required>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Date</label>
                                <input type="date" class="form-control" placeholder="Date" name="opp_date" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>  
                            <div class="col-lg-6 mb-2">
                                <label>Remarks</label>
                                <input type="text" class="form-control"  placeholder="Remarks" name="remarks" >
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Area </label>
                                <select data-plugin-selecttwo class="form-control select2-js"  name ="area">
                                    <option value="">Select Area</option>
                                    @foreach($ac_area as $key => $row)	
                                        <option value="{{$row->id}}">{{$row->area}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>City </label>
                                <select data-plugin-selecttwo class="form-control select2-js"  name ="city">
                                    <option value="">Select City</option>
                                    @foreach($ac_city as $key => $row)	
                                        <option value="{{$row->id}}">{{$row->city}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Address</label>
                                <textarea type="text" class="form-control" rows="2" placeholder="Address" name="address"></textarea>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Phone No.</label>
                                <input type="text" class="form-control"  placeholder="Phone No." name="phone_no" >
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Credit Limit<span style="color: red;"><strong>*</strong></span></label>
                                <input type="text" class="form-control"  placeholder="Credit Limit" value="0" name="credit_limit" required >
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Credit Days<span style="color: red;"><strong>*</strong></span></label>
                                <input type="text" class="form-control"  placeholder="Credit Days" value="0" name="days_limit" required >
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Account Group </label>
                                <select data-plugin-selecttwo class="form-control select2-js"  name ="group_cod">
                                    <option value="">Select Group</option>
                                    @foreach($ac_group as $key => $row)	
                                        <option value="{{$row->group_cod}}">{{$row->group_name}}</option>
                                    @endforeach
                                </select>
                                <a href="{{ route('all-acc-groups') }}">Add New A.Group</a>
                            </div>

                            <div class="col-lg-6 mb-2">
                                <label>Account Type<span style="color: red;"><strong>*</strong></span></label>
                                <select data-plugin-selecttwo class="form-control select2-js"  name="AccountType" required>
                                    <option value="" disabled selected>Select Account Type</option>
                                    @foreach($sub_head_of_acc as $key => $row)	
                                        <option value="{{$row->id}}">{{$row->sub}}</option>
                                    @endforeach
                                </select>
                                <a href="{{ route('all-acc-sub-heads-groups') }}">Add New A.Type</a>
                            </div>

                            <div class="col-lg-12 mb-2">
                                <label>Attachement</label>
                                <input type="file" class="form-control" name="att[]" multiple accept="application/pdf, image/png, image/jpeg">
                            </div>
  
                        </div>
                    </div>
                    <footer class="card-footer">
                        <div class="row">
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-primary">Add New Account</button>
                                <button class="btn btn-default modal-dismiss">Cancel</button>
                            </div>
                        </div>
                    </footer>
                </form>
            </section>
        </div>

        <div id="updateModal" class="modal-block modal-block-primary mfp-hide">
            <section class="card">
                <form method="post" id="updateForm" action="{{ route('update-acc') }}" enctype="multipart/form-data" onkeydown="return event.key != 'Enter';">
                    @csrf
                    <header class="card-header">
                        <h2 class="card-title">Update Account</h2>
                    </header>
                    <div class="card-body">
                        <div class="row form-group">
                            <div class="col-lg-6">
                                <label>Account Code</label>
                                <input type="number" class="form-control" placeholder="Account Code" id="ac_id" required disabled>
                                <input type="number" class="form-control"  name="ac_cod" id="update_ac_id" required hidden>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Account Name<span style="color: red;"><strong>*</strong></span></label>
                                <input type="text" class="form-control" placeholder="Account Name"  name="ac_name" id="update_ac_name" required>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Receivables<span style="color: red;"><strong>*</strong></span></label>
                                <input type="number" class="form-control" placeholder="Receivables" value="0" required name="rec_able" id="update_rec_able" step=".00001">
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Payables<span style="color: red;"><strong>*</strong></span></label>
                                <input type="number" class="form-control" placeholder="Payables" value="0" name="pay_able" required id="update_pay_able" step=".00001">
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Date</label>
                                <input type="date" class="form-control" placeholder="Date" name="opp_date" id="update_opp_date">
                            </div>  
                            <div class="col-lg-6 mb-2">
                                <label>Remarks</label>
                                <input type="text" class="form-control"  placeholder="Remarks" name="remarks" id="update_remarks">
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Area</label>
                                <select data-plugin-selecttwo class="form-control select2-js"  name="area" id="update_area">
                                    <option value="">Select Area</option>
                                    @foreach($ac_area as $key => $row)	
                                        <option value="{{$row->id}}">{{$row->area}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>City</label>
                                <select data-plugin-selecttwo class="form-control select2-js"  name="city" id="update_city">
                                    <option value="">Select City</option>
                                    @foreach($ac_city as $key => $row)	
                                        <option value="{{$row->id}}">{{$row->city}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Address</label>
                                <textarea type="text" class="form-control" rows="2" placeholder="Address" name="address" id="update_address"></textarea>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Phone No.</label>
                                <input type="text" class="form-control"  placeholder="Phone No." name="phone_no" id="update_phone_no">
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Credit Limit<span style="color: red;"><strong>*</strong></span></label>
                                <input type="text" class="form-control"  placeholder="Credit Limit." required name="credit_limit" id="update_credit_limit">
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Days Limit<span style="color: red;"><strong>*</strong></span></label>
                                <input type="text" class="form-control"  placeholder="Days Limit" required name="days_limit" id="update_days_limit">
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Account Group</label>
                                <select data-plugin-selecttwo class="form-control select2-js"  name="group_cod" id="update_group_cod">
                                    <option value="">Select Group</option>
                                    @foreach($ac_group as $key => $row)	
                                        <option value="{{$row->group_cod}}">{{$row->group_name}}</option>
                                    @endforeach
                                </select>
                                <a href="{{ route('all-acc-groups') }}">Add New A.Group</a>
                            </div>

                            <div class="col-lg-6 mb-2">
                                <label>Account Type<span style="color: red;"><strong>*</strong></span></label>
                                <select data-plugin-selecttwo class="form-control select2-js"  name="AccountType" required id="update_AccountType">
                                    <option disabled selected>Select Account Type</option>
                                    @foreach($sub_head_of_acc as $key => $row)	
                                        <option value="{{$row->id}}">{{$row->sub}}</option>
                                    @endforeach
                                </select>
                                <a href="{{ route('all-acc-sub-heads-groups') }}">Add New A.Type</a>
                            </div>

                            <div class="col-lg-6 mb-2">
                                <label>Attachements</label>
                                <input type="file" class="form-control" name="att[]" id="update_att" multiple accept="application/pdf, image/png, image/jpeg">
                            </div>
                        </div>
                    </div>
                    <footer class="card-footer">
                        <div class="row">
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-primary">Update Account</button>
                                <button class="btn btn-default modal-dismiss">Cancel</button>
                            </div>
                        </div>
                    </footer>
                </form>
            </section>
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

        $('#addForm').on('submit', function(e){
            e.preventDefault();

            var formData = $(this).serialize();

            $.ajax({
                type: 'POST',
                url: '/coa/acc/validate',
                data: formData,
                success: function(response){
                    var form = document.getElementById('addForm');
                    form.submit();
                },
                error: function(response){
                    var errors = response.responseJSON.errors;
                    var errorMessage = 'Account Already Exists';

                    alert(errorMessage);
                }
            });
        });

    });

    function setId(id){
        $('#deleteID').val(id);
    }

    function setAttId(id){
        $('#att_id').val(id);
    }

    function getAccountDetails(id){
        $.ajax({
            type: "GET",
            url: "/coa/detail",
            data: {id:id},
            success: function(result){
                $('#ac_id').val(result['ac_code']);
                $('#update_ac_id').val(result['ac_code']);
                $('#update_ac_name').val(result['ac_name']);
                $('#update_rec_able').val(result['rec_able']);
                $('#update_pay_able').val(result['pay_able']);
                $('#update_opp_date').val(result['opp_date']);
                $('#update_remarks').val(result['remarks']);
                $('#update_area').val(result['area']).trigger('change');
                $('#update_city').val(result['city']).trigger('change');
                $('#update_address').val(result['address']);
                $('#update_phone_no').val(result['phone_no']);
                $('#update_credit_limit').val(result['credit_limit']);
                $('#update_days_limit').val(result['days_limit']);
                $('#update_group_cod').val(result['group_cod']).trigger('change');
                $('#update_AccountType').val(result['AccountType']).trigger('change');
                $('#update_att').val(result['att']);
            },
            error: function(){
                alert("error");
            }
        });
	}

    function getAttachements(id){

        var table = document.getElementById('acc_attachements');
        while (table.rows.length > 0) {
            table.deleteRow(0);
        }

        $.ajax({
            type: "GET",
            url: "/coa/attachements",
            data: {id:id},
            success: function(result){
                $.each(result, function(k,v){
                    var html="<tr>";
                    html+= "<td>"+v['att_path']+"</td>"
                    html+= "<td class='text-center'><a class='mb-1 mt-1 me-1 text-danger' href='/coa/download/"+v['att_id']+"'><i class='fas fa-download'></i></a></td>"
                    html+= "<td class='text-center'><a class='mb-1 mt-1 me-1 text-primary' href='/coa/view/"+v['att_id']+"' target='_blank'><i class='fas fa-eye'></i></a></td>"
                    html+= "<td class='text-center'><a class='mb-1 mt-1 me-1 text-primary' href='#' onclick='deleteFile("+v['att_id']+")'><i class='fas fa-trash'></i></a></td>"
                    html+="</tr>";
                    $('#acc_attachements').append(html);
                });
                $('#download_id').val(result[0]['ac_code']);
            },
            error: function(){
                alert("error");
            }
        });
	}

    function printReport(){
        window.location.href = "{{ route('print-acc')}}";
    }

    function deleteFile(fileId) {
        if (!confirm('Are you sure you want to delete this file?')) {
            return;
        }

        fetch('/coa/deleteAtt/' + fileId, {
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
                alert("hello");
                
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