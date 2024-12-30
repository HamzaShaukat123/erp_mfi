@include('../layouts.header')
	<body>
		<section class="body">
        @include('../layouts.pageheader')
            <div class="inner-wrapper cust-pad">
				<section role="main" class="content-body" style="margin:0px">
					<form method="post" action="{{ route('create-role') }}" enctype="multipart/form-data" onkeydown="return event.key != 'Enter';" id="addForm">
						@csrf
						<div class="row">
							<div class="col-12 mb-3">								
								<section class="card">
									<header class="card-header">
										<h2 class="card-title">New User Role</h2>
									</header>

									<div class="card-body">
										<div class="row form-group mb-2">
											<div class="col-sm-12 col-md-4 mb-2">
												<label class="col-form-label" >Role Id</label>
												<input type="text" placeholder="(New Role)" class="form-control" disabled>
												<input type="hidden" value={{$modulesCount}} name="modules_count" class="form-control">

											</div>
											<div class="col-sm-12 col-md-4 mb-2">
												<label class="col-form-label">Role Name</label>
												<input type="text" name="role_name" required class="form-control">
											</div>
											<div class="col-sm-12 col-md-4 mb-2">
												<label class="col-form-label" >Role Shortcode</label>
												<input type="text" placeholder="Shortcode" name="shortcode" class="form-control">
											</div>
									  </div>
									</div>
								</section>
							</div>
							<div class="col-12 mb-3">
								<section class="card">
									<div class="card-body" style="height: 450px;overflow-y: auto;padding:0px !important">
										<table class="table table-bordered table-striped mb-0" id="myTable">
											<thead style="position: sticky;top: 0;background-color: white; ">
												<tr>
													<th>Module Name</th>
													<th>Create (<label for="checkAllcreate0"><input type="checkbox" onclick="checkAll(0, 'create')" id="checkAllcreate0"> All</label>)</th>
													<th>View (<label for="checkAllview0"><input type="checkbox" onclick="checkAll(0, 'view')" id="checkAllview0"> All</label>)</th>
													<th>Update (<label for="checkAllupdate0"><input type="checkbox" onclick="checkAll(0, 'update')" id="checkAllupdate0"> All</label>)</th>
													<th>Delete (<label for="checkAlldelete0"><input type="checkbox" onclick="checkAll(0, 'delete')" id="checkAlldelete0"> All</label>)</th>
													<th>Att. Add (<label for="checkAllattadd0"><input type="checkbox" onclick="checkAll(0, 'attadd')" id="checkAllattadd0"> All</label>)</th>
													<th>Att. Delete (<label for="checkAllattdelete0"><input type="checkbox" onclick="checkAll(0, 'attdelete')" id="checkAllattdelete0"> All</label>)</th>
													<th>Print (<label for="checkAllprint0"><input type="checkbox" onclick="checkAll(0, 'print')" id="checkAllprint0"> All</label>)</th></th>
													<th>Report (<label for="checkAllreport0"><input type="checkbox" onclick="checkAll(0, 'report')" id="checkAllreport0"> All</label>)</th></th>
												</tr>
											</thead>
											{{-- <tbody id="UserRoleTable" >
                                                @foreach ($modules as $key => $row)
                                                    <tr>
                                                        <td><input type="hidden" value="{{$row->id}}" name="module[{{$row->id}}]">{{$row->name}}</td>	
                                                        <td><input type="checkbox" class="create-checkbox-0" name="create[{{$row->id}}]"></td>
														<td><input type="checkbox" class="view-checkbox-0" name="view[{{$row->id}}]"></td>
                                                        <td><input type="checkbox" class="update-checkbox-0" name="update[{{$row->id}}]"></td>
                                                        <td><input type="checkbox" class="delete-checkbox-0" name="delete[{{$row->id}}]"></td>
														<td><input type="checkbox" class="attadd-checkbox-0" name="att_add[{{$row->id}}]"></td>
														<td><input type="checkbox" class="attdelete-checkbox-0" name="att_delete[{{$row->id}}]"></td>
                                                        <td><input type="checkbox" class="print-checkbox-0" name="print[{{$row->id}}]"></td>
														<td><input type="checkbox" class="report-checkbox-0" name="report[{{$row->id}}]"></td>
                                                    </tr>
                                                @endforeach
											</tbody> --}}


											<tbody id="UserRoleTable">
												@foreach ($modules as $key => $row)
													<tr>
														<td>
															<input type="hidden" value="{{$row->id}}" name="module[{{$row->id}}]">
															{{$row->name}}
															<label>
																(<input type="checkbox" onclick="checkRow({{ $row->id }})" id="checkRow{{$row->id}}"> All)
															</label>
														</td>    
														<td><input type="checkbox" class="create-checkbox-{{$row->id}}" name="create[{{$row->id}}]"></td>
														<td><input type="checkbox" class="view-checkbox-{{$row->id}}" name="view[{{$row->id}}]"></td>
														<td><input type="checkbox" class="update-checkbox-{{$row->id}}" name="update[{{$row->id}}]"></td>
														<td><input type="checkbox" class="delete-checkbox-{{$row->id}}" name="delete[{{$row->id}}]"></td>
														<td><input type="checkbox" class="attadd-checkbox-{{$row->id}}" name="att_add[{{$row->id}}]"></td>
														<td><input type="checkbox" class="attdelete-checkbox-{{$row->id}}" name="att_delete[{{$row->id}}]"></td>
														<td><input type="checkbox" class="print-checkbox-{{$row->id}}" name="print[{{$row->id}}]"></td>
														<td><input type="checkbox" class="report-checkbox-{{$row->id}}" name="report[{{$row->id}}]"></td>
													</tr>
												@endforeach
											</tbody>
											
										</table>
									</div>

									<footer class="card-footer">
										<div class="row form-group mb-2">
											<div class="text-end">
												<button type="button" class="btn btn-danger mt-2"  onclick="window.location='{{ route('all-roles') }}'"> <i class="fas fa-trash"></i> Discard</button>
												<button type="submit" class="btn btn-primary mt-2"> <i class="fas fa-save"></i> Add Role</button>
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
	
	$(document).ready(function() {
		$(window).keydown(function(event){
			if(event.keyCode == 13) {
				event.preventDefault();
				return false;
			}
		});
	});



	function checkAll(index, type) {
        const selectAllCheckbox = document.getElementById(`checkAll${type}${index}`);
        const checkboxes = document.querySelectorAll(`.${type}-checkbox-${index}`);

        // Check or uncheck all checkboxes based on the "Select All" checkbox
        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });
    }

	function checkRow(rowId) {
    const selectRowCheckbox = document.getElementById(`checkRow${rowId}`);
    const rowCheckboxes = document.querySelectorAll(`input[class*="-checkbox-${rowId}"]`);

    // Check or uncheck all checkboxes in the row based on the "Row Check All" checkbox
    rowCheckboxes.forEach(checkbox => {
        checkbox.checked = selectRowCheckbox.checked;
    });
}


</script>