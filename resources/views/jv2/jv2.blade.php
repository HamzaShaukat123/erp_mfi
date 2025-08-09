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
                                        <a>Journal Voucher 2</a>
                                        <a href="{{ route('all-jv2') }}"><small><small>&nbsp;&nbsp;&nbsp;Fetch All</small></small></a>
                                    </h2>                                    
                                    <form class="text-end" action="{{ route('new-jv2') }}" method="GET">
                                        <button type="submit" class="btn btn-primary"> <i class="fas fa-plus"></i> New Voucher</button>
                                    </form>
                                </header>
                                <div class="card-body">
                                    <div>
                                        <div class="col-md-5" style="display:flex;">
                                            <select class="form-control" style="margin-right:10px" id="columnSelect">
                                                <option selected disabled>Search by</option>
                                                <option value="0">by Vouch#</option>
                                                <option value="1">by Date</option>
                                                <option value="2">by Narration</option>
                                                <option value="3">by Account Name</option>
                                                <option value="4">by Debit/Credit</option>
                                                <option value="5">by Sale Invoices</option>
                                                <option value="6">by Purchase Invoices</option>
                                            </select>
                                            <input type="text" class="form-control" id="columnSearch" placeholder="Search By Column"/>
                                        </div>
                                    </div>
                                    <div class="modal-wrapper table-scroll">
                                        <table class="table table-bordered table-striped mb-0" id="cust-datatable-default">
                                            <thead>
                                                <tr>
                                                    <th>Voch#</th>
                                                    <th>Date</th>
                                                    <th>Narration</th>
                                                    <th>Account Name</th>
                                                    <th>Debit / Credit</th>
                                                    <th>Sales Invoices</th>
                                                    <th>Purchase Invoices</th>
                                                    <th>Att.</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($jv2 as $key => $row)
                                                    <tr>
                                                        <td>{{$row->jv_no}}</td>
                                                        <td>{{ \Carbon\Carbon::parse($row->jv_date)->format('d-m-y') }}</td>
                                                        <td>{{$row->narration}}</td>
                                                        <td>{{$row->account_names}}</td>
                                                        <td>{{ number_format($row->total_debit, 0) }} / {{ number_format($row->total_credit, 0) }}</td>
                                                    
                                                        <td style="color: {{ $row->sales_status == 0 ? 'red' : 'inherit' }}">
                                                            {{$row->merged_sales_ids}} {{$row->sales_account_name}}
                                                        </td>
                                                        
                                                        <td style="color: {{ $row->purchase_status == 0 ? 'red' : 'inherit' }}">
                                                            {{$row->merged_purchase_ids}} {{$row->purchase_account_name}}
                                                        </td>
                                                    
                                                        <td>
                                                            <a class="mb-1 mt-1 me-1 modal-with-zoom-anim ws-normal text-dark" onclick="getAttachements({{$row->jv_no}})" href="#attModal"><i class="fa fa-eye"> </i></a>
                                                            <span class="separator"> | </span>
                                                            <a class="mb-1 mt-1 me-1 modal-with-zoom-anim ws-normal text-danger" onclick="setAttId({{$row->jv_no}})" href="#addAttModal"> <i class="fas fa-paperclip"> </i></a>
                                                        </td>
                                                        <td class="actions">
                                                            <a class="mb-1 mt-1 me-1" target="_blank" href="{{ route('print-jv2', $row->jv_no) }}">
                                                                <i class="fas fa-print"></i>
                                                            </a>
                                                            <span class="separator"> | </span>
                                                            <a class="mb-1 mt-1 me-1" href="{{ route('edit-jv2', $row->jv_no) }}">
                                                                <i class="fas fa-pencil-alt"></i>
                                                            </a>
                                                            @if(session('user_role')==1)
                                                            <span class="separator"> | </span>
                                                            <a class="mb-1 mt-1 me-1 modal-with-zoom-anim ws-normal" onclick="setId({{$row->jv_no}})" href="#deleteModal">
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
            <form method="post" action="{{ route('delete-jv2') }}" enctype="multipart/form-data">
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
                                <input name="delete_jv_no" id="deleteID" hidden>
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
                                    <th>Attachement Path</th>
                                    <th>Download</th>
                                    <th>View</th>
                                    <th>Delete</th>
                                </tr>
                            </thead>
                            <tbody id="jv2_attachements">

                            </tbody>
                        </table>
                    </div>
                </div>
                <footer class="card-footer">
                    <div class="row">
                        <div class="col-md-12 text-end">
                            <button class="btn btn-default modal-dismiss">Cancel</button>
                        </div>
                    </div>
                </footer>
            </section>
        </div>

        <div id="addAttModal" class="zoom-anim-dialog modal-block modal-block-danger mfp-hide">
            <form method="post" action="{{ route('jv2-att-add') }}" enctype="multipart/form-data" onkeydown="return event.key != 'Enter';">
                @csrf  
                <section class="card">
                    <header class="card-header">
                        <h2 class="card-title">Upload Attachements</h2>
                    </header>
                    <div class="card-body">
                        <div class="modal-wrapper">
                            <div class="col-lg-12 mb-2">
                                <input type="file" class="form-control" name="addAtt[]" multiple accept="application/pdf, image/png, image/jpeg">
                                <input type="hidden" class="form-control" name="att_id" id="att_id">
                            </div>
                        </div>
                    </div>
                    <footer class="card-footer">
                        <div class="row">
                            <div class="col-md-12 text-end">
                                <button type="sumit" class="btn btn-danger">Upload</button>
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

    $(document).ready(function(){
        var table = $('#cust-datatable-default').DataTable();

        $('#columnSelect').on('change', function () {
            // Clear the previous search
            table.search('').columns().search('').draw(); // Reset global and column-specific filters
        });
        $('#columnSearch').on('keyup change', function () {
            var columnIndex = $('#columnSelect').val(); // Get selected column index
            table.column(columnIndex).search(this.value).draw(); // Apply search and redraw
        });
    });

    function setId(id){
        $('#deleteID').val(id);
    }

    function setAttId(id){
        $('#att_id').val(id);
    }

    function getAttachements(id){

        var table = document.getElementById('jv2_attachements');
        while (table.rows.length > 0) {
            table.deleteRow(0);
        }

        $.ajax({
            type: "GET",
            url: "/vouchers2/attachements",
            data: {id:id},
            success: function(result){
                $.each(result, function(k,v){
                    var html="<tr>";
                    html+= "<td>"+v['att_path']+"</td>"
                    html+= "<td class='text-center'><a class='mb-1 mt-1 mr-2 me-1 text-danger' href='/vouchers2/download/"+v['att_id']+"'><i class='fas fa-download'></i></a></td>"
                    html+= "<td class='text-center'><a class='mb-1 mt-1 me-1 text-primary' href='/vouchers2/view/"+v['att_id']+"' target='_blank'><i class='fas fa-eye'></i></a></td>"
                    html+= "<td class='text-center'><a class='mb-1 mt-1 me-1 text-primary' href='#' onclick='deleteFile("+v['att_id']+")'><i class='fas fa-trash'></i></a></td>"
                    html+="</tr>";
                    $('#jv2_attachements').append(html);
                });
            },
            error: function(){
                alert("error");
            }
        });
    }

    function deleteFile(fileId) {
        if (!confirm('Are you sure you want to delete this file?')) {
            return;
        }

        fetch('/vouchers2/deleteAttachment/' + fileId, {
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

</script>