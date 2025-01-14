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
                                <select data-plugin-selecttwo class="form-control select2-js" id="acc_id">
                                    <option value="" disabled selected>Item Group</option>
                                    @foreach($item_group as $key => $row)	
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
                                <a class="nav-link nav-link-rep" data-bs-target="#Comm" href="#Comm" data-bs-toggle="tab">Commissions</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div id="Comm" class="tab-pane">
                                <div class="row form-group pb-3">
                                    <div class="col-lg-6 ">
                                        <div class="bill-to">
                                            <h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold" style="display: flex; align-items: center;">
                                                <span style="color: #17365D;">From: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="comm_from"></span>
                                            
                                                <span style="flex: 0.3;"></span> <!-- Spacer to push the "To" to the right -->
                                            
                                                <span style="color: #17365D;">To: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="comm_to"></span>
                                            </h4>
                                                                
                                            <h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
                                                <span style="color:#17365D;font-size:20px;">Item Group: &nbsp;</span>
                                                <span style="font-weight:400; color:rgb(238, 19, 19);font-size:20px;" id="comm_acc"></span>
                                            </h4>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-6 text-end">
                                        <a class="mb-1 mt-1 me-1 btn btn-warning" aria-label="Download" onclick="downloadPDF('comm')"><i class="fa fa-download"></i> Download</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-danger" aria-label="Print PDF" onclick="printPDF('comm')"><i class="fa fa-file-pdf"></i> Print PDF</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-success" aria-label="Export to Excel" onclick="downloadExcel('comm')"><i class="fa fa-file-excel"></i> Excel</a>   
                                    </div>
                                    
                                    <div class="col-12 mt-4">
                                        <table class="table table-bordered table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th>S/No</th>
                                                    <th>Date</th>
                                                    <th>Inv No.</th>
                                                    <th>Ord No.</th>
                                                    <th>Basic Amnt</th>
                                                    <th>GST / I-Tax</th>
                                                    <th>Comm %</th>
                                                    <th>Comm Amnt</th>
                                                    <th>C.d %</th>
                                                    <th>C.d Amnt</th>
                                                </tr>
                                            </thead>
                                            <tbody id="CommTbleBody">
                                                    
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
            const fromDate = $('#fromDate').val();
            const toDate = $('#toDate').val();
            const accId = $('#acc_id').val();

            const formattedFromDate = moment(fromDate).format('DD-MM-YYYY');
            const formattedToDate = moment(toDate).format('DD-MM-YYYY');

            if (tabId === "#Comm") {
                $('#CommTbleBody').empty();

                $.ajax({
                    type: "GET",
                    url: "/rep-commissions/comm",
                    data: { fromDate, toDate, acc_id: accId },
                    success: function(result) {
                        $('#comm_from').text(formattedFromDate);
                        $('#comm_to').text(formattedToDate);
                        $('#comm_acc').text($('#acc_id option:selected').text());

                        let html = "";
                        let lastAccountName = null;
                        let subtotalBAmount = 0, subtotalCommDisc = 0, subtotalCdDisc = 0;
                        let rowNumber = 1;

                        $.each(result, function(_, data) {
                            const bAmount = data.B_amount || 0;
                            const commDisc = (bAmount * (data.comm_disc || 0)) / 100;
                            const totalTax = 1 + (((data.gst || 0) + (data.income_tax || 0)) / 100);
                            const cdDisc = bAmount && totalTax !== 0 
                            ? (bAmount * totalTax * (data.cd_disc || 0) / 100) / totalTax
                            : 0;

                            if (data.ac_name !== lastAccountName) {
                                if (lastAccountName) {
                                    html += `
                                        <tr style="background-color: #FFFFFF;">
                                            <td colspan="4" class="text-center"><strong>Subtotal for ${lastAccountName}</strong></td>
                                            <td class="text-danger">${subtotalBAmount.toFixed(0)}</td>
                                            <td></td>
                                            <td></td>
                                            <td class="text-danger">${subtotalCommDisc.toFixed(0)}</td>
                                            <td></td>
                                            <td class="text-danger">${subtotalCdDisc.toFixed(0)}</td>
                                        </tr>`;
                                    subtotalBAmount = subtotalCommDisc = subtotalCdDisc = 0;
                                }

                                html += `
                                    <tr>
                                        <td colspan="10" style="background-color: #cfe8e3; text-align: center; font-weight: bold;">
                                            ${data.ac_name || "No Account Name"}
                                        </td>
                                    </tr>`;
                                lastAccountName = data.ac_name;
                                rowNumber = 1;
                            }

                            html += `
                                <tr>
                                    <td>${rowNumber++}</td>
                                    <td>${data.sa_date ? moment(data.sa_date).format('DD-MM-YYYY') : ""}</td>
                                    <td>${data.Sale_inv_no || ""}</td>
                                    <td>${data.pur_ord_no || ""}</td>
                                    <td>${bAmount.toFixed(0)}</td>
                                    <td>${(data.gst || "") + (data.gst && data.income_tax ? " / " : "") + (data.income_tax || "")}</td>
                                    <td>${data.comm_disc || ""}</td>
                                    <td>${commDisc.toFixed(0)}</td>
                                    <td>${data.cd_disc || ""}</td>
                                    <td>${cdDisc.toFixed(0)}</td>
                                </tr>`;

                            subtotalBAmount += bAmount;
                            subtotalCommDisc += commDisc;
                            subtotalCdDisc += cdDisc;
                        });

                        if (lastAccountName) {
                            html += `
                                <tr style="background-color: #FFFFFF;">
                                    <td colspan="4" class="text-center"><strong>Sub Total for ${lastAccountName}</strong></td>
                                    <td class="text-danger">${subtotalBAmount.toFixed(0)}</td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-danger">${subtotalCommDisc.toFixed(0)}</td>
                                    <td></td>
                                    <td class="text-danger">${subtotalCdDisc.toFixed(0)}</td>
                                </tr>`;
                        }

                        $('#CommTbleBody').html(html);
                    },
                    error: function() {
                        alert("Error occurred while fetching data.");
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

            if (tabName === "comm") {
                window.open(`/rep-commissions/comm/report?outputType=excel&fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`, '_blank');

            }
        }

        function printPDF(tabName) {
            const { fromDate, toDate, acc_id } = getInputValues();

            if (!fromDate || !toDate || !acc_id) {
                alert('Please fill in all required fields.');
                return;
            }

            if (tabName === "comm") {
                window.open(`/rep-commissions/comm/report?outputType=view&fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`, '_blank');

            }
        }

        function downloadPDF(tabName) {
            const { fromDate, toDate, acc_id } = getInputValues();

            if (!fromDate || !toDate || !acc_id) {
                alert('Please fill in all required fields.');
                return;
            }

            if (tabName === "comm") {
                window.open(`/rep-commissions/comm/report?outputType=download&fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`, '_blank');
            }

        }
    </script>
</html>