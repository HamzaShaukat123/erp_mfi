@include('../layouts.header')
	<body>
		<section class="body">
        @include('layouts.homepageheader')
			<div class="inner-wrapper cust-pad" >
				@include('layouts.leftmenu')
				<section role="main" class="content-body">
                    <div class="row">
                        <div class="col">
                            <section class="card">
                                <header class="card-header" style="display: flex;justify-content: space-between;">
                                    <h2 class="card-title">All Modules</h2>
                                    <div class="card-actions">
                                        <button type="button" class="modal-with-form btn btn-primary" href="#addModal"> <i class="fas fa-plus"></i> New Module</button>
                                    </div>
                                </header>
                                <div class="card-body">
                                	<table class="table table-bordered table-striped mb-0" id="datatable-default">
                                        
                                        <thead>
                                            <tr>
                                                <th width="5%">ID</th>
                                                <th>Module Name</th>
                                                <th>Slug</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($modules as $key => $row)
                                                <tr>
                                                    <td>{{$row->id}}</td>
                                                    <td>{{$row->name}}</td>
                                                    <td>{{$row->slug}}</td>
                                                    <td class="actions">
                                                       <a class="mb-1 mt-1 me-1 modal-with-zoom-anim ws-normal" onclick="getModuleDetails({{$row->id}})" href="#updateModal">
                                                          <i class="fas fa-pencil-alt"></i>
                                                        </a>
                                                         <!-- <span class="separator"> | </span>
                                                        <a class="mb-1 mt-1 me-1 modal-with-zoom-anim ws-normal" onclick="setId({{$row->id}})" href="#deleteModal">
                                                         <i class="far fa-trash-alt" style="color:red"></i>
                                                        </a> -->
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

        <!-- <div id="deleteModal" class="zoom-anim-dialog modal-block modal-block-danger mfp-hide">
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
        </div> -->

        <div id="addModal" class="modal-block modal-block-primary mfp-hide">
            <section class="card">
                <form method="post" action="{{ route('add-module') }}" enctype="multipart/form-data" onkeydown="return event.key != 'Enter';">
                    @csrf
                    <header class="card-header">
                        <h2 class="card-title">Add Module</h2>
                    </header>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Module Name <span style="color: red;"><strong>*</strong></span></label>
                            <input type="text" class="form-control" placeholder="Name" name="module_name" required>
                        </div>
                        <div class="form-group">
                            <label>Module slug <span style="color: red;"><strong>*</strong></span></label>
                            <input type="text" class="form-control" placeholder="Remarks" name="module_slug">
                        </div>
                    </div>
                    <footer class="card-footer">
                        <div class="row">
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-primary">Add Module</button>
                                <button class="btn btn-default modal-dismiss">Cancel</button>
                            </div>
                        </div>
                    </footer>
                </form>
            </section>
        </div>

        <div id="updateModal" class="modal-block modal-block-primary mfp-hide">
            <section class="card">
                <form method="post" action="{{ route('update-module') }}" enctype="multipart/form-data" onkeydown="return event.key != 'Enter';">
                    @csrf
                    <header class="card-header">
                        <h2 class="card-title">Update Module</h2>
                    </header>
                    <div class="card-body">
                       <div class="form-group">
                            <label>Module ID Code</label>
                            <input type="number" class="form-control" id="module_id" required disabled>
                        </div>
                        <div class="form-group">
                            <label>Module Name<span style="color: red;"><strong>*</strong></span></label>
                            <input type="text" class="form-control" id="update_module_name" placeholder="Name" name="update_module_name" required>
                            <input type="hidden" class="form-control" id="update_module_id" name="update_module_id" required>
                        </div>
                        <div class="form-group">
                            <label>Module Slug</label>
                            <input type="text" class="form-control" id="update_module_slug" placeholder="Slug" name="update_module_slug" required>
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
    // function setId(id){
    //     $('#deleteID').val(id);
    // }

    function getModuleDetails(moduleID){
        $.ajax({
            type: "GET",
            url: "/modules/details",
            data: {id:moduleID},
            success: function(result){
                $('#module_id').val(result['id']);
                $('#update_module_id').val(result['id']);
                $('#update_module_name').val(result['name']);
                $('#update_module_slug').val(result['slug']);
            },
            error: function(){
                alert("error");
            }
        });
	}
</script>