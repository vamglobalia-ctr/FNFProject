<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $invoice->invoice_no }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 30px;
            background-color: #f5f5f5;
            line-height: 1.4;
            font-size: 14px;
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            min-height: 1100px;
            position: relative;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .company-address {
            position: absolute;
            top: 30px;
            right: 30px;
            padding: 15px;
            border-radius: 6px;
            font-size: 13px;
            width: 200px;
        }

        .details-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            margin-top: 120px;
            gap: 40px;
        }

        .patient-details {
            width: 55%;
        }

        .invoice-details {
            width: 40%;
        }

        .detail-row {
            margin-bottom: 12px;
            display: flex;
            align-items: center;
        }

        .label {
            font-weight: bold;
            width: 110px;
            color: #086838;
            flex-shrink: 0;
        }

        .value {
            flex: 1;
            border-bottom: 1px solid #ccc;
            padding-bottom: 4px;
            margin-left: 10px;
        }

        .table-container {
            margin: 30px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
            margin: 20px 0;
        }

        thead th {
            border-bottom: 2px solid #000;
            border-right: 1px solid #000;
            background-color: #f0f0f0;
            font-weight: bold;
            padding: 12px 15px;
            text-align: left;
        }

        thead th:last-child {
            border-right: none;
        }

        tbody td {
            padding: 15px;
            text-align: left;
            vertical-align: top;
            border-right: 1px solid #000;
            border-bottom: none;
        }

        tbody td:last-child {
            border-right: none;
        }

        tbody tr {
            height: 50px;
        }

        .due-payment-row {
            font-weight: bold;
            background-color: #f5f5f5;
            border-top: 2px solid #000;
        }

        .terms-section {
            margin: 35px 0 25px 0;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 6px;
        }

        .terms-heading {
            font-weight: bold;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            padding-bottom: 8px;
            border-bottom: 1px solid #ddd;
        }

        .terms-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .terms-list li {
            margin-bottom: 6px;
            font-size: 12px;
            line-height: 1.4;
            padding-left: 15px;
            position: relative;
        }

        .terms-list li:before {
            content: "•";
            color: #086838;
            font-weight: bold;
            position: absolute;
            left: 0;
        }

        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 60px;
            padding-top: 30px;
            border-top: 1px solid #ddd;
        }

        .signature-box {
            text-align: center;
            width: 45%;
        }

        .signature-line {
            width: 220px;
            border-top: 1px solid #000;
            margin: 50px auto 8px auto;
        }

        .print-btn {
            background: #086838;
            color: white;
            border: none;
            padding: 12px 25px;
            cursor: pointer;
            margin-bottom: 25px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: bold;
        }

        .print-btn:hover {
            background: #065c2e;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .print-btn {
                display: none;
            }

            .container {
                box-shadow: none;
                padding: 0;
                margin: 0;
                max-width: none;
            }
        }

        .invoice-title {
            text-align: left;
        }

        .invoice-title h2 {
            font-size: 22px;
        }

        .invoice-title h3 {
            font-size: 14px;
            color: #666;
        }

        .text-color {
            color: #086838
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="company-address">
            101, Priyanka Intercity,<br>
            Puna Kumbhariya Road,<br>
            Magob, Surat +91 875 8<br>
            875 020
        </div>
        <div class="invoice-title">
            <h2 class="text-color">Shree Vallabh Clinic</h2>
            <h3>Invoice</h3>
        </div>

        <div class="details-section">
            <div class="patient-details">
                <div class="detail-row">
                    <span class="label">Name</span>
                    <span class="value">{{ $invoice->resolved_patient->patient_name ?? 'N/A' }}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Address</span>
                    <span class="value">{{ $invoice->address }}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Mobile No.</span>
                    <span class="value">{{ $invoice->phone ?: '' }}</span>
                </div>
            </div>
            <div class="invoice-details">
                <div class="detail-row">
                    <span class="label">Invoice No.</span>
                    <span class="value">{{ $invoice->invoice_no }}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Invoice Date</span>
                    <span class="value">{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d/m/Y') }}</span>
                </div>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 10%">No.</th>
                        <th style="width: 70%">Description</th>
                        <th style="width: 20%">Amount</th>
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
                            <td>₹{{ number_format($invoice->total_payment, 2) }}</td>
                        </tr>
                        
                        @if ($invoice->discount > 0)
                            <tr>
                                <td>{{ $counter++ }}</td>
                                <td>IPD Discount</td>
                                <td>- ₹{{ number_format($invoice->discount, 2) }}</td>
                            </tr>
                        @endif
                        
                    @else
                        <!-- REGULAR INVOICE PROGRAMS/CHARGES -->
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
                                    <td>₹{{ number_format($program['price'], 2) }}</td>
                                </tr>
                            @endforeach
                        @elseif($invoice->program)
                            <tr>
                                <td>{{ $counter++ }}</td>
                                <td>{{ $invoice->program->program_name }} (Program)</td>
                                <td>₹{{ number_format($invoice->program->program_price, 2) }}</td>
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
                                    <td>₹{{ number_format($price, 2) }}</td>
                                </tr>
                            @endforeach
                        @elseif(!$invoice->program)
                            <tr>
                                <td>{{ $counter++ }}</td>
                                    <td>
                                        @php
                                            $fLabel = $invoice->charge->charges_name ?? 'Consulting charges';
                                            if ($invoice->branch_id === 'LB-0007') {
                                                // Check if this is an LHR inquiry registration
                                                $programsData = $invoice->programs_data;
                                                if (is_string($programsData)) {
                                                    $programsData = json_decode($programsData, true);
                                                }
                                                if (!empty($programsData) && is_array($programsData) && isset($programsData[0]['program_name'])) {
                                                    $fLabel = $programsData[0]['program_name'];
                                                } else {
                                                    $fLabel = 'LHR Registration & Initial Consultation';
                                                }
                                            }
                                            elseif ($invoice->branch_id === 'BH-00023') $fLabel = 'Hydra Service';
                                            elseif ($invoice->branch_id === 'SVC-0005') $fLabel = 'SVC Service';
                                            else $fLabel = 'FNF Service';
                                        @endphp
                                        {{ $fLabel }}
                                    </td>
                                <td>₹{{ number_format($invoice->price, 2) }}</td>
                            </tr>
                        @endif

                        @if ($invoice->pending_due > 0)
                            <tr>
                                <td>{{ $counter++ }}</td>
                                <td>Follow up charges</td>
                                <td>₹{{ number_format($invoice->pending_due, 2) }}</td>
                            </tr>
                        @endif

                        @if ($invoice->discount > 0)
                            <tr>
                                <td>{{ $counter++ }}</td>
                                <td>Discount</td>
                                <td>- ₹{{ number_format($invoice->discount, 2) }}</td>
                            </tr>
                        @endif
                    @endif

                    @for ($i = 0; $i < (5 - $counter); $i++)
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    @endfor

                    <tr class="due-payment-row">
                        <td></td>
                        <td>Total Amount</td>
                        <td>₹{{ number_format($invoice->total_payment, 2) }}</td>
                    </tr>
                    <tr class="due-payment-row">
                        <td></td>
                        <td>Paid Amount</td>
                        <td>₹{{ number_format($invoice->given_payment, 2) }}</td>
                    </tr>
                    <tr class="due-payment-row">
                        <td></td>
                        <td>Due Payment</td>
                        <td>₹{{ number_format($invoice->due_payment, 2) }}</td>
                    </tr>

                </tbody>
            </table>
        </div>

        <div class="terms-section">
            <div class="terms-heading">
                <span>Terms and Conditions of Treatment:</span>
                <span>For, Figure N Fit</span>
            </div>
            <ul class="terms-list">
                <li>1. Fees once paid are not refundable or transferable under any circumstances.</li>
                <li>2. Result of weight reduction program may vary from person to person.</li>
                <li>3. No two schemes can be merged at any given point of time.</li>
                <li>4. I have read and understood the above mentioned terms and conditions of treatment plan and
                    procedures.</li>
                <li>5. Interest @18% p.a. will be charged on amount remaining unpaid from the due date.</li>
                <li>6. The quoted price for goods or services in India is exclusive of GST, which is required to be paid
                    in addition to the price.</li>
                <li>7. Subject to Surat jurisdiction.</li>
            </ul>
        </div>

        <div class="signature-section">
            <div class="signature-box">
                <div>Receivers Name</div>
                <div class="signature-line"></div>
            </div>
            <div class="signature-box">
                <div>Authorised Signature</div>
                <div class="signature-line"></div>
            </div>
        </div>
    </div>
</body>

</html>
