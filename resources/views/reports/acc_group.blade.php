@include('../layouts.header')
	<body>
		<section class="body">
            @include('layouts.homepageheader')
			<div class="inner-wrapper cust-pad">
				@include('layouts.leftmenu')
				<section role="main" class="content-body">  
                    <div class="tabs">
                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a class="nav-link nav-link-rep" data-bs-target="#AG" href="#AG" data-bs-toggle="tab">Acc Group</a>
                            </li>
                            @if(session('user_role')==1 || session('user_role')==2)
                                <li class="nav-item">
                                    <a class="nav-link nav-link-rep" data-bs-target="#SHOA" href="#SHOA" data-bs-toggle="tab">SHOA</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link nav-link-rep" data-bs-target="#BA" href="#BA" data-bs-toggle="tab">Balance All</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link nav-link-rep" data-bs-target="#TB" href="#TB" data-bs-toggle="tab"> Trial Balance</a>
                                </li>
                            @endif
                        </ul>
                        <div class="tab-content">
                            <div id="AG" class="tab-pane">
                                <div class="row form-group pb-3">
                                    <div class="col-lg-8 row">
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label class="col-form-label"><strong>Account Group</strong></label>
                                                <select data-plugin-selecttwo class="form-control select2-js" id="ag_acc_id">
                                                    <option value="" disabled selected>Account Group</option>
                                                    @foreach($ac_group as $key => $row)    
                                                        <option value="{{$row->group_cod}}">{{$row->group_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <!-- Show Button Here -->
                                        <div class="col-lg-1">
                                            <a class="btn btn-primary" style="margin-top: 2.1rem;padding: 0.5rem 0.6rem;" onclick="tabChanged('#AG')"><i class="fa fa-filter"></i></a>
                                        </div>

                                        <!-- Show Today's Date Here -->
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label class="col-form-label"><strong>Date</strong></label>
                                                <input type="date" readonly class="form-control" id="toDate" value="<?php echo date('Y-m-d'); ?>">
                                            </div>
                                        </div>
                                    </div>
                            
                                    <div class="col-lg-4 text-end">
                                        <a class="mb-1 mt-1 me-1 btn btn-warning" aria-label="Download" onclick="downloadPDF('AG')"><i class="fa fa-download"></i> Download</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-danger" aria-label="Print PDF" onclick="printPDF('AG')"><i class="fa fa-file-pdf"></i> Print PDF</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-success" aria-label="Export to Excel" onclick="downloadExcel('AG')"><i class="fa fa-file-excel"></i> Excel</a>   
                                    </div>
                                    
                                    <div class="col-12 mt-4">
                                        <table class="table table-bordered table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th>S/No</th>
                                                    <th>AC-Code</th>
                                                    <th>Account Name</th>
                                                    <th>Address</th>
                                                    <th>Phone</th>
                                                    <th>Debit</th>
                                                    <th>Credit</th>
                                                </tr>
                                            </thead>
                                            <tbody id="AGTbleBody">
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div id="SHOA" class="tab-pane">
                                <div class="row form-group pb-3">
                                    <div class="col-lg-8 row">
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label class="col-form-label"><strong>Sub Head Of Account</strong></label>
                                                <select data-plugin-selecttwo class="form-control select2-js" id="shoa_acc_id">
                                                    <option value="" disabled selected>Sub Head Of Account</option>
                                                    @foreach($sub_head_of_acc as $key => $row)	
                                                        <option value="{{$row->id}}">{{$row->sub}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Show Button Here -->
                                        <div class="col-lg-1">
                                            <a class="btn btn-primary" style="margin-top: 2.1rem;padding: 0.5rem 0.6rem;" onclick="tabChanged('#SHOA')"><i class="fa fa-filter"></i></a>
                                        </div>

                                        <!-- Show Today's Date Here -->
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label class="col-form-label"><strong>Date</strong></label>
                                                <input type="date" readonly class="form-control" id="toDate" value="<?php echo date('Y-m-d'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-4 text-end">
                                        <a class="mb-1 mt-1 me-1 btn btn-warning" aria-label="Download" onclick="downloadPDF('SHOA')"><i class="fa fa-download"></i> Download</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-danger" aria-label="Print PDF" onclick="printPDF('SHOA')"><i class="fa fa-file-pdf"></i> Print PDF</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-success" aria-label="Export to Excel" onclick="downloadExcel('SHOA')"><i class="fa fa-file-excel"></i> Excel</a>   
                                    </div>
                                    
                                    <div class="col-12 mt-4">
                                        <table class="table table-bordered table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th>S/No</th>
                                                    <th>AC-Code</th>
                                                    <th>Account Name</th>
                                                    <th>Address</th>
                                                    <th>Phone</th>
                                                    <th>Debit</th>
                                                    <th>Credit</th>
                                                </tr>
                                            </thead>
                                            <tbody id="SHOATbleBody">
                                                    
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div id="BA" class="tab-pane">
                                <div class="row form-group pb-3">
                                    <div class="col-lg-12 text-end">
                                        <a class="mb-1 mt-1 me-1 btn btn-warning" aria-label="Download" onclick="downloadPDF('BA')"><i class="fa fa-download"></i> Download</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-danger" aria-label="Print PDF" onclick="printPDF('BA')"><i class="fa fa-file-pdf"></i> Print PDF</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-success" aria-label="Export to Excel" onclick="downloadExcel('BA')"><i class="fa fa-file-excel"></i> Excel</a>   
                                    </div>
                                    
                                    <div class="col-12 mt-4" id="BATbleBody">
                                        
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
                if(tabId=="#BA"){
                    tabChanged(tabId);
                }
            });
        });

        function tabChanged(tabId) {

            if(tabId=="#AG"){
                var acc_id = $('#ag_acc_id').val()

                if (!acc_id) {
                    alert('Please fill in all required fields.');
                    return;
                }
                var table = document.getElementById('AGTbleBody');

                while (table.rows.length > 0) {
                    table.deleteRow(0);
                }

                url="/rep-by-acc-grp/ag";
                tableID="#AGTbleBody";

                $.ajax({
                    type: "GET",
                    url: url,
                    data:{
                        acc_id: acc_id,
                    },
                    beforeSend: function() {
                        $(tableID).html('<tr><td colspan="7" class="text-center">Loading Data Please Wait...</td></tr>');
                    },
                    success: function(result){
                        $(tableID).empty(); // Clear the loading message
                        
                        var totalDrAmt = 0; // Variable to accumulate total
                        var totalCrAmt = 0; // Variable to accumulate total

                        $.each(result, function(k,v){

                            var drAmt = v['Debit'] ? parseFloat(v['Debit']) : 0;
                            var crAmt = v['Credit'] ? parseFloat(v['Credit']) : 0;
                            totalDrAmt += drAmt; // Add to total
                            totalCrAmt += crAmt; // Add to total

                            var html="<tr>";
                            html += "<td>"+(k+1)+"</td>"
                            html += "<td>" + (v['ac_code'] ? v['ac_code'] : "") +"</td>";
                            html += "<td>" + (v['ac_name'] ? v['ac_name'] : "") + "</td>";
                            html += "<td>" + (v['address'] ? v['address'] : "") + "</td>";
                            html += "<td>" + (v['phone_no'] ? v['phone_no'] : "") + "</td>";
                            html += "<td>" + (drAmt ? drAmt.toLocaleString("en-US", { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : "") + "</td>";
                            html += "<td>" + (crAmt ? crAmt.toLocaleString("en-US", { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : "") + "</td>";
                            html +="</tr>";
                            $(tableID).append(html);
                        });

                        var totalRow = "<tr><td colspan='5' style='text-align: right;'><strong>Total:</strong></td>";
                        totalRow += "<td class='text-danger'><strong>" + totalDrAmt.toLocaleString("en-US", { minimumFractionDigits: 0, maximumFractionDigits: 0 }) + "</strong></td>";
                        totalRow += "<td class='text-danger'><strong>" + totalCrAmt.toLocaleString("en-US", { minimumFractionDigits: 0, maximumFractionDigits: 0 }) + "</strong></td></tr>";

                        $(tableID).append(totalRow);

                        var balanceAmt = totalDrAmt + totalCrAmt;
                        var totalRow = "<tr><td colspan='5' style='text-align: right;'><strong>Balance:</strong></td>";
                        totalRow += "<td colspan='2' class='text-danger text-center'><strong>" + balanceAmt.toLocaleString("en-US", { minimumFractionDigits: 0, maximumFractionDigits: 0 }) + "</strong></td>";
                        $(tableID).append(totalRow);

                     },
                    error: function(){
                        $(tableID).html('<tr><td colspan="7" class="text-center text-danger">Error loading data. Please try again.</td></tr>');
                    }
                });
            }

            else if(tabId=="#SHOA"){
                var acc_id = $('#shoa_acc_id').val()

                if (!acc_id) {
                    alert('Please fill in all required fields.');
                    return;
                }

                var table = document.getElementById('SHOATbleBody');
                while (table.rows.length > 0) {
                    table.deleteRow(0);
                }
                url="/rep-by-acc-grp/shoa";
                tableID="#SHOATbleBody";

                $.ajax({
                    type: "GET",
                    url: url,
                    data:{
                        acc_id: acc_id,
                    },
                    beforeSend: function() {
                        $(tableID).html('<tr><td colspan="7" class="text-center">Loading Data Please Wait...</td></tr>');
                    },
                    success: function(result){
                        $(tableID).empty(); // Clear the loading message
                        
                        var totalDrAmt = 0; // Variable to accumulate total
                        var totalCrAmt = 0; // Variable to accumulate total

                        $.each(result, function(k,v){

                            var drAmt = v['Debit'] ? parseFloat(v['Debit']) : 0;
                            var crAmt = v['Credit'] ? parseFloat(v['Credit']) : 0;
                            totalDrAmt += drAmt; // Add to total
                            totalCrAmt += crAmt; // Add to total

                            var html="<tr>";
                            html += "<td>"+(k+1)+"</td>"
                            html += "<td>" + (v['ac_code'] ? v['ac_code'] : "") +"</td>";
                            html += "<td>" + (v['ac_name'] ? v['ac_name'] : "") + "</td>";
                            html += "<td>" + (v['address'] ? v['address'] : "") + "</td>";
                            html += "<td>" + (v['phone_no'] ? v['phone_no'] : "") + "</td>";
                            html += "<td>" + (drAmt ? drAmt.toLocaleString("en-US", { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : "") + "</td>";
                            html += "<td>" + (crAmt ? crAmt.toLocaleString("en-US", { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : "") + "</td>";
                            html +="</tr>";
                            $(tableID).append(html);
                        });

                        var totalRow = "<tr><td colspan='5' style='text-align: right;'><strong>Total:</strong></td>";
                        totalRow += "<td class='text-danger'><strong>" + totalDrAmt.toLocaleString("en-US", { minimumFractionDigits: 0, maximumFractionDigits: 0 }) + "</strong></td>";
                        totalRow += "<td class='text-danger'><strong>" + totalCrAmt.toLocaleString("en-US", { minimumFractionDigits: 0, maximumFractionDigits: 0 }) + "</strong></td></tr>";
                        $(tableID).append(totalRow);

                        var balanceAmt = totalDrAmt + totalCrAmt;
                        var totalRow = "<tr><td colspan='5' style='text-align: right;'><strong>Balance:</strong></td>";
                        totalRow += "<td colspan='2' class='text-danger text-center'><strong>" + balanceAmt.toLocaleString("en-US", { minimumFractionDigits: 0, maximumFractionDigits: 0 }) + "</strong></td>";
                        $(tableID).append(totalRow);

                    },
                    error: function(){
                        $(tableID).html('<tr><td colspan="6" class="text-center text-danger">Error loading data. Please try again.</td></tr>');
                    }
                });
            }

            else if (tabId === "#BA") {
            // Cache table body element
            const $tableBody = $('#BATbleBody');
            
            // Define URL and table ID
            const url = "/rep-by-acc-grp/ba";
            
            // Start the AJAX request
            $.ajax({
                type: "GET",
                url: url,
                beforeSend: function() {
                    // Show loading message before the data is fetched
                    $tableBody.html('<table class="table table-bordered table-striped mb-0"><tr><td colspan="6" class="text-center">Loading Data Please Wait...</td></tr></table>');
                },
                success: function(result) {
                    // Clear previous content
                    $tableBody.empty();

                    // Group data by head and subhead
                    const AllData = groupByHeadAndSub(result);

                    // Initialize HTML for the table
                    let html = '';

                    // Iterate through each head
                    $.each(AllData, function(headCount, heads) {
                        // Table structure for each head
                        html += `<table class='table table-bordered table-striped mb-0'>
                                    <thead>
                                        <tr><th class="text-danger" colspan="6" style="text-align:center; font-size:22px;">${headCount}</th></tr>
                                        <tr><th>S/No</th><th>AC</th><th>Account Name</th><th>Address</th><th>Debit</th><th>Credit</th></tr>
                                    </thead>`;

                        // Initialize subtotals for each head
                        let subtotaldebit = 0, subtotalcredit = 0;

                        // Iterate through each subhead
                        $.each(heads, function(subHeadCount, subheads) {
                            html += `<tbody>
                                        <tr><td colspan='6' style="background-color: #cfe8e3; text-align: center; font-weight: bold;"'>${subHeadCount}</td></tr>`;

                            // Iterate through each item in the subhead
                            $.each(subheads, function(itemCount, item) {
                                html += `<tr>
                                            <td>${itemCount + 1}</td>
                                            <td>${item.ac_code || ""}</td>
                                            <td>${item.ac_name || ""}</td>
                                            <td>${item.address || ""}</td>
                                            <td>${item.Debit ? Number(item.Debit).toLocaleString("en-US", { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : ""}</td>
                                            <td>${item.Credit ? Number(item.Credit).toLocaleString("en-US", { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : ""}</td>

                                        </tr>`;

                                // Add to subtotals
                                subtotaldebit += item.Debit || 0;
                                subtotalcredit += item.Credit || 0;
                            });

                            html += `</tbody>`;

                            // Append subtotal for the subhead
                            html += `
                            <tr style="background-color: #FFFFFF;">
                                <td colspan="4" class="text-center"><strong>Sub Total for ${subHeadCount}</strong></td>
                                <td class="text-danger"><strong>${subtotaldebit.toLocaleString("en-US", { minimumFractionDigits: 0, maximumFractionDigits: 0 })}</strong></td>
                                <td class="text-danger"><strong>${subtotalcredit.toLocaleString("en-US", { minimumFractionDigits: 0, maximumFractionDigits: 0 })}</strong></td>

                            </tr>`;

                        });

                        // Append the subtotal for the head
                        html += `</table>`;
                    });

                    // Append all the generated HTML at once
                    $tableBody.html(html);
                },
                error: function() {
                    // Show error message if the AJAX request fails
                    $tableBody.html('<table class="table table-bordered table-striped mb-0"><tr><td colspan="6" class="text-center text-danger">Error loading data. Please try again.</td></tr></table>');
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

        function downloadExcel(tabName) {

            if (tabName === "AG") {
                var acc_id = $('#ag_acc_id').val()

                if (!acc_id) {
                    alert('Please fill in all required fields.');
                    return;
                }

                window.location.href = `/rep-by-acc-grp/ag/excel?acc_id=${acc_id}`;
            }

            else if (tabName === "SHOA") {
                var acc_id = $('#shoa_acc_id').val()

                if (!acc_id) {
                    alert('Please fill in all required fields.');
                    return;
                }

                window.location.href = `/rep-by-acc-grp/shoa/excel?acc_id=${acc_id}`;
            }

            else if (tabName === "BA") {
                window.location.href = `/rep-by-acc-grp/ba/excel`;
            }
        }

        function printPDF(tabName) {

            if (tabName === "AG") {
                var acc_id = $('#ag_acc_id').val()

                if (!acc_id) {
                    alert('Please fill in all required fields.');
                    return;
                }

                window.open(`/rep-by-acc-grp/ag/report?outputType=view&acc_id=${acc_id}`, '_blank');
            }

            else if (tabName === "SHOA") {
                var acc_id = $('#shoa_acc_id').val()

                if (!acc_id) {
                    alert('Please fill in all required fields.');
                    return;
                }
                window.open(`/rep-by-acc-grp/shoa/report?outputType=view&acc_id=${acc_id}`, '_blank');
            }

            else if (tabName === "BA") {

                window.open(`/rep-by-acc-grp/ba/report?outputType=view`, '_blank');
            }
        }

        function downloadPDF(tabName) {
            if (tabName === "AG") {
                var acc_id = $('#ag_acc_id').val()

                if (!acc_id) {
                    alert('Please fill in all required fields.');
                    return;
                }
                window.location.href = `/rep-by-acc-grp/ag/report?outputType=download&acc_id=${acc_id}`;
            }

            else if (tabName === "SHOA") {
                var acc_id = $('#shoa_acc_id').val()

                if (!acc_id) {
                    alert('Please fill in all required fields.');
                    return;
                }
                window.location.href = `/rep-by-acc-grp/shoa/report?outputType=download&acc_id=${acc_id}`;
            }

            else if (tabName === "BA") {
                
                window.open(`/rep-by-acc-grp/ba/report?outputType=view`, '_blank');
            }
        }

        function groupByHeadAndSub(data) {
            const groupedData = {};

            // Loop through all available heads (keys in the data object)
            Object.keys(data).forEach(head => {
                groupedData[head] = {}; // Initialize the head category

                // Loop through each item under the current head (Assets or Liabilities, etc.)
                data[head].forEach(item => {
                    const sub = item.sub; // Get the subhead from each item
                    if (!groupedData[head][sub]) {
                        groupedData[head][sub] = []; // Initialize subhead if it doesn't exist
                    }
                    // Push the item into the appropriate subhead category under the head
                    groupedData[head][sub].push(item);
                });
            });

            return groupedData;
        }

    </script>
</html>