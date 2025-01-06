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
                        <div class="col-lg-4">
                            <a class="btn btn-primary" style="margin-top: 2.1rem;padding: 0.5rem 0.6rem;" onclick="getReport()"><i class="fa fa-filter"></i></a>
                        </div>
                    </div>   
                    <div class="tabs">
                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a class="nav-link nav-link-rep" data-bs-target="#sale_1" href="#sale_1" data-bs-toggle="tab">Sale 1</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link nav-link-rep" data-bs-target="#sale_pipe" href="#sale_pipe" data-bs-toggle="tab">Sale Pipe</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link nav-link-rep" data-bs-target="#purchase_1" href="#purchase_1" data-bs-toggle="tab">Purchase 1</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link nav-link-rep" data-bs-target="#purchase_pipe" href="#purchase_pipe" data-bs-toggle="tab">Purchase Pipe</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link nav-link-rep" data-bs-target="#JV1" href="#JV1" data-bs-toggle="tab">JV1</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link nav-link-rep" data-bs-target="#JV2" href="#JV2" data-bs-toggle="tab">JV2</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link nav-link-rep" data-bs-target="#sale_1_return" href="#sale_1_return" data-bs-toggle="tab">Sale 1 Return</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link nav-link-rep" data-bs-target="#sale_pipe_return" href="#sale_pipe_return" data-bs-toggle="tab">Sale Pipe Return</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link nav-link-rep" data-bs-target="#purchase_1_return" href="#purchase_1_return" data-bs-toggle="tab">Purchase 1 Return</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link nav-link-rep" data-bs-target="#purchase_pipe_return" href="#purchase_pipe_return" data-bs-toggle="tab">Purchase Pipe Return</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link nav-link-rep" data-bs-target="#daily_reg" href="#daily_reg" data-bs-toggle="tab">Daily Register</a>
                            </li>
                        </ul>
                        <div class="tab-content">
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
                                                    <th>Date</th>
                                                    <th>Inv No.</th>
                                                    <th>Bill No.</th>
                                                    <th>Account Name</th>
                                                    <th>Name Of Person</th>
                                                    <th>Remarks</th>
                                                    <th>Bill Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody id="Sale1TbleBody">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div id="sale_pipe" class="tab-pane">
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
                                            
                                        </div>
                                    </div>
                                    <div class="col-lg-12 text-end">
                                        <a class="mb-1 mt-1 me-1 btn btn-warning" aria-label="Download" onclick="downloadPDF('sale_pipe')"><i class="fa fa-download"></i> Download</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-danger" aria-label="Print PDF" onclick="printPDF('sale_pipe')"><i class="fa fa-file-pdf"></i> Print PDF</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-success" aria-label="Export to Excel" onclick="downloadExcel('sale_pipe')"><i class="fa fa-file-excel"></i> Excel</a>      
                                    </div>
                                    <div class="col-12 mt-4">
                                        <table class="table table-bordered table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th>S/No</th>
                                                    <th>Date</th>
                                                    <th>Inv No.</th>
                                                    <th>Bill No.</th>
                                                    <th>Account Name</th>
                                                    <th>Dispatch From</th>
                                                    <th>Remarks</th>
                                                    <th>Bill Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody id="SalePipeTbleBody">

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
                                                    <th>R/No.</th>
                                                    <th>Account Name</th>
                                                    <th>Name</th>
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
                            <div id="purchase_pipe" class="tab-pane">
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
                                            
                                        </div>
                                    </div>

                                    <div class="col-lg-12 text-end">
                                        <a class="mb-1 mt-1 me-1 btn btn-warning" aria-label="Download" onclick="downloadPDF('pp')"><i class="fa fa-download"></i> Download</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-danger" aria-label="Print PDF" onclick="printPDF('pp')"><i class="fa fa-file-pdf"></i> Print PDF</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-success" aria-label="Export to Excel" onclick="downloadExcel('pp')"><i class="fa fa-file-excel"></i> Excel</a>      
                                    </div>
                                    <div class="col-12 mt-4">
                                        <table class="table table-bordered table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th>S/No</th>
                                                    <th>Date</th>
                                                    <th>Inv No.</th>
                                                    <th>Order No.</th>
                                                    <th>Account Name</th>
                                                    <th>Customer Name</th>
                                                    <th>Remarks</th>
                                                    <th>Bill Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody id="P2TbleBody">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div id="JV1" class="tab-pane">
                                <div class="row form-group pb-3">
                                    <div class="col-lg-6">
                                        <div class="bill-to">
                                            <h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold" style="display: flex; align-items: center;">
                                                <span style="color: #17365D;">From: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="jv1_from"></span>
                                            
                                                <span style="flex: 0.3;"></span> <!-- Spacer to push the "To" to the right -->
                                            
                                                <span style="color: #17365D;">To: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="jv1_to"></span>
                                            </h4>
                                            
                                        </div>
                                    </div>
                                    <div class="col-lg-12 text-end">
                                        <a class="mb-1 mt-1 me-1 btn btn-warning" aria-label="Download" onclick="downloadPDF('jv1')"><i class="fa fa-download"></i> Download</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-danger" aria-label="Print PDF" onclick="printPDF('jv1')"><i class="fa fa-file-pdf"></i> Print PDF</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-success" aria-label="Export to Excel" onclick="downloadExcel('jv1')"><i class="fa fa-file-excel"></i> Excel</a>      
                                    </div>
                                    <div class="col-12 mt-4">
                                        <table class="table table-bordered table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th>S/No</th>
                                                    <th>R/No</th>
                                                    <th>Date</th>
                                                    <th>Debit</th>
                                                    <th>Credit</th>
                                                    <th>Remarks</th>
                                                    <th>Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody id="JV1TbleBody">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div id="JV2" class="tab-pane">
                                <div class="row form-group pb-3">
                                    <div class="col-lg-6">
                                        <div class="bill-to">
                                            <h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold" style="display: flex; align-items: center;">
                                                <span style="color: #17365D;">From: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="jv2_from"></span>
                                            
                                                <span style="flex: 0.3;"></span> <!-- Spacer to push the "To" to the right -->
                                            
                                                <span style="color: #17365D;">To: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="jv2_to"></span>
                                            </h4>
                                            
                                        </div>
                                    </div>
                                    <div class="col-lg-12 text-end">
                                        <a class="mb-1 mt-1 me-1 btn btn-warning" aria-label="Download" onclick="downloadPDF('jv2')"><i class="fa fa-download"></i> Download</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-danger" aria-label="Print PDF" onclick="printPDF('jv2')"><i class="fa fa-file-pdf"></i> Print PDF</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-success" aria-label="Export to Excel" onclick="downloadExcel('jv2')"><i class="fa fa-file-excel"></i> Excel</a>      
                                    </div>
                                    <div class="col-12 mt-4">
                                        <table class="table table-bordered table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    
                                                    <th>S/No</th>
                                                    <th>Account Name</th>
                                                    <th>Detail</th>
                                                    <th>Debit</th>
                                                    <th>Credit</th>
                                                </tr>
                                            </thead>
                                            <tbody id="JV2TbleBody">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div id="sale_1_return" class="tab-pane">
                                <div class="row form-group pb-3">
                                    <div class="col-lg-6">
                                        <div class="bill-to">
                                            <h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold" style="display: flex; align-items: center;">
                                                <span style="color: #17365D;">From: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="sale_1_return_from"></span>
                                            
                                                <span style="flex: 0.3;"></span> <!-- Spacer to push the "To" to the right -->
                                            
                                                <span style="color: #17365D;">To: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="sale_1_return_to"></span>
                                            </h4>
                                            
                                        </div>
                                    </div>
                                    <div class="col-lg-12 text-end">
                                        <a class="mb-1 mt-1 me-1 btn btn-warning" aria-label="Download" onclick="downloadPDF('sale_1_return')"><i class="fa fa-download"></i> Download</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-danger" aria-label="Print PDF" onclick="printPDF('sale_1_return')"><i class="fa fa-file-pdf"></i> Print PDF</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-success" aria-label="Export to Excel" onclick="downloadExcel('sale_1_return')"><i class="fa fa-file-excel"></i> Excel</a>      
                                    </div>
                                    <div class="col-12 mt-4">
                                        <table class="table table-bordered table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th>S/No</th>
                                                    <th>Sales Date</th>
                                                    <th>Inv No.</th>
                                                    <th>Ord No.</th>
                                                    <th>Account Name</th>
                                                    <th>Customer Name</th>
                                                    <th>Remarks</th>
                                                    <th>Bill Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody id="Sale1RetTbleBody">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div id="sale_pipe_return" class="tab-pane">
                                <div class="row form-group pb-3">
                                    <div class="col-lg-6">
                                        <div class="bill-to">
                                            <h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold" style="display: flex; align-items: center;">
                                                <span style="color: #17365D;">From: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="sale_pipe_from"></span>
                                            
                                                <span style="flex: 0.3;"></span> <!-- Spacer to push the "To" to the right -->
                                            
                                                <span style="color: #17365D;">To: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="sale_pipe_to"></span>
                                            </h4>
                                            
                                        </div>
                                    </div>
                                    <div class="col-lg-12 text-end">
                                        <a class="mb-1 mt-1 me-1 btn btn-warning" aria-label="Download" onclick="downloadPDF('sale_pipe_return')"><i class="fa fa-download"></i> Download</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-danger" aria-label="Print PDF" onclick="printPDF('sale_pipe_return')"><i class="fa fa-file-pdf"></i> Print PDF</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-success" aria-label="Export to Excel" onclick="downloadExcel('sale_pipe_return')"><i class="fa fa-file-excel"></i> Excel</a>      
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
                                            <tbody id="SalePipeRetTbleBody">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div id="purchase_1_return" class="tab-pane">
                                <div class="row form-group pb-3">
                                    <div class="col-lg-6">
                                        <div class="bill-to">
                                            <h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold" style="display: flex; align-items: center;">
                                                <span style="color: #17365D;">From: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="pur1_return_from"></span>
                                            
                                                <span style="flex: 0.3;"></span> <!-- Spacer to push the "To" to the right -->
                                            
                                                <span style="color: #17365D;">To: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="pur1_return_to"></span>
                                            </h4>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-6 text-end">
                                        <a class="mb-1 mt-1 me-1 btn btn-warning" aria-label="Download" onclick="downloadPDF('purchase1_return')"><i class="fa fa-download"></i> Download</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-danger" aria-label="Print PDF" onclick="printPDF('purchase1_return')"><i class="fa fa-file-pdf"></i> Print PDF</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-success" aria-label="Export to Excel" onclick="downloadExcel('purchase1_return')"><i class="fa fa-file-excel"></i> Excel</a>   
                                    </div>
                                    
                                    <div class="col-12 mt-4">
                                        <table class="table table-bordered table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th>S/No</th>
                                                    <th>Sales Date</th>
                                                    <th>Inv No.</th>
                                                    <th>Mill No.</th>
                                                    <th>Dispatch To Party</th>
                                                    <th>Sale Inv</th>
                                                    <th>Remarks</th>
                                                    <th>Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody id="P1RetTbleBody">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div id="purchase_pipe_return" class="tab-pane">
                                <div class="row form-group pb-3">
                                    <div class="col-lg-6">
                                        <div class="bill-to">
                                            <h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold" style="display: flex; align-items: center;">
                                                <span style="color: #17365D;">From: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="pp_from"></span>
                                            
                                                <span style="flex: 0.3;"></span> <!-- Spacer to push the "To" to the right -->
                                            
                                                <span style="color: #17365D;">To: &nbsp;</span>
                                                <span style="font-weight: 400; color: black;" id="pp_to"></span>
                                            </h4>
                                        </div>
                                    </div>

                                    <div class="col-lg-12 text-end">
                                        <a class="mb-1 mt-1 me-1 btn btn-warning" aria-label="Download" onclick="downloadPDF('pp_return')"><i class="fa fa-download"></i> Download</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-danger" aria-label="Print PDF" onclick="printPDF('pp_return')"><i class="fa fa-file-pdf"></i> Print PDF</a>
                                        <a class="mb-1 mt-1 me-1 btn btn-success" aria-label="Export to Excel" onclick="downloadExcel('pp_return')"><i class="fa fa-file-excel"></i> Excel</a>      
                                    </div>

                                    <div class="col-12 mt-4">
                                        <table class="table table-bordered table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th>S/No</th>
                                                    <th>Sales Date</th>
                                                    <th>Inv No.</th>
                                                    <th>Mill No.</th>
                                                    <th>Dispatch To Party</th>
                                                    <th>Sale Inv</th>
                                                    <th>Remarks</th>
                                                    <th>Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody id="PPRetTbleBody">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div id="daily_reg" class="tab-pane">
                                <div class="col-lg-6">
                                    <div class="bill-to">
                                        <h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold" style="display: flex; align-items: center;">
                                            <span style="color: #17365D;">From: &nbsp;</span>
                                            <span style="font-weight: 400; color: black;" id="daily_reg_from"></span>
                                        
                                            <span style="flex: 0.3;"></span> <!-- Spacer to push the "To" to the right -->
                                        
                                            <span style="color: #17365D;">To: &nbsp;</span>
                                            <span style="font-weight: 400; color: black;" id="daily_reg_to"></span>
                                        </h4>
                                    </div>
                                </div>

                                <div class="col-lg-12 text-end">
                                    <a class="mb-1 mt-1 me-1 btn btn-warning" aria-label="Download" onclick="downloadPDF('pp_return')"><i class="fa fa-download"></i> Download</a>
                                    <a class="mb-1 mt-1 me-1 btn btn-danger" aria-label="Print PDF" onclick="printPDF('pp_return')"><i class="fa fa-file-pdf"></i> Print PDF</a>
                                    <a class="mb-1 mt-1 me-1 btn btn-success" aria-label="Export to Excel" onclick="downloadExcel('pp_return')"><i class="fa fa-file-excel"></i> Excel</a>      
                                </div>

                                <div class="col-12 mt-4" id="div_daily_reg">
                                    <table class="table table-bordered table-striped mb-0" id="Daily Reg">

                                    </table>
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

        function getReport() {
            const activeTabLink = document.querySelector('.nav-link.active');
            if (activeTabLink) {
                activeTabLink.click();
            }
        }

        function tabChanged(tabId) {
            fromDate=$('#fromDate').val();
            toDate=$('#toDate').val();
            const formattedfromDate = moment(fromDate).format('DD-MM-YYYY'); // Format the date
            const formattedtoDate = moment(toDate).format('DD-MM-YYYY'); // Format the date

            if(tabId=="#sale_1"){
                var table = document.getElementById('Sale1TbleBody');
                while (table.rows.length > 0) {
                    table.deleteRow(0);
                }
                url="/rep-daily-register/sale1";
                tableID="#Sale1TbleBody";

                $.ajax({
                    type: "GET",
                    url: url,
                    data:{
                        fromDate: fromDate,
                        toDate: toDate,
                    }, 
                    beforeSend: function() {
                        $(tableID).html('<tr><td colspan="8" class="text-center">Loading Data Please Wait...</td></tr>');
                    },
                    success: function(result){
                        $('#sale_1_from').text(formattedfromDate);
                        $('#sale_1_to').text(formattedtoDate);
                        
                        $(tableID).empty(); // Clear the loading message

                        $(tableID).empty(); // Clear the loading message

                        var totalBillAmt = 0; // Variable to accumulate total

                        $.each(result, function(k,v){
                            var billAmt = v['bill_amt'] ? parseFloat(v['bill_amt']) : 0;
                            totalBillAmt += billAmt; // Add to total
                            var html="<tr>";
                            html += "<td>"+(k+1)+"</td>"
                            html += "<td>" + (v['sa_date'] ? moment(v['sa_date']).format('DD-MM-YYYY') : "") + "</td>";
                            html += "<td>" + (v['prefix'] ? v['prefix'] : "") + (v['Sal_inv_no'] ? v['Sal_inv_no'] : "") +"</td>";
                            html += "<td>" + (v['pur_ord_no'] ? v['pur_ord_no'] : "") + "</td>";
                            html += "<td>" + (v['ac_name'] ? v['ac_name'] : "") + "</td>";
                            html += "<td>" + (v['Cash_pur_name'] ? v['Cash_pur_name'] : "") + "</td>";
                            html += "<td>" + (v['Sales_remarks'] ? v['Sales_remarks'] : "") + "</td>";
                            html += "<td>" + (billAmt ? billAmt.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : "") + "</td>";
                            html +="</tr>";
                            $(tableID).append(html);
                        });

                        // Display the total in the last row or specific cell
                        var totalRow = "<tr><td colspan='7' style='text-align: right;'><strong>Total:</strong></td>";
                        totalRow += "<td class='text-danger'><strong>" + (totalBillAmt ? totalBillAmt.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : "0") + "</strong></td></tr>";
                        $(tableID).append(totalRow);
                    },
                    error: function(){
                        $(tableID).html('<tr><td colspan="8" class="text-center text-danger">Error loading data. Please try again.</td></tr>');
                    }
                });
            }
            else if(tabId=="#sale_pipe"){
                var table = document.getElementById('SalePipeTbleBody');
                while (table.rows.length > 0) {
                    table.deleteRow(0);
                }
                url="/rep-daily-register/sale2";
                tableID="#SalePipeTbleBody";

                $.ajax({
                    type: "GET",
                    url: url,
                    data:{
                        fromDate: fromDate,
                        toDate: toDate,
                    }, 
                    beforeSend: function() {
                        $(tableID).html('<tr><td colspan="8" class="text-center">Loading Data Please Wait...</td></tr>');
                    },
                    success: function(result){
                        $('#sale_2_from').text(formattedfromDate);
                        $('#sale_2_to').text(formattedtoDate);
                        
                        $(tableID).empty(); // Clear the loading message

                        var totalBillAmt = 0; // Variable to accumulate total

                        $.each(result, function(k,v){
                            var billAmt = v['bill_amt'] ? parseFloat(v['bill_amt']) : 0;
                            totalBillAmt += billAmt; // Add to total
                            var html="<tr>";
                            html += "<td>"+(k+1)+"</td>"
                            html += "<td>" + (v['sa_date'] ? moment(v['sa_date']).format('DD-MM-YYYY') : "") + "</td>";
                            html += "<td>" + (v['prefix'] ? v['prefix'] : "") + (v['Sal_inv_no'] ? v['Sal_inv_no'] : "") +"</td>";
                            html += "<td>" + (v['pur_ord_no'] ? v['pur_ord_no'] : "") + "</td>";
                            html += "<td>" + (v['ac_name'] ? v['ac_name'] : "") + "</td>";
                            html += "<td>" + (v['comp_name'] ? v['comp_name'] : "") + "</td>";
                            html += "<td>" + (v['Sales_remarks'] ? v['Sales_remarks'] : "") + "</td>";
                            html += "<td>" + (billAmt ? billAmt.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : "") + "</td>";
                            html +="</tr>";
                            $(tableID).append(html);
                        });

                        // Display the total in the last row or specific cell
                        var totalRow = "<tr><td colspan='7' style='text-align: right;'><strong>Total:</strong></td>";
                        totalRow += "<td class='text-danger'><strong>" + (totalBillAmt ? totalBillAmt.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : "0") + "</strong></td></tr>";
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
                url="/rep-daily-register/pur1";
                tableID="#P1TbleBody";

                $.ajax({
                    type: "GET",
                    url: url,
                    data:{
                        fromDate: fromDate,
                        toDate: toDate,
                    }, 
                    beforeSend: function() {
                        $(tableID).html('<tr><td colspan="8" class="text-center">Loading Data Please Wait...</td></tr>');
                    },
                    success: function(result){
                        $('#pur1_from').text(formattedfromDate);
                        $('#pur1_to').text(formattedtoDate);

                        $(tableID).empty(); // Clear the loading message
                        var totalBillAmt = 0; // Variable to accumulate total

                        $.each(result, function(k,v){
                            var billAmt = v['bill_amt'] ? parseFloat(v['bill_amt']) : 0;
                            totalBillAmt += billAmt; // Add to total
                            var html="<tr>";
                            html += "<td>"+(k+1)+"</td>"
                            html += "<td>" + (v['pur_date'] ? moment(v['pur_date']).format('DD-MM-YYYY') : "") + "</td>";
                            html += "<td>" + (v['prefix'] ? v['prefix'] : "") + (v['pur_id'] ? v['pur_id'] : "") +"</td>";
                            html += "<td>" + (v['acc_name'] ? v['acc_name'] : "") + "</td>";
                            html += "<td>" + (v['cash_saler_name'] ? v['cash_saler_name'] : "") + "</td>";
                            html += "<td>" + (v['Pur_remarks'] ? v['Pur_remarks'] : "") + "</td>";
                            html += "<td>" + (billAmt ? billAmt.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : "") + "</td>";
                            html +="</tr>";

                            $(tableID).append(html);
                        });
                        // Display the total in the last row or specific cell
                        var totalRow = "<tr><td colspan='6' style='text-align: right;'><strong>Total:</strong></td>";
                        totalRow += "<td class='text-danger'><strong>" + (totalBillAmt ? totalBillAmt.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : "0") + "</strong></td></tr>";
                        $(tableID).append(totalRow);
                    },
                    error: function(){
                        $(tableID).html('<tr><td colspan="8" class="text-center text-danger">Error loading data. Please try again.</td></tr>');
                    }
                });
            }
            else if(tabId=="#purchase_pipe"){
                var table = document.getElementById('P2TbleBody');
                while (table.rows.length > 0) {
                    table.deleteRow(0);
                }
                url="/rep-daily-register/pur2";
                tableID="#P2TbleBody";

                $.ajax({
                    type: "GET",
                    url: url,
                    data:{
                        fromDate: fromDate,
                        toDate: toDate,
                    }, 
                    beforeSend: function() {
                        $(tableID).html('<tr><td colspan="8" class="text-center">Loading Data Please Wait...</td></tr>');
                    },
                    success: function(result){
                        $('#pur2_from').text(formattedfromDate);
                        $('#pur2_to').text(formattedtoDate);

                        $(tableID).empty(); // Clear the loading message

                        var totalBillAmt = 0; // Variable to accumulate total

                        $.each(result, function(k,v){
                            var billAmt = v['bill_amt'] ? parseFloat(v['bill_amt']) : 0;
                            totalBillAmt += billAmt; // Add to total
                            var html="<tr>";
                            html += "<td>"+(k+1)+"</td>"
                            html += "<td>" + (v['sa_date'] ? moment(v['sa_date']).format('DD-MM-YYYY') : "") + "</td>";
                            html += "<td>" + (v['prefix'] ? v['prefix'] : "") + (v['Sale_inv_no'] ? v['Sale_inv_no'] : "") +"</td>";
                            html += "<td>" + (v['pur_ord_no'] ? v['pur_ord_no'] : "") + "</td>";
                            html += "<td>" + (v['acc_name'] ? v['acc_name'] : "") + "</td>";
                            html += "<td>" + (v['cust_name'] ? v['cust_name'] : "") + "</td>";
                            html += "<td>" + (v['Sales_Remarks'] ? v['Sales_Remarks'] : "") + "</td>";
                            html += "<td>" + (billAmt ? billAmt.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : "") + "</td>";
                            html +="</tr>";
                            $(tableID).append(html);
                        });
                        // Display the total in the last row or specific cell
                        var totalRow = "<tr><td colspan='7' style='text-align: right;'><strong>Total:</strong></td>";
                        totalRow += "<td class='text-danger'><strong>" + (totalBillAmt ? totalBillAmt.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : "0") + "</strong></td></tr>";
                        $(tableID).append(totalRow);
                    },
                    error: function(){
                        $(tableID).html('<tr><td colspan="8" class="text-center text-danger">Error loading data. Please try again.</td></tr>');
                    }
                });
            }
            else if(tabId=="#JV1"){
                var table = document.getElementById('JV1TbleBody');
                while (table.rows.length > 0) {
                    table.deleteRow(0);
                }
                url="/rep-daily-register/jv1";
                tableID="#JV1TbleBody";

                $.ajax({
                    type: "GET",
                    url: url,
                    data:{
                        fromDate: fromDate,
                        toDate: toDate,
                    }, 
                    beforeSend: function() {
                        $(tableID).html('<tr><td colspan="8" class="text-center">Loading Data Please Wait...</td></tr>');
                    },
                    success: function(result){
                        $('#jv1_from').text(formattedfromDate);
                        $('#jv1_to').text(formattedtoDate);


                        $(tableID).empty(); // Clear the loading message

                        var totalJv = 0; // Variable to accumulate total

                        $.each(result, function(k,v){

                            var jvAmount = v['Amount'] ? parseFloat(v['Amount']) : 0;
                            totalJv += jvAmount; // Add to total

                            var html="<tr>";
                            html += "<td>"+(k+1)+"</td>"
                            html += "<td>" + (v['prefix'] ? v['prefix'] : "") + (v['auto_lager'] ? v['auto_lager'] : "") +"</td>";
                            html += "<td>" + (v['Date'] ? moment(v['Date']).format('DD-MM-YYYY') : "") + "</td>";
                            html += "<td>" + (v['Debit_Acc'] ? v['Debit_Acc'] : "") + "</td>";
                            html += "<td>" + (v['Credit_Acc'] ? v['Credit_Acc'] : "") + "</td>";
                            html += "<td>" + (v['remarks'] ? v['remarks'] : "") + "</td>";
                            html += "<td>" + (jvAmount ? jvAmount.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : "") + "</td>";
                            html +="</tr>";

                            $(tableID).append(html);
                        });
                        // Display the total in the last row or specific cell
                        var totalRow = "<tr><td colspan='6' style='text-align: right;'><strong>Total:</strong></td>";
                        totalRow += "<td class='text-danger'><strong>" + (totalJv ? totalJv.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : "0") + "</strong></td></tr>";
                        $(tableID).append(totalRow);

                    },
                    error: function(){
                        $(tableID).html('<tr><td colspan="8" class="text-center text-danger">Error loading data. Please try again.</td></tr>');
                    }
                });
            }
            else if (tabId == "#JV2") {
                var table = document.getElementById("JV2TbleBody");
                while (table.rows.length > 0) {
                    table.deleteRow(0);
                }
                url = "/rep-daily-register/jv2";
                tableID = "#JV2TbleBody";

                $.ajax({
                    type: "GET",
                    url: url,
                    data: {
                        fromDate: fromDate,
                        toDate: toDate,
                    },
                    beforeSend: function () {
                        $(tableID).html('<tr><td colspan="5" class="text-center">Loading Data Please Wait...</td></tr>');
                    },
                    success: function (result) {
                        $('#jv2_from').text(formattedfromDate);
                        $('#jv2_to').text(formattedtoDate);

                        $(tableID).empty(); // Clear the loading message

                        var groupedData = {};
                        var totalDebit = 0;
                        var totalCredit = 0;

                        // Group data by `prefix + jv_no`
                        $.each(result, function (k, v) {
                            var groupKey = (v['prefix'] || "") + (v['jv_no'] || "");
                            if (!groupedData[groupKey]) {
                                groupedData[groupKey] = {
                                    header: {
                                        jv_identifier: (v['prefix'] || "") + (v['jv_no'] || ""), // Merged field
                                        jv_date: v['jv_date'] || "",
                                        narration: v['Narration'] || "",
                                    },
                                    rows: []
                                };
                            }
                            groupedData[groupKey].rows.push(v);
                        });

                        // Process grouped data
                        $.each(groupedData, function (key, group) {
                            var groupHeader = group.header;
                            var groupTotalDebit = 0; // Initialize subtotal for debit
                            var groupTotalCredit = 0; // Initialize subtotal for credit

                            // Add group header row inside the "Voucher" column
                            var headerHtml = "<tr class='table-primary'>";
                            headerHtml += "<td colspan='5'><strong>Voucher:</strong> " + groupHeader.jv_identifier;
                            headerHtml += " -- <strong>Date:</strong> " + (groupHeader.jv_date ? moment(groupHeader.jv_date).format('DD-MM-YYYY') : "");
                            headerHtml += " -- <strong>Narration:</strong> " + groupHeader.narration + "</td>";
                            headerHtml += "</tr>";
                            $(tableID).append(headerHtml);

                            // Add rows for the group
                            $.each(group.rows, function (index, v) {
                                var debit = v['debit'] ? parseFloat(v['debit']) : 0;
                                var credit = v['credit'] ? parseFloat(v['credit']) : 0;

                                groupTotalDebit += debit; // Add to group total debit
                                groupTotalCredit += credit; // Add to group total credit

                                totalDebit += debit; // Add to overall total debit
                                totalCredit += credit; // Add to overall total credit

                                var rowHtml = "<tr>";
                                rowHtml += "<td>" + (index + 1) + "</td>";
                                rowHtml += "<td>" + (v['ac_name'] || "") + "</td>";
                                rowHtml += "<td>" + (v['Remark'] || "") + "</td>";
                                rowHtml += "<td>" + (debit ? debit.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : "") + "</td>";
                                rowHtml += "<td>" + (credit ? credit.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : "") + "</td>";
                                rowHtml += "</tr>";

                                $(tableID).append(rowHtml);
                            });

                            // Add group subtotal row
                            var subtotalHtml = "<tr class='font-weight-bold bg-light'>";
                            subtotalHtml += "<td colspan='3' style='text-align: right;'>Subtotal</td>";
                            subtotalHtml += "<td class='text-danger'><strong>" + (groupTotalDebit ? groupTotalDebit.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : "0") + "</strong></td>";
                            subtotalHtml += "<td class='text-danger'><strong>" + (groupTotalCredit ? groupTotalCredit.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : "0") + "</strong></td>";
                            subtotalHtml += "</tr>";

                            $(tableID).append(subtotalHtml);
                        });

                        // Add overall total row
                        if (totalDebit === 0 && totalCredit === 0) {
                            $(tableID).append("<tr><td colspan='5' class='text-center'>No transactions found for the selected date range.</td></tr>");
                        } else {
                            var totalHtml = "<tr class='font-weight-bold bg-light'>";
                            totalHtml += "<td colspan='3' style='text-align: right;'>Total</td>";
                            totalHtml += "<td class='text-danger'><strong>" + (totalDebit ? totalDebit.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : "0") + "</strong></td>";
                            totalHtml += "<td class='text-danger'><strong>" + (totalCredit ? totalCredit.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : "0") + "</strong></td>";
                            totalHtml += "</tr>";
                            $(tableID).append(totalHtml);
                        }
                    },
                    error: function () {
                    $(tableID).html('<tr><td colspan="5" class="text-center text-danger">Error loading data. Please try again.</td></tr>');
                }
                });
            }
        }


           
      

        function getInputValues() {
            return {
                fromDate: $('#fromDate').val(),
                toDate: $('#toDate').val(),
            };
        }

        function downloadExcel(tabName) {
            const { fromDate, toDate } = getInputValues();

            if (!fromDate || !toDate) {
                alert('Please fill in all required fields.');
                return;
            }

            if (tabName === "sale_1") {
                window.location.href = `/rep-daily-register/sale1/excel?fromDate=${fromDate}&toDate=${toDate}`;
            }

            else if (tabName === "sale_pipe") {
                window.location.href = `/rep-daily-register/sale2/excel?fromDate=${fromDate}&toDate=${toDate}`;
            }

            else if (tabName === "purchase1") {
                window.location.href = `/rep-daily-register/pur1/excel?fromDate=${fromDate}&toDate=${toDate}`;
            }

            else if (tabName === "pp") {
                window.location.href = `/rep-daily-register/pur2/excel?fromDate=${fromDate}&toDate=${toDate}`;
            }

            else if (tabName === "jv1") {
                window.location.href = `/rep-daily-register/jv1/excel?fromDate=${fromDate}&toDate=${toDate}`;
            }

            else if (tabName === "jv2") {
                window.location.href = `/rep-daily-register/jv2/excel?fromDate=${fromDate}&toDate=${toDate}`;
            }
        }

        function printPDF(tabName) {
            const { fromDate, toDate } = getInputValues();

            if (!fromDate || !toDate) {
                alert('Please fill in all required fields.');
                return;
            }

            let url = '';

            if (tabName === "sale_1") {
                url = `/rep-daily-register/sale1/report?outputType=view&fromDate=${fromDate}&toDate=${toDate}`;
            }

            else if (tabName === "sale_pipe") {
                url = `/rep-daily-register/sale2/report?outputType=view&fromDate=${fromDate}&toDate=${toDate}`;
            }

            else if (tabName === "purchase1") {
                url = `/rep-daily-register/pur1/report?outputType=view&fromDate=${fromDate}&toDate=${toDate}`;
            }

            else if (tabName === "pp") {
                url = `/rep-daily-register/pur2/report?outputType=view&fromDate=${fromDate}&toDate=${toDate}`;
            }

            else if (tabName === "jv1") {
                url = `/rep-daily-register/jv1/report?outputType=view&fromDate=${fromDate}&toDate=${toDate}`;
            }

            else if (tabName === "jv2") {
                url = `/rep-daily-register/jv2/report?outputType=view&fromDate=${fromDate}&toDate=${toDate}`;
            }

            // Open the URL in a new tab
            window.open(url, '_blank');
        }


        function downloadPDF(tabName) {
            const { fromDate, toDate } = getInputValues();

            if (!fromDate || !toDate) {
                alert('Please fill in all required fields.');
                return;
            }

            if (tabName === "sale_1") {
                window.location.href = `/rep-daily-register/sale1/report?outputType=download&fromDate=${fromDate}&toDate=${toDate}`;
            }

            else if (tabName === "sale_pipe") {
                window.location.href = `/rep-daily-register/sale2/report?outputType=download&fromDate=${fromDate}&toDate=${toDate}`;
            }

            else if (tabName === "purchase1") {
                window.location.href = `/rep-daily-register/pur1/report?outputType=download&fromDate=${fromDate}&toDate=${toDate}`;
            }

            else if (tabName === "pp") {
                window.location.href = `/rep-daily-register/pur2/report?outputType=download&fromDate=${fromDate}&toDate=${toDate}`;
            }

            else if (tabName === "jv1") {
                window.location.href = `/rep-daily-register/jv1/report?outputType=download&fromDate=${fromDate}&toDate=${toDate}`;
            }

            else if (tabName === "jv2") {
                window.location.href = `/rep-daily-register/jv2/report?outputType=download&fromDate=${fromDate}&toDate=${toDate}`;
            }
        }
        
    </script>
</html>