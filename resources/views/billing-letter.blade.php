<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $billing->bill_no }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            padding: 40px 60px;
        }

        .header {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }

        .header-left {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }

        .header-left h1 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .header-left p {
            font-size: 11px;
            margin: 2px 0;
        }

        .header-right {
            display: table-cell;
            width: 40%;
            text-align: right;
            vertical-align: top;
        }

        .bill-box {
            text-align: right;
            margin-bottom: 10px;
        }

        .bill-box p {
            font-size: 11px;
            margin: 3px 0;
        }

        .original-box {
            border: 2px solid #000;
            padding: 5px 15px;
            display: inline-block;
            font-size: 12px;
            font-weight: bold;
        }

        .patient-info {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .patient-left {
            display: table-cell;
            width: 60%;
        }

        .patient-left p {
            font-size: 11px;
            margin: 3px 0;
        }

        .patient-left .label {
            display: inline-block;
            width: 100px;
        }

        .patient-right {
            display: table-cell;
            width: 40%;
            text-align: right;
            vertical-align: top;
        }

        .patient-right p {
            font-size: 11px;
            margin: 3px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th {
            background-color: #fff;
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            font-size: 11px;
            font-weight: bold;
        }

        table td {
            border: 1px solid #000;
            padding: 8px;
            font-size: 11px;
        }

        table td.description {
            font-weight: bold;
        }

        table td.amount,
        table td.discount,
        table td.net {
            text-align: right;
        }

        .total-row td {
            font-weight: bold;
            background-color: #f5f5f5;
        }

        .total-words {
            font-size: 11px;
            margin-bottom: 30px;
        }

        .footer-note {
            text-align: left;
            font-size: 10px;
            color: #0000FF;
            margin-bottom: 80px;
            line-height: 1.6;
        }

        .signature-section {
            text-align: right;
            margin-top: 60px;
        }

        .signature-line {
            border-top: 1px solid #000;
            width: 250px;
            margin-left: auto;
            padding-top: 5px;
            text-align: center;
            font-size: 11px;
        }

        .payment-method {
            font-size: 11px;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="header-left">
            <img src="{{ public_path('images/logo1.png') }}" alt="Angkor-F Hospital" style="width: 130px; margin-bottom: 5px; display: block;">
            <p>Phum Sala Kansaeng, Sangkat Svay Dangkum,</p>
            <p>Siem Reap, Kingdom of Cambodia.</p>
            <p>Tel: (855) 31 3 5555 88</p>
        </div>
        <div class="header-right">
            <div class="bill-box">
                <p><strong>Bill No</strong> {{ $billing->bill_no }}</p>
            </div>
            <p>Receipt</p>
            <div class="original-box">Original</div>
        </div>
    </div>

    <!-- Patient Info -->
    <div class="patient-info">
        <div class="patient-left">
            <p><span class="label">Patient Name</span> {{ $billing->patient->full_name ?? $billing->patient->user->name ?? 'Unknown Patient' }}</p>
            <p><span class="label">ID</span> {{ $billing->patient->id ?? 'N/A' }}</p>
        </div>
        <div class="patient-right">
            <p>{{ \Carbon\Carbon::parse($billing->billing_date)->format('d/m/y') }}</p>
            <p>{{ \Carbon\Carbon::parse($billing->created_at)->format('H:i') }}</p>
        </div>
    </div>

    <!-- Billing Table -->
    <table>
        <thead>
            <tr>
                <th style="width: 50%;">Description</th>
                <th style="width: 16%;">Amount</th>
                <th style="width: 17%;">Discount</th>
                <th style="width: 17%;">Net Amount</th>
            </tr>
        </thead>
        <tbody>
            @if($costBreakdown && isset($costBreakdown['groups']))
                @foreach($costBreakdown['groups'] as $group)
                    <tr>
                        <td class="description">{{ $group['name'] }}</td>
                        <td class="amount">{{ number_format($group['subtotal'], 2) }}</td>
                        <td class="discount"></td>
                        <td class="net">{{ number_format($group['subtotal'], 2) }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td class="description">Medical Services</td>
                    <td class="amount">{{ number_format($billing->amount, 2) }}</td>
                    <td class="discount"></td>
                    <td class="net">{{ number_format($billing->amount, 2) }}</td>
                </tr>
            @endif
            <tr class="total-row">
                <td class="description">Total</td>
                <td class="amount">{{ number_format($billing->amount, 2) }}</td>
                <td class="discount"></td>
                <td class="net">{{ number_format($billing->amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Total in Words -->
    <div class="total-words">
        Total amount in letters: <strong>{{ ucwords(convertNumberToWords($billing->amount)) }} USD</strong>
    </div>

    <!-- Footer Note -->
    <div class="footer-note">
        Patients may return the medicine only if they have experienced adverse reaction to that medication.<br>
        Please keep this receipt for reference.
    </div>

    <!-- Signature -->
    <div class="signature-section">
        <div class="signature-line">
            Cashier
        </div>
    </div>

    <!-- Payment Method -->
    <div class="payment-method">
        Payment By {{ $billing->payment_method ?? 'Cash' }}
    </div>
</body>
</html>

@php
function convertNumberToWords($number) {
    // Convert to integer (floor the decimal value)
    $number = floor($number);

    $words = [
        0 => '', 1 => 'one', 2 => 'two', 3 => 'three', 4 => 'four',
        5 => 'five', 6 => 'six', 7 => 'seven', 8 => 'eight', 9 => 'nine',
        10 => 'ten', 11 => 'eleven', 12 => 'twelve', 13 => 'thirteen',
        14 => 'fourteen', 15 => 'fifteen', 16 => 'sixteen', 17 => 'seventeen',
        18 => 'eighteen', 19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
        40 => 'forty', 50 => 'fifty', 60 => 'sixty', 70 => 'seventy',
        80 => 'eighty', 90 => 'ninety'
    ];

    if ($number < 0) return 'minus ' . convertNumberToWords(abs($number));

    if ($number == 0) return 'zero';

    $string = '';

    if ($number >= 1000) {
        $thousands = floor($number / 1000);
        $string .= convertNumberToWords($thousands) . ' thousand ';
        $number %= 1000;
    }

    if ($number >= 100) {
        $hundreds = floor($number / 100);
        $string .= $words[$hundreds] . ' hundred ';
        $number %= 100;
        if ($number > 0) $string .= 'and ';
    }

    if ($number >= 20) {
        $tens = floor($number / 10) * 10;
        $string .= $words[$tens];
        $number %= 10;
        if ($number > 0) $string .= '-' . $words[$number];
    } elseif ($number > 0) {
        $string .= $words[$number];
    }

    return trim($string);
}
@endphp
