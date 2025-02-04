@include('../layouts.header')
<body>
    <section class="body">
        <div class="inner-wrapper">
            <section role="main" class="content-body" style="margin:0px;padding:75px 10px !important">
                @include('../layouts.pageheader')

                <section class="card">
                    <div class="card-body">
                        <div class="invoice">
                            <header class="clearfix">
                                <div class="row">
                                    <div class="col-8 mt-3 mb-3">
                                        <h2 class="h2 mt-0 mb-1" style="color:#17365D">Selected PDC Report</h2>
                                        {{-- <h4 class="h4 m-0 text-dark font-weight-bold">
                                            Generated on: {{ \Carbon\Carbon::now()->format('d M Y, H:i A') }}
                                        </h4> --}}
                                    </div>
                                    <div class="col-4 text-end mt-3 mb-3">
                                        <div class="ib">
                                            <img width="100px" src="/assets/img/logo.png" alt="MFI Logo" />
                                        </div>
                                    </div>
                                </div>
                            </header>

                            <table class="table table-responsive-md table-striped invoice-items" style="font-size: 18px;">
                                <thead>
                                    <tr class="text-dark">
                                        <th class="text-center font-weight-semibold" style="color:#17365D;">ID#</th>
                                        <th class="text-center font-weight-semibold" style="color:#17365D;">Date</th>
                                        <th class="text-center font-weight-semibold" style="color:#17365D;">Account Debit</th>
                                        <th class="text-center font-weight-semibold" style="color:#17365D;">Account Credit</th>
                                        <th class="text-center font-weight-semibold" style="color:#17365D;">Remarks</th>
                                        <th class="text-center font-weight-semibold" style="color:#17365D;">Bank Name</th>
                                        <th class="text-center font-weight-semibold" style="color:#17365D;">Instrument</th>
                                        <th class="text-center font-weight-semibold" style="color:#17365D;">Chq Date</th>
                                        <th class="text-center font-weight-semibold" style="color:#17365D;">Amount</th>
                                        <th class="text-center font-weight-semibold" style="color:#17365D;">Voucher#</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pdcRecords as $pdc)
                                        <tr>
                                            <td class="font-weight-semibold text-dark text-center">{{ $pdc->pdc_id }}</td>
                                            <td class="font-weight-semibold text-dark text-center">{{ \Carbon\Carbon::parse($pdc->date)->format('d-m-y') }}</td>
                                            <td class="font-weight-semibold text-dark text-center">{{ $pdc->debit_account }}</td>
                                            <td class="font-weight-semibold text-dark text-center">{{ $pdc->credit_account }}</td>
                                            <td class="font-weight-semibold text-dark text-center">{{ $pdc->remarks }}</td>
                                            <td class="font-weight-semibold text-dark text-center">{{ $pdc->bankname }}</td>
                                            <td class="font-weight-semibold text-dark text-center">{{ $pdc->instrumentnumber }}</td>
                                            <td class="font-weight-semibold text-dark text-center">{{ \Carbon\Carbon::parse($pdc->chqdate)->format('d-m-y') }}</td>
                                            <td class="font-weight-semibold text-dark text-center">{{ number_format($pdc->amount, 2) }}</td>
                                            <td class="font-weight-semibold text-dark text-center">{{ $pdc->voch_prefix . $pdc->voch_id }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
								<tfoot>
									<tr class="text-dark">
										<th colspan="8" class="text-end font-weight-bold" style="color:#17365D;">Total Amount:</th>
										<th class="text-center font-weight-bold" style="color:#17365D;">{{ number_format($pdcRecords->sum('amount'), 2) }}</th>
										<th></th>
									</tr>
								</tfoot>
                            </table>

                            <div class="row">
                                <div class="col-12 col-md-8">
                                    <h3 style="color:#17365D; text-decoration: underline;" id="numberInWords"></h3>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="text-end">
                                        <a onclick="window.location='{{ route('all-pdc') }}'" class="btn btn-primary mt-2"> 
                                            <i class="fas fa-arrow-left"></i> Back
                                        </a>
										<a href="{{ route('print-pdc', ['selected_pdc' => implode(',', $pdcIds)]) }}" class="btn btn-danger mt-2" target="_blank">
											<i class="fas fa-print"></i> Print
										</a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </section>

            </section>
        </div>
    </section>
    @include('../layouts.footerlinks')
</body>

<script>
    var netAmount = <?php echo json_encode($pdcRecords->sum('amount')); ?>;
    var words = convertCurrencyToWords(netAmount);
    document.getElementById('numberInWords').innerHTML = words;
</script>

</html>
