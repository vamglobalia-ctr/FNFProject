
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $invoice->invoice_no }}</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: white;
            padding: 15px;
            font-size: 12px;
            color: #000;
            line-height: 1.4;
        }

        .invoice-container {
            max-width: 700px;
            margin: auto;
            background: white;
            border: 1px solid #ccc;
            padding: 20px;
        }

        /* HEADER */
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .company-address {
            font-size: 11px;
            color: #444;
            text-align: right
        }

        .invoice-title {
            text-align: left;
        }

        .invoice-title h2 {
            font-size: 18px;
            margin-bottom: 3px;
        }

        .invoice-title h3 {
            font-size: 14px;
            color: #666;
        }

        /* FLEX INFO SECTION */
        .info-section {
            display: flex;
            gap: 30px;
            margin-bottom: 20px;
        }

        .info-block {
            flex: 1;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
            padding-bottom: 2px;
            border-bottom: 1px dotted #999;
        }

        .info-row .info-label {
            font-weight: bold;
            min-width: 95px;
        }

        .info-label {
            color: #086838
        }

        .info-row .info-value {
            flex: 1;
            text-align: right;
        }

        /* TABLE */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            border: 1px solid #000;
        }

        thead th {
            background: #f0f0f0;
            border-bottom: 2px solid #000;
            border-right: 1px solid #000;
            padding: 8px;
            font-size: 11px;
            text-align: left;
        }

        thead th:last-child {
            border-right: none;
        }

        tbody td {
            padding: 10px;
            font-size: 11px;
            vertical-align: top;
            border-right: 1px solid #000;
        }

        tbody tr:last-child {
            border-top: 2px solid #000;
        }

        .due-payment-section {
            margin-top: 20px;
            padding: 10px;
            background: #f5f5f5;
            font-weight: bold;
            border: 1px solid #ccc;
            text-align: center;
        }

        /* TERMS */
        .terms-section {
            margin-top: 25px;
            background: #fafafa;
            padding: 10px;
            border-radius: 3px;
            font-size: 10px;
        }

        .terms-heading {
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .terms-list {
            list-style: none;
        }

        .terms-list li {
            margin-bottom: 3px;
            padding-left: 10px;
            position: relative;
        }

        .terms-list li:before {
            content: "•";
            position: absolute;
            left: 0;
        }

        /* SIGNATURE */
        .signature-section {
            margin-top: 30px;
        }

        .signature-line {
            width: 200px;
            border-top: 1px solid #000;
            margin-top: 30px;
        }

        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 9px;
            color: #666;
        }

        .text-color {
            color: #086838
        }

        @page {
            margin: 10px;
            size: A4;
        }
    </style>
</head>

<body>
    <div class="invoice-container">

        <!-- HEADER -->
        <div class="header-section">
            <div class="company-address">
                101, Priyanka Intercity,<br>
                Puna Kumbhariya Road,<br>
                Magob, Surat<br>
                +91 8758875020
            </div>

            <div class="invoice-title">
                <h2 class="text-color">Shree Vallabh Clinic</h2>
                <h3>Invoice</h3>
            </div>
        </div>

        <!-- FLEX INFO SECTION -->
        <div class="info-section">

            <!-- PATIENT INFO -->
            <div class="info-block">
                <div class="info-row">
                    <span class="info-label">Name</span>
                    <span class="info-value">{{ $invoice->resolved_patient->patient_name ?? 'N/A' }}</span>
                </div>

                <div class="info-row">
                    <span class="info-label">Address</span>
                    <span class="info-value">{{ $invoice->address ?? 'N/A' }}</span>
                </div>

                <div class="info-row">
                    <span class="info-label">Mobile</span>
                    <span class="info-value">{{ $invoice->phone ?: 'N/A' }}</span>
                </div>
            </div>

            <!-- INVOICE INFO -->
            <div class="info-block">
                <div class="info-row">
                    <span class="info-label">Invoice No.</span>
                    <span class="info-value">{{ $invoice->invoice_no }}</span>
                </div>

                <div class="info-row">
                    <span class="info-label">Date</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d/m/Y') }}</span>
                </div>
            </div>

        </div>

        <!-- TABLE -->
        <table>
            <thead>
                <tr>
                    <th style="width:10%">No.</th>
                    <th style="width:70%">Description</th>
                    <th style="width:20%">Amount</th>
                </tr>
            </thead>

            <tbody>
                @php $counter = 1; @endphp

                <!-- IPD SPECIFIC ROWS -->
                @php
                    $isIPDInvoice = str_starts_with($invoice->invoice_no, 'IPD-');
                @endphp
                
                @if($isIPDInvoice)
                    <tr>
                        <td>{{ $counter++ }}</td>
                        <td>IPD Patient Charges</td>
                        <td>{{ number_format($invoice->total_payment, 2) }}</td>
                    </tr>
                    
                    @if ($invoice->discount > 0)
                        <tr>
                            <td>{{ $counter++ }}</td>
                            <td>IPD Discount</td>
                            <td>-{{ number_format($invoice->discount, 2) }}</td>
                        </tr>
                    @endif
                    
                @else
                    <!-- PROGRAMS ROWS -->
                    @php
                        $programsData = $invoice->programs_data;
                        if (is_string($programsData)) {
                            $programsData = json_decode($programsData, true);
                        }
                    @endphp

                    @if(!empty($programsData) && is_array($programsData))
                        @foreach($programsData as $program)
                            <tr>
                                <td>{{ $counter++ }}</td>
                                <td>{{ $program['program_name'] ?? 'Service' }} (Program)</td>
                                <td>{{ number_format($program['price'], 2) }}</td>
                            </tr>
                        @endforeach
                    @elseif($invoice->program)
                        <tr>
                            <td>{{ $counter++ }}</td>
                            <td>{{ $invoice->program->program_name }} (Program)</td>
                            <td>{{ number_format($invoice->program->program_price, 2) }}</td>
                        </tr>
                    @endif

                    <!-- CHARGES ROWS -->
                    @php
                        $chargesData = $invoice->charges_data;
                        if (is_string($chargesData)) {
                            $chargesData = json_decode($chargesData, true);
                        }
                    @endphp

                    @if(!empty($chargesData) && is_array($chargesData))
                        @php
                            $consolidatedCharges = [];
                            foreach($chargesData as $charge) {
                                $chargeModel = !empty($charge['charge_id']) ? \App\Models\Charges::find($charge['charge_id']) : null;
                                $displayChargeName = $charge['charge_name'] ?? ($chargeModel ? $chargeModel->charges_name : 'Charge');
                                
                                // Branch-specific naming override
                                                               if (in_array($displayChargeName, ['Registration Charges', 'Registration', 'SVC-Charge', 'Followup Charges', 'Follow up charges', 'Consulting charges', 'Registration & Consultation Charges'])) {
                                        if ($invoice->branch_id === 'LB-0007') {
                                            // $displayChargeName = 'LHR Service';
                                            $displayChargeName = $displayChargeName . ' From LB-0007';
                                        } elseif ($invoice->branch_id === 'BH-00023') {
                                            // $displayChargeName = 'Hydra Service';
                                            $displayChargeName = $displayChargeName . ' From BH-00023';
                                        } elseif ($invoice->branch_id === 'SVC-0005') {
                                            // $displayChargeName = 'SVC Service';
                                            $displayChargeName = $displayChargeName . ' From SVC-0005';
                                        } else {
                                            $displayChargeName = 'FNF Service';
                                        }
                                    }
                                
                                if (isset($consolidatedCharges[$displayChargeName])) {
                                    $consolidatedCharges[$displayChargeName] += (float)$charge['price'];
                                } else {
                                    $consolidatedCharges[$displayChargeName] = (float)$charge['price'];
                                }
                            }
                        @endphp

                        @foreach($consolidatedCharges as $name => $price)
                            <tr>
                                <td>{{ $counter++ }}</td>
                                <td>{{ $name }}</td>
                                <td>{{ number_format($price, 2) }}</td>
                            </tr>
                        @endforeach
                    @elseif(!$invoice->program)
                        <tr>
                            <td>{{ $counter++ }}</td>
                                <td>
                                    @php
                                        $fLabel = $invoice->charge->charges_name ?? 'Consulting charges';
                                        if ($invoice->branch_id === 'LB-0007') $fLabel = 'LHR Service';
                                        elseif ($invoice->branch_id === 'BH-00023') $fLabel = 'Hydra Service';
                                        elseif ($invoice->branch_id === 'SVC-0005') $fLabel = 'SVC Service';
                                        else $fLabel = 'FNF Service';
                                    @endphp
                                    {{ $fLabel }}
                                </td>
                            <td>{{ number_format($invoice->price, 2) }}</td>
                        </tr>
                    @endif

                    @if ($invoice->pending_due > 0)
                        <tr>
                            <td>{{ $counter++ }}</td>
                            <td>Follow up charges</td>
                            <td>{{ number_format($invoice->pending_due, 2) }}</td>
                        </tr>
                    @endif

                    @if ($invoice->discount > 0)
                        <tr>
                            <td>{{ $counter++ }}</td>
                            <td>Discount</td>
                            <td>-{{ number_format($invoice->discount, 2) }}</td>
                        </tr>
                    @endif
                @endif

                <!-- EMPTY ROWS (Fill manually to maintain height if needed, optional) -->
                @for ($i = 0; $i < (5 - $counter); $i++)
                    <tr>
                        <td>&nbsp;</td>
                        <td></td>
                        <td></td>
                    </tr>
                @endfor

                <tr>
                    <td></td>
                    <td><strong>Total Amount</strong></td>
                    <td><strong>{{ number_format($invoice->total_payment, 2) }}</strong></td>
                </tr>
                <tr>
                    <td></td>
                    <td><strong>Paid Amount</strong></td>
                    <td><strong>{{ number_format($invoice->given_payment, 2) }}</strong></td>
                </tr>
                <tr>
                    <td></td>
                    <td><strong>Due Payment</strong></td>
                    <td><strong>{{ number_format($invoice->due_payment, 2) }}</strong></td>
                </tr>

            </tbody>
        </table>

        {{-- <div class="due-payment-section">
        Due Payment: {{ number_format($invoice->due_payment, 2) }}
    </div> --}}

        <!-- TERMS -->
        <div class="terms-section">
            <div class="terms-heading">
                <span>Terms and Conditions of Treatment:</span>
                <span>For, Figure N Fit</span>
            </div>

            <ul class="terms-list">
                <li>Fees once paid are not refundable or transferable.</li>
                <li>Results may vary person to person.</li>
                <li>Treatment may vary based on medical conditions.</li>
                <li>I have read and understood all terms.</li>
                <li>Interest is charged on unpaid amount after due date.</li>
                <li>Prices are exclusive of applicable taxes.</li>
                <li>Subject to Surat jurisdiction.</li>
            </ul>
        </div>

        <!-- SIGNATURE -->
        <div class="signature-section">
            <div>Receiver’s Name</div>
            <div class="signature-line"></div>
            <div style="margin-top: 5px; font-size: 10px;">Authorised Signature</div>
        </div>

        <div class="footer">
            Generated on: {{ now()->format('d/m/Y h:i A') }}
        </div>

    </div>
</body>

</html>
