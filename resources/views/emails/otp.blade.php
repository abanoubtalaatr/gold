<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification Code</title>
    <style>
        :root {
            /* Base Colors */
            --primary-color: #472f92;
            --primary-color-light: #e6e0f7;
            --bg-primary: #ffffff;
            --bg-secondary: #f8f9ff;
            --text-primary: #11142d;
            --text-secondary2: #989bcd;
            --text-secondary: #7f81ac;
            --border-color: #e4e4e4;
            --shadow-color: rgba(67, 24, 255, 0.1);
            --error-color: #ff3b3b;
            --success-color: #00b087;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: var(--bg-secondary);
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: var(--bg-primary);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 15px var(--shadow-color);
            overflow: hidden;
        }
        .header {
            text-align: center;
            background-color: var(--primary-color);
            color: var(--bg-primary);
            padding: 20px 0;
            border-radius: 10px 10px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        .content {
            padding: 20px;
            text-align: center;
            color: var(--text-primary);
        }
        .content p {
            margin: 10px 0;
            font-size: 16px;
        }
        .otp-code {
            font-size: 36px;
            font-weight: bold;
            color: var(--primary-color);
            margin: 20px 0;
        }
        .content .note {
            font-size: 14px;
            color: var(--text-secondary);
        }
        .footer {
            text-align: center;
            padding: 10px 0;
            background-color: var(--primary-color-light);
            color: var(--text-secondary2);
            font-size: 12px;
            border-radius: 0 0 10px 10px;
        }
        .footer p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Fudex</h1>
        </div>
        <div class="content">
            <p>Your OTP Code is:</p>
            <p class="otp-code">{{ $data['otp'] }}</p>
            <p class="note">This code is valid for 10 minutes. Please do not share it with anyone.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Fudex. All rights reserved.</p>
            <p>Need help? Contact us at support@Fudex.com</p>
        </div>
    </div>
</body>
</html>
