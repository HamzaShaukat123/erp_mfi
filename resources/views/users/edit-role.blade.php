@include('../layouts.header')
	<body>
		<section class="body">
        @include('../layouts.pageheader')
            <div class="inner-wrapper cust-pad">
				<section role="main" class="content-body" style="margin:0px">
					<form method="post" action="{{ route('update-role') }}" enctype="multipart/form-data" onkeydown="return event.key != 'Enter';" id="addForm">
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
												<input type="text" placeholder="(New Role)" value={{$role->id}} class="form-control" disabled>
												<input type="hidden" name="role_id" value={{$role->id}} class="form-control">
												<input type="hidden" value={{$count}} name="modules_count" class="form-control">

											</div>
											<div class="col-sm-12 col-md-4 mb-2">
												<label class="col-form-label">Role Name</label>
												<input type="text" name="role_name" value="{{$role->name}}" required class="form-control">
											</div>
											<div class="col-sm-12 col-md-4 mb-2">
												<label class="col-form-label" >Role Shortcode</label>
												<input type="text" placeholder="Shortcode" name="shortcode" value="{{$role->shortcode}}" class="form-control">
											</div>
									  </div>
									</div>
								</section>
							</div>
							<div class="col-12 mb-3">
								<section class="card">
									<div class="card-body" style="height: 450px;overflow-y: auto;padding:0px !important">
										<table class="table table-bordered table-striped mb-0" id="myTable">
											{{-- <thead style="position: sticky;top: 0;background-color: white; ">
												<tr>
													<th>Module Name</th>
													<th>Create</th>
													<th>View</th>
													<th>Update</th>
													<th>Delete</th>
													<th>Att. Add</th>
													<th>Att. Delete</th>
													<th>Print</th>
													<th>Report</th>
												</tr>
											</thead> --}}

											<thead style="position: sticky;top: 0;background-color: white; ">
												<tr>
													<th>Module Name</th>
													<th>Create (<input type="checkbox" data-column-class="create-checkbox"> All)</th>
													<th>View (<input type="checkbox" data-column-class="view-checkbox"> All)</th>
													<th>Update (<input type="checkbox" data-column-class="update-checkbox"> All)</th>
													<th>Delete (<input type="checkbox" data-column-class="delete-checkbox"> All)</th>
													<th>Att. Add (<input type="checkbox" data-column-class="attadd-checkbox"> All)</th>
													<th>Att. Delete (<input type="checkbox" data-column-class="attdelete-checkbox"> All)</th>
													<th>Print (<input type="checkbox" data-column-class="print-checkbox"> All)</th>
													<th>Report (<input type="checkbox" data-column-class="report-checkbox"> All)</th>
												</tr>
											</thead>
											<tbody id="UserRoleTable">
												@foreach ($role_access as $key => $row)
													<tr>
														<td>
															<input type="hidden" value="{{ $row->module_id }}" name="module[{{ $row->module_id }}]">
															<input type="checkbox" class="row-check-all"> {{ $row->module_name }}
														</td>
														<td><input type="checkbox" class="create-checkbox" name="create[{{ $row->module_id }}]" {{ $row->add == 1 ? 'checked' : '' }}></td>
														<td><input type="checkbox" class="view-checkbox" name="view[{{ $row->module_id }}]" {{ $row->view == 1 ? 'checked' : '' }}></td>
														<td><input type="checkbox" class="update-checkbox" name="update[{{ $row->module_id }}]" {{ $row->edit == 1 ? 'checked' : '' }}></td>
														<td><input type="checkbox" class="delete-checkbox" name="delete[{{ $row->module_id }}]" {{ $row->delete == 1 ? 'checked' : '' }}></td>
														<td><input type="checkbox" class="attadd-checkbox" name="att_add[{{ $row->module_id }}]" {{ $row->att_add == 1 ? 'checked' : '' }}></td>
														<td><input type="checkbox" class="attdelete-checkbox" name="att_delete[{{ $row->module_id }}]" {{ $row->att_delete == 1 ? 'checked' : '' }}></td>
														<td><input type="checkbox" class="print-checkbox" name="print[{{ $row->module_id }}]" {{ $row->print == 1 ? 'checked' : '' }}></td>
														<td><input type="checkbox" class="report-checkbox" name="report[{{ $row->module_id }}]" {{ $row->report == 1 ? 'checked' : '' }}></td>
													</tr>
												@endforeach
											</tbody>
											
											
											{{-- <tbody id="UserRoleTable">
                                                @foreach ($role_access as $key => $row)
                                                    <tr>
                                                        <td><input type="hidden"   value="{{$row->module_id}}" name="module[{{$row->module_id}}]">{{$row->module_name}}</td>	
														<td><input type="checkbox" name="create[{{$row->module_id}}]" {{ $row->add == 1 ? 'checked' : '' }}></td>
                                                        <td><input type="checkbox" name="view[{{$row->module_id}}]"   {{ $row->view == 1 ? 'checked' : '' }}></td>
                                                        <td><input type="checkbox" name="update[{{$row->module_id}}]" {{ $row->edit == 1 ? 'checked' : '' }}></td>
                                                        <td><input type="checkbox" name="delete[{{$row->module_id}}]" {{ $row->delete == 1 ? 'checked' : '' }}></td>
														<td><input type="checkbox" name="att_add[{{$row->module_id}}]" {{ $row->att_add == 1 ? 'checked' : '' }}></td>
														<td><input type="checkbox" name="att_delete[{{$row->module_id}}]" {{ $row->att_delete == 1 ? 'checked' : '' }}></td>
                                                        <td><input type="checkbox" name="print[{{$row->module_id}}]" {{ $row->print == 1 ? 'checked' : '' }}></td>
														<td><input type="checkbox" name="report[{{$row->module_id}}]" {{ $row->report == 1 ? 'checked' : '' }}></td>
                                                    </tr>
                                                @endforeach
											</tbody> --}}

											
											
										</table>
									</div>

									<footer class="card-footer">
										<div class="row form-group mb-2">
											<div class="text-end">
												<button type="button" class="btn btn-danger mt-2"  onclick="window.location='{{ route('all-roles') }}'"> <i class="fas fa-trash"></i> Discard</button>
												<button type="submit" class="btn btn-primary mt-2"> <i class="fas fa-save"></i> Update Role</button>
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
	
	// $(document).ready(function() {
	// 	$(window).keydown(function(event){
	// 		if(event.keyCode == 13) {
	// 			event.preventDefault();
	// 			return false;
	// 		}
	// 	});
	// });



	$(document).ready(function () {
    // Prevent form submission on Enter key press
    $(window).keydown(function (event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            return false;
        }
    });

    // Check All Columns
    function checkAllColumn(columnClass, isChecked) {
        $(`.${columnClass}`).prop('checked', isChecked);
    }

    // Check All Rows
    function checkAllRow(rowElement, isChecked) {
        $(rowElement).find('input[type="checkbox"]').prop('checked', isChecked);
    }

    // Add event listeners for column "check all"
    $('thead th input[type="checkbox"]').on('change', function () {
        const columnClass = $(this).data('column-class');
        if (columnClass) {
            checkAllColumn(columnClass, $(this).is(':checked'));
        }
    });

    // Add event listeners for row "check all"
    $('tbody tr').each(function () {
        const rowCheckbox = $(this).find('.row-check-all');
        rowCheckbox.on('change', function () {
            checkAllRow($(this).closest('tr'), $(this).is(':checked'));
        });
    });
});


</script>