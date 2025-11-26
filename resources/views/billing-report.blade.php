<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing Report - {{ $billing->id }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background: #fff;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #2c3e50;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header h1 {
            margin: 0 0 5px 0;
            font-size: 28px;
            font-weight: bold;
            color: #2c3e50;
            letter-spacing: 1px;
        }

        .header .subtitle {
            margin: 5px 0;
            font-size: 16px;
            color: #7f8c8d;
            font-weight: 600;
        }

        .header .meta {
            margin: 10px 0 0 0;
            font-size: 10px;
            color: #95a5a6;
        }

        .section {
            margin-bottom: 35px;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50;
            border-bottom: 2px solid #ecf0f1;
            padding-bottom: 8px;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .info-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }

        .info-row {
            display: table-row;
            border-bottom: 1px solid #ecf0f1;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-cell {
            display: table-cell;
            padding: 12px 15px;
            vertical-align: top;
            width: 50%;
        }

        .info-label {
            font-weight: 600;
            color: #7f8c8d;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .info-value {
            color: #2c3e50;
            font-size: 12px;
            font-weight: 500;
        }

        .card {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 4px;
            page-break-inside: avoid;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #dee2e6;
        }

        .card-label {
            font-weight: 600;
            color: #495057;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .card-value {
            color: #212529;
            font-size: 11px;
            line-height: 1.6;
        }

        .no-data {
            text-align: center;
            font-style: italic;
            color: #adb5bd;
            padding: 30px;
            background: #f8f9fa;
            border-radius: 4px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-pending { 
            background-color: rgba(255, 193, 7, 0.2); 
            color: #856404; 
            border: 1px solid #ffc107;
        }
        
        .status-paid { 
            background-color: rgba(40, 167, 69, 0.2); 
            color: #155724; 
            border: 1px solid #28a745;
        }
        
        .status-overdue { 
            background-color: rgba(220, 53, 69, 0.2); 
            color: #721c24; 
            border: 1px solid #dc3545;
        }
        
        .status-partial {
            background-color: rgba(0, 123, 255, 0.2);
            color: #004085;
            border: 1px solid #007bff;
        }
        
        .status-written-off,
        .status-cancelled { 
            background-color: rgba(108, 117, 125, 0.2); 
            color: #383d41; 
            border: 1px solid #6c757d;
        }

        @page {
            margin: 0.75in;
            size: A4;
        }

        .footer {
            margin-top: 60px;
            padding-top: 20px;
            text-align: center;
            font-size: 9px;
            color: #adb5bd;
            border-top: 2px solid #ecf0f1;
        }

        .footer p {
            margin: 5px 0;
        }

        .divider {
            height: 2px;
            background: linear-gradient(to right, transparent, #ecf0f1, transparent);
            margin: 30px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>ANGKOR F CLINIC</h1>
            <div class="subtitle">Billing Statement</div>
            <div class="meta">Generated: {{ now()->format('F j, Y \a\t g:i A') }}</div>
        </div>

        <!-- Billing Information -->
        <div class="section">
            <div class="section-title">Billing Details</div>
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
                        <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $report['billing_info']['status']->label())) }}">
                            {{ $report['billing_info']['status']->label() }}
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

        <div class="divider"></div>

        <!-- Patient Information -->
        <div class="section">
            <div class="section-title">Patient Information</div>
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

        <div class="divider"></div>

        <!-- Related Appointment -->
        @if($report['appointment_info'])
        <div class="section">
            <div class="section-title">Related Appointment</div>
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
            <div class="section-title">Related Visit</div>
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

        <div class="divider"></div>

        <!-- Related Medical Orders -->
        <div class="section">
            <div class="section-title">Medical Orders ({{ count($report['medical_orders']) }})</div>
            @if(count($report['medical_orders']) === 0)
                <div class="no-data">No medical orders associated with this billing.</div>
            @else
                @foreach($report['medical_orders'] as $order)
                    <div class="card">
                        <div class="card-header">
                            <div>
                                <div class="card-label">Order ID</div>
                                <div class="card-value">#{{ $order['id'] }}</div>
                            </div>
                            <div>
                                <div class="card-label">Type</div>
                                <div class="card-value">{{ $order['order_type']?->label() ?? $order['order_type'] ?? 'N/A' }}</div>
                            </div>
                            <div>
                                <div class="card-label">Status</div>
                                <div class="card-value">{{ $order['status']?->label() ?? $order['status'] ?? 'N/A' }}</div>
                            </div>
                            <div>
                                <div class="card-label">Priority</div>
                                <div class="card-value">{{ $order['priority']?->label() ?? $order['priority'] ?? 'N/A' }}</div>
                            </div>
                        </div>
                        <div>
                            <div class="card-label">Order Details</div>
                            <div class="card-value">{{ $order['order_details'] }}</div>
                        </div>
                        <div style="margin-top: 10px;">
                            <div class="card-label">Ordered At</div>
                            <div class="card-value">{{ \Carbon\Carbon::parse($order['ordered_at'])->format('M j, Y g:i A') }}</div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <div class="divider"></div>

        <!-- Related Medical Records -->
        <div class="section">
            <div class="section-title">Medical Records ({{ count($report['medical_records']) }})</div>
            @if(count($report['medical_records']) === 0)
                <div class="no-data">No medical records associated with this billing.</div>
            @else
                @foreach($report['medical_records'] as $record)
                    <div class="card">
                        <div class="card-header">
                            <div>
                                <div class="card-label">Record ID</div>
                                <div class="card-value">#{{ $record['id'] }}</div>
                            </div>
                            <div>
                                <div class="card-label">Date of Service</div>
                                <div class="card-value">{{ \Carbon\Carbon::parse($record['date_of_service'])->format('M j, Y') }}</div>
                            </div>
                        </div>
                        @if($record['diagnosis'])
                            <div style="margin-top: 10px;">
                                <div class="card-label">Diagnosis</div>
                                <div class="card-value">{{ $record['diagnosis'] }}</div>
                            </div>
                        @endif
                        @if($record['treatment'])
                            <div style="margin-top: 10px;">
                                <div class="card-label">Treatment</div>
                                <div class="card-value">{{ $record['treatment'] }}</div>
                            </div>
                        @endif
                        @if($record['notes'])
                            <div style="margin-top: 10px;">
                                <div class="card-label">Notes</div>
                                <div class="card-value">{{ $record['notes'] }}</div>
                            </div>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>

        <div class="footer">
            <p>ANGKOR F CLINIC</p>
            <p>This is an official billing statement. Please retain for your records.</p>
            <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }} • Confidential Medical Document</p>
        </div>
    </div>
</body>
</html>