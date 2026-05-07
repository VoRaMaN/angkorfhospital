<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sperm Freezing Report</title>
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

        .section-title { font-weight: bold; font-size: 13px; text-decoration: underline; margin: 10px 0 8px; text-align: center; }

        .row2 { display: table; width: 100%; margin-bottom: 4px; font-size: 11px; }
        .cell2 { display: table-cell; width: 50%; padding: 2px 0; }
        .underline-val { border-bottom: 1px solid #000; display: inline-block; min-width: 130px; }

        table.params { width: 100%; border-collapse: collapse; font-size: 11px; }
        table.params th { text-align: left; font-weight: normal; color: #555; font-size: 10px; padding: 2px 4px; border-bottom: 1px solid #ccc; }
        table.params td { padding: 3px 4px; border-bottom: 1px solid #e0e0e0; }
        table.params td.label-col { width: 35%; }
        table.params td.val-col { width: 25%; }
        table.params td.unit-col { width: 15%; color: #444; }
        table.params td.norm-col { width: 25%; color: #555; font-size: 10px; }
        table.params td.indent { padding-left: 20px; }
        table.params td.bold { font-weight: bold; }

        .vial-row { margin: 8px 0; font-size: 11px; }

        .times-row { display: table; width: 100%; margin-top: 10px; padding-top: 8px; border-top: 1px solid #ccc; font-size: 11px; }
        .times-cell { display: table-cell; width: 50%; padding: 2px 0; }

        .remark { margin-top: 8px; font-size: 11px; padding-top: 6px; border-top: 1px solid #e0e0e0; }

        .sig-row { display: table; width: 100%; margin-top: 14px; padding-top: 8px; border-top: 1px solid #ccc; font-size: 11px; }
        .sig-cell { display: table-cell; width: 50%; }
        .sig-cell .sig-label { font-weight: bold; font-size: 10px; color: #555; text-transform: uppercase; margin-bottom: 2px; }
        .sig-cell .sig-date { font-size: 10px; color: #555; margin-top: 2px; }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <div class="header-left">
            <img src="{{ public_path('images/logo1.png') }}" alt="Angkor-F Clinic" />
            <p>#National Road 6A, Salakonseng Village,</p>
            <p>Sangkat Svay Dangkum, Siem Reap, Cambodia</p>
            <p>Tel: (855) 31 3 5555 88 | (855) 12 881 307</p>
            <p>E-mail: angkorfhospital@gmail.com</p>
        </div>
        <div class="header-right">
            <p><span class="label">Name</span> &nbsp; {{ $report->patient_name ?? '' }}</p>
            <p><span class="label">HN.</span> &nbsp; {{ $report->patient_hn ?? '' }} &nbsp;&nbsp;&nbsp; <span class="label">SEX</span> &nbsp; MALE</p>
            <p><span class="label">DOB</span> &nbsp; {{ $report->patient_dob ?? '' }} &nbsp;&nbsp;&nbsp; <span class="label">Age</span> &nbsp; {{ $report->patient_age ? $report->patient_age.' Yrs.' : '' }}</p>
            <p><span class="label">Date</span> &nbsp; {{ $reportDate }}</p>
            <p><span class="label">Doctor</span> &nbsp; {{ $report->doctor_name ?? '' }}</p>
        </div>
    </div>

    <!-- Lab Title -->
    <div class="lab-title">
        <p>IVF LAB</p>
        <p class="sub">( Sperm Freezing )</p>
    </div>

    <!-- Section title -->
    <p class="section-title">Sperm Freezing</p>

    <!-- Wife / Abstinence row -->
    <div class="row2">
        <div class="cell2">
            <strong>Wife's name</strong> &nbsp;
            <span class="underline-val">{{ $report->wife_name ?? '-' }}</span>
        </div>
        <div class="cell2">
            <strong>Abstinence day</strong> &nbsp;
            <span class="underline-val">{{ $report->abstinence_days ? $report->abstinence_days.' Days' : '-' }}</span>
        </div>
    </div>

    <!-- Parameters table -->
    <table class="params">
        <thead>
            <tr>
                <th class="label-col">Parameter</th>
                <th class="val-col">Value</th>
                <th class="unit-col"></th>
                <th class="norm-col">Normal Range</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Appearance</td>
                <td>{{ $report->appearance ?? '' }}</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>Liquefaction</td>
                <td>{{ $report->liquefaction ?? '' }}</td>
                <td></td>
                <td>30 mins</td>
            </tr>
            <tr>
                <td>Viscosity</td>
                <td>{{ $report->viscosity ?? '' }}</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>Viability</td>
                <td>{{ $report->viability !== null ? $report->viability : '' }}</td>
                <td></td>
                <td>&gt; 75</td>
            </tr>
            <tr>
                <td>Volume</td>
                <td>{{ $report->volume !== null ? $report->volume : '' }}</td>
                <td>ml.</td>
                <td>&gt; 2</td>
            </tr>
            <tr>
                <td>Count</td>
                <td>{{ $report->count_per_ml !== null ? $report->count_per_ml : '' }}</td>
                <td>million/ml.</td>
                <td>&gt; 20</td>
            </tr>
            <tr>
                <td>Total count</td>
                <td>{{ $report->total_count !== null ? $report->total_count : '0' }}</td>
                <td>million</td>
                <td>&gt; 40</td>
            </tr>
            <tr>
                <td>Motile</td>
                <td>{{ $report->motile !== null ? $report->motile : '0' }}</td>
                <td>million/ml.</td>
                <td></td>
            </tr>
            <tr>
                <td>Total motile</td>
                <td>{{ $report->total_motile !== null ? $report->total_motile : '0' }}</td>
                <td>million</td>
                <td></td>
            </tr>
            <tr>
                <td>Motility</td>
                <td>{{ $report->motility !== null ? $report->motility : '100' }}</td>
                <td>%</td>
                <td>&gt; 50</td>
            </tr>
            <tr>
                <td class="bold">Motility rate</td>
                <td colspan="3"></td>
            </tr>
            <tr>
                <td class="indent">4 rapid</td>
                <td>{{ $report->motility_4_rapid !== null ? $report->motility_4_rapid : '' }}</td>
                <td>%</td>
                <td>&gt; 25</td>
            </tr>
            <tr>
                <td class="indent">3 medium</td>
                <td>{{ $report->motility_3_medium !== null ? $report->motility_3_medium : '' }}</td>
                <td>%</td>
                <td></td>
            </tr>
            <tr>
                <td class="indent">2 slow</td>
                <td>{{ $report->motility_2_slow !== null ? $report->motility_2_slow : '' }}</td>
                <td>%</td>
                <td></td>
            </tr>
            <tr>
                <td class="indent">1 static</td>
                <td>{{ $report->motility_1_static !== null ? $report->motility_1_static : '' }}</td>
                <td>%</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <!-- No. of vial -->
    <div class="vial-row">
        <strong>No. of vial</strong> &nbsp;
        <span style="border-bottom:1px solid #000; display:inline-block; min-width:60px; text-align:center;">{{ $report->no_of_vial !== null ? $report->no_of_vial : '' }}</span>
        &nbsp; vials
    </div>

    <!-- Times -->
    <div class="times-row">
        <div class="times-cell"><strong>Ejaculation time</strong> &nbsp; {{ $report->ejaculation_time ?? '' }}</div>
        <div class="times-cell"><strong>Examination time</strong> &nbsp; {{ $report->examination_time ?? '' }}</div>
    </div>
    <div class="times-row">
        <div class="times-cell"><strong>Receive time</strong> &nbsp; {{ $report->receive_time ?? '' }}</div>
        <div class="times-cell"><strong>Finish time</strong> &nbsp; {{ $report->finish_time ?? '' }}</div>
    </div>

    <!-- Remark -->
    <div class="remark">
        <strong>Remark :</strong> &nbsp; {{ $report->remark ?? '' }}
    </div>

    <!-- Signatures -->
    <div class="sig-row">
        <div class="sig-cell">
            <p><strong>Report by</strong> &nbsp; {{ $report->reported_by ?? '' }}</p>
            <p class="sig-date">
                Date &nbsp; {{ $report->reported_date ?? '' }}
                &nbsp;&nbsp; Time : &nbsp; {{ $report->reported_time ?? '' }}
            </p>
        </div>
        <div class="sig-cell">
            <p><strong>Approve by</strong> &nbsp; {{ $report->approved_by ?? '' }}</p>
            <p class="sig-date">
                Date &nbsp; {{ $report->approved_date ?? '' }}
                &nbsp;&nbsp; Time : &nbsp; {{ $report->approved_time ?? '' }}
            </p>
        </div>
    </div>

</body>
</html>
