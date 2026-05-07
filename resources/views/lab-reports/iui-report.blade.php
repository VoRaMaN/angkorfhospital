<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>IUI Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #000; padding: 30px 40px; }

        .header { display: table; width: 100%; margin-bottom: 12px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header-left { display: table-cell; vertical-align: top; width: 55%; }
        .header-left img { width: 120px; display: block; margin-bottom: 4px; }
        .header-left p { font-size: 10px; margin: 1px 0; color: #333; }
        .header-right { display: table-cell; vertical-align: top; text-align: right; width: 45%; font-size: 11px; }
        .header-right p { margin: 2px 0; }
        .header-right .label { font-weight: bold; }

        .lab-title { text-align: center; margin: 8px 0; }
        .lab-title p { font-weight: bold; font-size: 13px; }
        .lab-title .sub { font-size: 12px; font-weight: normal; }

        .section-title { font-weight: bold; font-size: 12px; text-decoration: underline; margin-bottom: 6px; }

        table.info { width: 100%; font-size: 11px; margin-bottom: 4px; border-collapse: collapse; }
        table.info td { padding: 2px 0; vertical-align: top; }
        .underline-val { border-bottom: 1px solid #000; display: inline-block; min-width: 140px; }

        .wash-table { display: table; width: 100%; margin-top: 10px; }
        .wash-col { display: table-cell; width: 50%; vertical-align: top; padding-right: 8px; }
        .wash-col:last-child { padding-right: 0; padding-left: 8px; }
        .wash-title { font-weight: bold; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #999; padding-bottom: 2px; margin-bottom: 4px; color: #444; }

        table.params { width: 100%; border-collapse: collapse; font-size: 11px; }
        table.params th { text-align: left; font-weight: normal; color: #666; font-size: 10px; padding: 2px 4px; }
        table.params td { padding: 3px 4px; border-bottom: 1px solid #e0e0e0; }
        table.params td.bold { font-weight: bold; }

        .times-row { display: table; width: 100%; margin-top: 10px; padding-top: 8px; border-top: 1px solid #ccc; font-size: 11px; }
        .times-cell { display: table-cell; width: 50%; padding: 2px 0; }

        .remark { margin-top: 8px; font-size: 11px; }

        .sig-row { display: table; width: 100%; margin-top: 12px; padding-top: 8px; border-top: 1px solid #ccc; font-size: 11px; }
        .sig-cell { display: table-cell; width: 50%; }
        .sig-cell .sig-label { font-weight: bold; font-size: 10px; color: #555; text-transform: uppercase; margin-bottom: 2px; }
        .sig-cell .sig-date { font-size: 10px; color: #555; margin-top: 2px; }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <div class="header-left">
            <img src="{{ public_path('images/logo1.png') }}" alt="Angkor-F Hospital" />
            <p>#National Road 6A, Salakonseng Village,</p>
            <p>Sangkat Svay Dangkum, Siem Reap, Cambodia</p>
            <p>Tel: (855) 31 3 5555 88 | (855) 12 881 307</p>
            <p>E-mail: angkorfhospital@gmail.com</p>
        </div>
        <div class="header-right">
            <p><span class="label">Name:</span> {{ $report->patient_name ?? '—' }}</p>
            <p><span class="label">HN:</span> {{ $report->patient_hn ?? '—' }} &nbsp;&nbsp; <span class="label">SEX:</span> MALE</p>
            <p><span class="label">DOB:</span> {{ $report->patient_dob ?? '—' }} &nbsp;&nbsp; <span class="label">Age:</span> {{ $report->patient_age ? $report->patient_age.' Yrs.' : '—' }}</p>
            <p><span class="label">Date:</span> {{ $reportDate }}</p>
            <p><span class="label">Doctor:</span> {{ $report->doctor_name ?? '—' }}</p>
        </div>
    </div>

    <!-- Lab Title -->
    <div class="lab-title">
        <p>IVF LAB</p>
        <p class="sub">( IUI Semen Analysis Report )</p>
    </div>

    <!-- Section title -->
    <p class="section-title">IUI Semen Analysis</p>

    <!-- Wife / Sperm info -->
    <table class="info">
        <tr>
            <td style="width:50%"><strong>Wife's Name:</strong> <span class="underline-val">{{ $report->wife_name ?? '' }}</span></td>
            <td><strong>H.N:</strong> {{ $report->wife_hn ?? '—' }}</td>
        </tr>
        <tr>
            <td colspan="2" style="padding-top:4px">
                <strong>Sperm type:</strong>
                {{ $report->owner_sperm ? '☑' : '☐' }} Owner &nbsp;
                {{ $report->donor_sperm ? '☑' : '☐' }} Donor &nbsp;
                {{ $report->fresh_sperm ? '☑' : '☐' }} Fresh &nbsp;
                {{ $report->frozen_sperm ? '☑' : '☐' }} Frozen
                @if($report->frozen_sperm && $report->frozen_vial) ({{ $report->frozen_vial }} vial) @endif
            </td>
        </tr>
        <tr>
            <td><strong>Abstinence Days:</strong> {{ $report->abstinence_days ? $report->abstinence_days.' Days' : '—' }}</td>
            <td><strong>Appearance:</strong> {{ $report->appearance ?? '—' }}</td>
        </tr>
        <tr>
            <td><strong>Liquefaction:</strong> {{ $report->liquefaction ?? '—' }}</td>
            <td><strong>Viscosity:</strong> {{ $report->viscosity ?? '—' }}</td>
        </tr>
    </table>

    <!-- Pre / Post Wash tables -->
    <div class="wash-table">
        <div class="wash-col">
            <p class="wash-title">Pre-Wash</p>
            <table class="params">
                <thead><tr><th style="width:55%">Parameter</th><th>Value</th></tr></thead>
                <tbody>
                    <tr><td>Volume</td><td>{{ $report->pre_volume !== null ? $report->pre_volume.' ml' : '—' }}</td></tr>
                    <tr><td>Count / ml</td><td>{{ $report->pre_count !== null ? $report->pre_count.' x10⁶/ml' : '—' }}</td></tr>
                    <tr><td>Total Count</td><td>{{ $report->pre_total_count !== null ? $report->pre_total_count.' x10⁶' : '—' }}</td></tr>
                    <tr><td>Motile Count</td><td>{{ $report->pre_motile !== null ? $report->pre_motile.' x10⁶/ml' : '—' }}</td></tr>
                    <tr><td>Total Motile</td><td>{{ $report->pre_total_motile !== null ? $report->pre_total_motile.' x10⁶' : '—' }}</td></tr>
                    <tr><td>Motility</td><td>{{ $report->pre_motility !== null ? $report->pre_motility.'%' : '—' }}</td></tr>
                    <tr><td>Grade 4 (Rapid)</td><td>{{ $report->pre_motility_4_rapid !== null ? $report->pre_motility_4_rapid.'%' : '—' }}</td></tr>
                    <tr><td>Grade 3 (Medium)</td><td>{{ $report->pre_motility_3_medium !== null ? $report->pre_motility_3_medium.'%' : '—' }}</td></tr>
                    <tr><td>Grade 2 (Slow)</td><td>{{ $report->pre_motility_2_slow !== null ? $report->pre_motility_2_slow.'%' : '—' }}</td></tr>
                    <tr><td>Grade 1 (Static)</td><td>{{ $report->pre_motility_1_static !== null ? $report->pre_motility_1_static.'%' : '—' }}</td></tr>
                </tbody>
            </table>
        </div>
        <div class="wash-col">
            <p class="wash-title">Post-Wash</p>
            <table class="params">
                <thead><tr><th style="width:55%">Parameter</th><th>Value</th></tr></thead>
                <tbody>
                    <tr><td>Volume</td><td>{{ $report->post_volume !== null ? $report->post_volume.' ml' : '—' }}</td></tr>
                    <tr><td>Count / ml</td><td>{{ $report->post_count !== null ? $report->post_count.' x10⁶/ml' : '—' }}</td></tr>
                    <tr><td>Total Count</td><td>{{ $report->post_total_count !== null ? $report->post_total_count.' x10⁶' : '—' }}</td></tr>
                    <tr><td>Motile Count</td><td>{{ $report->post_motile !== null ? $report->post_motile.' x10⁶/ml' : '—' }}</td></tr>
                    <tr><td>Total Motile</td><td>{{ $report->post_total_motile !== null ? $report->post_total_motile.' x10⁶' : '—' }}</td></tr>
                    <tr><td>Motility</td><td>{{ $report->post_motility !== null ? $report->post_motility.'%' : '—' }}</td></tr>
                    <tr><td>Grade 4 (Rapid)</td><td>{{ $report->post_motility_4_rapid !== null ? $report->post_motility_4_rapid.'%' : '—' }}</td></tr>
                    <tr><td>Grade 3 (Medium)</td><td>{{ $report->post_motility_3_medium !== null ? $report->post_motility_3_medium.'%' : '—' }}</td></tr>
                    <tr><td>Grade 2 (Slow)</td><td>{{ $report->post_motility_2_slow !== null ? $report->post_motility_2_slow.'%' : '—' }}</td></tr>
                    <tr><td>Grade 1 (Static)</td><td>{{ $report->post_motility_1_static !== null ? $report->post_motility_1_static.'%' : '—' }}</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Times -->
    <div class="times-row">
        <div class="times-cell"><strong>Ejaculation Time:</strong> {{ $report->ejaculation_time ?? '—' }}</div>
        <div class="times-cell"><strong>Receive Time:</strong> {{ $report->receive_time ?? '—' }}</div>
        <div class="times-cell"><strong>Examination Time:</strong> {{ $report->examination_time ?? '—' }}</div>
        <div class="times-cell"><strong>Finish Time:</strong> {{ $report->finish_time ?? '—' }}</div>
    </div>

    <!-- Remark -->
    @if($report->remark)
    <div class="remark"><strong>Remark:</strong> {{ $report->remark }}</div>
    @endif

    <!-- Signatures -->
    <div class="sig-row">
        <div class="sig-cell">
            <p class="sig-label">Reported By</p>
            <p>{{ $report->reported_by ?? '—' }}</p>
            <p class="sig-date">
                {{ $report->reported_date ? 'Date: '.$report->reported_date : '' }}
                {{ $report->reported_time ? ' Time: '.$report->reported_time : '' }}
                @if(!$report->reported_date && !$report->reported_time) — @endif
            </p>
        </div>
        <div class="sig-cell">
            <p class="sig-label">Approved By</p>
            <p>{{ $report->approved_by ?? '—' }}</p>
            <p class="sig-date">
                {{ $report->approved_date ? 'Date: '.$report->approved_date : '' }}
                {{ $report->approved_time ? ' Time: '.$report->approved_time : '' }}
                @if(!$report->approved_date && !$report->approved_time) — @endif
            </p>
        </div>
    </div>

</body>
</html>
