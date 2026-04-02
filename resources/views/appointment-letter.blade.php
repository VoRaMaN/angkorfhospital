<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Letter - {{ $appointment->id }}</title>
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

        .appointment-details {
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
        <img src="{{ public_path('images/logo.png') }}" alt="Angkor F Hospital" style="width: 80px; height: 80px; margin: 0 auto 10px; display: block;">
        <h1>Angkor F Hospital</h1>
        <p>Medical Center</p>
        <p>123 Healthcare Avenue, Medical District</p>
        <p>Phone: (555) 123-4567 | Email: info@angkorfclinic.com</p>
    </div>

    <div class="date">
        {{ now()->format('F j, Y') }}
    </div>

    <div class="recipient">
        <p>{{ $appointment->patient->user?->name ?? $appointment->patient->first_name.' '.$appointment->patient->last_name }}</p>
        <p>{{ $appointment->patient->address }}</p>
        <p>{{ $appointment->patient->phone_number }}</p>
        @if($appointment->patient->email)
        <p>{{ $appointment->patient->email }}</p>
        @endif
    </div>

    <div class="salutation">
        <p>Dear {{ $appointment->patient->user?->name ?? $appointment->patient->first_name.' '.$appointment->patient->last_name }},</p>
    </div>

    <div class="content">
        <p>This letter confirms your appointment with Angkor F Hospital.</p>

        <div class="appointment-details">
            <div class="detail-row">
                <div class="detail-label">Appointment ID:</div>
                <div class="detail-value">#{{ $appointment->id }}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Date & Time:</div>
                <div class="detail-value">{{ \Carbon\Carbon::parse($appointment->appointment_date_time)->format('l, F j, Y \a\t g:i A') }}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Duration:</div>
                <div class="detail-value">{{ $appointment->duration_minutes }} minutes</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Appointment Type:</div>
                <div class="detail-value">{{ $appointment->appointment_type?->label() ?? $appointment->appointment_type ?? 'General Consultation' }}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Healthcare Provider:</div>
                <div class="detail-value">{{ $appointment->staff->user?->name ?? $appointment->staff->first_name.' '.$appointment->staff->last_name }}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Status:</div>
                <div class="detail-value">{{ $appointment->status?->label() ?? $appointment->status ?? 'Scheduled' }}</div>
            </div>
            @if($appointment->reason_for_visit)
            <div class="detail-row">
                <div class="detail-label">Reason for Visit:</div>
                <div class="detail-value">{{ $appointment->reason_for_visit }}</div>
            </div>
            @endif
        </div>

        <p>Please arrive 15 minutes before your scheduled appointment time to complete any necessary paperwork. If you need to reschedule or cancel this appointment, please contact us at least 24 hours in advance.</p>

        <p>We look forward to seeing you at your appointment.</p>

        @if($appointment->notes)
        <p><strong>Additional Notes:</strong> {{ $appointment->notes }}</p>
        @endif
    </div>

    <div class="closing">
        <p>Sincerely,</p>
        <div class="signature">
            Angkor F Hospital Staff
        </div>
    </div>

    <div class="footer">
        <p>This is an official appointment confirmation from Angkor F Hospital. Please keep this letter for your records.</p>
        <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
    </div>
</body>
</html>
