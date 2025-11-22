<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Report - {{ $report['patient_info']['name'] }}</title>
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
    </style>
</head>
<body>
    <div class="header">
        <h1>Angkor F Clinic</h1>
        <p>Patient Report</p>
        <p>Generated on: {{ now()->format('F j, Y \a\t g:i A') }}</p>
    </div>

    <!-- Patient Information -->
    <div class="section">
        <h2>Patient Information</h2>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-cell">
                    <div class="info-label">Full Name:</div>
                    <div class="info-value">{{ $report['patient_info']['title'] ? $report['patient_info']['title'] . ' ' : '' }}{{ $report['patient_info']['first_name'] }} {{ $report['patient_info']['last_name'] }}</div>
                </div>
                <div class="info-cell">
                    <div class="info-label">Native Name:</div>
                    <div class="info-value">{{ $report['patient_info']['native_name'] ?? 'N/A' }}</div>
                </div>
            </div>
            <div class="info-row">
                <div class="info-cell">
                    <div class="info-label">Date of Birth:</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($report['patient_info']['date_of_birth'])->format('M j, Y') }}</div>
                </div>
                <div class="info-cell">
                    <div class="info-label">Gender:</div>
                    <div class="info-value">{{ ucfirst($report['patient_info']['gender']) }}</div>
                </div>
            </div>
            <div class="info-row">
                <div class="info-cell">
                    <div class="info-label">Patient ID:</div>
                    <div class="info-value">#{{ $report['patient_info']['id'] }}</div>
                </div>
                <div class="info-cell">
                    <div class="info-label">Email:</div>
                    <div class="info-value">{{ $report['patient_info']['email'] ?? 'N/A' }}</div>
                </div>
            </div>
            
            <!-- Contact -->
            <div class="info-row">
                <div class="info-cell">
                    <div class="info-label">Mobile Phone:</div>
                    <div class="info-value">{{ $report['patient_info']['mobile_phone'] ?? 'N/A' }}</div>
                </div>
                <div class="info-cell">
                    <div class="info-label">Home Phone:</div>
                    <div class="info-value">{{ $report['patient_info']['home_phone'] ?? 'N/A' }}</div>
                </div>
            </div>

            <!-- Address -->
            <div class="info-row">
                <div class="info-cell" style="width: 100%;">
                    <div class="info-label">Address:</div>
                    <div class="info-value">
                        {{ $report['patient_info']['address'] }}<br>
                        {{ $report['patient_info']['city'] }}, {{ $report['patient_info']['province'] }} {{ $report['patient_info']['postal_code'] }}<br>
                        {{ $report['patient_info']['country'] }}
                    </div>
                </div>
            </div>

            <!-- Employment -->
            <div class="info-row">
                <div class="info-cell">
                    <div class="info-label">Occupation:</div>
                    <div class="info-value">{{ $report['patient_info']['occupation'] ?? 'N/A' }}</div>
                </div>
                <div class="info-cell">
                    <div class="info-label">Employer:</div>
                    <div class="info-value">{{ $report['patient_info']['employer'] ?? 'N/A' }}</div>
                </div>
            </div>

            <!-- Emergency Contact -->
            <div class="info-row">
                <div class="info-cell">
                    <div class="info-label">Emergency Contact:</div>
                    <div class="info-value">
                        {{ $report['patient_info']['emergency_contact_name'] ?? 'N/A' }} 
                        ({{ $report['patient_info']['emergency_contact_relationship'] ?? 'N/A' }})
                    </div>
                </div>
                <div class="info-cell">
                    <div class="info-label">Emergency Phone:</div>
                    <div class="info-value">{{ $report['patient_info']['emergency_contact_phone'] ?? 'N/A' }}</div>
                </div>
            </div>

            <!-- Insurance -->
            <div class="info-row">
                <div class="info-cell" style="width: 100%;">
                    <div class="info-label">Insurance:</div>
                    <div class="info-value">
                        @if($report['patient_info']['insurance_provider'])
                            Provider: {{ $report['patient_info']['insurance_provider'] }}<br>
                            Policy #: {{ $report['patient_info']['insurance_policy_number'] ?? 'N/A' }}<br>
                            Plan: {{ $report['patient_info']['insurance_plan_name'] ?? 'N/A' }}
                        @else
                            {{ $report['patient_info']['insurance_info'] ?? 'N/A' }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Appointments -->
    <div class="section">
        <h2>Appointments ({{ count($report['appointments']) }})</h2>
        @if(count($report['appointments']) === 0)
            <div class="no-data">No appointments found.</div>
        @else
            <div class="records-list">
                @foreach($report['appointments'] as $appointment)
                    <div class="record-item">
                        <div class="record-header">
                            <div class="record-header-row">
                                <div class="record-header-cell">
                                    <div class="record-label">Date & Time:</div>
                                    <div class="record-value">{{ \Carbon\Carbon::parse($appointment['appointment_date_time'])->format('M j, Y g:i A') }}</div>
                                </div>
                                <div class="record-header-cell">
                                    <div class="record-label">Staff:</div>
                                    <div class="record-value">{{ $appointment['staff_name'] }}</div>
                                </div>
                            </div>
                            <div class="record-header-row">
                                <div class="record-header-cell">
                                    <div class="record-label">Reason:</div>
                                    <div class="record-value">{{ $appointment['reason_for_visit'] }}</div>
                                </div>
                                <div class="record-header-cell">
                                    <div class="record-label">Status:</div>
                                    <div class="record-value">{{ $appointment['status']?->label() ?? $appointment['status'] ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Visits -->
    <div class="section">
        <h2>Visits ({{ count($report['visits']) }})</h2>
        @if(count($report['visits']) === 0)
            <div class="no-data">No visits found.</div>
        @else
            <div class="records-list">
                @foreach($report['visits'] as $visit)
                    <div class="record-item">
                        <div class="record-header">
                            <div class="record-header-row">
                                <div class="record-header-cell">
                                    <div class="record-label">Visit Date & Time:</div>
                                    <div class="record-value">{{ \Carbon\Carbon::parse($visit['visit_date_time'])->format('M j, Y g:i A') }}</div>
                                </div>
                                <div class="record-header-cell">
                                    <div class="record-label">Status:</div>
                                    <div class="record-value">{{ $visit['status']?->label() ?? $visit['status'] ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="record-header-row">
                                <div class="record-header-cell">
                                    <div class="record-label">Staff:</div>
                                    <div class="record-value">{{ $visit['staff_name'] ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                        @if($visit['notes'])
                            <div class="record-content">
                                <div class="record-label">Notes:</div>
                                <div class="record-value">{{ $visit['notes'] }}</div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Medical Orders -->
    <div class="section">
        <h2>Medical Orders ({{ count($report['medical_orders']) }})</h2>
        @if(count($report['medical_orders']) === 0)
            <div class="no-data">No medical orders found.</div>
        @else
            <div class="records-list">
                @foreach($report['medical_orders'] as $order)
                    <div class="record-item">
                        <div class="record-header">
                            <div class="record-header-row">
                                <div class="record-header-cell">
                                    <div class="record-label">Order Type:</div>
                                    <div class="record-value">{{ $order['order_type']?->label() ?? $order['order_type'] ?? 'N/A' }}</div>
                                </div>
                                <div class="record-header-cell">
                                    <div class="record-label">Status:</div>
                                    <div class="record-value">{{ $order['status']?->label() ?? $order['status'] ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="record-header-row">
                                <div class="record-header-cell">
                                    <div class="record-label">Ordered At:</div>
                                    <div class="record-value">{{ \Carbon\Carbon::parse($order['ordered_at'])->format('M j, Y g:i A') }}</div>
                                </div>
                                <div class="record-header-cell">
                                    <div class="record-label">Staff:</div>
                                    <div class="record-value">{{ $order['staff_name'] ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="record-content">
                            <div class="record-label">Details:</div>
                            <div class="record-value">{{ $order['order_details'] }}</div>
                        </div>
                        @if($order['notes'])
                            <div class="record-content">
                                <div class="record-label">Notes:</div>
                                <div class="record-value">{{ $order['notes'] }}</div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Medical Records -->
    <div class="section">
        <h2>Medical Records ({{ count($report['medical_records']) }})</h2>
        @if(count($report['medical_records']) === 0)
            <div class="no-data">No medical records found.</div>
        @else
            <div class="records-list">
                @foreach($report['medical_records'] as $record)
                    <div class="record-item">
                        <div class="record-header">
                            <div class="record-header-row">
                                <div class="record-header-cell">
                                    <div class="record-label">Date of Service:</div>
                                    <div class="record-value">{{ \Carbon\Carbon::parse($record['date_of_service'])->format('M j, Y') }}</div>
                                </div>
                                <div class="record-header-cell">
                                    <div class="record-label">Record ID:</div>
                                    <div class="record-value">#{{ $record['id'] }}</div>
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

    <!-- Patient Files -->
    <div class="section">
        <h2>Patient Files ({{ count($report['patient_files']) }})</h2>
        @if(count($report['patient_files']) === 0)
            <div class="no-data">No files found.</div>
        @else
            <div class="records-list">
                @foreach($report['patient_files'] as $file)
                    <div class="record-item">
                        <div class="record-header">
                            <div class="record-header-row">
                                <div class="record-header-cell">
                                    <div class="record-label">File Type:</div>
                                    <div class="record-value">{{ $file['file_type']?->label() ?? $file['file_type'] ?? 'N/A' }}</div>
                                </div>
                                <div class="record-header-cell">
                                    <div class="record-label">Filename:</div>
                                    <div class="record-value">{{ $file['filename'] }}</div>
                                </div>
                            </div>
                            <div class="record-header-row">
                                <div class="record-header-cell">
                                    <div class="record-label">Uploaded At:</div>
                                    <div class="record-value">{{ $file['uploaded_at'] ? \Carbon\Carbon::parse($file['uploaded_at'])->format('M j, Y g:i A') : 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                        @if($file['notes'])
                            <div class="record-content">
                                <div class="record-label">Notes:</div>
                                <div class="record-value">{{ $file['notes'] }}</div>
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