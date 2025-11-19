<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing Report - {{ $billing->id }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }

        .header p {
            margin: 5px 0;
            font-size: 14px;
        }

        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .section h2 {
            font-size: 16px;
            font-weight: bold;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
            margin-bottom: 10px;
            color: #000;
        }

        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .info-row {
            display: table-row;
        }

        .info-cell {
            display: table-cell;
            padding: 4px 8px;
            vertical-align: top;
            width: 50%;
        }

        .info-label {
            font-weight: bold;
            color: #333;
        }

        .info-value {
            color: #000;
        }

        .records-list {
            margin-top: 10px;
        }

        .record-item {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
            page-break-inside: avoid;
        }

        .record-header {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }

        .record-header-row {
            display: table-row;
        }

        .record-header-cell {
            display: table-cell;
            padding: 2px 4px;
            width: 50%;
        }

        .record-content {
            margin-top: 8px;
        }

        .record-label {
            font-weight: bold;
            font-size: 11px;
            color: #333;
        }

        .record-value {
            margin-top: 2px;
            font-size: 11px;
            color: #000;
            white-space: pre-line;
        }

        .no-data {
            font-style: italic;
            color: #666;
            padding: 10px;
        }

        .page-break {
            page-break-before: always;
        }

        @page {
            margin: 1in;
            size: A4;
        }

        .footer {
            position: fixed;
            bottom: 20px;
            left: 20px;
            right: 20px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }

        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-paid { background-color: #d4edda; color: #155724; }
        .status-overdue { background-color: #f8d7da; color: #721c24; }
        .status-cancelled { background-color: #e2e3e5; color: #383d41; }

        .billing-summary {
            background-color: #f0f8ff;
            border: 2px solid #000;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
        }

        .billing-amount {
            font-size: 18px;
            font-weight: bold;
            color: #000;
            margin: 10px 0;
        }

        .billing-status {
            font-size: 14px;
            font-weight: bold;
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>CynoSys Clinic</h1>
        <p>Billing Report</p>
        <p>Generated on: {{ now()->format('F j, Y \a\t g:i A') }}</p>
    </div>

    <!-- Billing Summary -->
    <div class="billing-summary">
        <h2>Billing Summary</h2>
        <div class="billing-amount">
            ${{ number_format($report['billing_info']['amount'], 2) }}
        </div>
        <div class="billing-status">
            <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $report['billing_info']['status'])) }}">
                {{ $report['billing_info']['status'] }}
            </span>
        </div>
    </div>

    <!-- Billing Information -->
    <div class="section">
        <h2>Billing Information</h2>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-cell">
                    <div class="info-label">Billing ID:</div>
                    <div class="info-value">#{{ $report['billing_info']['id'] }}</div>
                </div>
                <div class="info-cell">
                    <div class="info-label">Amount:</div>
                    <div class="info-value">${{ number_format($report['billing_info']['amount'], 2) }}</div>
                </div>
            </div>
            <div class="info-row">
                <div class="info-cell">
                    <div class="info-label">Status:</div>
                    <div class="info-value">
                        <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $report['billing_info']['status'])) }}">
                            {{ $report['billing_info']['status'] }}
                        </span>
                    </div>
                </div>
                <div class="info-cell">
                    <div class="info-label">Billing Date:</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($report['billing_info']['billing_date'])->format('M j, Y') }}</div>
                </div>
            </div>
            <div class="info-row">
                <div class="info-cell">
                    <div class="info-label">Due Date:</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($report['billing_info']['due_date'])->format('M j, Y') }}</div>
                </div>
                <div class="info-cell">
                    <div class="info-label">Paid At:</div>
                    <div class="info-value">{{ $report['billing_info']['paid_at'] ? \Carbon\Carbon::parse($report['billing_info']['paid_at'])->format('M j, Y g:i A') : 'Not paid' }}</div>
                </div>
            </div>
            <div class="info-row">
                <div class="info-cell">
                    <div class="info-label">Created At:</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($report['billing_info']['created_at'])->format('M j, Y g:i A') }}</div>
                </div>
                <div class="info-cell">
                    <div class="info-label">Updated At:</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($report['billing_info']['updated_at'])->format('M j, Y g:i A') }}</div>
                </div>
            </div>
            @if($report['billing_info']['notes'])
            <div class="info-row">
                <div class="info-cell" style="width: 100%;">
                    <div class="info-label">Notes:</div>
                    <div class="info-value">{{ $report['billing_info']['notes'] }}</div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Patient Information -->
    <div class="section">
        <h2>Patient Information</h2>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-cell">
                    <div class="info-label">Full Name:</div>
                    <div class="info-value">{{ $report['patient_info']['name'] }}</div>
                </div>
                <div class="info-cell">
                    <div class="info-label">Date of Birth:</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($report['patient_info']['date_of_birth'])->format('M j, Y') }}</div>
                </div>
            </div>
            <div class="info-row">
                <div class="info-cell">
                    <div class="info-label">Gender:</div>
                    <div class="info-value">{{ ucfirst($report['patient_info']['gender']) }}</div>
                </div>
                <div class="info-cell">
                    <div class="info-label">Phone Number:</div>
                    <div class="info-value">{{ $report['patient_info']['phone_number'] }}</div>
                </div>
            </div>
            <div class="info-row">
                <div class="info-cell">
                    <div class="info-label">Email:</div>
                    <div class="info-value">{{ $report['patient_info']['email'] ?? 'N/A' }}</div>
                </div>
                <div class="info-cell">
                    <div class="info-label">Patient ID:</div>
                    <div class="info-value">#{{ $report['patient_info']['id'] }}</div>
                </div>
            </div>
            @if($report['patient_info']['insurance_info'])
            <div class="info-row">
                <div class="info-cell" style="width: 100%;">
                    <div class="info-label">Insurance Information:</div>
                    <div class="info-value">{{ $report['patient_info']['insurance_info'] }}</div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Related Appointment -->
    @if($report['appointment_info'])
    <div class="section">
        <h2>Related Appointment</h2>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-cell">
                    <div class="info-label">Appointment ID:</div>
                    <div class="info-value">#{{ $report['appointment_info']['id'] }}</div>
                </div>
                <div class="info-cell">
                    <div class="info-label">Appointment Date & Time:</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($report['appointment_info']['appointment_date_time'])->format('M j, Y g:i A') }}</div>
                </div>
            </div>
            <div class="info-row">
                <div class="info-cell">
                    <div class="info-label">Reason for Visit:</div>
                    <div class="info-value">{{ $report['appointment_info']['reason_for_visit'] }}</div>
                </div>
                <div class="info-cell">
                    <div class="info-label">Status:</div>
                    <div class="info-value">{{ $report['appointment_info']['status']?->label() ?? $report['appointment_info']['status'] ?? 'N/A' }}</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Related Visit -->
    @if($report['visit_info'])
    <div class="section">
        <h2>Related Visit</h2>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-cell">
                    <div class="info-label">Visit ID:</div>
                    <div class="info-value">#{{ $report['visit_info']['id'] }}</div>
                </div>
                <div class="info-cell">
                    <div class="info-label">Visit Date & Time:</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($report['visit_info']['visit_date_time'])->format('M j, Y g:i A') }}</div>
                </div>
            </div>
            <div class="info-row">
                <div class="info-cell">
                    <div class="info-label">Status:</div>
                    <div class="info-value">{{ $report['visit_info']['status']?->label() ?? $report['visit_info']['status'] ?? 'N/A' }}</div>
                </div>
                <div class="info-cell">
                    <div class="info-label">Staff:</div>
                    <div class="info-value">{{ $report['visit_info']['staff_name'] ?? 'N/A' }}</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Related Medical Orders -->
    <div class="section">
        <h2>Related Medical Orders ({{ count($report['medical_orders']) }})</h2>
        @if(count($report['medical_orders']) === 0)
            <div class="no-data">No related medical orders found.</div>
        @else
            <div class="records-list">
                @foreach($report['medical_orders'] as $order)
                    <div class="record-item">
                        <div class="record-header">
                            <div class="record-header-row">
                                <div class="record-header-cell">
                                    <div class="record-label">Order ID:</div>
                                    <div class="record-value">#{{ $order['id'] }}</div>
                                </div>
                                <div class="record-header-cell">
                                    <div class="record-label">Order Type:</div>
                                    <div class="record-value">{{ $order['order_type']?->label() ?? $order['order_type'] ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="record-header-row">
                                <div class="record-header-cell">
                                    <div class="record-label">Status:</div>
                                    <div class="record-value">{{ $order['status']?->label() ?? $order['status'] ?? 'N/A' }}</div>
                                </div>
                                <div class="record-header-cell">
                                    <div class="record-label">Priority:</div>
                                    <div class="record-value">{{ $order['priority']?->label() ?? $order['priority'] ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="record-content">
                            <div class="record-label">Details:</div>
                            <div class="record-value">{{ $order['order_details'] }}</div>
                        </div>
                        <div class="record-content">
                            <div class="record-label">Ordered At:</div>
                            <div class="record-value">{{ \Carbon\Carbon::parse($order['ordered_at'])->format('M j, Y g:i A') }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Related Medical Records -->
    <div class="section">
        <h2>Related Medical Records ({{ count($report['medical_records']) }})</h2>
        @if(count($report['medical_records']) === 0)
            <div class="no-data">No related medical records found.</div>
        @else
            <div class="records-list">
                @foreach($report['medical_records'] as $record)
                    <div class="record-item">
                        <div class="record-header">
                            <div class="record-header-row">
                                <div class="record-header-cell">
                                    <div class="record-label">Record ID:</div>
                                    <div class="record-value">#{{ $record['id'] }}</div>
                                </div>
                                <div class="record-header-cell">
                                    <div class="record-label">Date of Service:</div>
                                    <div class="record-value">{{ \Carbon\Carbon::parse($record['date_of_service'])->format('M j, Y') }}</div>
                                </div>
                            </div>
                        </div>
                        @if($record['diagnosis'])
                            <div class="record-content">
                                <div class="record-label">Diagnosis:</div>
                                <div class="record-value">{{ $record['diagnosis'] }}</div>
                            </div>
                        @endif
                        @if($record['treatment'])
                            <div class="record-content">
                                <div class="record-label">Treatment:</div>
                                <div class="record-value">{{ $record['treatment'] }}</div>
                            </div>
                        @endif
                        @if($record['notes'])
                            <div class="record-content">
                                <div class="record-label">Notes:</div>
                                <div class="record-value">{{ $record['notes'] }}</div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="footer">
        <p>This report was generated by CynoSys Clinic on {{ now()->format('F j, Y \a\t g:i A') }}</p>
        <p>Confidential - For medical use only</p>
    </div>
</body>
</html>