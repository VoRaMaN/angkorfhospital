<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Order Report - {{ $medicalOrder->id }}</title>
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
        .status-processing { background-color: #cce5ff; color: #004085; }
        .status-completed { background-color: #d4edda; color: #155724; }
        .status-cancelled { background-color: #f8d7da; color: #721c24; }

        .priority-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .priority-low { background-color: #e2e3e5; color: #383d41; }
        .priority-normal { background-color: #cce5ff; color: #004085; }
        .priority-high { background-color: #fff3cd; color: #856404; }
        .priority-urgent { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="header">
        <h1>CynoSys Clinic</h1>
        <p>Medical Order Report</p>
        <p>Generated on: {{ now()->format('F j, Y \a\t g:i A') }}</p>
    </div>

    <!-- Medical Order Information -->
    <div class="section">
        <h2>Medical Order Information</h2>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-cell">
                    <div class="info-label">Order ID:</div>
                    <div class="info-value">#{{ $report['order_info']['id'] }}</div>
                </div>
                <div class="info-cell">
                    <div class="info-label">Order Type:</div>
                    <div class="info-value">{{ $report['order_info']['order_type']?->label() ?? $report['order_info']['order_type'] ?? 'N/A' }}</div>
                </div>
            </div>
            <div class="info-row">
                <div class="info-cell">
                    <div class="info-label">Status:</div>
                    <div class="info-value">
                        <span class="status-badge status-{{ strtolower($report['order_info']['status']?->value ?? $report['order_info']['status'] ?? 'pending') }}">
                            {{ $report['order_info']['status']?->label() ?? $report['order_info']['status'] ?? 'Pending' }}
                        </span>
                    </div>
                </div>
                <div class="info-cell">
                    <div class="info-label">Priority:</div>
                    <div class="info-value">
                        <span class="priority-badge priority-{{ strtolower($report['order_info']['priority']?->value ?? $report['order_info']['priority'] ?? 'normal') }}">
                            {{ $report['order_info']['priority']?->label() ?? $report['order_info']['priority'] ?? 'Normal' }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="info-row">
                <div class="info-cell">
                    <div class="info-label">Ordered At:</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($report['order_info']['ordered_at'])->format('M j, Y g:i A') }}</div>
                </div>
                <div class="info-cell">
                    <div class="info-label">Completed At:</div>
                    <div class="info-value">{{ $report['order_info']['completed_at'] ? \Carbon\Carbon::parse($report['order_info']['completed_at'])->format('M j, Y g:i A') : 'Not completed' }}</div>
                </div>
            </div>
            <div class="info-row">
                <div class="info-cell" style="width: 100%;">
                    <div class="info-label">Order Details:</div>
                    <div class="info-value">{{ $report['order_info']['order_details'] }}</div>
                </div>
            </div>
            @if($report['order_info']['notes'])
            <div class="info-row">
                <div class="info-cell" style="width: 100%;">
                    <div class="info-label">Notes:</div>
                    <div class="info-value">{{ $report['order_info']['notes'] }}</div>
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
        </div>
    </div>

    <!-- Staff Information -->
    <div class="section">
        <h2>Ordering Staff</h2>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-cell">
                    <div class="info-label">Staff Name:</div>
                    <div class="info-value">{{ $report['staff_info']['name'] }}</div>
                </div>
                <div class="info-cell">
                    <div class="info-label">Role:</div>
                    <div class="info-value">{{ $report['staff_info']['role'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Items -->
    <div class="section">
        <h2>Order Items ({{ count($report['order_items']) }})</h2>
        @if(count($report['order_items']) === 0)
            <div class="no-data">No order items found.</div>
        @else
            <div class="records-list">
                @foreach($report['order_items'] as $item)
                    <div class="record-item">
                        <div class="record-header">
                            <div class="record-header-row">
                                <div class="record-header-cell">
                                    <div class="record-label">Item Type:</div>
                                    <div class="record-value">{{ $item['item_type'] }}</div>
                                </div>
                                <div class="record-header-cell">
                                    <div class="record-label">Item Name:</div>
                                    <div class="record-value">{{ $item['item_name'] }}</div>
                                </div>
                            </div>
                            <div class="record-header-row">
                                <div class="record-header-cell">
                                    <div class="record-label">Required Quantity:</div>
                                    <div class="record-value">{{ $item['quantity_required'] }}</div>
                                </div>
                                <div class="record-header-cell">
                                    <div class="record-label">Used Quantity:</div>
                                    <div class="record-value">{{ $item['quantity_used'] ?? 0 }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="record-content">
                            <div class="record-label">Status:</div>
                            <div class="record-value">{{ $item['status'] }}</div>
                        </div>
                        @if($item['completed_at'])
                            <div class="record-content">
                                <div class="record-label">Completed At:</div>
                                <div class="record-value">{{ \Carbon\Carbon::parse($item['completed_at'])->format('M j, Y g:i A') }}</div>
                            </div>
                        @endif
                        @if($item['notes'])
                            <div class="record-content">
                                <div class="record-label">Notes:</div>
                                <div class="record-value">{{ $item['notes'] }}</div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

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

    <!-- Related Billings -->
    <div class="section">
        <h2>Related Billings ({{ count($report['billings']) }})</h2>
        @if(count($report['billings']) === 0)
            <div class="no-data">No billings found for this medical order.</div>
        @else
            <div class="records-list">
                @foreach($report['billings'] as $billing)
                    <div class="record-item">
                        <div class="record-header">
                            <div class="record-header-row">
                                <div class="record-header-cell">
                                    <div class="record-label">Amount:</div>
                                    <div class="record-value">${{ number_format($billing['amount'], 2) }}</div>
                                </div>
                                <div class="record-header-cell">
                                    <div class="record-label">Status:</div>
                                    <div class="record-value">{{ $billing['status'] }}</div>
                                </div>
                            </div>
                            <div class="record-header-row">
                                <div class="record-header-cell">
                                    <div class="record-label">Billing Date:</div>
                                    <div class="record-value">{{ \Carbon\Carbon::parse($billing['billing_date'])->format('M j, Y') }}</div>
                                </div>
                                <div class="record-header-cell">
                                    <div class="record-label">Due Date:</div>
                                    <div class="record-value">{{ \Carbon\Carbon::parse($billing['due_date'])->format('M j, Y') }}</div>
                                </div>
                            </div>
                        </div>
                        @if($billing['paid_at'])
                            <div class="record-content">
                                <div class="record-label">Paid At:</div>
                                <div class="record-value">{{ \Carbon\Carbon::parse($billing['paid_at'])->format('M j, Y g:i A') }}</div>
                            </div>
                        @endif
                        @if($billing['notes'])
                            <div class="record-content">
                                <div class="record-label">Notes:</div>
                                <div class="record-value">{{ $billing['notes'] }}</div>
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