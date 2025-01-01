@include('../layouts.header')
    <style>
        /* Circle styles */
        .status-circle {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px; /* Spacing between circle and username */
        }

        /* Green circle for active/logged-in users */
        .green-circle {
            background-color: green;
        }

        /* Gray circle for inactive/not logged-in users */
        .gray-circle {
            background-color: lightgray;
        }
    </style>
	<body>
		<section class="body">
        @include('layouts.homepageheader')
			<div class="inner-wrapper cust-pad">
				@include('layouts.leftmenu')
				<section role="main" class="content-body">
                    <div class="row">
                        <div class="col">
                            <section class="card">
                                <header class="card-header" style="display:flex;justify-content:space-between">
                                    <h2 class="card-title mb-2">All Users</h2>
                                    <div class="card-actions">
                                        <button type="button" class="modal-with-form btn btn-danger" href="#RegDevices" onclick="getRegDevices()"> <i class="fa fa-desktop">  </i>  Registered Devices</button>
                                        <button type="button" class="modal-with-form btn btn-primary" href="#addModal"> <i class="fas fa-plus">  </i>  New User</button>
                                    </div>
                                </header>
                                <div class="card-body">
                                    <div class="modal-wrapper">
                                        <table class="table table-bordered table-striped mb-0" id="datatable-default">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Employee Name</th>
                                                    <th>Username</th>
                                                    <th>Role</th>
                                                    <th>CNIC</th>
                                                    <th>Address</th>
                                                    <th>Phone No.</th>
                                                    <th>Email</th>
                                                    <th>Doc.</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($users as $key => $row)
                                                    <tr>
                                                        <td>{{$row->id}}</td>
                                                        @if($row->is_login==0)
                                                            <td>{{$row->name}} <span class="status-circle gray-circle"> </span> </td>
                                                        @elseif($row->is_login==1)
                                                            <td>{{$row->name}} <span class="status-circle green-circle"> </span> </td>
                                                        @endif
                                                        <td>{{$row->username}}</td>
                                                        <td>{{$row->role_name}}</td>
                                                        <td>{{$row->cnic_no}}</td>
                                                        <td>{{$row->address}}</td>
                                                        <td>{{$row->phone_no}}</td>
                                                        <td>{{$row->email}}</td>
                                                        <td><a class="mb-1 mt-1 me-1 modal-with-zoom-anim ws-normal" onclick="getAttachements({{$row->id}})" href="#attModal">View</a></td>
                                                        @if($row->status==1)
                                                        <td class="actions">
                                                            <a class="mb-1 mt-1 me-1 modal-with-zoom-anim ws-normal modal-with-form" onclick="getUserDetails({{$row->id}})" href="#updateModal"><i class="fas fa-pencil-alt"></i></a>
                                                            <a class="mb-1 mt-1 me-1 modal-with-zoom-anim ws-normal text-primary" href="#updateCred" onclick="setUserCredID({{$row->id}},'{{$row->username}}')" ><i class="fa fa-user-lock"></i></a>
                                                            <a class="mb-1 mt-1 me-1 modal-with-zoom-anim ws-normal text-danger" href="#deactivateUser" onclick="setDeactivateID({{$row->id}})"><i class="fa fa-times"></i></a>
                                                        </td>
                                                        @elseif($row->status==0)
                                                        <td class="actions">
                                                            <a class="mb-1 mt-1 me-1 modal-with-zoom-anim ws-normal text-success" href="#activateUser" onclick="setActivateID({{$row->id}})"><i class="fa fa-user-check"></i></a>
                                                            <!-- <a class="mb-1 mt-1 me-1 modal-with-zoom-anim ws-normal text-danger" onclick="setId(1)" href="#deleteModal"><i class="fa fa-trash"></i></a> -->
                                                        </td>
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
                        <h2 class="card-title">Deactivate User</h2>
                    </header>
                    <div class="card-body">
                        <div class="modal-wrapper">
                            <div class="modal-icon">
                                <i class="fas fa-question-circle"></i>
                            </div>
                            <div class="modal-text">
                                <p class="mb-0">Are you sure that you want to deactivate this user?</p>
                                <input name="acc_id" id="deleteID" hidden>
                            </div>
                        </div>
                    </div>
                    <footer class="card-footer">
                        <div class="row">
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-danger">Deactivate</button>
                                <button class="btn btn-default modal-dismiss">Cancel</button>
                            </div>
                        </div>
                    </footer>
                </section>
            </form>
        </div>
        
        <div id="deactivateUser" class="zoom-anim-dialog modal-block modal-block-danger mfp-hide">
            <form method="post" action="{{ route('deactivate-user') }}" enctype="multipart/form-data">
                @csrf
                <section class="card">
                    <header class="card-header">
                        <h2 class="card-title">Deactivate User</h2>
                    </header>
                    <div class="card-body">
                        <div class="modal-wrapper">
                            <div class="modal-icon">
                                <i class="fas fa-question-circle"></i>
                            </div>
                            <div class="modal-text">
                                <p class="mb-0">Are you sure that you want to deactivate this user?</p>
                                <input type="hidden" name="deactivate_user" id="deactivate_user" >
                            </div>
                        </div>
                    </div>
                    <footer class="card-footer">
                        <div class="row">
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-danger">Deactivate</button>
                                <button class="btn btn-default modal-dismiss">Cancel</button>
                            </div>
                        </div>
                    </footer>
                </section>
            </form>
        </div>

        <div id="activateUser" class="zoom-anim-dialog modal-block modal-block-primary mfp-hide">
            <form method="post" action="{{ route('activate-user') }}" enctype="multipart/form-data">
                @csrf
                <section class="card">
                    <header class="card-header">
                        <h2 class="card-title">Activate User</h2>
                    </header>
                    <div class="card-body">
                        <div class="modal-wrapper">
                            <div class="modal-icon">
                                <i class="fas fa-question-circle"></i>
                            </div>
                            <div class="modal-text">
                                <p class="mb-0">Are you sure that you want to Activate this user?</p>
                                <input type="hidden" name="activate_user" id="activate_user" >
                            </div>
                        </div>
                    </div>
                    <footer class="card-footer">
                        <div class="row">
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-primary">Activate</button>
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

                        <table class="table table-bordered table-striped mb-0" id="datatable-default">
                            <thead>
                                <tr>
                                    <th>Name</th>
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
                            <button class="btn btn-default modal-dismiss">Close</button>
                        </div>
                    </div>
                </footer>
            </section>
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
                <form method="post" id="addForm" action="{{ route('new-user') }}" enctype="multipart/form-data" onkeydown="return event.key != 'Enter';">
                    @csrf
                    <header class="card-header">
                        <h2 class="card-title">Add New User</h2>
                    </header>
                    <div class="card-body">
                        <div class="row form-group">
                            <div class="col-lg-6">
                                <label>Employee ID</label>
                                <input type="number" class="form-control" placeholder="User ID" name="user_id" required disabled>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Date</label>
                                <input type="date" class="form-control" placeholder="Date" name="date" value="<?php echo date('Y-m-d'); ?>">
                            </div> 
                            <div class="col-lg-6 mb-2">
                                <label>Employee Name</label>
                                <input type="text" class="form-control" placeholder="Employee Name"  name="name" required>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Phone No.</label>
                                <input type="text" class="form-control"  placeholder="Phone No." name="phone_no"  >
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Email Address</label>
                                <input type="email" class="form-control" placeholder="Email Name" name="email" >
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>CNIC No.</label>
                                <input type="text" class="form-control" placeholder="CNIC No." name="cnic_no" >
                            </div>
                            <div class="col-lg-12 mb-2">
                                <label>Address</label>
                                <textarea type="text" class="form-control" rows="2" placeholder="Address" name="address"></textarea>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Picture</label>
                                <input type="file" class="form-control" name="att" accept="image/png, image/jpeg">
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>CNIC Front</label>
                                <input type="file" class="form-control" name="cnic_front" accept="image/png, image/jpeg">
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>CNIC Back</label>
                                <input type="file" class="form-control" name="cnic_back" accept="image/png, image/jpeg">
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Role</label>
                                <select class="form-control" name="role_id" required>
                                    <option value="" selected disabled>Select User Role</option>
                                    @foreach ($roles as $key => $row)
                                        <option value="{{$row->id}}">{{$row->name}}</option>
                                   @endforeach
                                </select>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Login Username</label>
                                <input type="text" class="form-control" placeholder="@username" name="username">
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Login Password</label>
                                <input type="password" class="form-control" placeholder="password" name="password">
                            </div>
                        </div>
                        <div id="validationError"></div>
                    </div>
                    <footer class="card-footer">
                        <div class="row">
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-primary">Add User</button>
                                <button class="btn btn-default modal-dismiss">Cancel</button>
                            </div>
                        </div>
                    </footer>
                </form>
            </section>
        </div>

        <div id="updateModal" class="modal-block modal-block-primary mfp-hide">
            <section class="card">
                <form method="post" id="updateForm" action="{{ route('update-user') }}" enctype="multipart/form-data" onkeydown="return event.key != 'Enter';">
                    @csrf
                    <header class="card-header">
                        <h2 class="card-title">Update User</h2>
                    </header>
                    <div class="card-body">
                        <div class="row form-group">
                            <div class="col-lg-6">
                                <label>Employee ID</label>
                                <input type="number" class="form-control" placeholder="User ID" id="update_user_id" required disabled>
                                <input type="hidden" class="form-control" name="update_user_id" id="show_update_user_id">
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Date</label>
                                <input type="date" class="form-control" placeholder="Date" name="update_date" id="update_date">
                            </div> 
                            <div class="col-lg-6 mb-2">
                                <label>Employee Name</label>
                                <input type="text" class="form-control" placeholder="Employee Name" id="update_emp_name" name="update_emp_name" required>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Phone No.</label>
                                <input type="text" class="form-control"  placeholder="Phone No." id="update_phone_no" name="update_phone_no"  >
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Email Address</label>
                                <input type="email" class="form-control" placeholder="Email Name" id="update_email_add" name="update_email_add" >
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>CNIC No.</label>
                                <input type="text" class="form-control" placeholder="CNIC No." id="update_cnic_no" name="update_cnic_no" >
                            </div>
                            <div class="col-lg-12 mb-2">
                                <label>Address</label>
                                <textarea type="text" class="form-control" rows="2" id="update_add" placeholder="Address" name="update_add"></textarea>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Picture</label>
                                <input type="file" class="form-control" name="update_att" multiple accept="image/png, image/jpeg">
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>CNIC Front</label>
                                <input type="file" class="form-control" name="update_cnic_front" multiple accept="image/png, image/jpeg">
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>CNIC Back</label>
                                <input type="file" class="form-control" name="update_cnic_back" multiple accept="image/png, image/jpeg">
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Role</label>
                                <select class="form-control" name="update_role" required id="update_role">
                                    <option value="" selected disabled>Select User Role</option>
                                    @foreach ($roles as $key => $row)
                                        <option value={{$row->id}}>{{$row->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <footer class="card-footer">
                        <div class="row">
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-primary">Update User</button>
                                <button class="btn btn-default modal-dismiss">Cancel</button>
                            </div>
                        </div>
                    </footer>
                </form>
            </section>
        </div>

        <div id="updateCred" class="modal-block modal-block-primary mfp-hide">
            <section class="card">
                <form method="post" action="{{ route('change-user-credentials') }}" enctype="multipart/form-data" onkeydown="return event.key != 'Enter';">
                    @csrf
                    <header class="card-header">
                        <h2 class="card-title">Update User Login Details</h2>
                    </header>
                    <div class="card-body">
                        <div class="row form-group">    
                            <div class="col-lg-6 mb-2">
                                <label>Username</label>
                                <input type="text" class="form-control" placeholder="username" id="update_user_username" name="update_user_username" required>
                                <input type="hidden" class="form-control" name="user_cred_id" id="user_cred_id">
                            </div> 
                            <div class="col-lg-6 mb-2">
                                <label>Password</label>
                                <input type="password" class="form-control" placeholder="password" name="update_user_password" required>
                            </div>
                        </div>
                    </div>
                    <footer class="card-footer">
                        <div class="row">
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-primary">Change Details</button>
                                <button class="btn btn-default modal-dismiss">Cancel</button>
                            </div>
                        </div>
                    </footer>
                </form>
            </section>
        </div>

        <div id="RegDevices" class="modal-block modal-block-danger mfp-hide">
            <section class="card">
                <section class="card">
                    <header class="card-header">
                        <h2 class="card-title">All Registered</h2>
                    </header>
                    <div class="card-body">
                        <div class="modal-wrapper">

                            <table class="table table-bordered table-striped mb-0" id="datatable-default">
                                <thead>
                                    <tr>
                                        <th>S No.</th>
                                        <th>User Name</th>
                                        <th>Device Name</th>
                                        <th>Browser</th>
                                        <th>Registration Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="regDevices">

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <footer class="card-footer">
                        <div class="row">
                            <div class="col-md-12 text-end">
                                <button class="btn btn-default modal-dismiss">Close</button>
                            </div>
                        </div>
                    </footer>
                </section>
            </section>
        </div>

        @include('../layouts.footerlinks')
	</body>
</html>

<script>
    
    $(document).ready(function(){
    
        $('#addForm').on('submit', function(e){
            e.preventDefault();

            var formData = $(this).serialize();

            $.ajax({
                type: 'POST',
                url: '/user/create/validate',
                data: formData,
                success: function(response){
                    var form = document.getElementById('addForm');
                    form.submit();
                },
                error: function(response) {
                    if (response.responseJSON.errors) {
                        var errors = response.responseJSON.errors;
                        for (const key in errors) {
                            if (errors.hasOwnProperty(key)) {
                                const messages = errors[key];
                                messages.forEach(message => {
                                    $('#validationError').append(`<span style="color:red">${message}</span><br>`);
                                });
                            }
                        }
                        
                    } else {
                        $('#validationError').html('An unexpected error occurred.');
                    }
                }
            });
        });

    });

    function setId(id){
        $('#deleteID').val(id);
    }

    function setDeactivateID(id){
        $('#deactivate_user').val(id);
    }

    function setUserCredID(id,username){
        $('#user_cred_id').val(id);
        $('#update_user_username').val(username);
    }

    function setActivateID(id){
        $('#activate_user').val(id);
    } 

    function getAccountDetails(id){
        $.ajax({
            type: "GET",
            url: "/coa/acc/detail",
            data: {id:id},
            success: function(result){
                $('#ac_id').val(result['ac_code']);
                $('#update_ac_id').val(result['ac_code']);
                $('#update_ac_name').val(result['ac_name']);
                $('#update_rec_able').val(result['rec_able']);
                $('#update_pay_able').val(result['pay_able']);
                $('#update_opp_date').val(result['opp_date']);
                $('#update_remarks').val(result['remarks']);
                $('#update_address').val(result['address']);
                $('#update_phone_no').val(result['phone_no']);
                $('#update_group_cod').val(result['group_cod']);
                $('#update_AccountType').val(result['AccountType']);
                $('#update_att').val(result['att']);
            },
            error: function(){
                alert("error");
            }
        });
	}

    function getUserDetails(id){
        $.ajax({
            type: "GET",
            url: "/user/details",
            data: {id:id},
            success: function(result){
                $('#update_user_id').val(result['user']['id']);
                $('#show_update_user_id').val(result['user']['id']);
                $('#update_date').val(result['user']['date']);
                $('#update_emp_name').val(result['user']['name']);
                $('#update_phone_no').val(result['user']['phone_no']);
                $('#update_email_add').val(result['user']['email']);
                $('#update_cnic_no').val(result['user']['cnic_no']);
                $('#update_add').val(result['user']['address']);
                $('#update_role').val(result['user_role']['role_id']);
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
            url: "/user/attachements",
            data: {id:id},
            success: function(result){
                $.each(result, function(k,v){                    
                    if(v['picture']!=null){
                        var html="<tr>";
                        html+= "<td>Profile Picture</td>"
                        html+= "<td>"+v['picture']+"</td>"
                        html+= "<td class='text-center'><a class='mb-1 mt-1 me-1 text-danger' href='/user/att/download/"+id+"'><i class='fas fa-download'></i></a></td>"
                        html+= "<td class='text-center'><a class='mb-1 mt-1 me-1 text-primary' href='/user/att/view/"+id+"' target='_blank'><i class='fas fa-eye'></i></a></td>"
                        html+= "<td class='text-center'><a class='mb-1 mt-1 me-1 text-primary' href='#' onclick='deleteFile("+id+")'><i class='fas fa-trash'></i></a></td>"
                        html+="</tr>";
                        $('#acc_attachements').append(html);
                    }
                    if(v['cnic_front']!=null){
                        var html="<tr>";
                        html+= "<td>CNIC Front</td>"
                        html+= "<td>"+v['cnic_front']+"</td>"
                        html+= "<td class='text-center'><a class='mb-1 mt-1 me-1 text-danger' href='/user/att/download/"+id+"'><i class='fas fa-download'></i></a></td>"
                        html+= "<td class='text-center'><a class='mb-1 mt-1 me-1 text-primary' href='/user/att/view/"+id+"' target='_blank'><i class='fas fa-eye'></i></a></td>"
                        html+= "<td class='text-center'><a class='mb-1 mt-1 me-1 text-primary' href='#' onclick='deleteFile("+id+")'><i class='fas fa-trash'></i></a></td>"
                        html+="</tr>";
                        $('#acc_attachements').append(html);
                    }
                    if(v['cnic_back']!=null){
                        var html="<tr>";
                        html+= "<td>CNIC Back</td>"
                        html+= "<td>"+v['cnic_back']+"</td>"
                        html+= "<td class='text-center'><a class='mb-1 mt-1 me-1 text-danger' href='/user/att/download/"+id+"'><i class='fas fa-download'></i></a></td>"
                        html+= "<td class='text-center'><a class='mb-1 mt-1 me-1 text-primary' href='/user/att/view/"+id+"' target='_blank'><i class='fas fa-eye'></i></a></td>"
                        html+= "<td class='text-center'><a class='mb-1 mt-1 me-1 text-primary' href='#' onclick='deleteFile("+id+")'><i class='fas fa-trash'></i></a></td>"
                        html+="</tr>";
                        $('#acc_attachements').append(html);
                    }
                    
                });
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

        fetch('/coa/acc/deleteAtt/' + fileId, {
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

    function deleteDevice(deviceID) {
        if (!confirm('Are you sure you want to delete this device?')) {
            return;
        }

        fetch('/user/del-devices/' + deviceID, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (response.ok) {
                alert('Device deleted successfully.');
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
    
    function getRegDevices(){
        var table = document.getElementById('regDevices');
        while (table.rows.length > 0) {
            table.deleteRow(0);
        }

        $.ajax({
            type: "GET",
            url: "/user/reg-devices",
            success: function(result){
                $.each(result, function(k,v){                    
                    var html="<tr>";
                    html+= "<td>"+(k+1)+"</td>"
                    html+= "<td>"+v['user']+"</td>"
                    html+= "<td>"+v['system_name']+"</td>"
                    html+= "<td>"+v['browser']+"</td>"
                    html+= "<td>"+v['date']+"</td>"
                    html+= "<td class='text-center'><a class='mb-1 mt-1 me-1 text-danger' href='#' onclick='deleteDevice("+v['id']+")'><i class='fas fa-trash'></i></a></td>"
                    html+="</tr>";
                    $('#regDevices').append(html);
                });
            },
            error: function(){
                alert("error");
            }
        });
    }

    
    
</script>