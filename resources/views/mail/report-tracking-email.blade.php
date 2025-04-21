<!DOCTYPE html>
<html lang="km">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $mission_purpose }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }

        table {
            width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-collapse: collapse;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        td {
            padding: 15px;
            font-size: 16px;
            color: #333;
        }

        .header {
            background-color: #34A853;
            /* Green Header */
            color: white;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            padding: 20px;
        }

        .footer {
            background-color: #f4f4f4;
            text-align: center;
            font-size: 12px;
            color: #777;
            padding: 10px;
        }

        .button {
            background-color: #4285F4;
            /* Google Blue */
            color: white;
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            display: block;
            text-align: center;
            margin-top: 20px;
        }

        ul {
            margin: 0 0 15px 20px;
            padding: 0;
            font-size: 16px;
            color: #333;
        }

        ul li {
            margin-bottom: 8px;
        }
    </style>
</head>

<body>

    <table>
        <!-- Header -->
        <tr>
            <td class="header">
                {{ $mission_purpose }}
            </td>
        </tr>

        <!-- Body -->
        <tr>
            <td style="padding: 20px;">
                <p style="margin: 0 0 15px 0; font-size: 16px; color: #333;">
                    ជម្រាបសួរ,<br>
                    សូមជម្រាបជូនថា អ្នកបានទទួលរបាយការណ៍នៃការចុះគាំទ្រសាលារៀនពី:
                    <strong>
                        {{ $schools }}
                    </strong>
                </p>
                <p style="margin: 15px 0; font-size: 16px; color: #333;">
                    <strong>ព័ត៌មានសម្រាប់ការវាយតម្លៃ:</strong>
                </p>
                <ul>
                    <li><strong>គោលបំណងបេសកកម្ម:</strong> {{ $mission_purpose }}</li>
                    <li><strong>កាលបរិច្ឆេទចាប់ផ្តើម:</strong> {{ formatKhmerDate($start_date) }}</li>
                    <li><strong>កាលបរិច្ឆេទបញ្ចប់:</strong> {{ formatKhmerDate($end_date) }}</li>
                </ul>

                <p style="margin: 15px 0; font-size: 16px; color: #333;">
                    សូមអរគុណសម្រាប់ការចូលរួមក្នុងការពិចារណារបាយការណ៍នេះ។
                </p>
            </td>
        </tr>

        <!-- Footer -->
        <tr>
            <td class="footer">
                © {{ date('Y') }} Your Company Name. All rights reserved.
            </td>
        </tr>
    </table>

</body>

</html>
