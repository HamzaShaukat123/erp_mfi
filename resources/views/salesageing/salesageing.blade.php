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
                                    <h2 class="card-title">
                                        <a>Sales Ageing</a>
                                        <a href="{{ route('all-salesageing') }}"><small><small>&nbsp;&nbsp;&nbsp;Fetch All</small></small></a>
                                    </h2>                                    
                                    <form class="text-end" action="{{ route('new-jv2') }}" method="GET">
                                        <button type="submit" class="btn btn-primary"> <i class="fas fa-plus"></i> New Entry</button>
                                    </form>
                                </header>
                                <div class="card-body">
                                    <div>
                                        <div class="col-md-5" style="display:flex;">
                                            <select class="form-control" style="margin-right:10px" id="columnSelect">
                                                <option selected disabled>Search by</option>
                                                <option value="0">by ID</option>
                                                <option value="1">by Voch#</option>
                                                <option value="2">by Sales Invoices</option>
                                                <option value="3">by Account Name</option>
                                                <option value="4">by Amount</option>
                                            </select>
                                            <input type="text" class="form-control" id="columnSearch" placeholder="Search By Column"/>
                                        </div>
                                    </div>
                                    <div class="modal-wrapper table-scroll">
                                        <table class="table table-bordered table-striped mb-0" id="cust-datatable-default">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Voch#</th>
                                                    <th>Sales Invoices</th>
                                                    <th>Account Name</th>
                                                    <th>Amount</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($jv2 as $key => $row)
                                                    <tr>
                                                        <td>{{$row->id}}</td>
                                                        <td>{{$row->voch_prefix}}{{$row->jv2_id}}</td>
                                                        <td style="color: {{ $row->status == 0 ? 'red' : 'inherit' }}">
                                                            {{$row->sales_prefix}}{{$row->sales_id}}
                                                        </td>
                                                        <td>{{$row->ac_name}}</td>
                                                        <td>{{ number_format($row->amount, 0) }}</td>
                                                        <td class="actions">
                                                            {{-- <a class="mb-1 mt-1 me-1" target="_blank" href="{{ route('print-jv2', $row->id) }}">
                                                                <i class="fas fa-print"></i>
                                                            </a>
                                                            <span class="separator"> | </span> --}}
                                                            <a class="mb-1 mt-1 me-1" href="{{ route('edit-jv2', $row->id) }}">
                                                                <i class="fas fa-pencil-alt"></i>
                                                            </a>
                                                            @if(session('user_role')==1)
                                                            <span class="separator"> | </span>
                                                            <a class="mb-1 mt-1 me-1 modal-with-zoom-anim ws-normal" onclick="setId({{$row->id}})" href="#deleteModal">
                                                                <i class="far fa-trash-alt" style="color:red"></i>
                                                            </a>
                                                            @endif
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

        <div id="deleteModal" class="zoom-anim-dialog modal-block modal-block-danger mfp-hide">
            <form method="post" action="{{ route('delete-salesageing') }}" enctype="multipart/form-data">
                @csrf
                <section class="card">
                    <header class="card-header">
                        <h2 class="card-title">Delete Journal Voucher</h2>
                    </header>
                    <div class="card-body">
                        <div class="modal-wrapper">
                            <div class="modal-icon">
                                <i class="fas fa-question-circle"></i>
                            </div>
                            <div class="modal-text">
                                <p class="mb-0">Are you sure that you want to delete this Journal Voucher?</p>
                                <input name="delete_id_no" id="deleteID" hidden>
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

       

        @include('../layouts.footerlinks')
	</body>
</html>
<script>


   

</script>