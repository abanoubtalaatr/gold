<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('report') }}</title>
    <style>
        body { 
            font-family: {{ app()->getLocale() === 'ar' ? '"Arial Unicode MS", "Tahoma", "DejaVu Sans", sans-serif' : 'Arial, sans-serif' }}; 
            margin: 0;
            padding: 15px;
            background-color: #ffffff;
            font-size: 10px;
            line-height: 1.3;
            direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }
        
        .title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
            /* text-transform: uppercase; */
        }
        
        .subtitle {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .date-range {
            font-size: 10px;
            color: #666;
            font-weight: bold;
        }
        
        .company-info {
            text-align: center;
            margin-bottom: 15px;
            color: #666;
            font-size: 9px;
        }
        
        .summary-section {
            margin: 15px 0;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 3px;
        }
        
        .summary-title {
            font-size: 12px;
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
        }
        
        .stats-section {
            display: table;
            width: 100%;
            margin: 10px 0;
        }
        
        .stat-box {
            display: table-cell;
            text-align: center;
            width: 33.33%;
            padding: 5px;
        }
        
        .stat-number {
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }
        
        .stat-label {
            font-size: 9px;
            color: #666;
            margin-top: 3px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 8px;
            direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
        }
        
        th {
            background-color: #f8f9fa;
            color: #333;
            padding: 8px 4px;
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
            font-weight: bold;
            font-size: 8px;
            /* text-transform: uppercase; */
            border: 1px solid #dee2e6;
        }
        
        td {
            padding: 6px 4px;
            border: 1px solid #dee2e6;
            font-size: 8px;
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .status-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: bold;
            /* text-transform: uppercase; */
        }
        
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-pending-approval { background-color: #fff3cd; color: #856404; }
        .status-approved { background-color: #d1ecf1; color: #0c5460; }
        .status-piece-sent { background-color: #d1ecf1; color: #0c5460; }
        .status-vendor-already-take-the-piece { background-color: #e2e3e5; color: #495057; }
        .status-sold { background-color: #d4edda; color: #155724; }
        .status-rejected { background-color: #f8d7da; color: #721c24; }
        .status-canceled { background-color: #e2e3e5; color: #495057; }
        .status-confirm-sold-from-vendor { background-color: #d4edda; color: #155724; }
        .status-rented { background-color: #d4edda; color: #155724; }
        .status-available { background-color: #d1ecf1; color: #0c5460; }
        .status-returned { background-color: #e2e3e5; color: #495057; }
        
        .price-cell {
            font-weight: bold;
            color: #333;
            text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};
        }
        
        .weight-cell {
            color: #666;
            font-weight: bold;
        }
        
        .carat-cell {
            color: #666;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 20px;
            text-align: center;
            color: #666;
            font-size: 8px;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
        }
        
        .no-data {
            text-align: center;
            padding: 30px;
            color: #666;
            font-style: italic;
        }

        /* Arabic-specific styles */
        @media screen {
            .arabic-text {
                font-family: "Arial Unicode MS", "Tahoma", "DejaVu Sans", sans-serif;
            }
        }
    </style>

    <script>
        // Auto-print when page loads
        window.addEventListener('load', function() {
            // Small delay to ensure content is fully rendered
            setTimeout(function() {
                window.print();
            }, 500);
        });

        // Optional: Close window after printing (for popup windows)
        window.addEventListener('afterprint', function() {
            // Uncomment the line below if you want to close the window after printing
            // window.close();
        });
    </script>
</head>

<body>
    <div class="header">
        <div class="title"> {{ __('report') }}</div>
        <div class="date-range">
            {{ __('period') }} {{ date('d M Y', strtotime($start_date)) }} {{ __('to') }} {{ date('d M Y', strtotime($end_date)) }}
        </div>
    </div>


    @if(count($data) > 1)
        @php
            $headers = $data[0];
            $rows = array_slice($data, 1);
            $totalRecords = count($rows);
            $totalSales = 0;
            
            if($type === 'sales') {
                foreach($rows as $row) {
                    if(isset($row[5]) && is_numeric($row[5])) {
                        $totalSales += $row[5];
                    }
                }
            }
        @endphp

        <div class="summary-section">
            <div class="summary-title">{{ __('report_summary') }}</div>
            <div class="stats-section">
                <div class="stat-box">
                    <div class="stat-number">{{ $totalRecords }}</div>
                    <div class="stat-label">{{ __('total_records') }}</div>
                </div>
                @if($type === 'sales' && $totalSales > 0)
                <div class="stat-box">
                    <div class="stat-number">{{ number_format($totalSales, 2) }} </div>
                    <div class="stat-label">{{ __('total_sales_value') }}</div>
                </div>
                @endif
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    @foreach($headers as $header)
                        <th>{{ __($header)}}</th>
                        
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $row)
                    <tr>
                        @foreach($row as $index => $cell)
                            @if($headers[$index] === 'Total Price' || $headers[$index] === 'Price')
                                <td class="price-cell">{{ number_format($cell, 2) }} {{ __('currency_egp') }}</td>
                            @elseif($headers[$index] === 'Weight (g)' || $headers[$index] === 'Weight')
                                <td class="weight-cell">{{ $cell }}{{ __('weight_unit') }}</td>
                            @elseif($headers[$index] === 'Carat')
                                <td class="carat-cell">{{ $cell }}{{ __('carat_unit') }}</td>
                            @elseif($headers[$index] === 'Status')
                                @php
                                    $statusClass = 'status-' . str_replace('_', '-', strtolower($cell));
                                    $statusKey = 'status.' . strtolower($cell);
                                @endphp
                                <td><span class="status-badge {{ $statusClass }}">{{ __($statusKey) }}</span></td>
                            @else
                                <td>{{ __($cell) }}</td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">
            <h3>{{ __('no_data_available') }}</h3>
            <p>{{ __('no_records_found') }}</p>
        </div>
    @endif

</body>
</html>