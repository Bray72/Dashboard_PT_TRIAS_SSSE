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
    </style>
</head>
<body>
    <!-- HEADER COMPANY -->
    <table width="100%" style="border-bottom: 2px solid #0b2d5c; padding-bottom: 10px; margin-bottom: 10px; background-color:#FFFBB1">
        <tr>
            <!-- LOGO -->
            <td width="8%" style="vertical-align: top;">
                <img src="https://trias-sentosa.com/images/ts.jpg"
                    style="width: 40px; height: auto;">
            </td>

            <!-- TITLE -->
            <td width="92%" style="vertical-align: middle;">
                <div style="font-size: 20px; font-weight: bold; color: #0b2d5c; letter-spacing: 1px;">
                    PT TRIAS SENTOSA Tbk
                </div>

                <div style="margin-top: 8px;">
                    <table width="100%" style="font-size: 10px; color: #000;">
                        <tr>
                            <td width="50%" style="vertical-align: top; padding-right: 10px;">
                                <div style="font-weight: bold; margin-bottom: 4px;">HEAD OFFICE / WARU PLANT :</div>
                                <div>Jl. Raya Waru No.1 B, Waru,</div>
                                <div>Sidoarjo 61256, Indonesia</div>
                                <div>Ph: +62-31-8533125, Fax: +62-31-8534116</div>
                            </td>

                            <td width="50%" style="vertical-align: top;">
                                <div style="font-weight: bold; margin-bottom: 4px;">JAKARTA OFFICE :</div>
                                <div>Altira Business Park</div>
                                <div>Jl. Yos Sudarso Kav.85 Blok A01-07, 5<sup>th</sup> Floor, Sunter</div>
                                <div>Jakarta Utara 14350, Indonesia</div>
                                <div>Ph: +62-21-29615575, Fax: +62-21-29615565</div>
                            </td>
                        </tr>

                        <tr>
                            <td width="50%" style="vertical-align: top; padding-right: 10px; padding-top: 8px;">
                                <div style="font-weight: bold; margin-bottom: 4px;">KRIAN PLANT :</div>
                                <div>Desa Keboharan, Km 26, Krian,</div>
                                <div>Sidoarjo 61262, Indonesia</div>
                                <div>Ph: +62-31-8975825, Fax: +62-31-8972998</div>
                            </td>

                            <td width="50%" style="vertical-align: top; padding-top: 8px;">
                                <div style="font-weight: bold; margin-bottom: 4px;">SURABAYA OFFICE :</div>
                                <div>Spazio Tower 15<sup>th</sup> Floor</div>
                                <div>Jl. Mayjen Yono Suwoyo,</div>
                                <div>Surabaya 60225, Indonesia</div>
                                <div>Ph: +62-31-99144888, Fax: +62-31-99148510</div>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>
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
