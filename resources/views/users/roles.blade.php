@include('../layouts.header')
	<body>
		<section class="body">
            @include('../layouts.homepageheader')
			<div class="inner-wrapper cust-pad">
                @include('layouts.leftmenu')
				<section role="main" class="content-body">
                    <div class="row">
                        <div class="col">
                            <section class="card">
                                <header class="card-header" style="display: flex;justify-content: space-between;">
                                    <h2 class="card-title">All Roles</h2>
                                    <form class="text-end" action="{{ route('new-role') }}" method="GET">
                                        <button type="submit" class="btn btn-primary mt-2"> <i class="fas fa-plus"></i> New Role</button>
                                    </form>
                                </header>
                                <div class="card-body">
                                    <div class="modal-wrapper">
                                        <table class="table table-bordered table-striped mb-0" id="datatable-default">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Name</th>
                                                    <th>Code</th>
                                                    <th>Total Users</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($roles as $key => $row)
                                                    <tr>
                                                        <td>{{$row->id}}</td>
                                                        <td>{{$row->name}}</td>
                                                        <td>{{$row->shortcode}}</td>
                                                        <td>{{$row->user_count}}</td>
                                                        <td class="actions">
                                                            <a href="{{ route('edit-role', $row->id) }}" class="mb-1 mt-1 me-1"><i class="fas fa-pencil-alt"></i></a>
                                                        </td>
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

        @include('../layouts.footerlinks')
	</body>
</html>
 
