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
                                    <option value="" disabled selected>Item Group</option>
                                    @foreach($items as $key => $row)	
                                        <option value="{{$row->item_group_cod}}">{{$row->group_name}}</option>
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
                                <a class="nav-link nav-link-rep" data-bs-target="#SA" href="#SA" data-bs-toggle="tab">Stock All</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link nav-link-rep" data-bs-target="#SI" href="#SI" data-bs-toggle="tab">Stock In</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link nav-link-rep" data-bs-target="#SO" href="#SO" data-bs-toggle="tab">Stock Out</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link nav-link-rep" data-bs-target="#SAT" href="#SAT" data-bs-toggle="tab">Stock All Tabuller</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div id="SA" class="tab-pane">
                                <div class="row form-group pb-3">
                                    <div class="col-lg-6 ">
                                        <div class="bill-to">
                                            <h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold" style="display: flex; align-items: center;">
                                                <span style="color: #17365D;">From: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="sa_from"></span>
                                            
                                                <span style="flex: 0.3;"></span> <!-- Spacer to push the "To" to the right -->
                                            
                                                <span style="color: #17365D;">To: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="sa_to"></span>
                                            </h4>
                                                                
                                            <h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
                                                <span style="color:#17365D;font-size:20px;">Item Group: &nbsp;</span>
                                                <span style="font-weight:400; color:rgb(238, 19, 19);font-size:20px;" id="sa_acc"></span>
                                            </h4>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-6 text-end">
                                        <a class="mb-1 mt-1 me-1 btn btn-warning" aria-label="Download" onclick="downloadPDF('SA')"><i class="fa fa-download"></i> Download</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-danger" aria-label="Print PDF" onclick="printPDF('SA')"><i class="fa fa-file-pdf"></i> Print PDF</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-success" aria-label="Export to Excel" onclick="downloadExcel('SA')"><i class="fa fa-file-excel"></i> Excel</a>   
                                    </div>
                                    
                                    <div class="col-12 mt-4">
                                        <table class="table table-bordered table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th>S/No</th>
                                                    <th>Item Name</th>
                                                    <th>Remarks</th>
                                                    <th>Quantity In Hand</th>
                                                    <th>Weight In Hand</th>
                                                </tr>
                                            </thead>
                                            <tbody id="SATbleBody">
                                                    
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div id="SI" class="tab-pane">
                                <div class="row form-group pb-3">
                                    <div class="col-lg-6 ">
                                        <div class="bill-to">
                                            <h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold" style="display: flex; align-items: center;">
                                                <span style="color: #17365D;">From: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="si_from"></span>
                                            
                                                <span style="flex: 0.3;"></span> <!-- Spacer to push the "To" to the right -->
                                            
                                                <span style="color: #17365D;">To: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="si_to"></span>
                                            </h4>
                                            
                                    
                                            <h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
                                                <span style="color:#17365D;font-size:20px;">Item Group: &nbsp;</span>
                                                <span style="font-weight:400; color:rgb(238, 19, 19);font-size:20px;" id="si_acc"></span>
                                            </h4>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-6 text-end">
                                        <a class="mb-1 mt-1 me-1 btn btn-warning" aria-label="Download" onclick="downloadPDF('SI')">
                                            <i class="fa fa-download"></i> Download
                                        </a>
                                        <a class="mb-1 mt-1 me-1 btn btn-danger" aria-label="Print PDF" onclick="printPDF('SI')">
                                            <i class="fa fa-file-pdf"></i> Print PDF
                                        </a>
                                           <a class="mb-1 mt-1 me-1 btn btn-success" aria-label="Export to Excel" onclick="downloadExcel('SI')">
                                            <i class="fa fa-file-excel"></i> Excel</a>   
                                    </div>
                                    
                                    <div class="col-12 mt-4">
                                        <table class="table table-bordered table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th>S/No</th>
                                                    <th>Voucher#</th>
                                                    <th>Item Name</th>
                                                    <th>Party Name</th>
                                                    <th>Quantity</th>
                                                    <th>Weight</th>
                                                </tr>
                                            </thead>
                                            <tbody id="SITbleBody">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div id="SO" class="tab-pane">
                                <div class="row form-group pb-3">
                                    <div class="col-lg-6 ">
                                        <div class="bill-to">
                                            <h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold" style="display: flex; align-items: center;">
                                                <span style="color: #17365D;">From: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="so_from"></span>
                                            
                                                <span style="flex: 0.3;"></span> <!-- Spacer to push the "To" to the right -->
                                            
                                                <span style="color: #17365D;">To: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="so_to"></span>
                                            </h4>
                                            
                                    
                                            <h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
                                                <span style="color:#17365D;font-size:20px;">Item Group: &nbsp;</span>
                                                <span style="font-weight:400; color:rgb(238, 19, 19);font-size:20px;" id="so_acc"></span>
                                            </h4>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-6 text-end">
                                        <a class="mb-1 mt-1 me-1 btn btn-warning" aria-label="Download" onclick="downloadPDF('SO')">
                                            <i class="fa fa-download"></i> Download
                                        </a>
                                        <a class="mb-1 mt-1 me-1 btn btn-danger" aria-label="Print PDF" onclick="printPDF('SO')">
                                            <i class="fa fa-file-pdf"></i> Print PDF
                                        </a>
                                           <a class="mb-1 mt-1 me-1 btn btn-success" aria-label="Export to Excel" onclick="downloadExcel('SO ')">
                                            <i class="fa fa-file-excel"></i> Excel</a>   
                                    </div>
                                    
                                    <div class="col-12 mt-4">
                                        <table class="table table-bordered table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th>S/No</th>
                                                    <th>Voucher#</th>
                                                    <th>Item Name</th>
                                                    <th>Party Name</th>
                                                    <th>Quantity</th>
                                                    <th>Weight</th>
                                                </tr>
                                            </thead>
                                            <tbody id="SOTbleBody">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div id="SAT" class="tab-pane">
                                <div class="row form-group pb-3">
                                    <div class="col-lg-6 ">
                                        <div class="bill-to">
                                            <h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold" style="display: flex; align-items: center;">
                                                <span style="color: #17365D;">From: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="SAT_from"></span>
                                            
                                                <span style="flex: 0.3;"></span> <!-- Spacer to push the "To" to the right -->
                                            
                                                <span style="color: #17365D;">To: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="SAT_to"></span>
                                            </h4>
                                            
                                    
                                            <h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
                                                <span style="color:#17365D;font-size:20px;">Item Group: &nbsp;</span>
                                                <span style="font-weight:400; color:rgb(238, 19, 19);font-size:20px;" id="SAT_acc"></span>
                                            </h4>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-6 text-end">
                                        <a class="mb-1 mt-1 me-1 btn btn-info" aria-label="Print PDF" onclick="printPDFStar('SAT')" title="Print Star PDF">
                                            <i class="fa fa-file-pdf"></i> Print Star PDF
                                        </a>
                                        <a class="mb-1 mt-1 me-1 btn btn-warning " aria-label="Print PDF" onclick="printPDFFiltered('SAT')" title="Print Filtered PDF">
                                            <i class="fa fa-file-pdf"></i> Print Filtered PDF
                                        </a>
                                        <a class="mb-1 mt-1 me-1 btn btn-danger" aria-label="Print PDF" onclick="printPDF('SAT')" title="Print PDF">
                                            <i class="fa fa-file-pdf"></i> Print PDF
                                        </a>
                                        <a class="mb-1 mt-1 me-1 btn btn-success" aria-label="Export to Excel" onclick="downloadExcel('SAT')" title="Export to Excel">
                                            <i class="fa fa-file-excel"></i> Excel
                                        </a>   
                                    </div>                                    
                                    
                                    <div class="col-12 mt-4" style="height: 775px; overflow-y: auto; padding: 0 !important;">
                                        <table class="table table-bordered table-striped mb-0" id="TSAThead">
                                            <thead style="position: sticky; top: 0; background-color: white;">
                                                <tr id="tableHeaderRow">
                                                    <!-- Dynamic headers will be appended here -->
                                                </tr>
                                            </thead>
                                            <tbody id="SATTble">
                                                <!-- Dynamic rows will be appended here -->
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

            if(tabId=="#SA"){
                var table = document.getElementById('SATbleBody');
                while (table.rows.length > 0) {
                    table.deleteRow(0);
                }
                url="/rep-godown-by-group-name/sa";
                tableID="#SATbleBody";

                $.ajax({
                    type: "GET",
                    url: url,
                    data:{
                        fromDate: fromDate,
                        toDate: toDate,
                        acc_id: acc_id,
                    }, 
                    success: function(result){
                        $('#sa_from').text(formattedfromDate);
                        $('#sa_to').text(formattedtoDate);
                        var selectedAcc = $('#acc_id').find("option:selected").text();
                        $('#sa_acc').text(selectedAcc);

                        $(tableID).empty(); // Clear the loading message

                        var totalop = 0; // Variable to accumulate total
                        var totalwt = 0; // Variable to accumulate total

                        $.each(result, function(k,v){
                            var op = v['opp_bal'] ? parseFloat(v['opp_bal']) : 0;
                            var wt = v['wt'] ? parseFloat(v['wt']) : 0;
                            totalop += op; // Add to total
                            totalwt += wt; // Add to total

                            var html="<tr>";
                            html += "<td>"+(k+1)+"</td>"
                            html += "<td>" + (v['item_name'] ? v['item_name'] : "") +"</td>";
                            html += "<td>" + (v['item_remark'] ? v['item_remark'] : "") + "</td>";
                            html += "<td>" + (op ? op.toFixed(0) : "") + "</td>";
                            html += "<td>" + (wt ? wt.toFixed(0) : "") + "</td>";
                            html +="</tr>";
                            $(tableID).append(html);
                        });

                        // Display the total in the last row or specific cell
                        var totalRow = "<tr><td colspan='3' style='text-align: right;'><strong>Total:</strong></td>";
                        totalRow += "<td class='text-danger'><strong>" + totalop.toFixed(0) + "</strong></td>";
                        totalRow += "<td class='text-danger'><strong>" + totalwt.toFixed(0) + "</strong></td></tr>";
                        $(tableID).append(totalRow);
                    },
                    error: function(){
                        alert("error");
                    }
                });
            }
            
            else if(tabId=="#SI"){
                var table = document.getElementById('SITbleBody');
                while (table.rows.length > 0) {
                    table.deleteRow(0);
                }
                url="/rep-godown-by-group-name/si";
                tableID="#SITbleBody";

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
                        $('#si_from').text(formattedfromDate);
                        $('#si_to').text(formattedtoDate);
                        var selectedAcc = $('#acc_id').find("option:selected").text();
                        $('#si_acc').text(selectedAcc);

                        $(tableID).empty(); // Clear the loading message

                        
                        var totalop = 0; // Variable to accumulate total
                        var totalwt = 0; // Variable to accumulate total

                        $.each(result, function(k,v){
                            var op = v['Sales_qty'] ? parseFloat(v['Sales_qty']) : 0;
                            var wt = v['wt'] ? parseFloat(v['wt']) : 0;
                            totalop += op; // Add to total
                            totalwt += wt; // Add to total
                            var html="<tr>";
                            html += "<td>"+(k+1)+"</td>"
                            html += "<td>" + (v['Sal_inv_no'] ? v['Sal_inv_no'] : "") + "</td>";
                            html += "<td>" + (v['item_name'] ? v['item_name'] : "") +"</td>";
                            html += "<td>" + (v['ac_name'] ? v['ac_name'] : "") + "</td>";
                            html += "<td>" + (op ? op.toFixed(0) : "") + "</td>";
                            html += "<td>" + (wt ? wt.toFixed(0) : "") + "</td>";
                            html +="</tr>";
                            $(tableID).append(html);
                        });

                        
                        // Display the total in the last row or specific cell
                        var totalRow = "<tr><td colspan='4' style='text-align: right;'><strong>Total:</strong></td>";
                        totalRow += "<td class='text-danger'><strong>" + totalop.toFixed(0) + "</strong></td>";
                        totalRow += "<td class='text-danger'><strong>" + totalwt.toFixed(0) + "</strong></td></tr>";
                        $(tableID).append(totalRow);

                    },
                    error: function(){
                        alert("error");
                    }
                });
            }
            
            else if (tabId === "#SO") {
                let table = document.getElementById('SOTbleBody');
                
                // Clear the table
                while (table.rows.length > 0) {
                    table.deleteRow(0);
                }

                const url = "/rep-godown-by-group-name/so";
                const tableID = "#SOTbleBody";

                // Helper function to safely access data
                const safeVal = (val) => val ? val : "";

                $.ajax({
                    type: "GET",
                    url: url,
                    data: {
                        fromDate: fromDate,
                        toDate: toDate,
                        acc_id: acc_id,
                    },
                    beforeSend: function() {
                        $(tableID).html('<tr><td colspan="8" class="text-center">Loading Data Please Wait...</td></tr>');
                    },
                    success: function (result) {
                        $('#so_from').text(formattedfromDate);
                        $('#so_to').text(formattedtoDate);

                        const selectedAcc = $('#acc_id').find("option:selected").text();
                        $('#so_acc').text(selectedAcc);

                        $(tableID).empty(); // Clear the loading message
                        var totalop = 0; // Variable to accumulate total
                        var totalwt = 0; // Variable to accumulate total

                        $.each(result, function(k,v){
                            var op = v['Sales_qty'] ? parseFloat(v['Sales_qty']) : 0;
                            var wt = v['wt'] ? parseFloat(v['wt']) : 0;
                            totalop += op; // Add to total
                            totalwt += wt; // Add to total
                            var html="<tr>";
                            html += "<td>"+(k+1)+"</td>"
                            html += "<td>" + (v['Sal_inv_no'] ? v['Sal_inv_no'] : "") + "</td>";
                            html += "<td>" + (v['item_name'] ? v['item_name'] : "") +"</td>";
                            html += "<td>" + (v['ac_name'] ? v['ac_name'] : "") + "</td>";
                            html += "<td>" + (op ? op.toFixed(0) : "") + "</td>";
                            html += "<td>" + (wt ? wt.toFixed(0) : "") + "</td>";
                            html +="</tr>";
                            $(tableID).append(html);
                        });

                         // Display the total in the last row or specific cell
                         var totalRow = "<tr><td colspan='4' style='text-align: right;'><strong>Total:</strong></td>";
                        totalRow += "<td class='text-danger'><strong>" + totalop.toFixed(0) + "</strong></td>";
                        totalRow += "<td class='text-danger'><strong>" + totalwt.toFixed(0) + "</strong></td></tr>";
                        $(tableID).append(totalRow);
                    },
                    error: function () {
                        $(tableID).html('<tr><td colspan="8" class="text-center text-danger">Error loading data. Please try again.</td></tr>');
                    }
                });
            }
            else if (tabId == "#SAT") {
    const tableBody = document.getElementById('SATTble');
    const tableHeader = document.getElementById('tableHeaderRow');

    // Clear existing rows and headers
    tableBody.innerHTML = '';
    tableHeader.innerHTML = '';

    const url = "/rep-godown-by-group-name/sat";

    $.ajax({
        type: "GET",
        url: url,
        data: {
            fromDate: fromDate,
            toDate: toDate,
            acc_id: acc_id,
        },
        beforeSend: function () {
            tableBody.innerHTML = '<tr><td colspan="13" class="text-center">Loading Data Please Wait...</td></tr>';
        },
        success: function (result) {
            $('#SAT_from').text(formattedfromDate);
            $('#SAT_to').text(formattedtoDate);

            const selectedAcc = $('#acc_id').find("option:selected").text();
            $('#SAT_acc').text(selectedAcc);
            // Clear loading message
            tableBody.innerHTML = '';
            tableHeader.innerHTML = '';

            // Process the data
            const processedData = result.map(item => {
                const itemChunks = item.item_name.split(' ');
                return {
                    ...item,
                    item_group: itemChunks[0] || '',
                    item_mm: itemChunks[1] || '',
                    item_name: itemChunks.slice(2).join(' ') || '',
                };
            });

            // Separate the items into three groups: ROUND X, SQR, and others
            const roundItems = processedData.filter(item => item.item_name.startsWith('ROUND X')).sort((a, b) => a.item_name.localeCompare(b.item_name));
            const sqrItems = processedData.filter(item => item.item_name.endsWith('SQR')).sort((a, b) => a.item_name.localeCompare(b.item_name));
            const otherItems = processedData.filter(item => !item.item_name.startsWith('ROUND X') && !item.item_name.endsWith('SQR')).sort((a, b) => a.item_name.localeCompare(b.item_name));

            // Merge the groups in the order: ROUND X, SQR, and others
            const orderedData = [...roundItems, ...sqrItems, ...otherItems];

            // Get unique headers
            const uniqueHeaders = [...new Set(orderedData.map(item => item.item_mm))].filter(Boolean);

            // Sort headers numerically
            uniqueHeaders.sort((a, b) => {
                const numA = parseInt(a, 10);
                const numB = parseInt(b, 10);
                return numA - numB;
            });

            // Append dynamic headers
            let headerHTML = '<th>Item Name</th>';
            uniqueHeaders.forEach(header => {
                headerHTML += `<th style="text-align: center;">${header}</th>`;
            });
            tableHeader.innerHTML = headerHTML;

            // Group items by `item_name`
            const groupedData = orderedData.reduce((acc, item) => {
                if (!acc[item.item_name]) acc[item.item_name] = [];
                acc[item.item_name].push(item);
                return acc;
            }, {});

            // Append rows dynamically
            Object.keys(groupedData).forEach(itemName => {
                let rowHTML = `<tr><td>${itemName}</td>`;
                uniqueHeaders.forEach(header => {
                    const item = groupedData[itemName].find(i => i.item_mm === header);
                    if (item && item.opp_bal !== null && item.opp_bal !== undefined && item.opp_bal !== '') {
                        const value = parseFloat(item.opp_bal); // Ensure opp_bal is treated as a number
                        if (value < 0) {
                            rowHTML += `<td style="text-align: center; color: red;">${value}</td>`;
                        } else if (value > 0) {
                            rowHTML += `<td style="text-align: center; color: red; font-weight: bold;">${value}</td>`;
                        } else {
                            rowHTML += `<td style="text-align: center;">${value}</td>`;
                        }
                    } else {
                        rowHTML += '<td style="text-align: center;"></td>';
                    }
                });
                rowHTML += '</tr>';
                tableBody.insertAdjacentHTML('beforeend', rowHTML);
            });

        },
        error: function () {
            tableBody.innerHTML = '<tr><td colspan="13" class="text-center text-danger">Error loading data. Please try again.</td></tr>';
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

        function downloadExcel(tabName) {
            const { fromDate, toDate, acc_id } = getInputValues();

            if (!fromDate || !toDate || !acc_id) {
                alert('Please fill in all required fields.');
                return;
            }

            if (tabName === "SI") {
                window.location.href = `/rep-godown-by-item-name/si/excel?fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`;
            }
        }

        function printPDF(tabName) {
            const { fromDate, toDate, acc_id } = getInputValues();

            if (!fromDate || !toDate || !acc_id) {
                alert('Please fill in all required fields.');
                return;
            }

            if (tabName === "SA") {
                window.open(`/rep-godown-by-group-name/sa/report?outputType=view&fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`, '_blank');
            }

            else if (tabName === "SI") {
                window.open(`/rep-godown-by-group-name/si/report?outputType=view&fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`, '_blank');
            }

            else if (tabName === "SO") {
                window.open(`/rep-godown-by-group-name/so/report?outputType=view&fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`, '_blank');
            }

            else if (tabName === "SAT") {
                window.open(`/rep-godown-by-group-name/sat/report?outputType=view&fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`, '_blank');
            }
        }


        function printPDFStar(tabName) {
            const { fromDate, toDate, acc_id } = getInputValues();

            if (!fromDate || !toDate || !acc_id) {
                alert('Please fill in all required fields.');
                return;
            }
            
            if (tabName === "SAT") {
                window.open(`/rep-godown-by-group-name/sat/star/report?outputType=view&fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`, '_blank');
            }
        }

        function printPDFFiltered(tabName) {
            const { fromDate, toDate, acc_id } = getInputValues();

            if (!fromDate || !toDate || !acc_id) {
                alert('Please fill in all required fields.');
                return;
            }

            if (tabName === "SAT") {
                window.open(`/rep-godown-by-group-name/sat/filtered/report?outputType=view&fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`, '_blank');
            }
        }


        function downloadPDF(tabName) {
            const { fromDate, toDate, acc_id } = getInputValues();

            if (!fromDate || !toDate || !acc_id) {
                alert('Please fill in all required fields.');
                return;
            }

            if (tabName === "SA") {
                window.location.href = `/rep-godown-by-group-name/sa/report?outputType=download&fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`;
            }

            else if (tabName === "SI") {
                window.location.href = `/rep-godown-by-group-name/si/report?outputType=download&fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`;
            }

            else if (tabName === "SO") {
                window.location.href = `/rep-godown-by-group-name/so/report?outputType=download&fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`;
            }

            else if (tabName === "SAT") {
                window.location.href = `/rep-godown-by-group-name/sat/report?outputType=download&fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`;
            }

        }
    </script>
</html>