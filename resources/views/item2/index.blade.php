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
                                    <h2 class="card-title">All Item Pipes</h2>
                                    <form class="text-end" action="{{ route('create-item-2') }}" method="GET">
                                        <button type="button" class="btn btn-primary mt-2 modal-with-zoom-anim ws-normal" href="#createModal"> <i class="fas fa-plus" ></i> New Item (Single)</button>
                                        <button type="submit" class="btn btn-danger mt-2"> <i class="fas fa-plus"></i> New Item (Multiple)</button>
                                    </form>
                                </header>

                                <div class="card-body">
                                    <div>
                                        <div class="col-md-5" style="display:flex;">
                                            <select class="form-control" style="margin-right:10px" id="columnSelect">
                                                <option selected disabled>Search by</option>
                                                <option value="0">by Code</option>
                                                <option value="1">by Item Name</option>
                                                <option value="2">by Remarks</option>
                                                <option value="3">by Group Name</option>
                                                <option value="4">by Qty</option>
                                                <option value="5">by Wt.</option>
                                                <option value="6">by P.Date</option>
                                                <option value="7">by P.Price</option>
                                                <option value="8">by S.Date</option>
                                                <option value="9">by S.Price</option>
                                                <option value="10">by L.Price</option>
                                            </select>
                                            <input type="text" class="form-control" id="columnSearch" placeholder="Search By Column"/>
                                        </div>
                                    </div>
                                    <div class="modal-wrapper table-scroll">
                                        <table class="table table-bordered table-striped mb-0" id="cust-datatable-default">
                                            <thead>
                                                <tr>
                                                    <th width="4%">Code</th>
                                                    <th width="13%">Item Name</th>
                                                    <th width="13%">Remarks</th>
                                                    <th width="13%">Group Name</th>
                                                    <th width="4%">Qty</th>
                                                    <th width="4%">Wt.</th>
                                                    <th width="6%">P.Date</th>
                                                    <th width="2%">P.Price</th>
                                                    <th width="6%">S.Date</th>
                                                    <th width="2%">S.Price</th>
                                                    <th width="4%">L.Price</th>
                                                    <th width="4%"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($items as $key => $row)
                                                    <tr>
                                                        <td>{{$row->it_cod}}</td>
                                                        <td><strong>{{$row->item_name}}</strong></td>
                                                        <td>{{$row->item_remark}}</td>
                                                        <td>{{$row->group_name}}</td>
                                                        @if(substr(strval($row->opp_qty), strpos(strval($row->opp_qty), '.') + 1)>0)
                                                            <td>{{$row->opp_qty}}</td>
                                                        @else
                                                            <td>{{ intval($row->opp_qty) }}</td>
                                                        @endif
                                                        @if(substr(strval($row->weight), strpos(strval($row->weight), '.') + 1)>0)
                                                            <td>{{$row->weight}}</td>
                                                        @else
                                                            <td>{{ intval($row->weight) }}</td>
                                                        @endif
                                                        <td>{{ \Carbon\Carbon::parse($row->pur_rate_date)->format('d-m-y') }}</td>
                                                        @if(substr(strval($row->OPP_qty_cost), strpos(strval($row->OPP_qty_cost), '.') + 1)>0)
                                                            <td>{{$row->OPP_qty_cost}}</td>
                                                        @else
                                                            <td>{{ intval($row->OPP_qty_cost) }}</td>
                                                        @endif
                                                        <td>{{ \Carbon\Carbon::parse($row->sale_rate_date)->format('d-m-y') }}</td>

                                                        @if(substr(strval($row->sales_price), strpos(strval($row->sales_price), '.') + 1)>0)
                                                            <td>{{$row->sales_price}}</td>
                                                        @else
                                                            <td>{{ intval($row->sales_price) }}</td>
                                                        @endif
                                                        @if(substr(strval($row->labourprice), strpos(strval($row->labourprice), '.') + 1)>0)
                                                            <td>{{$row->labourprice}}</td>
                                                        @else
                                                            <td>{{ intval($row->labourprice) }}</td>
                                                        @endif
                                                        <td class="actions">
                                                            <a class="mb-1 mt-1 me-1 modal-with-zoom-anim ws-normal" onclick="getItemDetails({{$row->it_cod}})" href="#updateModal">
                                                                <i class="fas fa-pencil-alt"></i>
                                                            </a>
                                                            @if(session('user_role')==1)
                                                            <span class="separator"> | </span>
                                                            <a class="mb-1 mt-1 me-1 modal-with-zoom-anim ws-normal" onclick="setId({{$row->it_cod}})" href="#deleteModal">
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
            <form method="post" action="{{ route('delete-item-2') }}" enctype="multipart/form-data">
                @csrf
                <section class="card">
                    <header class="card-header">
                        <h2 class="card-title">Delete Item</h2>
                    </header>
                    <div class="card-body">
                        <div class="modal-wrapper">
                            <div class="modal-icon">
                                <i class="fas fa-question-circle"></i>
                            </div>
                            <div class="modal-text">
                                <p class="mb-0">Are you sure that you want to delete this item?</p>
                                <input name="item_id" id="deleteID" hidden>
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

        <div id="updateModal" class="modal-block modal-block-primary mfp-hide">
            <section class="card">
                <form method="post" action="{{ route('update-item-2') }}" enctype="multipart/form-data" onkeydown="return event.key != 'Enter';">
                    @csrf
                    <header class="card-header">
                        <h2 class="card-title">Update Item</h2>
                    </header>
                    <div class="card-body">
                        <div class="row form-group">
                            <div class="col-lg-6">
                                <label>Item Code</label>
                                <input type="number" class="form-control" id="it_cod_display" placeholder="Item Code" name="it_cod_display" required disabled>
                                <input type="hidden" class="form-control" id="it_cod" placeholder="Item Code" name="it_cod" required>                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Item Group<span style="color: red;"><strong>*</strong></span></label>
                                <select data-plugin-selecttwo class="form-control select2-js"  id="item_group" name ="item_group" required>
                                    <option  value="" disabled selected>Select Group</option>
                                    @foreach($itemGroups as $key => $row)	
                                        <option value="{{$row->item_group_cod}}">{{$row->group_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Item Name<span style="color: red;"><strong>*</strong></span></label>
                                <input type="text" class="form-control" id="item_name" autofocus placeholder="Item Name" name="item_name" required>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Remarks</label>
                                <input type="text" class="form-control" value=" " id="item_remark" placeholder="Remarks" name="item_remark">
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Opening Stock<span style="color: red;"><strong>*</strong></span></label>
                                <input type="text" class="form-control" id="qty" placeholder="Opening Stock" name="qty"  step=".00001" required>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Weigth<span style="color: red;"><strong>*</strong></span></label>
                                <input type="text" class="form-control" id="weight" placeholder="Weigth" name="weight"  step=".00001" required>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Purchase Price<span style="color: red;"><strong>*</strong></span></label>
                                <input type="text" class="form-control" id="OPP_qty_cost" placeholder="Purchase Price" name="OPP_qty_cost" step=".00001" required>
                            </div>

                            <div class="col-lg-6 mb-2">
                                <label>Purchase Rate Date<span style="color: red;"><strong>*</strong></span></label>
                                <input type="date" class="form-control" id="pur_rate_date" placeholder="Date"  name="pur_rate_date" required value="<?php echo date('Y-m-d'); ?>">
                            </div>  

                            <div class="col-lg-6 mb-2">
                                <label>Sale Price<span style="color: red;"><strong>*</strong></span></label>
                                <input type="text" class="form-control" id="sales_price" placeholder="Sale Price" name="sales_price"  step=".00001" required>
                            </div>

                            <div class="col-lg-6 mb-2">
                                <label>Sale Rate Date<span style="color: red;"><strong>*</strong></span></label>
                                <input type="date" class="form-control" id="sale_rate_date" placeholder="Date"  name="sale_rate_date" required value="<?php echo date('Y-m-d'); ?>">
                            </div>  

                            <div class="col-lg-6 mb-2">
                                <label>Date<span style="color: red;"><strong>*</strong></span></label>
                                <input type="date" class="form-control" id="date" placeholder="Date" name="date">
                            </div>  
                            <div class="col-lg-6 mb-2">
                                <label>Stock Level<span style="color: red;"><strong>*</strong></span></label>
                                <input type="text" class="form-control" id="stock_level" placeholder="Stock Level" name="stock_level"  step=".00001" required>
                            </div>  
                            <div class="col-lg-6 mb-2">
                                <label>Labour Price<span style="color: red;"><strong>*</strong></span></label>
                                <input type="text" class="form-control" id="labourprice" placeholder="Labour Price" name="labourprice"  step=".00001" required>
                            </div>  
                        </div>
                    </div>
                    <footer class="card-footer">
                        <div class="row">
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-primary">Update Item</button>
                                <button class="btn btn-default modal-dismiss">Cancel</button>
                            </div>
                        </div>
                    </footer>
                </form>
            </section>
        </div>

        <div id="createModal" class="modal-block modal-block-primary mfp-hide">
            <section class="card">
                <form method="post" action="{{ route('store-item-2') }}" enctype="multipart/form-data" onkeydown="return event.key != 'Enter';">
                    @csrf
                    <header class="card-header">
                        <h2 class="card-title">Add Item</h2>
                    </header>
                    <div class="card-body">
                        <div class="row form-group">
                            <div class="col-lg-6">
                                <label>Item Code</label>
                                <input type="number" class="form-control" placeholder="Item Code" name="it_cod" required disabled>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Item Group<span style="color: red;"><strong>*</strong></span></label>
                                <input type="hidden" id="itemCount" name="items" value="1" placeholder="Code" class="form-control">
                                <select data-plugin-selecttwo class="form-control select2-js"  name ="item_group[]" required>
                                    <option  value="" disabled selected>Select Group</option>
                                    @foreach($itemGroups as $key => $row)	
                                        <option value="{{$row->item_group_cod}}">{{$row->group_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Item Name<span style="color: red;"><strong>*</strong></span></label>
                                <input type="text" class="form-control" placeholder="Item Name" autofocus name="item_name[]" onchange="validateItemName(this)" required>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Remarks</label>
                                <input type="text" class="form-control"  placeholder="Remarks" name="item_remarks[]" value=" ">
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Opening Stock<span style="color: red;"><strong>*</strong></span></label>
                                <input type="number" class="form-control" placeholder="Stock" value="0" name="item_stock[]" required step=".00001">
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Weigth<span style="color: red;"><strong>*</strong></span></label>
                                <input type="number" class="form-control" placeholder="Weigth" value="0" name="weight[]" step=".00001" required>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Purchase Price<span style="color: red;"><strong>*</strong></span></label>
                                <input type="number" class="form-control" placeholder="Purchase Price" value="0" name="item_pur_cost[]" required step=".00001">
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Purchase Rate Date<span style="color: red;"><strong>*</strong></span></label>
                                <input type="date" class="form-control" placeholder="Date"  name="purchase_rate_date[]" required value="<?php echo date('Y-m-d'); ?>">
                            </div>  
                            <div class="col-lg-6 mb-2">
                                <label>Sale Price<span style="color: red;"><strong>*</strong></span></label>
                                <input type="number" class="form-control" placeholder="Sale Price" value="0" name="item_s_price[]" required step=".00001">
                            </div>
                            <div class="col-lg-6 mb-2">
                            <label>Sale Rate Date<span style="color: red;"><strong>*</strong></span></label>
                                <input type="date" class="form-control" placeholder="Date" name="sale_rate_date[]" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>  
                            <div class="col-lg-6 mb-2">
                                <label>Date<span style="color: red;"><strong>*</strong></span></label>
                                <input type="date" class="form-control" placeholder="Date"  name="item_date[]" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>  
                            <div class="col-lg-6 mb-2">
                                <label>Stock Level<span style="color: red;"><strong>*</strong></span></label>
                                <input type="number" class="form-control" placeholder="Stock Level" value="0" name="item_stock_level[]" required step=".00001">
                            </div>  
                            <div class="col-lg-6 mb-2">
                                <label>Labour Price<span style="color: red;"><strong>*</strong></span></label>
                                <input type="number" class="form-control" placeholder="Labour Price" value="0" name="item_l_price[]" required step=".00001">
                            </div>  
                        </div>
                    </div>
                    <footer class="card-footer">
                        <div class="row">
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-primary">Add Item</button>
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

    function getItemDetails(id){
        $.ajax({
            type: "GET",
            url: "/item2/detail",
            data: {id:id},
            success: function(result){
                var dateParts = result[0]['opp_date'].split("-");
                var year = dateParts[0];
                var month = dateParts[1];
                var day = dateParts[2];
                var date = year + "-" + month + "-" + day;  
                
                $('#it_cod').val(result[0]['it_cod']);
                $('#it_cod_display').val(result[0]['it_cod']);
                $('#item_group').val(result[0]['item_group']).trigger('change');
                $('#item_name').val(result[0]['item_name']);
                $('#item_remark').val(result[0]['item_remark']);
                $('#qty').val(result[0]['opp_qty']);
                $('#weight').val(result[0]['weight']);
                $('#OPP_qty_cost').val(result[0]['OPP_qty_cost']);
                $('#pur_rate_date').val(result[0]['pur_rate_date']);
                $('#sales_price').val(result[0]['sales_price']);
                $('#sale_rate_date').val(result[0]['sale_rate_date']);
                $("#date" ).val(result[0]['opp_date'])
                $('#stock_level').val(result[0]['stock_level']);
                $('#labourprice').val(result[0]['labourprice']);
            },  
            error: function(){
                alert("error");
            }
        });
	}

    function validateItemName(inputElement)
	{
		var item_name = inputElement.value;

		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

        $.ajax({
            type: 'POST',
			url: '/item2/new-item/validate',
            data: {'item_name': item_name},
            success: function(response){
            },
            error: function(response){
                var errors = response.responseJSON.errors;
                var errorMessage = 'Product Already Exists';
                alert(errorMessage);
            }
        });
    }
</script>