<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet" />
    <title>تقرير الاشتراكات</title>
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
        .summary {
            margin-bottom: 30px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        .summary-item {
            padding: 10px;
            background-color: white;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: right;
        }
        th {
            background-color: #f8f9fa;
        }
        .status-active {
            color: #10B981;
        }
        .status-expired {
            color: #EF4444;
        }
        .status-pending {
            color: #F59E0B;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>تقرير الاشتراكات</h2>
        <p>{{ $date }}</p>
    </div>

    <div class="summary">
        <h3>ملخص التقرير</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <strong>إجمالي الاشتراكات:</strong>
                <span>{{ $summary['total_subscriptions'] }}</span>
            </div>
            <div class="summary-item">
                <strong>الاشتراكات النشطة:</strong>
                <span>{{ $summary['active_subscriptions'] }}</span>
            </div>
            <div class="summary-item">
                <strong>إجمالي الإيرادات:</strong>
                <span>{{ number_format($summary['total_revenue'], 2) }} ريال</span>
            </div>
            <div class="summary-item">
                <strong>متوسط قيمة الاشتراك:</strong>
                <span>{{ number_format($summary['average_subscription'], 2) }} ريال</span>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>رقم الاشتراك</th>
                <th>اسم المزود</th>
                <th>خطة الاشتراك</th>
                <th>المبلغ</th>
                <th>تاريخ البداية</th>
                <th>تاريخ النهاية</th>
                <th>الحالة</th>
                <th>حالة التجديد</th>
            </tr>
        </thead>
        <tbody>
            @foreach($subscriptions as $subscription)
                <tr>
                    <td>{{ $subscription['subscription_id'] }}</td>
                    <td>{{ $subscription['provider_name'] }}</td>
                    <td>{{ $subscription['plan'] }}</td>
                    <td>{{ number_format($subscription['amount'], 2) }} ريال</td>
                    <td>{{ $subscription['start_date'] }}</td>
                    <td>{{ $subscription['end_date'] }}</td>
                    <td class="status-{{ $subscription['status'] }}">
                        {{ $subscription['status'] === 'active' ? 'نشط' : ($subscription['status'] === 'expired' ? 'منتهي' : 'قيد الانتظار') }}
                    </td>
                    <td class="status-{{ $subscription['renewal_status'] }}">
                        {{ $subscription['renewal_status'] === 'active' ? 'نشط' : ($subscription['renewal_status'] === 'expired' ? 'منتهي' : 'قيد التجديد') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
