<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Record Report - {{ $medicalRecord->id }}</title>
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

        .large-textarea {
            border: 1px solid #ccc;
            padding: 10px;
            background-color: #f9f9f9;
            margin-top: 5px;
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            white-space: pre-line;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Angkor F Clinic</h1>
        <p>Medical Record Report</p>
        <p>Generated on: {{ now()->format('F j, Y \a\t g:i A') }}</p>
    </div>

    <!-- Medical Record Information -->
    <div class="section">
        <h2>Medical Record Information</h2>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-cell">
                    <div class="info-label">Record ID:</div>
                    <div class="info-value">#{{ $report['record_info']['id'] }}</div>
                </div>
                <div class="info-cell">
                    <div class="info-label">Date of Service:</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($report['record_info']['date_of_service'])->format('M j, Y') }}</div>
                </div>
            </div>
            <div class="info-row">
                <div class="info-cell">
                    <div class="info-label">Created At:</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($report['record_info']['created_at'])->format('M j, Y g:i A') }}</div>
                </div>
                <div class="info-cell">
                    <div class="info-label">Updated At:</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($report['record_info']['updated_at'])->format('M j, Y g:i A') }}</div>
                </div>
            </div>
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
        <h2>Staff Information</h2>
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

    <!-- Diagnosis -->
    @if($report['record_info']['diagnosis'])
    <div class="section">
        <h2>Diagnosis</h2>
        <div class="large-textarea">
            {{ $report['record_info']['diagnosis'] }}
        </div>
    </div>
    @endif

    <!-- Treatment -->
    @if($report['record_info']['treatment'])
    <div class="section">
        <h2>Treatment</h2>
        <div class="large-textarea">
            {{ $report['record_info']['treatment'] }}
        </div>
    </div>
    @endif

    <!-- Notes -->
    @if($report['record_info']['notes'])
    <div class="section">
        <h2>Additional Notes</h2>
        <div class="large-textarea">
            {{ $report['record_info']['notes'] }}
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

    <!-- Related Medical Services -->
    <div class="section">
        <h2>Related Medical Services ({{ count($report['medical_services']) }})</h2>
        @if(count($report['medical_services']) === 0)
            <div class="no-data">No related medical services found.</div>
        @else
            <div class="records-list">
                @foreach($report['medical_services'] as $service)
                    <div class="record-item">
                        <div class="record-header">
                            <div class="record-header-row">
                                <div class="record-header-cell">
                                    <div class="record-label">Service Name:</div>
                                    <div class="record-value">{{ $service['name'] }}</div>
                                </div>
                                <div class="record-header-cell">
                                    <div class="record-label">Category:</div>
                                    <div class="record-value">{{ $service['category'] }}</div>
                                </div>
                            </div>
                            <div class="record-header-row">
                                <div class="record-header-cell">
                                    <div class="record-label">Cost:</div>
                                    <div class="record-value">${{ number_format($service['cost'], 2) }}</div>
                                </div>
                                <div class="record-header-cell">
                                    <div class="record-label">Duration (minutes):</div>
                                    <div class="record-value">{{ $service['duration_minutes'] }}</div>
                                </div>
                            </div>
                        </div>
                        @if($service['description'])
                            <div class="record-content">
                                <div class="record-label">Description:</div>
                                <div class="record-value">{{ $service['description'] }}</div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="footer">
        <p>This report was generated by Angkor F Clinic on {{ now()->format('F j, Y \a\t g:i A') }}</p>
        <p>Confidential - For medical use only</p>
    </div>
</body>
</html>