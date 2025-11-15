<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Comprehensive Physician Order Form</title>
    <style>
        /* Base styles for clean printing */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 9pt;
            /* Smaller font for higher density */
            color: #000;
        }

        .container {
            width: 100%;
            max-width: 750px;
            margin: 0 auto;
        }

        h1 {
            font-size: 11pt;
            text-align: center;
            margin-bottom: 5px;
            font-weight: normal;
        }

        /* Main document title */
        h2.form-title {
            font-size: 14pt;
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .section-header,
        .admin-section {
            margin-bottom: 10px;
            line-height: 20pt;
            /* Space out the lines */
        }

        /* Style for the blank input lines */
        .input-line {
            display: inline-block;
            border-bottom: 1px solid #000;
            margin-left: 5px;
            margin-right: 20px;
            height: 10pt;
            vertical-align: bottom;
        }

        /* Adjusted widths for the minimal fields */
        .input-line.short {
            width: 100px;
        }

        .input-line.medium {
            width: 150px;
        }

        .input-line.long {
            width: 200px;
        }

        .input-line.half {
            width: 35%;
        }

        .line-wrapper {
            display: block;
            margin-bottom: 5px;
        }

        /* Table styles for all orders */
        .orders-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            border: 1px solid #000;
            font-size: 8pt;
            /* Even smaller font in table */
            margin-bottom: 20px;
            /* Space between order sections */
        }

        .orders-table th,
        .orders-table td {
            border: 1px solid #000;
            padding: 3px 5px;
            height: 18px;
            /* Fixed height for blank rows */
            text-align: left;
            vertical-align: middle;
        }

        .orders-table th {
            font-weight: bold;
            text-align: center;
            font-size: 9pt;
        }

        .orders-table th:first-child {
            text-align: left;
        }

        .instruction-box {
            border: 1px solid #000;
            padding: 5px;
            margin-bottom: 15px;
            font-style: italic;
        }

        .admin-footer {
            margin-top: 30px;
            line-height: 20pt;
        }
    </style>
</head>

<body>

    <div class="container">

        <h1>Hospital / Clinic Name</h1>
        <h2 class="form-title">COMPREHENSIVE PHYSICIAN ORDER FORM</h2>

        <div class="section-header">
            <!-- Simplified Patient and Order Header -->
            <span class="line-wrapper">
                Patient Name: <span class="input-line half"></span>
                DOB: <span class="input-line short"></span>
                MRN/Acct #: <span class="input-line medium"></span>
            </span>
            <span class="line-wrapper">
                Ordering Physician: <span class="input-line long"></span>
                Physician ID/Pager: <span class="input-line short"></span>
            </span>
            <span class="line-wrapper">
                Service/Location: <span class="input-line medium"></span>
                Date/Time of Order: <span class="input-line long"></span>
            </span>
        </div>

        <div class="instruction-box">
            (Physician orders must be checked and signed upon completion by nursing/pharmacy staff)
        </div>

        <!-- 1. MEDICATION ORDERS -->
        <div class="orders-section">
            <table class="orders-table">
                <thead>
                    <tr>
                        <th colspan="5" style="text-align: center; background: none !important;">MEDICATION ORDERS</th>
                    </tr>
                    <tr>
                        <th style="width: 35%; text-align: left;">Medication (Name / Indication)</th>
                        <th style="width: 25%;">Dose / Route</th>
                        <th style="width: 15%;">Frequency</th>
                        <th style="width: 10%;">Start Time</th>
                        <th style="width: 15%;">Order Status</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Placeholder rows for dynamic content -->
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- 2. DIAGNOSTIC & PROCEDURE ORDERS -->
        <div class="orders-section">
            <table class="orders-table">
                <thead>
                    <tr>
                        <th colspan="4" style="text-align: center; background: none !important;">DIAGNOSTIC, IMAGING &
                            PROCEDURE ORDERS</th>
                    </tr>
                    <tr>
                        <th style="width: 45%; text-align: left;">Item / Procedure</th>
                        <th style="width: 25%;">Priority (STAT/Routine)</th>
                        <th style="width: 15%;">Order Time</th>
                        <th style="width: 15%;">Notes/Result</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Placeholder rows for dynamic content -->
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- 3. GENERAL CARE ORDERS -->
        <div class="orders-section">
            <table class="orders-table">
                <thead>
                    <tr>
                        <th colspan="3" style="text-align: center; background: none !important;">GENERAL CARE & NURSING
                            ORDERS</th>
                    </tr>
                    <tr>
                        <th style="width: 35%; text-align: left;">Order Type (e.g., Diet, Activity, Vitals)</th>
                        <th style="width: 45%;">Details / Instructions</th>
                        <th style="width: 20%;">Frequency / PRN</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Placeholder rows for dynamic content -->
                    <tr>
                        <td>Activity</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Diet</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Vitals Monitoring</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>I/O</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Other</td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>


        <div class="admin-footer">
            <span class="line-wrapper">
                Physician Signature: <span class="input-line long"></span>
                DATE: <span class="input-line short"></span>
            </span>
            <span class="line-wrapper">
                Nurse/Staff Name: <span class="input-line full"></span>
            </span>
            <span class="line-wrapper">
                License #: <span class="input-line long"></span>
                Time Implemented: <span class="input-line long"></span>
            </span>
            <span class="line-wrapper">
                Witness Signature (If Required): <span class="input-line half"></span>
            </span>
            <span class="line-wrapper">
                Witness Title: <span class="input-line medium"></span>
                Witness ID: <span class="input-line medium"></span>
            </span>
        </div>

        <div style="text-align: center; margin-top: 20px; font-size: 8pt; font-style: italic;">
            Form generated for {{ date('Y-m-d H:i') }}
        </div>

    </div>

</body>

</html>