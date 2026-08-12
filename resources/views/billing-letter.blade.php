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
            font-size: 17px;
            line-height: 1.55;
            color: #000;
            padding: 36px 52px;
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
            font-size: 19px;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .header-left p {
            font-size: 15px;
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
            font-size: 15px;
            margin: 3px 0;
        }

        .original-box {
            border: 2px solid #000;
            padding: 5px 15px;
            display: inline-block;
            font-size: 16px;
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
            font-size: 16px;
            margin: 3px 0;
        }

        .patient-left .label {
            display: inline-block;
            width: 120px;
        }

        .patient-right {
            display: table-cell;
            width: 40%;
            text-align: right;
            vertical-align: top;
        }

        .patient-right p {
            font-size: 16px;
            margin: 3px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid #000;
        }

        table th {
            background-color: #fff;
            border-bottom: 1px solid #000;
            padding: 9px 10px;
            text-align: center;
            font-size: 16px;
            font-weight: bold;
        }

        .col-sep {
            border-left: 1px solid #000;
        }

        table td {
            border: none;
            padding: 10px 12px;
            font-size: 16px;
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

        .total-divider td {
            border-top: 2px solid #000 !important;
            padding-top: 8px;
        }

        .total-words {
            font-size: 16px;
            margin-bottom: 30px;
        }

        .footer-note {
            text-align: left;
            font-size: 14px;
            color: #000;
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
            font-size: 15px;
        }

        .pkg-label {
            font-size: 11px;
            font-weight: normal;
            color: #067647;
            border: 1px solid #067647;
            border-radius: 3px;
            padding: 0 4px;
            white-space: nowrap;
        }

        .payment-method {
            font-size: 15px;
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
                <th class="col-sep" style="width: 16%;">Amount</th>
                <th class="col-sep" style="width: 17%;">Discount</th>
                <th class="col-sep" style="width: 17%;">Net Amount</th>
            </tr>
        </thead>
        <tbody>
            @if($costBreakdown && isset($costBreakdown['groups']))
                @foreach($costBreakdown['groups'] as $group)
                    @if($group['type'] === 'special_item')
                        {{-- Special items: show each item individually by name --}}
                        @foreach($group['items'] as $item)
                            <tr>
                                <td class="description">{{ $item['item_name'] }} @if(!empty($item['is_package_included']))<span class="pkg-label">Include Package</span>@endif</td>
                                <td class="amount col-sep">{{ number_format($item['total'], 2) }}</td>
                                <td class="discount col-sep"></td>
                                <td class="net col-sep">{{ number_format($item['total'], 2) }}</td>
                            </tr>
                        @endforeach
                    @elseif($group['type'] === 'rx_medicine')
                        {{-- Medicines: keep grouped, collapse package-included items into one generic row --}}
                        <tr>
                            <td class="description">{{ $group['name'] }}</td>
                            <td class="amount col-sep">{{ number_format($group['subtotal'], 2) }}</td>
                            <td class="discount col-sep"></td>
                            <td class="net col-sep">{{ number_format($group['subtotal'], 2) }}</td>
                        </tr>
                        @if(collect($group['items'])->contains(fn ($item) => !empty($item['is_package_included'])))
                        <tr>
                            <td class="description" style="font-weight:normal;padding-left:28px;">Medicine <span class="pkg-label">Include Package</span></td>
                            <td class="amount col-sep">0.00</td>
                            <td class="discount col-sep"></td>
                            <td class="net col-sep">0.00</td>
                        </tr>
                        @endif
                    @else
                        {{-- Lab and everything else: keep grouped --}}
                        <tr>
                            <td class="description">{{ $group['name'] }}</td>
                            <td class="amount col-sep">{{ number_format($group['subtotal'], 2) }}</td>
                            <td class="discount col-sep"></td>
                            <td class="net col-sep">{{ number_format($group['subtotal'], 2) }}</td>
                        </tr>
                        @foreach($group['items'] as $item)
                            @if(!empty($item['is_package_included']))
                            <tr>
                                <td class="description" style="font-weight:normal;padding-left:28px;">{{ $item['item_name'] }} <span class="pkg-label">Include Package</span></td>
                                <td class="amount col-sep">0.00</td>
                                <td class="discount col-sep"></td>
                                <td class="net col-sep">0.00</td>
                            </tr>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            @else
                <tr>
                    <td class="description">Medical Services</td>
                    <td class="amount col-sep">{{ number_format($billing->amount, 2) }}</td>
                    <td class="discount col-sep"></td>
                    <td class="net col-sep">{{ number_format($billing->amount, 2) }}</td>
                </tr>
            @endif
            @if($billing->discount_amount > 0)
            <tr>
                <td class="description" style="color:#006600;">Discount</td>
                <td class="amount col-sep"></td>
                <td class="discount col-sep" style="color:#006600;">-{{ number_format($billing->discount_amount, 2) }}</td>
                <td class="net col-sep" style="color:#006600;">-{{ number_format($billing->discount_amount, 2) }}</td>
            </tr>
            @endif
            <tr class="total-row total-divider">
                <td class="description">Total</td>
                <td class="amount col-sep">{{ number_format($billing->amount, 2) }}</td>
                <td class="discount col-sep">{{ $billing->discount_amount > 0 ? '-'.number_format($billing->discount_amount, 2) : '' }}</td>
                <td class="net col-sep">{{ number_format($billing->amount - $billing->discount_amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Total in Words -->
    <div class="total-words">
        Total amount in letters: <strong>{{ ucwords(convertNumberToWords($billing->amount - $billing->discount_amount)) }} USD</strong>
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
