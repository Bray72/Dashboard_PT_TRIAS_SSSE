<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Work Permit Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
        }

        .container {
            padding: 20px;
            max-width: 100%;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }

        .header h1 {
            font-size: 24px;
            color: #1a5f2e;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 12px;
            color: #666;
            margin: 3px 0;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 11px;
        }

        .info-item {
            flex: 1;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        thead {
            background-color: #9bbb59;
            color: white;
        }

        th {
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #333;
        }

        td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tbody tr:hover {
            background-color: #f0f0f0;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .total-row {
            background-color: #e8f4f0;
            font-weight: bold;
            color: #1a5f2e;
        }

        .metric-summary {
            display: flex;
            justify-content: space-around;
            margin: 25px 0;
            gap: 20px;
            flex-wrap: wrap;
        }

        .metric-item {
            flex: 1;
            min-width: 180px;
            background-color: #f0f8f5;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #9bbb59;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .metric-label {
            font-size: 11px;
            color: #666;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .metric-value {
            font-size: 20px;
            color: #1a5f2e;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ccc;
            font-size: 10px;
            color: #999;
            text-align: center;
        }

        .page-break {
            page-break-after: always;
        }
        .company-header {
            display: flex;
            align-items: flex-start;
            gap: 20px;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #000;
        }

        .header-logo {
            width: 50px;
            height: 50px;
            flex-shrink: 0;
        }

        .header-center {
            flex: 1;
        }

        .header-center h1 {
            font-size: 22px;
            font-weight: bold;
            color: #000;
            margin: 0 0 12px 0;
            letter-spacing: 0.5px;
        }

        .office-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px 25px;
            font-size: 9px;
            line-height: 1.3;
        }

        .office-section strong {
            display: block;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .header-right {
            flex-shrink: 0;
            text-align: center;
        }

        .astra-brand {
            font-size: 14px;
            font-weight: bold;
            color: #000;
            letter-spacing: 1px;
            margin-bottom: 1px;
        }

        .astra-tagline {
            font-size: 8px;
            font-weight: 600;
            color: #333;
            margin-bottom: 6px;
        }

        .certifications {
            display: flex;
            gap: 3px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .cert-box {
            border: 1px solid #666;
            padding: 2px 4px;
            font-size: 7px;
            background-color: #fff;
            min-width: 32px;
            text-align: center;
            line-height: 1.2;
        }
    </style>
</head>
<body>
    <div class="company-header">
        <!-- LOGO -->
        <img src="https://trias-sentosa.com/images/ts.jpg" alt="PT Trias Sentosa Logo" class="header-logo">
        
        <!-- CENTER: Company Name & Offices -->
        <div class="header-center">
            <h1>PT TRIAS SENTOSA Tbk</h1>
            
            <div class="office-grid">
                <div class="office-section">
                    <strong>HEAD OFFICE / WARU PLANT :</strong>
                    Jl. Raya Waru No.1 B, Waru,<br>
                    Sidoarjo 61256, Indonesia<br>
                    Ph: +62-31-8533125<br>
                    Fax: +62-31-8534116
                </div>

                <div class="office-section">
                    <strong>JAKARTA OFFICE :</strong>
                    Altira Business Park<br>
                    Jl. Yos Sudarso Kav.85 Blok A01-07, 5th Floor, Sunter<br>
                    Jakarta Utara 14350, Indonesia<br>
                    Ph: +62-21-29615575<br>
                    Fax: +62-21-29615565
                </div>

                <div class="office-section">
                    <strong>KRIAN PLANT :</strong>
                    Desa Keboharan, Km 26, Krian,<br>
                    Sidoarjo 61262, Indonesia<br>
                    Ph: +62-31-8975825<br>
                    Fax: +62-31-8972998
                </div>

                <div class="office-section">
                    <strong>SURABAYA OFFICE :</strong>
                    Spazio Tower 15th Floor<br>
                    Jl. Mayjen Yono Suwoyo,<br>
                    Surabaya 60225, Indonesia<br>
                    Ph: +62-31-99144888<br>
                    Fax: +62-31-99148510
                </div>
            </div>
        </div>

        <!-- RIGHT: ASTRIA & Certifications -->
        <div class="header-right">
            <div class="astra-brand">ASTRIA</div>
            <div class="astra-tagline">Flexible Packaging<br>Film Manufacturer</div>
            
            <div class="certifications">
                <div class="cert-box">LRQA<br>CERT.</div>
                <div class="cert-box">✓<br>UKAS</div>
                <div class="cert-box">LRQA<br>CERT.</div>
                <div class="cert-box">✓<br>UKAS</div>
                <div class="cert-box">ISCC</div>
                <div class="cert-box">ISO<br>14001</div>
            </div>
        </div>
    </div>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>Work Permit Report</h1>
            <p>Safety Work Permit Statistics</p>
        </div>

        <!-- Info -->
        <div class="info-row">
            <div class="info-item">
                <strong>Month:</strong> {{ $monthName }} {{ $year }}
            </div>
            <div class="info-item">
                <strong>Generated:</strong> {{ $generatedDate }}
            </div>
        </div>

        <!-- Main Table -->
        <table>
            <thead>
                <tr>
                    <th style="width: 40%;">Safety Work Permit Type</th>
                    <th style="width: 15%;" class="text-center">Monthly ({{ substr($monthName, 0, 3) }})</th>
                    <th style="width: 15%;" class="text-center">YTD {{ $year }}</th>
                    <th style="width: 15%;" class="text-center">YTD {{ $year - 1 }}</th>
                    <th style="width: 15%;" class="text-center">Variance</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tableData as $data)
                <tr>
                    <td><strong>{{ $data['name'] }}</strong></td>
                    <td class="text-center">{{ $data['monthly'] }}</td>
                    <td class="text-center">{{ $data['ytd_current'] }}</td>
                    <td class="text-center">{{ $data['ytd_previous'] }}</td>
                    <td class="text-center">
                        @php
                            $variance = $data['ytd_current'] - $data['ytd_previous'];
                            $variancePercent = $data['ytd_previous'] != 0 ? round(($variance / $data['ytd_previous']) * 100, 2) : 0;
                        @endphp
                        <span style="color: {{ $variance >= 0 ? '#d9534f' : '#5cb85c' }};">
                            {{ $variance >= 0 ? '+' : '' }}{{ $variance }} ({{ $variancePercent }}%)
                        </span>
                    </td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td><strong>TOTAL</strong></td>
                    <td class="text-center"><strong>{{ $totalMonthly }}</strong></td>
                    <td class="text-center"><strong>{{ $totalYtdCurrent }}</strong></td>
                    <td class="text-center"><strong>{{ $totalYtdPrevious }}</strong></td>
                    <td class="text-center">
                        @php
                            $totalVariance = $totalYtdCurrent - $totalYtdPrevious;
                            $totalVariancePercent = $totalYtdPrevious != 0 ? round(($totalVariance / $totalYtdPrevious) * 100, 2) : 0;
                        @endphp
                        <strong style="color: {{ $totalVariance >= 0 ? '#d9534f' : '#5cb85c' }};">
                            {{ $totalVariance >= 0 ? '+' : '' }}{{ $totalVariance }} ({{ $totalVariancePercent }}%)
                        </strong>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Metrics Summary -->
        <div class="metric-summary">
            <div class="metric-item">
                <div class="metric-label">Monthly Total</div>
                <div class="metric-value">{{ $totalMonthly }}</div>
            </div>
            <div class="metric-item">
                <div class="metric-label">YTD {{ $year }}</div>
                <div class="metric-value">{{ $totalYtdCurrent }}</div>
            </div>
            <div class="metric-item">
                <div class="metric-label">YTD {{ $year - 1 }}</div>
                <div class="metric-value">{{ $totalYtdPrevious }}</div>
            </div>
            <div class="metric-item">
                <div class="metric-label">Year-over-Year Variance</div>
                <div class="metric-value" style="color: {{ $totalVariance >= 0 ? '#d9534f' : '#5cb85c' }};">
                    {{ $totalVariance >= 0 ? '+' : '' }}{{ $totalVariance }}
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>This report was automatically generated on {{ $generatedDate }}</p>
            <p>For inquiries, please contact the Safety Department</p>
        </div>
    </div>
</body>
</html>
