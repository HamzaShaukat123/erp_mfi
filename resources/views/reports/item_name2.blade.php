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
                                <label class="col-form-label"><strong>Item Name</strong></label>
                                <select data-plugin-selecttwo class="form-control select2-js"  id="acc_id">
                                    <option value="" disabled selected>Select Item</option>
                                    @foreach($items2 as $key => $row)	
                                        <option value="{{$row->it_cod}}">{{$row->item_name}}</option>
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
                                <a class="nav-link nav-link-rep" data-bs-target="#SALE" href="#SALE" data-bs-toggle="tab">Sale</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link nav-link-rep" data-bs-target="#PURCHASE" href="#PURCHASE" data-bs-toggle="tab">Purchase</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            
                            <div id="SALE" class="tab-pane">
                                <div class="row form-group pb-3">
                                    <div class="col-lg-6">
                                        <div class="bill-to">
                                            <h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold" style="display: flex; align-items: center;">
                                                <span style="color: #17365D;">From: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="sale_from"></span>
                                            
                                                <span style="flex: 0.3;"></span> <!-- Spacer to push the "To" to the right -->
                                            
                                                <span style="color: #17365D;">To: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="sale_to"></span>
                                            </h4>
                                            
                                            <h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
                                                <span style="color:#17365D;font-size:20px;">Item Name: &nbsp;</span>
                                                <span style="font-weight:400; color:rgb(238, 19, 19);font-size:20px;" id="sale_acc"></span>
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 text-end">
                                        <a class="mb-1 mt-1 me-1 btn btn-warning" aria-label="Download" onclick="downloadPDF('sale')"><i class="fa fa-download"></i> Download</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-danger" aria-label="Print PDF" onclick="printPDF('sale')"><i class="fa fa-file-pdf"></i> Print PDF</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-success" aria-label="Export to Excel" onclick="downloadExcel('sale')"><i class="fa fa-file-excel"></i> Excel</a>      
                                    </div>
                                    <div class="col-12 mt-4">
                                        <table class="table table-bordered table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th>S/No</th>
                                                    <th>Sales Date</th>
                                                    <th>Inv No.</th>
                                                    <th>Account Name</th>
                                                    <th>Qty</th>
                                                    <th>Price</th>
                                                    <th>Length</th>
                                                    <th>Percent</th>
                                                    <th>Weight</th>
                                                </tr>
                                            </thead>
                                            <tbody id="SaleTbleBody">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="PURCHASE" class="tab-pane">
                                <div class="row form-group pb-3">
                                    <div class="col-lg-6">
                                        <div class="bill-to">
                                            <h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold" style="display: flex; align-items: center;">
                                                <span style="color: #17365D;">From: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="pur_from"></span>
                                            
                                                <span style="flex: 0.3;"></span> <!-- Spacer to push the "To" to the right -->
                                            
                                                <span style="color: #17365D;">To: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="pur_to"></span>
                                            </h4>
                                            
                                            <h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
                                                <span style="color:#17365D;font-size:20px;">Item Name: &nbsp;</span>
                                                <span style="font-weight:400; color:rgb(238, 19, 19);font-size:20px;" id="pur_acc"></span>
                                            </h4>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-6 text-end">
                                        <a class="mb-1 mt-1 me-1 btn btn-warning" aria-label="Download" onclick="downloadPDF('purchase')"><i class="fa fa-download"></i> Download</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-danger" aria-label="Print PDF" onclick="printPDF('purchase')"><i class="fa fa-file-pdf"></i> Print PDF</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-success" aria-label="Export to Excel" onclick="downloadExcel('purchase')"><i class="fa fa-file-excel"></i> Excel</a>   
                                    </div>
                                    
                                    <div class="col-12 mt-4">
                                        <table class="table table-bordered table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th>S/No</th>
                                                    <th>Sales Date</th>
                                                    <th>Inv No.</th>
                                                    <th>Account Name</th>
                                                    <th>Qty</th>
                                                    <th>Price</th>
                                                    <th>Length</th>
                                                    <th>Percent</th>
                                                    <th>Weight</th>
                                                </tr>
                                            </thead>
                                            <tbody id="PurTbleBody">

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

            if(tabId=="#SALE"){
                var table = document.getElementById('SaleTbleBody');
                while (table.rows.length > 0) {
                    table.deleteRow(0);
                }
                url="/rep-by-item-name2/sale";
                tableID="#SaleTbleBody";

                $.ajax({
                    type: "GET",
                    url: url,
                    data:{
                        fromDate: fromDate,
                        toDate: toDate,
                        acc_id:acc_id,
                    }, 
                    beforeSend: function() {
                        $(tableID).html('<tr><td colspan="9" class="text-center">Loading Data Please Wait...</td></tr>');
                    },
                    success: function(result){
                        $('#sale_from').text(formattedfromDate);
                        $('#sale_to').text(formattedtoDate);
                        var selectedAcc = $('#acc_id').find("option:selected").text();
                        $('#sale_acc').text(selectedAcc);
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
                        totalHtml += "<td colspan='4' style='text-align: right;'><strong>Total:</strong></td>";
                        totalHtml += "<td>" + totalQty.toFixed(0) + "</td>";
                        totalHtml += "<td></td>";
                        totalHtml += "<td></td>";
                        totalHtml += "<td></td>";
                        totalHtml += "<td>" + totalWeight.toFixed(2) + "</td>";
                        totalHtml += "</tr>";
                        $(tableID).append(totalHtml);
                        },
                    error: function() {
                        $(tableID).html('<tr><td colspan="9" class="text-center text-danger">Error loading data. Please try again.</td></tr>');
                    }
                });
            }
            else if(tabId=="#PURCHASE"){
                var table = document.getElementById('PurTbleBody');
                while (table.rows.length > 0) {
                    table.deleteRow(0);
                }
                url="/rep-by-item-name2/pur";
                tableID="#PurTbleBody";

                $.ajax({
                    type: "GET",
                    url: url,
                    data:{
                        fromDate: fromDate,
                        toDate: toDate,
                        acc_id:acc_id,
                    }, 
                    beforeSend: function() {
                        $(tableID).html('<tr><td colspan="9" class="text-center">Loading Data Please Wait...</td></tr>');
                    },
                    success: function(result){
                        $('#pur_from').text(formattedfromDate);
                        $('#pur_to').text(formattedtoDate);
                        var selectedAcc = $('#acc_id').find("option:selected").text();
                        $('#pur_acc').text(selectedAcc);
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
                        totalHtml += "<td colspan='4' style='text-align: right;'><strong>Total:</strong></td>";
                        totalHtml += "<td>" + totalQty.toFixed(0) + "</td>";
                        totalHtml += "<td></td>";
                        totalHtml += "<td></td>";
                        totalHtml += "<td></td>";
                        totalHtml += "<td>" + totalWeight.toFixed(2) + "</td>";
                        totalHtml += "</tr>";
                        $(tableID).append(totalHtml);
                        },
                    error: function() {
                        $(tableID).html('<tr><td colspan="9" class="text-center text-danger">Error loading data. Please try again.</td></tr>');
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

            if (tabName === "sale") {
                window.open(`/rep-by-item-name2/sale/report?outputType=view&fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`, '_blank');

            }
            else if (tabName === "purchase") {
                window.open(`/rep-by-item-name2/pur/report?outputType=view&fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`, '_blank');

            }
        }

        function downloadPDF(tabName) {
            const { fromDate, toDate, acc_id } = getInputValues();

            if (!fromDate || !toDate || !acc_id) {
                alert('Please fill in all required fields.');
                return;
            }
            if (tabName === "sale") {
                window.location.href = `/rep-by-item-name2/sale/report?outputType=download&fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`;
            }
            else if (tabName === "purchase") {
                window.location.href = `/rep-by-item-name2/pur/report?outputType=download&fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`;
            }
        }

       
    </script>
</html>