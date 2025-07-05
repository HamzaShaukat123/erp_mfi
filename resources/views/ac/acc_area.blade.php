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
                                    <h2 class="card-title">COA Area</h2>
                                    <div class="card-actions">
                                        <button type="button" class="modal-with-form btn btn-primary" href="#addModal"> <i class="fas fa-plus"></i> New Area</button>
                                    </div>
                                </header>
                                <div class="card-body">
                                	<table class="table table-bordered table-striped mb-0" id="datatable-default">
                                        <thead>
                                            <tr>
                                                <th width="5%">Code</th>
                                                <th>Area Name</th>
                                                <th class="text-end">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($accArea as $key => $row)
                                                <tr>
                                                    <td>{{$row->id}}</td>
                                                    <td>{{$row->area}}</td>
                                                    <td class="actions text-end">
                                                        <a class="mb-1 mt-1 me-1 modal-with-zoom-anim ws-normal" onclick="getDetails({{$row->id}})" href="#updateModal"><i class="fas fa-pencil-alt"></i></a>
                                                        @if(session('user_role')==1)
                                                        <span class="separator"> | </span>
                                                        <a class="mb-1 mt-1 me-1 modal-with-zoom-anim ws-normal" onclick="setId({{$row->id}})" href="#deleteModal"><i class="far fa-trash-alt" style="color:red"></i></a>
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
            
            <form method="post" action="{{ route('delete-acc-area') }}" enctype="multipart/form-data">
                @csrf
                <section class="card">
                    <header class="card-header">
                        <h2 class="card-title">Delete Area Name</h2>
                    </header>
                    <div class="card-body">
                        <div class="modal-wrapper">
                            <div class="modal-icon">
                                <i class="fas fa-question-circle"></i>
                            </div>
                            <div class="modal-text">
                                <p class="mb-0">Are you sure that you want to delete this Area Name?</p>
                                <input name="area_cod" id="deleteID" hidden>
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
                <form method="post" action="{{ route('store-acc-area') }}" enctype="multipart/form-data" onkeydown="return event.key != 'Enter';">
                    @csrf
                    <header class="card-header">
                        <h2 class="card-title">Add New Area</h2>
                    </header>
                    <div class="card-body">
                        <div class="form-group mt-2">
                            <input type="number" class="form-control" placeholder="Area code" disabled>
                        </div>
                        <div class="form-group mb-3">
                            <label>Area Name<span style="color: red;"><strong>*</strong></span></label>
                            <input type="text" class="form-control" placeholder="Name" name="acc_area_name" required>
                        </div>
                    </div>
                    <footer class="card-footer">
                        <div class="row">
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-primary">Add Area</button>
                                <button class="btn btn-default modal-dismiss">Cancel</button>
                            </div>
                        </div>
                    </footer>
                </form>
            </section>
        </div>

        <div id="updateModal" class="modal-block modal-block-primary mfp-hide">
            <section class="card">
                <form method="post" action="{{ route('update-acc-area') }}" enctype="multipart/form-data" onkeydown="return event.key != 'Enter';">
                    @csrf
                    <header class="card-header">
                        <h2 class="card-title">Update Area Name</h2>
                    </header>
                    <div class="card-body">
                        <div class="form-group">
                            <input type="number" class="form-control" id="update_area_id" required disabled>
                        </div>
                        <div class="form-group">
                            <label>Area Name<span style="color: red;"><strong>*</strong></span></label>
                            <input type="text" class="form-control" id="update_area_name" placeholder="Name" name="area_name" required>
                            <input type="hidden" class="form-control" id="area_id" name="area_cod" required>
                        </div>
                    </div>
                    <footer class="card-footer">
                        <div class="row">
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-primary">Update Area</button>
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

    function getDetails(id){
        $.ajax({
            type: "GET",
            url: "/coa-area/detail", // Change to area endpoint
            data: {id:id},
            success: function(result){
                $('#area_id').val(result.id);
                $('#update_area_id').val(result.id);
                $('#update_area_name').val(result.area);
            },
            error: function(){
                alert("Error fetching area details.");
            }
        });
    }

</script>