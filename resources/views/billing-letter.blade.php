<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing Statement - {{ $billing->id }}</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            font-size: 11px;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 30px;
            max-width: 8.5in;
            margin: 0 auto;
            page-break-inside: avoid;
        }

        .letterhead {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .letterhead h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .letterhead p {
            margin: 3px 0;
            font-size: 12px;
        }

        .date {
            text-align: right;
            margin-bottom: 25px;
            font-size: 12px;
        }

        .recipient {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .recipient p {
            margin: 3px 0;
            font-size: 11px;
        }

        .salutation {
            margin-bottom: 20px;
            font-size: 13px;
            page-break-inside: avoid;
        }

        .content {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .content p {
            margin-bottom: 10px;
            text-align: justify;
        }

        .billing-details {
            background-color: #f9f9f9;
            padding: 15px;
            border-left: 3px solid #000;
            margin: 15px 0;
            page-break-inside: avoid;
        }

        .detail-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }

        .detail-label {
            display: table-cell;
            width: 140px;
            font-weight: bold;
            vertical-align: top;
            font-size: 11px;
        }

        .detail-value {
            display: table-cell;
            vertical-align: top;
            font-size: 11px;
        }

        .closing {
            margin-top: 30px;
            page-break-inside: avoid;
        }

        .signature {
            margin-top: 40px;
            border-top: 1px solid #000;
            width: 180px;
            text-align: center;
            padding-top: 8px;
            font-size: 11px;
        }

        .footer {
            position: fixed;
            bottom: 20px;
            left: 30px;
            right: 30px;
            text-align: center;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 8px;
            page-break-inside: avoid;
        }

        @page {
            margin: 0.75in;
            size: letter;
        }

        /* Force single page */
        html, body {
            height: auto !important;
            overflow: visible !important;
        }

        /* Prevent any page breaks */
        * {
            page-break-inside: avoid;
            page-break-before: avoid;
            page-break-after: avoid;
        }
    </style>
</head>
<body>
    <div class="letterhead">
        <h1>CynoSys Clinic</h1>
        <p>Medical Center</p>
        <p>123 Healthcare Avenue, Medical District</p>
        <p>Phone: (555) 123-4567 | Email: info@cynosysclinic.com</p>
    </div>

    <div class="date">
        {{ now()->format('F j, Y') }}
    </div>

    <div class="recipient">
        @php
            $patientName = 'Unknown Patient';
            $patientAddress = '';
            $patientPhone = '';
            $patientEmail = '';

            if ($billing->appointment && $billing->appointment->patient) {
                $patient = $billing->appointment->patient;
                $patientName = $patient->user?->name ?? $patient->first_name.' '.$patient->last_name;
                $patientAddress = $patient->address;
                $patientPhone = $patient->phone_number;
                $patientEmail = $patient->email ?? $patient->user?->email;
            } elseif ($billing->visit && $billing->visit->patient) {
                $patient = $billing->visit->patient;
                $patientName = $patient->user?->name ?? $patient->first_name.' '.$patient->last_name;
                $patientAddress = $patient->address;
                $patientPhone = $patient->phone_number;
                $patientEmail = $patient->email ?? $patient->user?->email;
            } elseif ($billing->medicalOrder && $billing->medicalOrder->patient) {
                $patient = $billing->medicalOrder->patient;
                $patientName = $patient->user?->name ?? $patient->first_name.' '.$patient->last_name;
                $patientAddress = $patient->address;
                $patientPhone = $patient->phone_number;
                $patientEmail = $patient->email ?? $patient->user?->email;
            }
        @endphp
        <p>{{ $patientName }}</p>
        <p>{{ $patientAddress }}</p>
        <p>{{ $patientPhone }}</p>
        @if($patientEmail)
        <p>{{ $patientEmail }}</p>
        @endif
    </div>

    <div class="salutation">
        <p>Dear {{ $patientName }},</p>
    </div>

    <div class="content">
        <p>This letter contains your billing statement from CynoSys Clinic.</p>

        <div class="billing-details">
            <div class="detail-row">
                <div class="detail-label">Billing ID:</div>
                <div class="detail-value">#{{ $billing->id }}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Billing Date:</div>
                <div class="detail-value">{{ \Carbon\Carbon::parse($billing->billing_date)->format('F j, Y') }}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Due Date:</div>
                <div class="detail-value">{{ $billing->due_date ? \Carbon\Carbon::parse($billing->due_date)->format('F j, Y') : 'N/A' }}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Total Amount:</div>
                <div class="detail-value">${{ number_format($billing->amount, 2) }}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Status:</div>
                <div class="detail-value">{{ $billing->status?->label() ?? $billing->status ?? 'Pending' }}</div>
            </div>
            @if($billing->appointment)
            <div class="detail-row">
                <div class="detail-label">Appointment:</div>
                <div class="detail-value">ID #{{ $billing->appointment->id }} - {{ \Carbon\Carbon::parse($billing->appointment->appointment_date_time)->format('M j, Y g:i A') }}</div>
            </div>
            @endif
            @if($billing->visit)
            <div class="detail-row">
                <div class="detail-label">Visit:</div>
                <div class="detail-value">ID #{{ $billing->visit->id }} - {{ \Carbon\Carbon::parse($billing->visit->visit_date_time)->format('M j, Y g:i A') }}</div>
            </div>
            @endif
            @if($billing->medicalOrder)
            <div class="detail-row">
                <div class="detail-label">Medical Order:</div>
                <div class="detail-value">ID #{{ $billing->medicalOrder->id }} - {{ $billing->medicalOrder->order_details }}</div>
            </div>
            @endif
        </div>

        <p>Please review the billing details above. If you have any questions about this statement or need to make payment arrangements, please contact our billing department at (555) 123-4567.</p>

        <p>Payment is due by the due date shown above. We accept cash, check, and major credit cards.</p>

        @if($billing->notes)
        <p><strong>Additional Notes:</strong> {{ $billing->notes }}</p>
        @endif
    </div>

    <div class="closing">
        <p>Sincerely,</p>
        <div class="signature">
            CynoSys Clinic Billing Department
        </div>
    </div>

    <div class="footer">
        <p>This is an official billing statement from CynoSys Clinic. Please keep this statement for your records.</p>
        <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
    </div>
</body>
</html>