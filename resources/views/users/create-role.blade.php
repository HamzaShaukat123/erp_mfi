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

											<thead style="position: sticky; top: 0; background-color: white;">
												<tr>
													<th>Module Name</th>
													<th>
														Create (<input type="checkbox" onclick="checkAllColumn('create')" id="checkAllcreate"> All)
													</th>
													<th>
														View (<input type="checkbox" onclick="checkAllColumn('view')" id="checkAllview"> All)
													</th>
													<th>
														Update (<input type="checkbox" onclick="checkAllColumn('update')" id="checkAllupdate"> All)
													</th>
													<th>
														Delete (<input type="checkbox" onclick="checkAllColumn('delete')" id="checkAlldelete"> All)
													</th>
													<th>
														Att. Add (<input type="checkbox" onclick="checkAllColumn('attadd')" id="checkAllattadd"> All)
													</th>
													<th>
														Att. Delete (<input type="checkbox" onclick="checkAllColumn('attdelete')" id="checkAllattdelete"> All)
													</th>
													<th>
														Print (<input type="checkbox" onclick="checkAllColumn('print')" id="checkAllprint"> All)
													</th>
													<th>
														Report (<input type="checkbox" onclick="checkAllColumn('report')" id="checkAllreport"> All)
													</th>
												</tr>
											</thead>

											<tbody id="UserRoleTable">
												@foreach ($modules as $key => $row)
													<tr>
														<td>
															<input type="hidden" value="{{$row->id}}" name="module[{{$row->id}}]">
															{{$row->name}}
															(<input type="checkbox" onclick="checkAllRow(this, {{ $key }})" id="checkAllRow{{ $key }}"> Row All)
														</td>
														<td><input type="checkbox" class="create-checkbox row-{{ $key }}" name="create[{{$row->id}}]"></td>
														<td><input type="checkbox" class="view-checkbox row-{{ $key }}" name="view[{{$row->id}}]"></td>
														<td><input type="checkbox" class="update-checkbox row-{{ $key }}" name="update[{{$row->id}}]"></td>
														<td><input type="checkbox" class="delete-checkbox row-{{ $key }}" name="delete[{{$row->id}}]"></td>
														<td><input type="checkbox" class="attadd-checkbox row-{{ $key }}" name="att_add[{{$row->id}}]"></td>
														<td><input type="checkbox" class="attdelete-checkbox row-{{ $key }}" name="att_delete[{{$row->id}}]"></td>
														<td><input type="checkbox" class="print-checkbox row-{{ $key }}" name="print[{{$row->id}}]"></td>
														<td><input type="checkbox" class="report-checkbox row-{{ $key }}" name="report[{{$row->id}}]"></td>
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



	// Check All for a Column
	function checkAllColumn(type) {
		const selectAllCheckbox = document.getElementById(`checkAll${type}`);
		const checkboxes = document.querySelectorAll(`.${type}-checkbox`);

		// Check or uncheck all checkboxes in the column
		checkboxes.forEach(checkbox => {
			checkbox.checked = selectAllCheckbox.checked;
		});

		// Update row-level "Check All" checkboxes
		updateRowChecks();
	}

	// Check All for a Row
	function checkAllRow(rowCheckbox, rowIndex) {
		const checkboxes = document.querySelectorAll(`.row-${rowIndex}`);

		// Check or uncheck all checkboxes in the row
		checkboxes.forEach(checkbox => {
			checkbox.checked = rowCheckbox.checked;
		});

		// Update column-level "Check All" checkboxes
		updateColumnChecks();
	}

	// Update Column-Level "Check All" States
	function updateColumnChecks() {
		const columnTypes = ['create', 'view', 'update', 'delete', 'attadd', 'attdelete', 'print', 'report'];

		columnTypes.forEach(type => {
			const allCheckboxes = document.querySelectorAll(`.${type}-checkbox`);
			const selectAllCheckbox = document.getElementById(`checkAll${type}`);

			// Set "Check All" checkbox state
			selectAllCheckbox.checked = [...allCheckboxes].every(checkbox => checkbox.checked);
		});
	}

	// Update Row-Level "Check All" States
	function updateRowChecks() {
		const rows = document.querySelectorAll('#UserRoleTable tr');

		rows.forEach((row, index) => {
			const rowCheckboxes = row.querySelectorAll(`.row-${index}`);
			const rowSelectAll = document.getElementById(`checkAllRow${index}`);

			// Set "Check All" checkbox state for the row
			rowSelectAll.checked = [...rowCheckboxes].every(checkbox => checkbox.checked);
		});
	}

	// Attach Event Listeners to Individual Checkboxes
	document.addEventListener('change', event => {
		if (event.target.matches('.create-checkbox, .view-checkbox, .update-checkbox, .delete-checkbox, .attadd-checkbox, .attdelete-checkbox, .print-checkbox, .report-checkbox')) {
			updateColumnChecks();
			updateRowChecks();
		}
	});



</script>