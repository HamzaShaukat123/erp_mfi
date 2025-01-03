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
                                    <h2 class="card-title">Item Groups</h2>
                                    <div class="card-actions">
                                        <button type="button" class="modal-with-form btn btn-primary" href="#addModal"> <i class="fas fa-plus"></i> New Group</button>
                                    </div>
                                </header>
                                <div class="card-body">
                                	<table class="table table-bordered table-striped mb-0" id="datatable-default">
                                        
                                        <thead>
                                            <tr>
                                                <th width="5%">Code</th>
                                                <th>Group Name</th>
                                                <th>Remarks</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($itemGroups as $key => $row)
                                                <tr>
                                                    <td>{{$row->item_group_cod}}</td>
                                                    <td>{{$row->group_name}}</td>
                                                    <td>{{$row->group_remarks}}</td>
                                                    <td class="actions">
                                                       <a class="mb-1 mt-1 me-1 modal-with-zoom-anim ws-normal" onclick="getGroupDetails({{$row->item_group_cod}})" href="#updateModal">
                                                          <i class="fas fa-pencil-alt"></i>
                                                        </a>
                                                        @if(session('user_role')==1)
                                                         <span class="separator"> | </span>
                                                        <a class="mb-1 mt-1 me-1 modal-with-zoom-anim ws-normal" onclick="setId({{$row->item_group_cod}})" href="#deleteModal">
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
            <form method="post" action="{{ route('delete-item-group') }}" enctype="multipart/form-data">
                @csrf
                <section class="card">
                    <header class="card-header">
                        <h2 class="card-title">Delete Group</h2>
                    </header>
                    <div class="card-body">
                        <div class="modal-wrapper">
                            <div class="modal-icon">
                                <i class="fas fa-question-circle"></i>
                            </div>
                            <div class="modal-text">
                                <p class="mb-0">Are you sure that you want to delete this group?</p>
                                <input name="item_group_cod" id="deleteID" hidden>
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
                <form method="post" action="{{ route('store-item-group') }}" enctype="multipart/form-data" onkeydown="return event.key != 'Enter';">
                    @csrf
                    <header class="card-header">
                        <h2 class="card-title">Add Group</h2>
                    </header>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Group Name<span style="color: red;"><strong>*</strong></span></label>
                            <input type="text" class="form-control" placeholder="Name" name="group_name" required>
                        </div>
                        <div class="form-group">
                            <label>Remarks</label>
                            <input type="text" class="form-control" placeholder="Remarks" name="group_remarks">
                        </div>
                    </div>
                    <footer class="card-footer">
                        <div class="row">
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-primary">Add Group</button>
                                <button class="btn btn-default modal-dismiss">Cancel</button>
                            </div>
                        </div>
                    </footer>
                </form>
            </section>
        </div>

        <div id="updateModal" class="modal-block modal-block-primary mfp-hide">
            <section class="card">
                <form method="post" action="{{ route('update-item-group') }}" enctype="multipart/form-data" onkeydown="return event.key != 'Enter';">
                    @csrf
                    <header class="card-header">
                        <h2 class="card-title">Update Group</h2>
                    </header>
                    <div class="card-body">
                       <div class="form-group">
                            <label>Group Code</label>
                            <input type="number" class="form-control" id="group_id" required disabled>
                        </div>
                        <div class="form-group">
                            <label>Group Name<span style="color: red;"><strong>*</strong></span></label>
                            <input type="text" class="form-control" id="update_group_name" placeholder="Name" name="group_name" required>
                            <input type="hidden" class="form-control" id="update_group_id" name="item_group_cod" required>
                        </div>
                        <div class="form-group">
                            <label>Remarks</label>
                            <input type="text" class="form-control" id="update_group_remarks" placeholder="Remarks" name="group_remarks">
                        </div>
                    </div>
                    <footer class="card-footer">
                        <div class="row">
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-primary">Update Group</button>
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
    function setId(id){
        $('#deleteID').val(id);
    }

    function getGroupDetails(groupID){
        $.ajax({
            type: "GET",
            url: "/item-groups/detail",
            data: {id:groupID},
            success: function(result){
                $('#group_id').val(result['item_group_cod']);
                $('#update_group_id').val(result['item_group_cod']);
                $('#update_group_name').val(result['group_name']);
                $('#update_group_remarks').val(result['group_remarks']);
            },
            error: function(){
                alert("error");
            }
        });
	}
</script>