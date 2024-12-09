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
                                    <h2 class="card-title">COA Sub Heads</h2>
                                    <div class="card-actions">
                                        <button type="button" class="modal-with-form btn btn-primary" href="#addModal"> <i class="fas fa-plus"></i> New Sub Head</button>
                                    </div>
                                </header>
                                <div class="card-body">
                                	<table class="table table-bordered table-striped mb-0" id="datatable-default">
                                        <thead>
                                            <tr>
                                                <th width="5%">ID</th>
                                                <th>Sub Head Name</th>
                                                <th width="15%">Head Name</th>
                                                <th class="text-end">Action</th>
                                                
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($subheads as $key => $row)
                                                <tr>
                                                    <td>{{$row->id}}</td>
                                                    <td>{{$row->sub_name}}</td>
                                                    <td>{{$row->name}}</td>
                                                    <td class="actions text-end">
                                                        <a class="mb-1 mt-1 me-1  modal-with-zoom-anim ws-normal" onclick="getCOASubHeadDetails({{$row->id}})" href="#updateModal">
                                                            <i class="fas fa-pencil-alt"></i>
                                                        </a>
                                                        @if(session('user_role')==1)
                                                        <span class="separator"> | </span>
                                                        <a class="mb-1 mt-1 me-1  modal-with-zoom-anim ws-normal" onclick="setId({{$row->id}})" href="#deleteModal">
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
            <form method="post" action="{{ route('delete-acc-sub-heads-groups') }}" enctype="multipart/form-data">
                @csrf
                <section class="card">
                    <header class="card-header">
                        <h2 class="card-title">Delete Sub Head</h2>
                    </header>
                    <div class="card-body">
                        <div class="modal-wrapper">
                            <div class="modal-icon">
                                <i class="fas fa-question-circle"></i>
                            </div>
                            <div class="modal-text">
                                <p class="mb-0">Are you sure that you want to delete this Sub Head?</p>
                                <input name="sub_head_id" id="deleteID" hidden>
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
                <form method="post" action="{{ route('store-acc-sub-heads-groups') }}" enctype="multipart/form-data" onkeydown="return event.key != 'Enter';">
                    @csrf
                    <header class="card-header">
                        <h2 class="card-title">Add COA Sub Head</h2>
                    </header>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Select Head<span style="color: red;"><strong>*</strong></span></label>
                            <select data-plugin-selecttwo class="form-control select2-js" name ="main" required>
                                <option value="" selected disabled>Select Group</option>
                                @foreach($heads as $key => $row)	
                                    <option value="{{$row->id}}">{{$row->heads}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label>Sub Head Name<span style="color: red;"><strong>*</strong></span></label>
                            <input type="text" class="form-control" placeholder="Sub Head Name" name="sub" required>
                        </div>
                    </div>
                    <footer class="card-footer">
                        <div class="row">
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-primary">Add COA Sub Head</button>
                                <button class="btn btn-default modal-dismiss">Cancel</button>
                            </div>
                        </div>
                    </footer>
                </form>
            </section>
        </div>

        <div id="updateModal" class="modal-block modal-block-primary mfp-hide">
            <section class="card">
                <form method="post" action="{{ route('update-acc-sub-heads-groups') }}" enctype="multipart/form-data" onkeydown="return event.key != 'Enter';">
                    @csrf
                    <header class="card-header">
                        <h2 class="card-title">Update COA Sub Head</h2>
                    </header>
                    <div class="card-body">
                       <div class="form-group">
                            <label>ID</label>
                            <input type="number" class="form-control" id="sub_head_id" name="id" required disabled>
                        </div>
                        <div class="form-group">
                            <label>Select Head<span style="color: red;"><strong>*</strong></span></label>
                            <select data-plugin-selecttwo class="form-control select2-js" name="main" required id="update_head_name">
                                <option disabled selected>Select Group</option>
                                @foreach($heads as $key => $row)	
                                    <option value="{{$row->id}}">{{$row->heads}}</option>
                                @endforeach
                            </select>
                            <input type="hidden" class="form-control" id="update_sub_head_id" name="sub_head_id" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>Sub Head Name<span style="color: red;"><strong>*</strong></span></label>
                            <input type="text" class="form-control" id="update_sub_head_name" placeholder="Remarks" name="sub" required>
                        </div>
                    </div>
                    <footer class="card-footer">
                        <div class="row">
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-primary">Update COA Sub Head</button>
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

    function getCOASubHeadDetails(id){
        $.ajax({
            type: "GET",
            url: "/coa-sub-heads/detail",
            data: {id:id},
            success: function(result){
                $('#sub_head_id').val(result['id']);
                $('#update_sub_head_id').val(result['id']);
                $('#update_head_name').val(result['main']).trigger('change');
                $('#update_sub_head_name').val(result['sub']);
            },
            error: function(){
                alert("error");
            }
        });
	}
</script>