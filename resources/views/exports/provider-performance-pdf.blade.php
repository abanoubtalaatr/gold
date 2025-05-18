<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet" />
    <title>تقرير أداء المزودين</title>
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            direction: rtl;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
            margin: 30px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: right;
        }

        th {
            background-color: #f8f9fa;
        }

        .summary {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }

        .date {
            text-align: left;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>تقرير أداء المزودين</h2>
        <p class="date">التاريخ: {{ $date }}</p>
    </div>

    <div class="summary">
        <h3>ملخص التقرير</h3>
        <p>إجمالي المزودين: {{ $summary['total_providers'] }}</p>
        <p>إجمالي الإيرادات: {{ number_format($summary['total_revenue'], 2) }} ريال</p>
        <p>متوسط التقييم: {{ number_format($summary['average_rating'], 1) }}</p>
        <p>إجمالي الحجوزات: {{ $summary['total_bookings'] }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>اسم المزود</th>
                <th>الخدمات الرئيسية</th>
                <th>الخدمات الفرعية</th>
                <th>عدد الحجوزات</th>
                <th>متوسط التقييم</th>
                <th>الإيرادات</th>
                <th>خطة الاشتراك</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($providers as $provider)
                <tr>
                    <td>{{ $provider['name'] }}</td>
                    <td>{{ is_array($provider['main_services']) ? implode(', ', $provider['main_services']) : $provider['main_services'] }}
                    </td>
                    <td>{{ is_array($provider['sub_services']) ? implode(', ', $provider['sub_services']) : $provider['sub_services'] }}
                    </td>
                    <td>{{ $provider['bookings_count'] }}</td>
                    <td>{{ number_format($provider['average_rating'], 1) }}</td>
                    <td>{{ number_format($provider['revenue'], 2) }} ريال</td>
                    <td>{{ $provider['subscription_plan'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
