<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Safety Metrics Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #2c3e50;
            padding-bottom: 15px;
        }
        
        .header h1 {
            margin: 0;
            color: #2c3e50;
            font-size: 24px;
        }
        
        .header p {
            margin: 5px 0;
            color: #666;
            font-size: 14px;
        }
        
        .info-box {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }
        
        .info-item {
            font-size: 12px;
        }
        
        .info-item strong {
            color: #2c3e50;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        thead {
            background-color: #2c3e50;
            color: white;
        }
        
        th {
            padding: 12px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ddd;
            font-size: 11px;
        }
        
        td {
            padding: 10px 12px;
            border: 1px solid #ddd;
            font-size: 11px;
        }
        
        tbody tr:nth-child(odd) {
            background-color: #f8f9fa;
        }
        
        tbody tr:hover {
            background-color: #e8eef7;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
        
        .metric-summary {
            display: flex;
            gap: 20px;
            margin-top: 20px;
            padding: 15px;
            background-color: #f0f7ff;
            border-left: 4px solid #2c3e50;
        }
        
        .metric-item {
            flex: 1;
            padding: 10px;
        }
        
        .metric-label {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .metric-value {
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50;
        }
    </style>
</head>
<body>
    <style>
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

    <div class="header">
        <h1>Safety Metrics Report</h1>
        <p>{{ $company->name }}</p>
        <p>Year: {{ $year }}</p>
    </div>

    <div class="info-box">
        <div class="info-item">
            <strong>Company:</strong> {{ $company->name }}
        </div>
        <div class="info-item">
            <strong>Year:</strong> {{ $year }}
        </div>
        <div class="info-item">
            <strong>Generated:</strong> {{ $generatedDate }}
        </div>
    </div>

    <h3 style="color: #2c3e50; border-bottom: 2px solid #2c3e50; padding-bottom: 10px;">Monthly Safety Metrics</h3>

    <table>
        <thead>
            <tr>
                <th>Month</th>
                <th class="text-right">Man Hours</th>
                <th class="text-right">Employees</th>
                <th class="text-right">LTA</th>
                <th class="text-right">Lost Work Days</th>
                <th class="text-right">Lost Time (hrs)</th>
                <th class="text-right">Work Accidents</th>
                <th class="text-right">SR</th>
                <th class="text-right">FR</th>
                <th class="text-right">IR</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tableData as $row)
            <tr>
                <td>{{ $row['month'] }}</td>
                <td class="text-right">{{ number_format($row['man_hours'], 0) }}</td>
                <td class="text-right">{{ $row['employee'] }}</td>
                <td class="text-right">{{ $row['lta'] }}</td>
                <td class="text-right">{{ $row['lost_work_days'] }}</td>
                <td class="text-right">{{ $row['lost_time'] }}</td>
                <td class="text-right">{{ $row['kecelakaan_kerja'] }}</td>
                <td class="text-right">{{ number_format($row['sr'], 2) }}</td>
                <td class="text-right">{{ number_format($row['fr'], 2) }}</td>
                <td class="text-right">{{ number_format($row['ir'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="metric-summary">
        <div class="metric-item">
            <div class="metric-label">Total Man Hours</div>
            <div class="metric-value">{{ number_format(array_sum(array_column($tableData, 'man_hours')), 0) }}</div>
        </div>
        <div class="metric-item">
            <div class="metric-label">Total LTA</div>
            <div class="metric-value">{{ array_sum(array_column($tableData, 'lta')) }}</div>
        </div>
        <div class="metric-item">
            <div class="metric-label">Total Lost Work Days</div>
            <div class="metric-value">{{ array_sum(array_column($tableData, 'lost_work_days')) }}</div>
        </div>
        <div class="metric-item">
            <div class="metric-label">Total Work Accidents</div>
            <div class="metric-value">{{ array_sum(array_column($tableData, 'kecelakaan_kerja')) }}</div>
        </div>
    </div>

    <div class="footer">
        <p>This is an automatically generated report from the Safety Management System.</p>
        <p>For questions or discrepancies, please contact the Safety Department.</p>
    </div>
</body>
</html>
