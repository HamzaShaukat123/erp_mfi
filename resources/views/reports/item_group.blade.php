@include('../layouts.header')
	<body>
		<section class="body">
            @include('layouts.homepageheader')
			<div class="inner-wrapper cust-pad">
				@include('layouts.leftmenu')
				<section role="main" class="content-body">
                    <div class="row mb-4">
                        <div class="col-lg-2">
                            <div class="form-group">
                                <?php $previousDate = date('Y-m-d', strtotime('-30 days')); ?>
                                <label class="col-form-label"><strong>From</strong></label>
                                <input type="date" class="form-control" id="fromDate" value="<?php echo $previousDate; ?>">
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <label class="col-form-label" ><strong>To</strong></label>
                                <input type="date" class="form-control" id="toDate" value="<?php echo date('Y-m-d'); ?>">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label class="col-form-label"><strong>Item Group</strong></label>
                                <select data-plugin-selecttwo class="form-control select2-js"  id="acc_id">
                                    <option value="" disabled selected>Select Item</option>
                                    @foreach($items_group as $key => $row)	
                                        <option value="{{$row->item_group_cod }}">{{$row->group_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <a class="btn btn-primary" style="margin-top: 2.1rem;padding: 0.5rem 0.6rem;" onclick="getReport()"><i class="fa fa-filter"></i></a>
                        </div>
                    </div>   
                    <div class="tabs">
                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a class="nav-link nav-link-rep" data-bs-target="#SALE1" href="#SALE1" data-bs-toggle="tab">Sale-1</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link nav-link-rep" data-bs-target="#PURCHASE1" href="#PURCHASE1" data-bs-toggle="tab">Purchase-1</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link nav-link-rep" data-bs-target="#SALE2" href="#SALE2" data-bs-toggle="tab">Sale-2</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link nav-link-rep" data-bs-target="#PURCHASE2" href="#PURCHASE2" data-bs-toggle="tab">Purchase-2</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            
                            <div id="SALE1" class="tab-pane">
                                <div class="row form-group pb-3">
                                    <div class="col-lg-6">
                                        <div class="bill-to">
                                            <h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold" style="display: flex; align-items: center;">
                                                <span style="color: #17365D;">From: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="sale1_from"></span>
                                            
                                                <span style="flex: 0.3;"></span> <!-- Spacer to push the "To" to the right -->
                                            
                                                <span style="color: #17365D;">To: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="sale1_to"></span>
                                            </h4>
                                            
                                            <h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
                                                <span style="color:#17365D;font-size:20px;">Item Group: &nbsp;</span>
                                                <span style="font-weight:400; color:rgb(238, 19, 19);font-size:20px;" id="sale1_acc"></span>
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 text-end">
                                        <a class="mb-1 mt-1 me-1 btn btn-warning" aria-label="Download" onclick="downloadPDF('sale1')"><i class="fa fa-download"></i> Download</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-danger" aria-label="Print PDF" onclick="printPDF('sale1')"><i class="fa fa-file-pdf"></i> Print PDF</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-success" aria-label="Export to Excel" onclick="downloadExcel('sale1')"><i class="fa fa-file-excel"></i> Excel</a>      
                                    </div>
                                    <div class="col-12 mt-4">
                                        <table class="table table-bordered table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th>S/No</th>
                                                    <th>Sales Date</th>
                                                    <th>Inv No.</th>
                                                    <th>Account Name</th>
                                                    <th>Item Name</th>
                                                    <th>Qty</th>
                                                    <th>Price</th>
                                                    <th>Weight</th>
                                                    <th>Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody id="Sale1TbleBody">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="PURCHASE1" class="tab-pane">
                                <div class="row form-group pb-3">
                                    <div class="col-lg-6">
                                        <div class="bill-to">
                                            <h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold" style="display: flex; align-items: center;">
                                                <span style="color: #17365D;">From: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="pur1_from"></span>
                                            
                                                <span style="flex: 0.3;"></span> <!-- Spacer to push the "To" to the right -->
                                            
                                                <span style="color: #17365D;">To: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="pur1_to"></span>
                                            </h4>
                                            
                                            <h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
                                                <span style="color:#17365D;font-size:20px;">Item Group: &nbsp;</span>
                                                <span style="font-weight:400; color:rgb(238, 19, 19);font-size:20px;" id="pur1_acc"></span>
                                            </h4>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-6 text-end">
                                        <a class="mb-1 mt-1 me-1 btn btn-warning" aria-label="Download" onclick="downloadPDF('purchase1')"><i class="fa fa-download"></i> Download</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-danger" aria-label="Print PDF" onclick="printPDF('purchase1')"><i class="fa fa-file-pdf"></i> Print PDF</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-success" aria-label="Export to Excel" onclick="downloadExcel('purchase1')"><i class="fa fa-file-excel"></i> Excel</a>   
                                    </div>
                                    
                                    <div class="col-12 mt-4">
                                        <table class="table table-bordered table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th>S/No</th>
                                                    <th>Sales Date</th>
                                                    <th>Inv No.</th>
                                                    <th>Account Name</th>
                                                    <th>Item Name</th>
                                                    <th>Qty</th>
                                                    <th>Price</th>
                                                    <th>Weight</th>
                                                    <th>Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody id="Pur1TbleBody">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div id="SALE2" class="tab-pane">
                                <div class="row form-group pb-3">
                                    <div class="col-lg-6">
                                        <div class="bill-to">
                                            <h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold" style="display: flex; align-items: center;">
                                                <span style="color: #17365D;">From: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="sale2_from"></span>
                                            
                                                <span style="flex: 0.3;"></span> <!-- Spacer to push the "To" to the right -->
                                            
                                                <span style="color: #17365D;">To: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="sale2_to"></span>
                                            </h4>
                                            
                                            <h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
                                                <span style="color:#17365D;font-size:20px;">Item Group: &nbsp;</span>
                                                <span style="font-weight:400; color:rgb(238, 19, 19);font-size:20px;" id="sale2_acc"></span>
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 text-end">
                                        <a class="mb-1 mt-1 me-1 btn btn-warning" aria-label="Download" onclick="downloadPDF('sale2')"><i class="fa fa-download"></i> Download</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-danger" aria-label="Print PDF" onclick="printPDF('sale2')"><i class="fa fa-file-pdf"></i> Print PDF</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-success" aria-label="Export to Excel" onclick="downloadExcel('sale2')"><i class="fa fa-file-excel"></i> Excel</a>      
                                    </div>
                                    <div class="col-12 mt-4">
                                        <table class="table table-bordered table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th>S/No</th>
                                                    <th>Sales Date</th>
                                                    <th>Inv No.</th>
                                                    <th>Account Name</th>
                                                    <th>Item Name</th>
                                                    <th>Qty</th>
                                                    <th>Price</th>
                                                    <th>Length</th>
                                                    <th>Percent</th>
                                                    <th>Weight</th>
                                                </tr>
                                            </thead>
                                            <tbody id="Sale2TbleBody">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="PURCHASE2" class="tab-pane">
                                <div class="row form-group pb-3">
                                    <div class="col-lg-6">
                                        <div class="bill-to">
                                            <h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold" style="display: flex; align-items: center;">
                                                <span style="color: #17365D;">From: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="pur2_from"></span>
                                            
                                                <span style="flex: 0.3;"></span> <!-- Spacer to push the "To" to the right -->
                                            
                                                <span style="color: #17365D;">To: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="pur2_to"></span>
                                            </h4>
                                            
                                            <h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
                                                <span style="color:#17365D;font-size:20px;">Item Name: &nbsp;</span>
                                                <span style="font-weight:400; color:rgb(238, 19, 19);font-size:20px;" id="pur2_acc"></span>
                                            </h4>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-6 text-end">
                                        <a class="mb-1 mt-1 me-1 btn btn-warning" aria-label="Download" onclick="downloadPDF('purchase2')"><i class="fa fa-download"></i> Download</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-danger" aria-label="Print PDF" onclick="printPDF('purchase2')"><i class="fa fa-file-pdf"></i> Print PDF</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-success" aria-label="Export to Excel" onclick="downloadExcel('purchase2')"><i class="fa fa-file-excel"></i> Excel</a>   
                                    </div>
                                    
                                    <div class="col-12 mt-4">
                                        <table class="table table-bordered table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th>S/No</th>
                                                    <th>Sales Date</th>
                                                    <th>Inv No.</th>
                                                    <th>Account Name</th>
                                                    <th>Item Name</th>
                                                    <th>Qty</th>
                                                    <th>Price</th>
                                                    <th>Length</th>
                                                    <th>Percent</th>
                                                    <th>Weight</th>
                                                </tr>
                                            </thead>
                                            <tbody id="Pur2TbleBody">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                           
                        </div>
                    </div>
                </section>		
			</div>
		</section>
        @include('../layouts.footerlinks')
	</body>
    <script>

        document.querySelectorAll('.nav-link-rep').forEach(tabLink => {
            tabLink.addEventListener('click', function() {
                tabId = this.getAttribute('data-bs-target');
                tabChanged(tabId);
            });
        });

        function tabChanged(tabId) {

            fromDate=$('#fromDate').val();
            toDate=$('#toDate').val();
            acc_id=$('#acc_id').val();

            const formattedfromDate = moment(fromDate).format('DD-MM-YYYY'); // Format the date
            const formattedtoDate = moment(toDate).format('DD-MM-YYYY'); // Format the date

            if(tabId=="#SALE1"){
                var table = document.getElementById('Sale1TbleBody');
                while (table.rows.length > 0) {
                    table.deleteRow(0);
                }
                url="/rep-by-item-group/sale1";
                tableID="#Sale1TbleBody";

                $.ajax({
                    type: "GET",
                    url: url,
                    data: {
                        fromDate: fromDate,
                        toDate: toDate,
                        acc_id: acc_id,
                    },
                    beforeSend: function() {
                        $(tableID).html('<tr><td colspan="10" class="text-center">Loading Data Please Wait...</td></tr>');
                    },
                    success: function(result) {
                        $('#sale1_from').text(formattedfromDate);
                        $('#sale1_to').text(formattedtoDate);
                        var selectedAcc = $('#acc_id').find("option:selected").text();

                        $('#sale1_acc').text(selectedAcc);
                        $(tableID).empty(); // Clear the loading message

                        let totalQty = 0;
                        let totalWeight = 0;
                        let totalAmount = 0;

                        $.each(result, function(k, v) {
                            let qty = parseFloat(v['qty'] || 0);
                            let weight = parseFloat(v['weight'] || 0);
                            let price = parseFloat(v['price'] || 0);
                            let amount = weight * price;

                            totalQty += qty;
                            totalWeight += weight;
                            totalAmount += amount;

                            let html = "<tr>";
                            html += "<td>" + (k + 1) + "</td>";
                            html += "<td>" + (v['sa_date'] ? moment(v['sa_date']).format('DD-MM-YY') : "") + "</td>";
                            html += `<td>${v['prefix'] ? v['prefix'] : ''} ${v['Sal_inv_no'] ? v['Sal_inv_no'] : ''}</td>`;
                            html += "<td>" + (v['ac_name'] ? v['ac_name'] : "") + "</td>";
                            html += "<td>" + (v['item_name'] ? v['item_name'] : "") + "</td>";
                            html += "<td>" + (v['qty'] ? v['qty'] : "0") + "</td>";
                            html += "<td>" + (v['price'] ? v['price'] : "0") + "</td>";
                            html += "<td>" + (v['weight'] ? v['weight'] : "0") + "</td>";
                            html += "<td>" + (amount ? amount.toFixed(2) : "") + "</td>";
                            html += "</tr>";
                            $(tableID).append(html);
                        });

                        // Append totals row
                        let totalHtml = "<tr class='font-weight-bold'>";
                        totalHtml += "<td colspan='5' style='text-align: right;'><strong>Total:</strong></td>";
                        totalHtml += "<td>" + totalQty.toFixed(0) + "</td>";
                        totalHtml += "<td></td>";
                        totalHtml += "<td>" + totalWeight.toFixed(2) + "</td>";
                        totalHtml += "<td>" + totalAmount.toFixed(2) + "</td>";
                        totalHtml += "</tr>";
                        $(tableID).append(totalHtml);
                    },
                    error: function() {
                        $(tableID).html('<tr><td colspan="10" class="text-center text-danger">Error loading data. Please try again.</td></tr>');
                    }
                });

            }
            else if(tabId=="#PURCHASE1"){
                var table = document.getElementById('Pur1TbleBody');
                while (table.rows.length > 0) {
                    table.deleteRow(0);
                }
                url="/rep-by-item-group/pur1";
                tableID="#Pur1TbleBody";

                $.ajax({
                    type: "GET",
                    url: url,
                    data:{
                        fromDate: fromDate,
                        toDate: toDate,
                        acc_id:acc_id,
                    }, 
                    beforeSend: function() {
                        $(tableID).html('<tr><td colspan="10" class="text-center">Loading Data Please Wait...</td></tr>');
                    },
                    success: function(result){
                        $('#pur1_from').text(formattedfromDate);
                        $('#pur1_to').text(formattedtoDate);
                        var selectedAcc = $('#acc_id').find("option:selected").text();
                        $('#pur1_acc').text(selectedAcc);
                        $(tableID).empty(); // Clear the loading message

                        let totalQty = 0;
                        let totalWeight = 0;
                        let totalAmount = 0;

                        $.each(result, function(k, v) {
                            let qty = parseFloat(v['qty'] || 0);
                            let weight = parseFloat(v['weight'] || 0);
                            let price = parseFloat(v['price'] || 0);
                            let amount = weight * price;

                            totalQty += qty;
                            totalWeight += weight;
                            totalAmount += amount;

                            let html = "<tr>";
                            html += "<td>" + (k + 1) + "</td>";
                            html += "<td>" + (v['pur_date'] ? moment(v['pur_date']).format('DD-MM-YY') : "") + "</td>";
                            html += `<td>${v['prefix'] ? v['prefix'] : ''} ${v['pur_id'] ? v['pur_id'] : ''}</td>`;
                            html += "<td>" + (v['ac_name'] ? v['ac_name'] : "") + "</td>";
                            html += "<td>" + (v['item_name'] ? v['item_name'] : "") + "</td>";
                            html += "<td>" + (v['qty'] ? v['qty'] : "0") + "</td>";
                            html += "<td>" + (v['price'] ? v['price'] : "0") + "</td>";
                            html += "<td>" + (v['weight'] ? v['weight'] : "0") + "</td>";
                            html += "<td>" + (amount ? amount.toFixed(2) : "") + "</td>";
                            html += "</tr>";
                            $(tableID).append(html);
                        });

                        // Append totals row
                        let totalHtml = "<tr class='font-weight-bold'>";
                        totalHtml += "<td colspan='5' style='text-align: right;'><strong>Total:</strong></td>";
                        totalHtml += "<td>" + totalQty.toFixed(0) + "</td>";
                        totalHtml += "<td></td>";
                        totalHtml += "<td>" + totalWeight.toFixed(2) + "</td>";
                        totalHtml += "<td>" + totalAmount.toFixed(2) + "</td>";
                        totalHtml += "</tr>";
                        $(tableID).append(totalHtml);
                    },
                    error: function() {
                        $(tableID).html('<tr><td colspan="10" class="text-center text-danger">Error loading data. Please try again.</td></tr>');
                    }
                });
            }
            else if(tabId=="#SALE2"){
                var table = document.getElementById('Sale2TbleBody');
                while (table.rows.length > 0) {
                    table.deleteRow(0);
                }
                url="/rep-by-item-group/sale2";
                tableID="#Sale2TbleBody";

                $.ajax({
                    type: "GET",
                    url: url,
                    data:{
                        fromDate: fromDate,
                        toDate: toDate,
                        acc_id:acc_id,
                    }, 
                    beforeSend: function() {
                        $(tableID).html('<tr><td colspan="10" class="text-center">Loading Data Please Wait...</td></tr>');
                    },
                    success: function(result){
                        $('#sale2_from').text(formattedfromDate);
                        $('#sale2_to').text(formattedtoDate);
                        var selectedAcc = $('#acc_id').find("option:selected").text();
                        $('#sale2_acc').text(selectedAcc);
                        $(tableID).empty(); // Clear the loading message

                        let totalQty = 0;
                        let totalWeight = 0;

                        $.each(result, function(k, v) {
                            let qty = parseFloat(v['qty'] || 0);
                            let weight = parseFloat(v['weight'] || 0);
                            let price = parseFloat(v['price'] || 0); 

                            totalQty += qty;
                            totalWeight += weight;

                            let html = "<tr>";
                            html += "<td>" + (k + 1) + "</td>";
                            html += "<td>" + (v['sa_date'] ? moment(v['sa_date']).format('DD-MM-YY') : "") + "</td>";
                            html += `<td>${v['prefix'] ? v['prefix'] : ''} ${v['Sal_inv_no'] ? v['Sal_inv_no'] : ''}</td>`;
                            html += "<td>" + (v['ac_name'] ? v['ac_name'] : "") + "</td>";
                            html += "<td>" + (v['item_name'] ? v['item_name'] : "") + "</td>";
                            html += "<td>" + (v['qty'] ? v['qty'] : "0") + "</td>";
                            html += "<td>" + (v['price'] ? v['price'] : "0") + "</td>";
                            html += "<td>" + (v['length'] ? v['length'] : "0") + "</td>";
                            html += "<td>" + (v['percent'] ? v['percent'] : "0") + "</td>";
                            html += "<td>" + (v['weight'] ? v['weight'] : "0") + "</td>";
                            html += "</tr>";
                            $(tableID).append(html);
                        });

                        // Append totals row
                        let totalHtml = "<tr class='font-weight-bold'>";
                        totalHtml += "<td colspan='5' style='text-align: right;'><strong>Total:</strong></td>";
                        totalHtml += "<td>" + totalQty.toFixed(0) + "</td>";
                        totalHtml += "<td></td>";
                        totalHtml += "<td></td>";
                        totalHtml += "<td></td>";
                        totalHtml += "<td>" + totalWeight.toFixed(2) + "</td>";
                        totalHtml += "</tr>";
                        $(tableID).append(totalHtml);
                        },
                    error: function() {
                        $(tableID).html('<tr><td colspan="10" class="text-center text-danger">Error loading data. Please try again.</td></tr>');
                    }
                });
            }
            else if(tabId=="#PURCHASE2"){
                var table = document.getElementById('Pur2TbleBody');
                while (table.rows.length > 0) {
                    table.deleteRow(0);
                }
                url="/rep-by-item-group/pur2";
                tableID="#Pur2TbleBody";

                $.ajax({
                    type: "GET",
                    url: url,
                    data:{
                        fromDate: fromDate,
                        toDate: toDate,
                        acc_id:acc_id,
                    }, 
                    beforeSend: function() {
                        $(tableID).html('<tr><td colspan="10" class="text-center">Loading Data Please Wait...</td></tr>');
                    },
                    success: function(result){
                        $('#pur2_from').text(formattedfromDate);
                        $('#pur2_to').text(formattedtoDate);
                        var selectedAcc = $('#acc_id').find("option:selected").text();
                        $('#pur2_acc').text(selectedAcc);
                        $(tableID).empty(); // Clear the loading message

                        let totalQty = 0;
                        let totalWeight = 0;

                        $.each(result, function(k, v) {
                            let qty = parseFloat(v['qty'] || 0);
                            let weight = parseFloat(v['weight'] || 0);
                            let price = parseFloat(v['price'] || 0); 

                            totalQty += qty;
                            totalWeight += weight;

                            let html = "<tr>";
                            html += "<td>" + (k + 1) + "</td>";
                            html += "<td>" + (v['sa_date'] ? moment(v['sa_date']).format('DD-MM-YY') : "") + "</td>";
                            html += `<td>${v['prefix'] ? v['prefix'] : ''} ${v['Sale_inv_no'] ? v['Sale_inv_no'] : ''}</td>`;
                            html += "<td>" + (v['ac_name'] ? v['ac_name'] : "") + "</td>";
                            html += "<td>" + (v['item_name'] ? v['item_name'] : "") + "</td>";
                            html += "<td>" + (v['qty'] ? v['qty'] : "0") + "</td>";
                            html += "<td>" + (v['price'] ? v['price'] : "0") + "</td>";
                            html += "<td>" + (v['length'] ? v['length'] : "0") + "</td>";
                            html += "<td>" + (v['percent'] ? v['percent'] : "0") + "</td>";
                            html += "<td>" + (v['weight'] ? v['weight'] : "0") + "</td>";
                            html += "</tr>";
                            $(tableID).append(html);
                        });

                        // Append totals row
                        let totalHtml = "<tr class='font-weight-bold'>";
                        totalHtml += "<td colspan='5' style='text-align: right;'><strong>Total:</strong></td>";
                        totalHtml += "<td>" + totalQty.toFixed(0) + "</td>";
                        totalHtml += "<td></td>";
                        totalHtml += "<td></td>";
                        totalHtml += "<td></td>";
                        totalHtml += "<td>" + totalWeight.toFixed(2) + "</td>";
                        totalHtml += "</tr>";
                        $(tableID).append(totalHtml);
                        },
                    error: function() {
                        $(tableID).html('<tr><td colspan="10" class="text-center text-danger">Error loading data. Please try again.</td></tr>');
                    }
                });
            }
           
        }

        function getReport() {
            const activeTabLink = document.querySelector('.nav-link.active');
            if (activeTabLink) {
                activeTabLink.click();
            }
        }

        function getInputValues() {
            return {
                fromDate: $('#fromDate').val(),
                toDate: $('#toDate').val(),
                acc_id: $('#acc_id').val()
            };
        }

        function printPDF(tabName) {
            const { fromDate, toDate, acc_id } = getInputValues();

            if (!fromDate || !toDate || !acc_id) {
                alert('Please fill in all required fields.');
                return;
            }

            if (tabName === "sale1") {
                window.open(`/rep-by-item-group/sale1/report?outputType=view&fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`, '_blank');

            }
            else if (tabName === "purchase1") {
                window.open(`/rep-by-item-group/pur1/report?outputType=view&fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`, '_blank');

            }
            else if (tabName === "sale2") {
                window.open(`/rep-by-item-group/sale2/report?outputType=view&fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`, '_blank');

            }
            else if (tabName === "purchase2") {
                window.open(`/rep-by-item-group/pur2/report?outputType=view&fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`, '_blank');

            }
        }

        function downloadPDF(tabName) {
            const { fromDate, toDate, acc_id } = getInputValues();

            if (!fromDate || !toDate || !acc_id) {
                alert('Please fill in all required fields.');
                return;
            }
            if (tabName === "sale1") {
                window.location.href = `/rep-by-item-group/sale1/report?outputType=download&fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`;
            }
            else if (tabName === "purchase1") {
                window.location.href = `/rep-by-item-group/pur1/report?outputType=download&fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`;
            }
            else if (tabName === "sale2") {
                window.location.href = `/rep-by-item-group/sale2/report?outputType=download&fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`;
            }
            else if (tabName === "purchase2") {
                window.location.href = `/rep-by-item-group/pur2/report?outputType=download&fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`;
            }
        }

       
    </script>
</html>