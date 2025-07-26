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
                                <label class="col-form-label"><strong>Account Name</strong></label>
                                <select data-plugin-selecttwo class="form-control select2-js"  id="acc_id">
                                    <option value="" disabled selected>Select Account</option>
                                    @foreach($coa as $key => $row)	
                                        <option value="{{$row->ac_code}}">{{$row->ac_name}}</option>
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
                                <a class="nav-link nav-link-rep" data-bs-target="#GL" href="#GL" data-bs-toggle="tab">General Ledger</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link nav-link-rep" data-bs-target="#GL_R" href="#GL_R" data-bs-toggle="tab">General Ledger R</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link nav-link-rep" data-bs-target="#sale_age" href="#sale_age" data-bs-toggle="tab">Sales Ageing</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link nav-link-rep" data-bs-target="#pur_age" href="#pur_age" data-bs-toggle="tab">Purchase Ageing</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link nav-link-rep" data-bs-target="#sale_1" href="#sale_1" data-bs-toggle="tab">Sale 1</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link nav-link-rep" data-bs-target="#sale_2" href="#sale_2" data-bs-toggle="tab">Sale 2</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link nav-link-rep" data-bs-target="#comb_sale" href="#comb_sale" data-bs-toggle="tab">Combine Sale</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link nav-link-rep" data-bs-target="#purchase_1" href="#purchase_1" data-bs-toggle="tab">Purchase 1</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link nav-link-rep" data-bs-target="#purchase_2" href="#purchase_2" data-bs-toggle="tab">Purchase 2</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link nav-link-rep" data-bs-target="#comb_purchase" href="#comb_purchase" data-bs-toggle="tab">Combine Purchase</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link nav-link-rep" data-bs-target="#JV" href="#JV" data-bs-toggle="tab">Vouchers</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link nav-link-rep" data-bs-target="#sal_ret" href="#sal_ret" data-bs-toggle="tab">Sale Return</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link nav-link-rep" data-bs-target="#pur_ret" href="#pur_ret" data-bs-toggle="tab">Purchase Return</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div id="GL" class="tab-pane">
                                <div class="row form-group pb-3">
                                    <div class="col-lg-6">
                                        <div class="bill-to">
                                            <h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold" style="display: flex; align-items: center;">
                                                <span style="color: #17365D;">From: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="gl_from"></span>
                                            
                                                <span style="flex: 0.3;"></span> <!-- Spacer to push the "To" to the right -->
                                            
                                                <span style="color: #17365D;">To: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="gl_to"></span>
                                            </h4>
                                            
                                            <h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
                                                <span style="color:#17365D;font-size:20px;">Account Name: &nbsp;</span>
                                                <span style="font-weight:400; color:rgb(238, 19, 19);font-size:20px;" id="gl_acc"></span>
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 text-end">
                                        <a class="mb-1 mt-1 me-1 btn btn-warning" aria-label="Download" onclick="downloadPDF('gl')"><i class="fa fa-download"></i> Download</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-danger" aria-label="Print PDF" onclick="printPDF('gl')"><i class="fa fa-file-pdf"></i> Print PDF</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-success" aria-label="Export to Excel" onclick="downloadExcel('gl')"><i class="fa fa-file-excel"></i> Excel</a>      
                                    </div>
                                    <div class="col-12 mt-4">
                                        <!-- General Ledger Table -->
                                        <table class="table table-bordered table-striped mb-4">
                                            <thead>
                                                <tr>
                                                    <th>S/No</th>
                                                    <th>R/No</th>
                                                    <th>Voucher</th>
                                                    <th>Date</th>
                                                    <th>Account Name</th>
                                                    <th>Debit</th>
                                                    <th>Credit</th>
                                                    <th>Balance</th>
                                                </tr>
                                            </thead>
                                            <tbody id="GLTbleBody">
                                                <!-- Data Rows Here -->
                                            </tbody>
                                        </table>
                                    
                                        <!-- Unadjusted Post Dated Cheques Table -->
                                        <table class="table table-bordered text-center mt-4">
                                            <thead>
                                                <tr style="background-color: #bfe3d0; font-weight: bold;">
                                                    <td colspan="8" class="py-2">Unadjusted Post Dated Cheques</td>
                                                </tr>
                                                <tr class="table-secondary">
                                                    <th>Sr</th>
                                                    <th>Voucher#</th>
                                                    <th>Date</th>
                                                    <th>Remarks</th>
                                                    <th>Cheque#</th>
                                                    <th>Cheque Date</th>
                                                    <th>Debit</th>
                                                    <th>Credit</th>
                                                </tr>
                                            </thead>
                                            <tbody id="GLPostTbleBody">
                                                <!-- Data Rows Here -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div id="GL_R" class="tab-pane">
                                <div class="row form-group pb-3">
                                    <div class="col-lg-6">
                                        <div class="bill-to">
                                            <h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold" style="display: flex; align-items: center;">
                                                <span style="color: #17365D;">From: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="glr_from"></span>
                                            
                                                <span style="flex: 0.3;"></span> <!-- Spacer to push the "To" to the right -->
                                            
                                                <span style="color: #17365D;">To: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="glr_to"></span>
                                            </h4>
                                            
                                            <h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
                                                <span style="color:#17365D;font-size:20px;">Account Name: &nbsp;</span>
                                                <span style="font-weight:400; color:rgb(238, 19, 19);font-size:20px;" id="glr_acc"></span>
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 text-end">
                                        <a class="mb-1 mt-1 me-1 btn btn-warning" aria-label="Download" onclick="downloadPDF('glr')"><i class="fa fa-download"></i> Download</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-danger" aria-label="Print PDF" onclick="printPDF('glr')"><i class="fa fa-file-pdf"></i> Print PDF</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-success" aria-label="Export to Excel" onclick="downloadExcel('glr')"><i class="fa fa-file-excel"></i> Excel</a>      
                                    </div>
                                    <div class="col-12 mt-4">
                                        <!-- General Ledger Table -->
                                        <table class="table table-bordered table-striped mb-4">
                                            <thead>
                                                <tr>
                                                    <th>S/No</th>
                                                    <th>R/No</th>
                                                    <th>Voucher</th>
                                                    <th>Date</th>
                                                    <th>Account Name</th>
                                                    <th>Remarks</th>
                                                    <th>Debit</th>
                                                    <th>Credit</th>
                                                    <th>Balance</th>
                                                </tr>
                                            </thead>
                                            <tbody id="GLRTbleBody">
                                                <!-- Data Rows Here -->
                                            </tbody>
                                        </table>
                                    
                                        <!-- Unadjusted Post Dated Cheques Table -->
                                        <table class="table table-bordered text-center mt-4">
                                            <thead>
                                                <tr style="background-color: #bfe3d0; font-weight: bold;">
                                                    <td colspan="8" class="py-2">Unadjusted Post Dated Cheques</td>
                                                </tr>
                                                <tr class="table-secondary">
                                                    <th>Sr</th>
                                                    <th>Voucher#</th>
                                                    <th>Date</th>
                                                    <th>Remarks</th>
                                                    <th>Cheque#</th>
                                                    <th>Cheque Date</th>
                                                    <th>Debit</th>
                                                    <th>Credit</th>
                                                </tr>
                                            </thead>
                                            <tbody id="GLRPostTbleBody">
                                                <!-- Data Rows Here -->
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                </div>
                            </div>
                            <div id="sale_age" class="tab-pane">
                                <div class="row form-group pb-3">
                                    <div class="col-lg-6">
                                        <div class="bill-to">
                                            <h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold" style="display: flex; align-items: center;">
                                                <span style="color: #17365D;">From: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="sale_age_from"></span>
                                            
                                                <span style="flex: 0.3;"></span> <!-- Spacer to push the "To" to the right -->
                                            
                                                <span style="color: #17365D;">To: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="sale_age_to"></span>
                                            </h4>
                                            
                                            <h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
                                                <span style="color:#17365D;font-size:20px;">Account Name: &nbsp;</span>
                                                <span style="font-weight:400; color:rgb(238, 19, 19);font-size:20px;" id="sale_age_acc"></span>
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 text-end">
                                        <a class="mb-1 mt-1 me-1 btn btn-warning" aria-label="Download" onclick="downloadPDF('sales_ageing')"><i class="fa fa-download"></i> Download</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-danger" aria-label="Print PDF" onclick="printPDF('sales_ageing')"><i class="fa fa-file-pdf"></i> Print PDF</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-success" aria-label="Export to Excel" onclick="downloadExcel('sales_ageing')"><i class="fa fa-file-excel"></i> Excel</a>      
                                    </div>
                                    <div class="col-12 mt-4">
                                        <table class="table table-bordered table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th>S/No</th>
                                                    <th>Bill No.</th>
                                                    <th>Date</th>
                                                    <th>Detail</th>
                                                    <th>Bill Amount</th>
                                                    <th>UnPaid Amount</th>
                                                    <th>Days</th>
                                                    <th>1-20 Days</th>
                                                    <th>21-35 Days</th>
                                                    <th>36-50 Days</th>
                                                    <th>Over 50 Days</th>
                                                    <th>Cleared In Days</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody id="SaleAgeTbleBody">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div id="pur_age" class="tab-pane">
                                <div class="row form-group pb-3">
                                    <div class="col-lg-6">
                                        <div class="bill-to">
                                            <h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold" style="display: flex; align-items: center;">
                                                <span style="color: #17365D;">From: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="pur_age_from"></span>
                                            
                                                <span style="flex: 0.3;"></span> <!-- Spacer to push the "To" to the right -->
                                            
                                                <span style="color: #17365D;">To: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="pur_age_to"></span>
                                            </h4>
                                            
                                            <h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
                                                <span style="color:#17365D;font-size:20px;">Account Name: &nbsp;</span>
                                                <span style="font-weight:400; color:rgb(238, 19, 19);font-size:20px;" id="pur_age_acc"></span>
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 text-end">
                                        <a class="mb-1 mt-1 me-1 btn btn-warning" aria-label="Download" onclick="downloadPDF('pur_ageing')"><i class="fa fa-download"></i> Download</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-danger" aria-label="Print PDF" onclick="printPDF('pur_ageing')"><i class="fa fa-file-pdf"></i> Print PDF</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-success" aria-label="Export to Excel" onclick="downloadExcel('pur_ageing')"><i class="fa fa-file-excel"></i> Excel</a>      
                                    </div>
                                    <div class="col-12 mt-4">
                                        <table class="table table-bordered table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th>S/No</th>
                                                    <th>Bill No.</th>
                                                    <th>Date</th>
                                                    <th>Detail</th>
                                                    <th>Bill Amount</th>
                                                    <th>UnPaid Amount</th>
                                                    <th>Days</th>
                                                    <th>Advance</th>
                                                    <th>1-7 Days</th>
                                                    <th>8-15 Days</th>
                                                    <th>16-20 Days</th>
                                                    <th>21-35 Days</th>
                                                    <th>36-50 Days</th>
                                                    <th>Over 50 Days</th>
                                                    <th>Cleared In Days</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody id="PurAgeTbleBody">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div id="sale_1" class="tab-pane">
                                <div class="row form-group pb-3">
                                    <div class="col-lg-6">
                                        <div class="bill-to">
                                            <h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold" style="display: flex; align-items: center;">
                                                <span style="color: #17365D;">From: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="sale_1_from"></span>
                                            
                                                <span style="flex: 0.3;"></span> <!-- Spacer to push the "To" to the right -->
                                            
                                                <span style="color: #17365D;">To: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="sale_1_to"></span>
                                            </h4>
                                            
                                            <h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
                                                <span style="color:#17365D;font-size:20px;">Account Name: &nbsp;</span>
                                                <span style="font-weight:400; color:rgb(238, 19, 19);font-size:20px;" id="sale_1_acc"></span>
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 text-end">
                                        <a class="mb-1 mt-1 me-1 btn btn-warning" aria-label="Download" onclick="downloadPDF('sale_1')"><i class="fa fa-download"></i> Download</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-danger" aria-label="Print PDF" onclick="printPDF('sale_1')"><i class="fa fa-file-pdf"></i> Print PDF</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-success" aria-label="Export to Excel" onclick="downloadExcel('sale_1')"><i class="fa fa-file-excel"></i> Excel</a>      
                                    </div>
                                    <div class="col-12 mt-4">
                                        <table class="table table-bordered table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th>S/No</th>
                                                    <th>Sales Date</th>
                                                    <th>Inv No.</th>
                                                    <th>Bill</th>
                                                    <th>Name/Address</th>
                                                    <th>Remarks</th>
                                                    <th>Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody id="Sale1TbleBody">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div id="sale_2" class="tab-pane">
                                <div class="row form-group pb-3">
                                    <div class="col-lg-6">
                                        <div class="bill-to">
                                            <h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold" style="display: flex; align-items: center;">
                                                <span style="color: #17365D;">From: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="sale_2_from"></span>
                                            
                                                <span style="flex: 0.3;"></span> <!-- Spacer to push the "To" to the right -->
                                            
                                                <span style="color: #17365D;">To: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="sale_2_to"></span>
                                            </h4>
                                            
                                            <h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
                                                <span style="color:#17365D;font-size:20px;">Account Name: &nbsp;</span>
                                                <span style="font-weight:400; color:rgb(238, 19, 19);font-size:20px;" id="sale_2_acc"></span>
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 text-end">
                                        <a class="mb-1 mt-1 me-1 btn btn-warning" aria-label="Download" onclick="downloadPDF('sale_2')"><i class="fa fa-download"></i> Download</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-danger" aria-label="Print PDF" onclick="printPDF('sale_2')"><i class="fa fa-file-pdf"></i> Print PDF</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-success" aria-label="Export to Excel" onclick="downloadExcel('sale_2')"><i class="fa fa-file-excel"></i> Excel</a>      
                                    </div>
                                    <div class="col-12 mt-4">
                                        <table class="table table-bordered table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th>S/No</th>
                                                    <th>Sales Date</th>
                                                    <th>Inv No.</th>
                                                    <th>Bill</th>
                                                    <th>Company Name</th>
                                                    <th>Pur Inv</th>
                                                    <th>Remarks</th>
                                                    <th>Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody id="Sale2TbleBody">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div id="comb_sale" class="tab-pane">
                                <div class="row form-group pb-3">
                                    <div class="col-lg-6">
                                        <div class="bill-to">
                                            <h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold" style="display: flex; align-items: center;">
                                                <span style="color: #17365D;">From: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="sale_comb_from"></span>
                                            
                                                <span style="flex: 0.3;"></span> <!-- Spacer to push the "To" to the right -->
                                            
                                                <span style="color: #17365D;">To: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="sale_comb_to"></span>
                                            </h4>
                                            
                                            <h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
                                                <span style="color:#17365D;font-size:20px;">Account Name: &nbsp;</span>
                                                <span style="font-weight:400; color:rgb(238, 19, 19);font-size:20px;" id="sale_comb_acc"></span>
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 text-end">
                                        <a class="mb-1 mt-1 me-1 btn btn-warning" aria-label="Download" onclick="downloadPDF('comb_sale')"><i class="fa fa-download"></i> Download</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-danger" aria-label="Print PDF" onclick="printPDF('comb_sale')"><i class="fa fa-file-pdf"></i> Print PDF</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-success" aria-label="Export to Excel" onclick="downloadExcel('comb_sale')"><i class="fa fa-file-excel"></i> Excel</a>      
                                    </div>
                                    <div class="col-12 mt-4">
                                        <table class="table table-bordered table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th>S/No</th>
                                                    <th>Date</th>
                                                    <th>Entry OF</th>
                                                    <th>Inv No.</th>
                                                    <th>Bill No</th>
                                                    <th>Detail</th>
                                                    <th>Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody id="ComSaleTbleBody">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div id="purchase_1" class="tab-pane">
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
                                                <span style="color:#17365D;font-size:20px;">Account Name: &nbsp;</span>
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
                                                    <th>Date</th>
                                                    <th>Inv No.</th>
                                                    <th>Mill No.</th>
                                                    <th>Name Of Person</th>
                                                    <th>Sale Inv</th>
                                                    <th>Remarks</th>
                                                    <th>Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody id="P1TbleBody">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div id="purchase_2" class="tab-pane">
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
                                                <span style="color:#17365D;font-size:20px;">Account Name: &nbsp;</span>
                                                <span style="font-weight:400; color:rgb(238, 19, 19);font-size:20px;" id="pur2_acc"></span>
                                            </h4>
                                        </div>
                                    </div>

                                    <div class="col-lg-12 text-end">
                                        <a class="mb-1 mt-1 me-1 btn btn-warning" aria-label="Download" onclick="downloadPDF('purchase2')"><i class="fa fa-download"></i> Download</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-danger" aria-label="Print PDF" onclick="printPDF('purchase2')"><i class="fa fa-file-pdf"></i> Print PDF</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-success" aria-label="Export to Excel" onclick="downloadExcel('purchase2')"><i class="fa fa-file-excel"></i> Excel</a>      
                                    </div>
                                    <div class="col-12 mt-4">
                                        <table class="table table-bordered table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th>S/No</th>
                                                    <th>Date</th>
                                                    <th>Inv No.</th>
                                                    <th>Mill No.</th>
                                                    <th>Dispatch To Party</th>
                                                    <th>Sale Inv</th>
                                                    <th>Remarks</th>
                                                    <th>Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody id="P2TbleBody">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div id="comb_purchase" class="tab-pane">
                                <div class="row form-group pb-3">
                                    <div class="col-lg-6">
                                        <div class="bill-to">
                                            <h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold" style="display: flex; align-items: center;">
                                                <span style="color: #17365D;">From: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="pur_comb_from"></span>
                                            
                                                <span style="flex: 0.3;"></span> <!-- Spacer to push the "To" to the right -->
                                            
                                                <span style="color: #17365D;">To: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="pur_comb_to"></span>
                                            </h4>
                                            
                                            <h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
                                                <span style="color:#17365D;font-size:20px;">Account Name: &nbsp;</span>
                                                <span style="font-weight:400; color:rgb(238, 19, 19);font-size:20px;" id="pur_comb_acc"></span>
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 text-end">
                                        <a class="mb-1 mt-1 me-1 btn btn-warning" aria-label="Download" onclick="downloadPDF('comb_purchase')"><i class="fa fa-download"></i> Download</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-danger" aria-label="Print PDF" onclick="printPDF('comb_purchase')"><i class="fa fa-file-pdf"></i> Print PDF</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-success" aria-label="Export to Excel" onclick="downloadExcel('comb_purchase')"><i class="fa fa-file-excel"></i> Excel</a>      
                                    </div>
                                    <div class="col-12 mt-4">
                                        <table class="table table-bordered table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th>S/No</th>
                                                    <th>Date</th>
                                                    <th>Entry OF</th>
                                                    <th>Inv No.</th>
                                                    <th>Bill No</th>
                                                    <th>Detail</th>
                                                    <th>Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody id="ComPurTbleBody">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div id="JV" class="tab-pane">
                                <div class="row form-group pb-3">
                                    <div class="col-lg-6">
                                        <div class="bill-to">
                                            <h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold" style="display: flex; align-items: center;">
                                                <span style="color: #17365D;">From: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="jv_from"></span>
                                            
                                                <span style="flex: 0.3;"></span> <!-- Spacer to push the "To" to the right -->
                                            
                                                <span style="color: #17365D;">To: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="jv_to"></span>
                                            </h4>
                                            
                                            <h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
                                                <span style="color:#17365D;font-size:20px;">Account Name: &nbsp;</span>
                                                <span style="font-weight:400; color:rgb(238, 19, 19);font-size:20px;" id="jv_acc"></span>
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 text-end">
                                        <a class="mb-1 mt-1 me-1 btn btn-warning" aria-label="Download" onclick="downloadPDF('jv')"><i class="fa fa-download"></i> Download</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-danger" aria-label="Print PDF" onclick="printPDF('jv')"><i class="fa fa-file-pdf"></i> Print PDF</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-success" aria-label="Export to Excel" onclick="downloadExcel('jv')"><i class="fa fa-file-excel"></i> Excel</a>      
                                    </div>
                                    <div class="col-12 mt-4">
                                        <table class="table table-bordered table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th>S/No</th>
                                                    <th>Voucher</th>
                                                    <th>Date</th>
                                                    <th>Account Name</th>
                                                    <th>Remarks</th>
                                                    <th>Debit</th>
                                                    <th>Credit</th>
                                                </tr>
                                            </thead>
                                            <tbody id="JVTbleBody">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div id="#sal_ret" class="tab-pane">
                                <p>Sale Return</p>
                                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitat.</p>
                            </div>
                            <div id="#pur_ret" class="tab-pane">
                                <p>Purchase Return</p>
                                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitat.</p>
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

            if (tabId == "#GL") {
                var table = document.getElementById('GLTbleBody');
                while (table.rows.length > 0) {
                    table.deleteRow(0);
                }

                var pdcTable = document.getElementById('GLPostTbleBody');
                while (pdcTable.rows.length > 0) {
                    pdcTable.deleteRow(0);
                }

                url = "/rep-by-acc-name/gl";
                tableID = "#GLTbleBody";
                pdcTableID = "#GLPostTbleBody";

                $.ajax({
                    type: "GET",
                    url: url,
                    data: {
                        fromDate: fromDate,
                        toDate: toDate,
                        acc_id: acc_id,
                    },
                    beforeSend: function () {
                        $(tableID).html('<tr><td colspan="8" class="text-center">Loading Data Please Wait...</td></tr>');
                        $(pdcTableID).html('<tr><td colspan="8" class="text-center">Loading Post Dated Cheques...</td></tr>');
                    },
                    success: function (result) {
                        $('#gl_from').text(formattedfromDate);
                        $('#gl_to').text(formattedtoDate);

                        var SOD = 0;
                        var SOC = 0;

                        $.each(result['lager_much_op_bal'], function (k, v) {
                            SOD += v['SumOfDebit'] || 0;
                            SOC += v['SumOfrec_cr'] || 0;
                        });

                        var opening_bal = SOD - SOC;
                        var balance = opening_bal || 0;
                        var totalDebit = 0;
                        var totalCredit = 0;

                        $(tableID).empty(); // Clear the loading message

                        var html = "<tr>";
                        html += "<th></th>";
                        html += "<th></th>";
                        html += "<th></th>";
                        html += "<th></th>";
                        html += "<th colspan='3' style='text-align: center'><-----Opening Balance-----></th>";
                        html += "<th style='text-align: left'>" + (typeof opening_bal === 'number' ? opening_bal.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : opening_bal) + "</th>";
                        html += "</tr>";
                        $(tableID).append(html);

                        $.each(result['lager_much_all'], function (k, v) {
                            var html = "<tr>";
                            html += "<td>" + (k + 1) + "</td>";

                            if (v['entry_of'] === 'Sale') {
                                html += "<td><a href='/sales/saleinvoice/view/" + v['auto_lager'] + "' target='_blank'>" + (v['prefix'] || "") + (v['auto_lager'] || "") + "</a></td>";
                            } else if (v['entry_of'] === 'SP') {
                                html += "<td><a href='/sales2/show/" + v['auto_lager'] + "' target='_blank'>" + (v['prefix'] || "") + (v['auto_lager'] || "") + "</a></td>";
                            } else if (v['entry_of'] === 'Pur') {
                                html += "<td><a href='/purchase1/show/" + v['auto_lager'] + "' target='_blank'>" + (v['prefix'] || "") + (v['auto_lager'] || "") + "</a></td>";
                            } else if (v['entry_of'] === 'PP') {
                                html += "<td><a href='/purchase2/show/" + v['auto_lager'] + "' target='_blank'>" + (v['prefix'] || "") + (v['auto_lager'] || "") + "</a></td>";
                            } else if (v['entry_of'] === 'JV1') {
                                html += "<td><a href='/vouchers/show/" + v['auto_lager'] + "' target='_blank'>" + (v['prefix'] || "") + (v['auto_lager'] || "") + "</a></td>";
                            } else if (v['entry_of'] === 'JV2') {
                                html += "<td><a href='/vouchers2/print/" + v['auto_lager'] + "' target='_blank'>" + (v['prefix'] || "") + (v['auto_lager'] || "") + "</a></td>";
                            } else {
                                html += "<td>" + (v['entry_of'] || "") + " " + (v['prefix'] || "") + "</td>";
                            }

                            html += "<td>" + (v['entry_of'] || "") + "</td>";
                            html += "<td>" + (v['jv_date'] ? moment(v['jv_date']).format('DD-MM-YYYY') : "") + "</td>";
                            html += "<td>" + (v['ac2'] || "") + "</td>";
                            html += "<td>" + (v['Debit'] ? v['Debit'].toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : "0") + "</td>";
                            html += "<td>" + (v['Credit'] ? v['Credit'].toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : "0") + "</td>";

                            if (v['Debit'] && !isNaN(v['Debit'])) {
                                balance += v['Debit'];
                                totalDebit += v['Debit'];
                            }

                            if (v['Credit'] && !isNaN(v['Credit'])) {
                                balance -= v['Credit'];
                                totalCredit += v['Credit'];
                            }

                            html += "<td>" + (typeof balance === 'number' ? balance.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : balance) + "</td>";
                            html += "</tr>";
                            $(tableID).append(html);
                        });

                        // AFTER transactions are processed, now update #gl_acc
                        var selectedAcc = $('#acc_id').find("option:selected").text();
                        var selectedAccId = $('#acc_id').find("option:selected").val();
                        $('#gl_acc').html(
                            selectedAccId + ' - ' + selectedAcc +
                            ' <span style="color: green; display: inline-block; width: 200px; text-align: right;"> Balance = ' +
                            '<span style="color: green;">' +
                            balance.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) +
                            '</span>' +
                            '</span>'
                        );

                        // Now add the total row
                        var netAmount = balance;
                        var words = convertCurrencyToWords(netAmount);
                        var totalHtml = "<tr><td style='color:#17365D' colspan='4'><strong>" + words + "</strong></td>";
                        totalHtml += "<td style='text-align: right;'><strong>Total</strong></td>";
                        totalHtml += "<td class='text-danger'><strong>" + totalDebit.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) + "</strong></td>";
                        totalHtml += "<td class='text-danger'><strong>" + totalCredit.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) + "</strong></td>";
                        totalHtml += "<td class='text-danger'><strong>" + (typeof balance === 'number' ? balance.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : balance) + "</strong></td>";
                        $(tableID).append(totalHtml);

                        // Now handle PDCs (Post Dated Cheques)
                        var pdcHtml = "";
                        var totalDebit2 = 0;
                        var totalCredit2 = 0;

                        $.each(result['lager_pdc'], function (k, cheque) {
                            var bgColor = (k % 2 === 0) ? '#f1f1f1' : '#ffffff';

                            totalDebit2 += cheque['Debit'] || 0;
                            totalCredit2 += cheque['Credit'] || 0;

                            pdcHtml += "<tr style='background-color:" + bgColor + ";'>";
                            pdcHtml += "<td>" + (k + 1) + "</td>";
                            pdcHtml += "<td>" + (cheque['pdc_id'] ? cheque['prefix'] + cheque['pdc_id'] : "") + "</td>";
                            pdcHtml += "<td>" + (cheque['date'] ? moment(cheque['date']).format('DD-MM-YYYY') : "") + "</td>";
                            pdcHtml += "<td>" + (cheque['remarks'] || "") + " " + (cheque['bankname'] || "") + "</td>";
                            pdcHtml += "<td>" + (cheque['instrumentnumber'] || "") + "</td>";
                            pdcHtml += "<td>" + (cheque['chqdate'] ? moment(cheque['chqdate']).format('DD-MM-YYYY') : "") + "</td>";
                            pdcHtml += "<td>" + (cheque['Debit'] ? cheque['Debit'].toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : "0") + "</td>";
                            pdcHtml += "<td>" + (cheque['Credit'] ? cheque['Credit'].toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : "0") + "</td>";
                            pdcHtml += "</tr>";
                        });

                        if (result['lager_pdc'].length === 0) {
                            pdcHtml += "<tr><td colspan='8' style='padding:10px; text-align:center; font-style:italic; color:gray;'>No pending unadjusted post-dated cheques.</td></tr>";
                        }

                        pdcHtml += "<tr style='background-color:#bfe3d0; font-weight:bold;'>";
                        pdcHtml += "<td colspan='6' style='text-align:right; padding:10px;'>Total</td>";
                        pdcHtml += "<td style='text-align:center;'>" + totalDebit2.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) + "</td>";
                        pdcHtml += "<td style='text-align:center;'>" + totalCredit2.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) + "</td>";
                        pdcHtml += "</tr>";

                        $(pdcTableID).html(pdcHtml);
                    },
                    error: function () {
                        $(tableID).html('<tr><td colspan="9" class="text-center text-danger">Error loading data. Please try again.</td></tr>');
                        $(pdcTableID).html('<tr><td colspan="8" class="text-center text-danger">Error loading Post Dated Cheques. Please try again.</td></tr>');
                    }
                });
            }

            else if(tabId=="#GL_R"){
                var table = document.getElementById('GLRTbleBody');
                while (table.rows.length > 0) {
                    table.deleteRow(0);
                }

                var pdcTable = document.getElementById('GLRPostTbleBody');
                while (pdcTable.rows.length > 0) {
                    pdcTable.deleteRow(0);
                }

                url = "/rep-by-acc-name/glr";
                tableID = "#GLRTbleBody";
                pdcTableID = "#GLRPostTbleBody"; // Reference to PDC table

                $.ajax({
                    type: "GET",
                    url: url,
                    data: {
                        fromDate: fromDate,
                        toDate: toDate,
                        acc_id: acc_id,
                    }, 
                    beforeSend: function() {
                        $(tableID).html('<tr><td colspan="9" class="text-center">Loading Data Please Wait...</td></tr>');
                        $(pdcTableID).html('<tr><td colspan="8" class="text-center">Loading Post Dated Cheques...</td></tr>');
                    },
                    success: function(result){
                        $('#glr_from').text(formattedfromDate);
                        $('#glr_to').text(formattedtoDate);
                       
                        $(tableID).empty(); // Clear the loading message

                        var SOD = 0;                        
                        var SOC = 0;                        

                        $.each(result['lager_much_op_bal'], function(k, v){
                            SOD += v['SumOfDebit'] || 0;
                            SOC += v['SumOfrec_cr'] || 0;
                        });

                        var opening_bal = SOD - SOC;
                        var balance = opening_bal || 0; // Ensure balance starts as 0 if opening_bal is not defined
                        var totalDebit = 0;
                        var totalCredit = 0;

                        var html = "<tr>";
                        html += "<th></th>"; 
                        html += "<th></th>"; 
                        html += "<th></th>"; 
                        html += "<th></th>";
                        html += "<th></th>"; 
                        html += "<th colspan='3' style='text-align: center'><-----Opening Balance-----></th>"; // Merged and centered across two columns
                        html += "<th style='text-align: left'>" + (typeof opening_bal === 'number' ? opening_bal.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : opening_bal) + "</th>";
                        html += "</tr>";
                        $(tableID).append(html);

                        $.each(result['lager_much_all'], function(k, v) {
                            var html = "<tr>";
                            html += "<td>" + (k + 1) + "</td>";
                            if (v['entry_of'] === 'Sale') {
                                html += "<td><a href='/sales/saleinvoice/view/" + v['auto_lager'] + "' target='_blank'>" + (v['prefix'] ? v['prefix'] : "") + (v['auto_lager'] ? v['auto_lager'] : "") + "</a></td>";
                            } else if (v['entry_of'] === 'SP') {
                                html += "<td><a href='/sales2/show/" + v['auto_lager'] + "' target='_blank'>" + (v['prefix'] ? v['prefix'] : "") + (v['auto_lager'] ? v['auto_lager'] : "") + "</a></td>";
                            } else if (v['entry_of'] === 'Pur') {
                                html += "<td><a href='/purchase1/show/" + v['auto_lager'] + "' target='_blank'>" + (v['prefix'] ? v['prefix'] : "") + (v['auto_lager'] ? v['auto_lager'] : "") + "</a></td>";
                            } else if (v['entry_of'] === 'PP') {
                                html += "<td><a href='/purchase2/show/" + v['auto_lager'] + "' target='_blank'>" + (v['prefix'] ? v['prefix'] : "") + (v['auto_lager'] ? v['auto_lager'] : "") + "</a></td>";
                            } else if (v['entry_of'] === 'JV1') {
                                html += "<td><a href='/vouchers/show/" + v['auto_lager'] + "' target='_blank'>" + (v['prefix'] ? v['prefix'] : "") + (v['auto_lager'] ? v['auto_lager'] : "") + "</a></td>";
                            } else if (v['entry_of'] === 'JV2') {
                                html += "<td><a href='/vouchers2/print/" + v['auto_lager'] + "' target='_blank'>" + (v['prefix'] ? v['prefix'] : "") + (v['auto_lager'] ? v['auto_lager'] : "") + "</a></td>";
                            } else {
                                html += "<td>" + (v['entry_of'] ? v['entry_of'] : "") + " " + (v['prefix'] ? v['prefix'] : "") + "</td>";
                            }

                            html += "<td>" + (v['entry_of'] ? v['entry_of'] : "") + "</td>";
                            html += "<td>" + (v['jv_date'] ? moment(v['jv_date']).format('DD-MM-YYYY') : "") + "</td>";
                            html += "<td>" + (v['ac2'] ? v['ac2'] : "") + "</td>";
                            html += "<td>" + (v['Narration'] ? v['Narration'] : "") + "</td>";
                            html += "<td>" + (v['Debit'] ? v['Debit'].toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : "0") + "</td>";
                            html += "<td>" + (v['Credit'] ? v['Credit'].toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : "0") + "</td>";

                            // Add to totals (check for valid numbers)
                            if (v['Debit'] && !isNaN(v['Debit'])) {
                                balance += v['Debit']; // Add to balance
                                totalDebit += v['Debit']; // Accumulate total debit
                            }

                            // Subtract from balance and accumulate credit total (check for valid numbers)
                            if (v['Credit'] && !isNaN(v['Credit'])) {
                                balance -= v['Credit']; // Subtract from balance
                                totalCredit += v['Credit']; // Accumulate total credit
                            }

                            html += "<td>" + (typeof balance === 'number' ? balance.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : balance) + "</td>";
                            html += "</tr>";
                            $(tableID).append(html);
                        });


                        // AFTER transactions are processed, now update #gl_acc
                        var selectedAcc = $('#acc_id').find("option:selected").text();
                        var selectedAccId = $('#acc_id').find("option:selected").val();
                        $('#glr_acc').html(
                            selectedAccId + ' - ' + selectedAcc +
                            ' <span style="color: green; display: inline-block; width: 200px; text-align: right;"> Balance = ' +
                            '<span style="color: green;">' +
                            balance.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) +
                            '</span>' +
                            '</span>'
                        );

                        // After the loop, add the totals row
                        var netAmount = balance; 
                        var words = convertCurrencyToWords(netAmount);
                        var totalHtml = "<tr><td style='color:#17365D' colspan='5'><strong>" + words + "</strong></td>";
                        totalHtml += "<td style='text-align: right;'><strong>Total</strong></td>";
                        totalHtml += "<td class='text-danger'><strong>" + totalDebit.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) + "</strong></td>";
                        totalHtml += "<td class='text-danger'><strong>" + totalCredit.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) + "</strong></td>";
                        totalHtml += "<td class='text-danger'><strong>" + (typeof balance === 'number' ? balance.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : balance) + "</strong></td>";

                        $(tableID).append(totalHtml);

                        // Unadjusted Post Dated Cheques
                        var pdcHtml = "";
                        var totalDebit2 = 0;
                        var totalCredit2 = 0;
                        $.each(result['lager_pdc'], function(k, cheque) {
                            var bgColor = (k % 2 === 0) ? '#f1f1f1' : '#ffffff';

                            totalDebit2 += cheque['Debit'] || 0;
                            totalCredit2 += cheque['Credit'] || 0;

                            pdcHtml += "<tr style='background-color:" + bgColor + ";'>";
                            pdcHtml += "<td>" + (k + 1) + "</td>";
                            pdcHtml += "<td>" + (cheque['pdc_id'] ? cheque['prefix'] + cheque['pdc_id'] : "") + "</td>";
                            pdcHtml += "<td>" + (cheque['date'] ? moment(cheque['date']).format('DD-MM-YYYY') : "") + "</td>";
                            pdcHtml += "<td>" + (cheque['remarks'] || "") + " " + (cheque['bankname'] || "") + "</td>";
                            pdcHtml += "<td>" + (cheque['instrumentnumber'] || "") + "</td>";
                            pdcHtml += "<td>" + (cheque['chqdate'] ? moment(cheque['chqdate']).format('DD-MM-YYYY') : "") + "</td>";
                            pdcHtml += "<td>" + (cheque['Debit'] ? cheque['Debit'].toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : "0") + "</td>";
                            pdcHtml += "<td>" + (cheque['Credit'] ? cheque['Credit'].toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : "0") + "</td>";
                            pdcHtml += "</tr>";
                        });

                        // Add no records message if no unadjusted cheques are found
                        if (result['lager_pdc'].length === 0) {
                            pdcHtml += "<tr><td colspan='8' style='padding:10px; text-align:center; font-style:italic; color:gray;'>No pending unadjusted post-dated cheques.</td></tr>";
                        }

                        // Add totals row for unadjusted cheques
                        pdcHtml += "<tr style='background-color:#bfe3d0; font-weight:bold;'>";
                        pdcHtml += "<td colspan='6' style='text-align:right; padding:10px;'>Total</td>";
                        pdcHtml += "<td style='text-align:center;'>" + totalDebit2.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) + "</td>";
                        pdcHtml += "<td style='text-align:center;'>" + totalCredit2.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) + "</td>";
                        pdcHtml += "</tr>";

                        $(pdcTableID).html(pdcHtml); // Insert PDC data into its table
                    },
                    error: function(){
                        $(tableID).html('<tr><td colspan="9" class="text-center text-danger">Error loading data. Please try again.</td></tr>');
                        $(pdcTableID).html('<tr><td colspan="8" class="text-center text-danger">Error loading Post Dated Cheques. Please try again.</td></tr>');
                    }
                });
            }

            else if (tabId == "#sale_age") {
                var table = document.getElementById('SaleAgeTbleBody');
                while (table.rows.length > 0) {
                    table.deleteRow(0);
                }
                url = "/rep-by-acc-name/sales_age";
                tableID = "#SaleAgeTbleBody";

                $.ajax({
                    type: "GET",
                    url: url,
                    data: {
                        fromDate: fromDate,
                        toDate: toDate,
                        acc_id: acc_id,
                    },
                    beforeSend: function () {
                        $(tableID).html('<tr><td colspan="13" class="text-center">Loading Data Please Wait...</td></tr>');
                    },
                    success: function (result) {
                        // Update the header fields with the formatted dates and selected account name
                        $('#sale_age_from').text(formattedfromDate);
                        $('#sale_age_to').text(formattedtoDate);
                        var selectedAcc = $('#acc_id').find("option:selected").text();
                        $('#sale_age_acc').text(selectedAcc);

                        // Check if the result has data
                        if (!result.length) {
                            $(tableID).html('<tr><td colspan="13" class="text-center">No data available for the selected criteria.</td></tr>');
                            return;
                        }

                        var rows = '';
                        var totalRemainingAmount = 0; // Initialize total

                        $.each(result, function (k, v) {
                            // Parse remaining amount and handle possible issues with null/undefined or invalid values
                            const remainingAmount = isNaN(parseFloat(v['remaining_amount'])) ? 0 : parseFloat(v['remaining_amount']);
                            totalRemainingAmount += remainingAmount; // Accumulate total

                            // Apply red color style if remaining amount is not 0
                            const maxDaysStyle = (remainingAmount !== 0) ? "color: red;" : "";

                            // Calculate the number of days from the bill date to today
                            const daysFromBillDate = v['bill_date'] ? moment().diff(moment(v['bill_date']), 'days') : '';

                            // Generate row
                            rows += `<tr>
                                <td>${k + 1}</td>
                                <td>${(v['sale_prefix'] ? v['sale_prefix'] : '')} ${(v['Sal_inv_no'] ? v['Sal_inv_no'] : '')}</td>
                                <td>${v['bill_date'] ? moment(v['bill_date']).format('DD-MM-YYYY') : ''}</td>
                                <td>${(v['ac2'] ? v['ac2'] : '')} ${(v['remarks'] ? v['remarks'] : '')}</td>
                                <td>${v['bill_amount'] ? v['bill_amount'].toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : "0"}</td>
                                <td>${remainingAmount ? remainingAmount.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : "0"}</td>
                                <td>${remainingAmount !== 0 ? daysFromBillDate : ''}</td>
                                <td>${v['1_20_Days'] ? v['1_20_Days'] : ''}</td>
                                <td>${v['21_35_Days'] ? v['21_35_Days'] : ''}</td>
                                <td>${v['36_50_Days'] ? v['36_50_Days'] : ''}</td>
                                <td>${v['over_50_Days'] ? v['over_50_Days'] : ''}</td>
                                <td style="${remainingAmount === 0 ? maxDaysStyle : ''}">${remainingAmount === 0 ? (v['max_days'] ? v['max_days'] : '') : ''}</td>
                                <td>${remainingAmount === 0 ? 'Cleared' : 'Not Cleared'}</td>
                            </tr>`;
                        });

                        // Append total row
                        rows += `<tr style="font-weight: bold; background-color: #f8f9fa;">
                            <td colspan="5" style="text-align: right;">Balance:</td>
                            <td class="text-danger">${totalRemainingAmount.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 })}</td>
                            <td colspan="7"></td>
                        </tr>`;

                        // Replace table content with new rows
                        $(tableID).html(rows);
                    },
                    error: function (xhr, status, error) {
                        $(tableID).html(`<tr><td colspan="13" class="text-center text-danger">
                            Error loading data: ${xhr.responseText || error}.
                        </td></tr>`);
                    }
                });
            }

            else if (tabId == "#pur_age") {
                var table = document.getElementById('PurAgeTbleBody');
                while (table.rows.length > 0) {
                    table.deleteRow(0);
                }
                url = "/rep-by-acc-name/pur_age";
                tableID = "#PurAgeTbleBody";

                $.ajax({
                    type: "GET",
                    url: url,
                    data: {
                        fromDate: fromDate,
                        toDate: toDate,
                        acc_id: acc_id,
                    },
                    beforeSend: function () {
                        $(tableID).html('<tr><td colspan="15" class="text-center">Loading Data Please Wait...</td></tr>');
                    },
                    success: function (result) {
                        $('#pur_age_from').text(formattedfromDate);
                        $('#pur_age_to').text(formattedtoDate);
                        var selectedAcc = $('#acc_id').find("option:selected").text();
                        $('#pur_age_acc').text(selectedAcc);

                        if (!result.length) {
                            $(tableID).html('<tr><td colspan="15" class="text-center">No data available for the selected criteria.</td></tr>');
                            return;
                        }

                        var rows = '';
                        var totalRemainingAmount = 0; // Initialize total

                        $.each(result, function (k, v) {
                            const remainingAmount = isNaN(parseFloat(v['remaining_amount'])) ? 0 : parseFloat(v['remaining_amount']);
                            totalRemainingAmount += remainingAmount; // Accumulate total

                            const maxDaysStyle = (remainingAmount !== 0) ? "color: red;" : "";
                            const daysFromBillDate = v['bill_date'] ? moment().diff(moment(v['bill_date']), 'days') : '';

                            rows += `<tr>
                                <td>${k + 1}</td>
                                <td>${(v['sale_prefix'] ? v['sale_prefix'] : '')} ${(v['Sal_inv_no'] ? v['Sal_inv_no'] : '')}</td>
                                <td>${v['bill_date'] ? moment(v['bill_date']).format('DD-MM-YYYY') : ''}</td>
                                <td>${(v['ac2'] ? v['ac2'] : '')} ${(v['remarks'] ? v['remarks'] : '')}</td>
                                <td>${v['bill_amount'] ? v['bill_amount'].toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : "0"}</td>
                                <td>${remainingAmount ? remainingAmount.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : "0"}</td>
                                <td>${remainingAmount !== 0 ? daysFromBillDate : ''}</td>
                                <td>${v['Advance'] ? v['Advance'] : ''}</td>
                                <td>${v['1_7_Days'] ? v['1_7_Days'] : ''}</td>
                                <td>${v['8_15_Days'] ? v['8_15_Days'] : ''}</td>
                                <td>${v['16_20_Days'] ? v['16_20_Days'] : ''}</td>
                                <td>${v['21_35_Days'] ? v['21_35_Days'] : ''}</td>
                                <td>${v['36_50_Days'] ? v['36_50_Days'] : ''}</td>
                                <td>${v['over_50_Days'] ? v['over_50_Days'] : ''}</td>
                                <td style="${remainingAmount === 0 ? maxDaysStyle : ''}">${remainingAmount === 0 ? (v['max_days'] ? v['max_days'] : '') : ''}</td>
                                <td>${remainingAmount === 0 ? 'Cleared' : 'Not Cleared'}</td>
                            </tr>`;
                        });

                        // Append total row
                        rows += `<tr style="font-weight: bold; background-color: #f8f9fa;">
                            <td colspan="5" style="text-align: right;">Balance:</td>
                            <td class="text-danger">${totalRemainingAmount.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 })}</td>
                            <td colspan="9"></td>
                        </tr>`;


                        $(tableID).html(rows);
                    },
                    error: function (xhr, status, error) {
                        $(tableID).html(`<tr><td colspan="13" class="text-center text-danger">
                            Error loading data: ${xhr.responseText || error}.
                        </td></tr>`);
                    }
                });
            }

            else if(tabId=="#sale_1"){
                var table = document.getElementById('Sale1TbleBody');
                while (table.rows.length > 0) {
                    table.deleteRow(0);
                }
                url="/rep-by-acc-name/sale1";
                tableID="#Sale1TbleBody";

                $.ajax({
                    type: "GET",
                    url: url,
                    data:{
                        fromDate: fromDate,
                        toDate: toDate,
                        acc_id:acc_id,
                    }, 
                    beforeSend: function() {
                        $(tableID).html('<tr><td colspan="8" class="text-center">Loading Data Please Wait...</td></tr>');
                    },
                    success: function(result){
                        $('#sale_1_from').text(formattedfromDate);
                        $('#sale_1_to').text(formattedtoDate);
                        var selectedAcc = $('#acc_id').find("option:selected").text();
                        
                        $('#sale_1_acc').text(selectedAcc);
                        $(tableID).empty(); // Clear the loading message

                        var totalCrAmt = 0; // Variable to accumulate total

                        $.each(result, function(k,v){
                            var crAmt = v['cr_amt'] ? parseFloat(v['cr_amt']) : 0;
                            totalCrAmt += crAmt; // Add to total

                            var html="<tr>";
                            html += "<td>"+(k+1)+"</td>"
                            html += "<td>" + (v['date'] ? moment(v['date']).format('DD-MM-YYYY') : "") + "</td>";
                            html += "<td>" + (v['sal_inv'] ? v['sal_inv'] : "") + "</td>";
                            html += "<td>" + (v['bill'] ? v['bill'] : "") + "</td>";
                            html += "<td>" + (v['ac2'] ? v['ac2'] : "") + "</td>";
                            html += "<td>" + (v['remarks'] ? v['remarks'] : "") + "</td>";
                            html += "<td>" + (crAmt ? crAmt.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : "") + "</td>";
                            html +="</tr>";
                            $(tableID).append(html);
                        });

                        // Display the total in the last row or specific cell
                        var totalRow = "<tr><td colspan='6' style='text-align: right;'><strong>Total:</strong></td>";
                            totalRow += "<td class='text-danger'><strong>" + (totalCrAmt ? totalCrAmt.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : "0") + "</strong></td></tr>";
                        $(tableID).append(totalRow);

                    },
                    error: function(){
                        $(tableID).html('<tr><td colspan="8" class="text-center text-danger">Error loading data. Please try again.</td></tr>');
                    }
                });
            }
            else if(tabId=="#sale_2"){
                var table = document.getElementById('Sale2TbleBody');
                while (table.rows.length > 0) {
                    table.deleteRow(0);
                }
                url="/rep-by-acc-name/sale2";
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
                        $(tableID).html('<tr><td colspan="8" class="text-center">Loading Data Please Wait...</td></tr>');
                    },
                    success: function(result){
                        $('#sale_2_from').text(formattedfromDate);
                        $('#sale_2_to').text(formattedtoDate);
                        var selectedAcc = $('#acc_id').find("option:selected").text();
                        
                        $('#sale_2_acc').text(selectedAcc);
                        $(tableID).empty(); // Clear the loading message

                        var totalCrAmt = 0; // Variable to accumulate total

                        $.each(result, function(k,v){
                            var crAmt = v['dr_amt'] ? parseFloat(v['dr_amt']) : 0;
                            totalCrAmt += crAmt; // Add to total

                            var html="<tr>";
                            html += "<td>"+(k+1)+"</td>"
                            html += "<td>" + (v['date'] ? moment(v['date']).format('DD-MM-YYYY') : "") + "</td>";
                            html += "<td>" + (v['sal_inv'] ? v['sal_inv'] : "") + "</td>";
                            html += "<td>" + (v['pur_ord_no'] ? v['pur_ord_no'] : "") + "</td>";
                            html += "<td>" + (v['ac_name'] ? v['ac_name'] : "") + "</td>";
                            html += "<td>" + (v['pur_no'] ? v['pur_no'] : "") + "</td>";
                            html += "<td>" + (v['remarks'] ? v['remarks'] : "") + "</td>";
                            html += "<td>" + (crAmt ? crAmt.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : "") + "</td>";
                            html +="</tr>";
                            $(tableID).append(html);
                        });

                        // Display the total in the last row or specific cell
                        var totalRow = "<tr><td colspan='7' style='text-align: right;'><strong>Total:</strong></td>";
                            totalRow += "<td class='text-danger'><strong>" + (totalCrAmt ? totalCrAmt.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : "0") + "</strong></td></tr>";

                        $(tableID).append(totalRow);
                    },
                    error: function(){
                        $(tableID).html('<tr><td colspan="8" class="text-center text-danger">Error loading data. Please try again.</td></tr>');
                    }
                });
            }
            else if(tabId=="#comb_sale"){
                var table = document.getElementById('ComSaleTbleBody');
                while (table.rows.length > 0) {
                    table.deleteRow(0);
                }
                url="/rep-by-acc-name/comb-sale";
                tableID="#ComSaleTbleBody";

                $.ajax({
                    type: "GET",
                    url: url,
                    data:{
                        fromDate: fromDate,
                        toDate: toDate,
                        acc_id:acc_id,
                    }, 
                    beforeSend: function() {
                        $(tableID).html('<tr><td colspan="8" class="text-center">Loading Data Please Wait...</td></tr>');
                    },
                    success: function(result){
                        $('#sale_comb_from').text(formattedfromDate);
                        $('#sale_comb_to').text(formattedtoDate);
                        var selectedAcc = $('#acc_id').find("option:selected").text();
                        $('#sale_comb_acc').text(selectedAcc);
                        
                        $(tableID).empty(); // Clear the loading message

                        var totalCrAmt = 0; // Variable to accumulate total

                        $.each(result, function(k,v){
                            var crAmt = v['dr_amt'] ? parseFloat(v['dr_amt']) : 0;
                            totalCrAmt += crAmt; // Add to total

                            var html="<tr>";
                            html += "<td>"+(k+1)+"</td>"
                            html += "<td>" + (v['date'] ? moment(v['date']).format('DD-MM-YYYY') : "") + "</td>";
                            html += "<td>" + (v['Entry_of'] ? v['Entry_of'] : "") + "</td>";
                            html += "<td>" + (v['no'] ? v['no'] : "") + "</td>";
                            html += "<td>" + (v['ac2'] ? v['ac2'] : "") + "</td>";
                            html += "<td>" + (v['remarks'] ? v['remarks'] : "") + "</td>";
                            html += "<td>" + (crAmt ? crAmt.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : "") + "</td>";
                            html +="</tr>";

                            $(tableID).append(html);
                        });

                        // Display the total in the last row or specific cell
                        var totalRow = "<tr><td colspan='6' style='text-align: right;'><strong>Total:</strong></td>";
                        totalRow += "<td class='text-danger'><strong>" + (totalCrAmt ? totalCrAmt.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : "0") + "</strong></td></tr>";
                        $(tableID).append(totalRow);
                    },
                    error: function(){
                        $(tableID).html('<tr><td colspan="8" class="text-center text-danger">Error loading data. Please try again.</td></tr>');
                    }
                });
            }
            else if(tabId=="#purchase_1"){
                var table = document.getElementById('P1TbleBody');
                while (table.rows.length > 0) {
                    table.deleteRow(0);
                }
                url="/rep-by-acc-name/pur1";
                tableID="#P1TbleBody";

                $.ajax({
                    type: "GET",
                    url: url,
                    data:{
                        fromDate: fromDate,
                        toDate: toDate,
                        acc_id:acc_id,
                    }, 
                    beforeSend: function() {
                        $(tableID).html('<tr><td colspan="8" class="text-center">Loading Data Please Wait...</td></tr>');
                    },
                    success: function(result){
                        $('#pur1_from').text(formattedfromDate);
                        $('#pur1_to').text(formattedtoDate);
                        var selectedAcc = $('#acc_id').find("option:selected").text();
                        $('#pur1_acc').text(selectedAcc);
                        $(tableID).empty(); // Clear the loading message

                        var totalCrAmt = 0; // Variable to accumulate total

                        $.each(result, function(k,v){
                            var crAmt = v['cr_amt'] ? parseFloat(v['cr_amt']) : 0;
                            totalCrAmt += crAmt; // Add to total
                            var html="<tr>";
                            html += "<td>"+(k+1)+"</td>"
                            html += "<td>" + (v['date'] ? moment(v['date']).format('DD-MM-YYYY') : "") + "</td>";
                            html += "<td>" + (v['no'] ? v['no'] : "") + "</td>";
                            html += "<td>" + (v['mill_inv'] ? v['mill_inv'] : "") + "</td>";
                            html += "<td>" + (v['name_of'] ? v['name_of'] : "") + "</td>";
                            html += "<td>" + (v['sal_inv'] ? v['sal_inv'] : "") + "</td>";
                            html += "<td>" + (v['remarks'] ? v['remarks'] : "") + "</td>";
                            html += "<td>" + (crAmt ? crAmt.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : "") + "</td>";
                            html +="</tr>";
                            $(tableID).append(html);
                        });

                        // Display the total in the last row or specific cell
                        var totalRow = "<tr><td colspan='7' style='text-align: right;'><strong>Total:</strong></td>";
                        totalRow += "<td class='text-danger'><strong>" + (totalCrAmt ? totalCrAmt.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : "0") + "</strong></td></tr>";
                        $(tableID).append(totalRow);
                    },
                    error: function(){
                        $(tableID).html('<tr><td colspan="8" class="text-center text-danger">Error loading data. Please try again.</td></tr>');
                    }
                });
            }
            else if(tabId=="#purchase_2"){
                var table = document.getElementById('P2TbleBody');
                while (table.rows.length > 0) {
                    table.deleteRow(0);
                }
                url="/rep-by-acc-name/pur2";
                tableID="#P2TbleBody";

                $.ajax({
                    type: "GET",
                    url: url,
                    data:{
                        fromDate: fromDate,
                        toDate: toDate,
                        acc_id:acc_id,
                    }, 
                    beforeSend: function() {
                        $(tableID).html('<tr><td colspan="8" class="text-center">Loading Data Please Wait...</td></tr>');
                    },
                    success: function(result){
                        $('#pur2_from').text(formattedfromDate);
                        $('#pur2_to').text(formattedtoDate);
                        var selectedAcc = $('#acc_id').find("option:selected").text();
                        $('#pur2_acc').text(selectedAcc);
                        $(tableID).empty(); // Clear the loading message

                        var totalCrAmt = 0; // Variable to accumulate total

                        $.each(result, function(k,v){
                            var crAmt = v['cr_amt'] ? parseFloat(v['cr_amt']) : 0;
                            totalCrAmt += crAmt; // Add to total
                            var html="<tr>";
                            html += "<td>"+(k+1)+"</td>"
                            html += "<td>" + (v['date'] ? moment(v['date']).format('DD-MM-YYYY') : "") + "</td>";
                            html += "<td>" + (v['no'] ? v['no'] : "") + "</td>";
                            html += "<td>" + (v['pur_ord_no'] ? v['pur_ord_no'] : "") + "</td>";
                            html += "<td>" + (v['ac2'] ? v['ac2'] : "") + "</td>";
                            html += "<td>" + (v['sal_inv'] ? v['sal_inv'] : "") + "</td>";
                            html += "<td>" + (v['remarks'] ? v['remarks'] : "") + "</td>";
                            html += "<td>" + (crAmt ? crAmt.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : "") + "</td>";
                            html +="</tr>";
                            $(tableID).append(html);
                        });

                        // Display the total in the last row or specific cell
                        var totalRow = "<tr><td colspan='7' style='text-align: right;'><strong>Total:</strong></td>";
                        totalRow += "<td class='text-danger'><strong>" + (totalCrAmt ? totalCrAmt.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : "0") + "</strong></td></tr>";
                        $(tableID).append(totalRow);
                    },
                    error: function(){
                        $(tableID).html('<tr><td colspan="8" class="text-center text-danger">Error loading data. Please try again.</td></tr>');
                    }
                });
            }
            else if(tabId=="#comb_purchase"){
                var table = document.getElementById('ComPurTbleBody');
                while (table.rows.length > 0) {
                    table.deleteRow(0);
                }
                url="/rep-by-acc-name/comb-pur";
                tableID="#ComPurTbleBody";

                $.ajax({
                    type: "GET",
                    url: url,
                    data:{
                        fromDate: fromDate,
                        toDate: toDate,
                        acc_id:acc_id,
                    }, 
                    beforeSend: function() {
                        $(tableID).html('<tr><td colspan="8" class="text-center">Loading Data Please Wait...</td></tr>');
                    },
                    success: function(result){
                        $('#pur_comb_from').text(formattedfromDate);
                        $('#pur_comb_to').text(formattedtoDate);
                        var selectedAcc = $('#acc_id').find("option:selected").text();
                        $('#pur_comb_acc').text(selectedAcc);

                        $(tableID).empty(); // Clear the loading message

                        var totalCrAmt = 0; // Variable to accumulate total

                        $.each(result, function(k,v){
                            var crAmt = v['cr_amt'] ? parseFloat(v['cr_amt']) : 0;
                            totalCrAmt += crAmt; // Add to total
                            var html="<tr>";
                            html += "<td>"+(k+1)+"</td>"
                            html += "<td>" + (v['date'] ? moment(v['date']).format('DD-MM-YYYY') : "") + "</td>";
                            html += "<td>" + (v['entry_of'] ? v['entry_of'] : "") + "</td>";
                            html += "<td>" + (v['no'] ? v['no'] : "") + "</td>";
                            html += "<td>" + (v['ac2'] ? v['ac2'] : "") + "</td>";
                            html += "<td>" + (v['remarks'] ? v['remarks'] : "") + "</td>";
                            html += "<td>" + (crAmt ? crAmt.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : "") + "</td>";
                            html +="</tr>";

                            $(tableID).append(html);
                        });
                        // Display the total in the last row or specific cell
                        var totalRow = "<tr><td colspan='6' style='text-align: right;'><strong>Total:</strong></td>";
                        totalRow += "<td class='text-danger'><strong>" + (totalCrAmt ? totalCrAmt.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : "0") + "</strong></td></tr>";
                        $(tableID).append(totalRow);
                    },
                    error: function(){
                        $(tableID).html('<tr><td colspan="8" class="text-center text-danger">Error loading data. Please try again.</td></tr>');
                    }
                });
            }
            else if(tabId=="#JV"){
                var table = document.getElementById('JVTbleBody');
                while (table.rows.length > 0) {
                    table.deleteRow(0);
                }
                url="/rep-by-acc-name/jv";
                tableID="#JVTbleBody";

                $.ajax({
                    type: "GET",
                    url: url,
                    data:{
                        fromDate: fromDate,
                        toDate: toDate,
                        acc_id:acc_id,
                    }, 
                    beforeSend: function() {
                        $(tableID).html('<tr><td colspan="8" class="text-center">Loading Data Please Wait...</td></tr>');
                    },
                    success: function(result){
                        $('#jv_from').text(formattedfromDate);
                        $('#jv_to').text(formattedtoDate);
                        var selectedAcc = $('#acc_id').find("option:selected").text();
                        $('#jv_acc').text(selectedAcc);

                        $(tableID).empty(); // Clear the loading message

                        var totalCrAmt = 0; // Variable to accumulate total
                        var totalDrAmt = 0; // Variable to accumulate total

                        $.each(result, function(k,v){
                            var crAmt = v['Credit'] ? parseFloat(v['Credit']) : 0;
                            var drAmt = v['Debit'] ? parseFloat(v['Debit']) : 0;
                            totalCrAmt += crAmt; // Add to total
                            totalDrAmt += drAmt; // Add to total
                            var html="<tr>";
                            html += "<td>"+(k+1)+"</td>"
                            html += "<td>" + (v['entry_of'] ? v['entry_of'] : "") + (v['auto_lager'] ? "-" + v['auto_lager'] : "") + "</td>";
                            html += "<td>" + (v['jv_date'] ? moment(v['jv_date']).format('DD-MM-YYYY') : "") + "</td>";
                            html += "<td>" + (v['ac2'] ? v['ac2'] : "") + "</td>";
                            html += "<td>" + (v['Narration'] ? v['Narration'] : "") + "</td>";
                            html += "<td>" + (drAmt ? drAmt.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : "") + "</td>";
                            html += "<td>" + (crAmt ? crAmt.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : "") + "</td>";
                            html +="</tr>";

                            $(tableID).append(html);
                        });

                        // Display the total in the last row or specific cell
                        var totalRow = "<tr><td colspan='5' style='text-align: right;'><strong>Total:</strong></td>";
                        totalRow += "<td class='text-danger'><strong>" + (totalDrAmt ? totalDrAmt.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : "0") + "</strong></td>";
                        totalRow += "<td class='text-danger'><strong>" + (totalCrAmt ? totalCrAmt.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : "0") + "</strong></td></tr>";
                        $(tableID).append(totalRow);
                    },
                    error: function(){
                        $(tableID).html('<tr><td colspan="8" class="text-center text-danger">Error loading data. Please try again.</td></tr>');
                    }
                });
            }
            else if(tabId=="#sal_ret"){
            }
            else if(tabId=="#pur_ret"){
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

        function downloadExcel(tabName){
            const { fromDate, toDate, acc_id } = getInputValues();

            if (!fromDate || !toDate || !acc_id) {
                alert('Please fill in all required fields.');
                return;
            }

            if (tabName === "gl") {
                window.location.href = `/rep-by-acc-name/gl/excel?fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`;
            }
            else if (tabName === "glr") {
                window.location.href = `/rep-by-acc-name/glr/excel?fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`;
            }
            else if (tabName === "sales_ageing") {
                window.location.href = `/rep-by-acc-name/sales_age/excel?fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`;
            }
            else if (tabName === "pur_ageing") {
                window.location.href = `/rep-by-acc-name/pur_age/excel?fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`;
            }
            else if (tabName === "purchase1") {
                window.location.href = `/rep-by-acc-name/pur1/excel?fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`;
            }

            else if (tabName === "purchase2") {
                window.location.href = `/rep-by-acc-name/pur2/excel?fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`;
            }

            else if (tabName === "comb_purchase") {
                window.location.href = `/rep-by-acc-name/comb-pur/excel?fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`;
            }

            else if (tabName === "sale_1") {
                window.location.href = `/rep-by-acc-name/sale1/excel?fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`;
            }

            else if (tabName === "sale_2") {
                window.location.href = `/rep-by-acc-name/sale2/excel?fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`;
            }

            else if (tabName === "comb_sale") {
                window.location.href = `/rep-by-acc-name/comb-sale/excel?fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`;
            }

            else if (tabName === "jv") {
                window.location.href = `/rep-by-acc-name/jv/excel?fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`;
            }
        }

        function printPDF(tabName){
            const { fromDate, toDate, acc_id } = getInputValues();

            if (!fromDate || !toDate || !acc_id) {
                alert('Please fill in all required fields.');
                return;
            }

            if (tabName === "gl") {
                window.open(`/rep-by-acc-name/gl/PDF?fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`, '_blank');
            }

            else if (tabName === "glr") {
                window.open(`/rep-by-acc-name/glr/PDF?fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`, '_blank');
            }

            else if (tabName === "sales_ageing") {
                window.open(`/rep-by-acc-name/sales_age/PDF?fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`, '_blank');
            }

            else if (tabName === "pur_ageing") {
                window.open(`/rep-by-acc-name/pur_age/PDF?fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`, '_blank');
            }

            else if (tabName === "purchase1") {
                window.open(`/rep-by-acc-name/pur1/PDF?fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`, '_blank');

            }

            else if (tabName === "purchase2") {
                window.open(`/rep-by-acc-name/pur2/PDF?fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`, '_blank');
            }

            else if (tabName === "comb_purchase") {
                window.open(`/rep-by-acc-name/comb-pur/PDF?fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`, '_blank');
            }

            else if (tabName === "sale_1") {
               window.open(`/rep-by-acc-name/sale1/PDF?fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`, '_blank');
            }

            else if (tabName === "sale_2") {
                window.open( `/rep-by-acc-name/sale2/PDF?fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`, '_blank');
            }

            else if (tabName === "comb_sale") {
                window.open(`/rep-by-acc-name/comb-sale/PDF?fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`, '_blank');
            }

            else if (tabName === "jv") {
                window.open( `/rep-by-acc-name/jv/PDF?fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`, '_blank');
            }
        }

        function downloadPDF(tabName){
            const { fromDate, toDate, acc_id } = getInputValues();

            if (!fromDate || !toDate || !acc_id) {
                alert('Please fill in all required fields.');
                return;
            }

            if (tabName === "gl") {
                window.location.href = `/rep-by-acc-name/gl/download?fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`;
            }

            else if (tabName === "glr") {
                window.location.href = `/rep-by-acc-name/glr/download?fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`;
            }

            else if (tabName === "sales_ageing") {
                window.location.href = `/rep-by-acc-name/sales_age/download?fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`;
            }

            else if (tabName === "pur_ageing") {
                window.location.href = `/rep-by-acc-name/pur_age/download?fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`;
            }

            else if (tabName === "purchase1") {
                window.location.href = `/rep-by-acc-name/pur1/download?fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`;
            }

            else if (tabName === "purchase2") {
                window.location.href = `/rep-by-acc-name/pur2/download?fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`;
            }

            else if (tabName === "comb_purchase") {
                window.location.href = `/rep-by-acc-name/comb-pur/download?fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`;
            }
            
            else if (tabName === "sale_1") {
                window.location.href = `/rep-by-acc-name/sale1/download?fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`;
            }

            else if (tabName === "sale_2") {
                window.location.href = `/rep-by-acc-name/sale2/download?fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`;
            }

            else if (tabName === "comb_sale") {
                window.location.href = `/rep-by-acc-name/comb-sale/download?fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`;
            }
            
            else if (tabName === "jv") {
                window.location.href = `/rep-by-acc-name/jv/download?fromDate=${fromDate}&toDate=${toDate}&acc_id=${acc_id}`;
            }
        }
    </script>
</html>