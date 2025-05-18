<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet" />
    <title>تقرير نشاط المستخدمين</title>
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
        <h1>تقرير نشاط المستخدمين</h1>
        <p>{{ $date }}</p>
    </div>

    <table>
        <thead>
            <tr>
                @foreach (['رقم المستخدم', 'الاسم', 'البريد الإلكتروني', 'تاريخ التسجيل', 'آخر دخول', 'النوع', 'الحالة'] as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user['user_id'] }}</td>
                    <td>{{ $user['full_name'] }}</td>
                    <td>{{ $user['email'] }}</td>
                    <td>{{ $user['registration_date'] }}</td>
                    <td>{{ $user['last_login'] }}</td>
                    <td>{{ $user['user_type'] }}</td>
                    <td>{{ $user['status'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
