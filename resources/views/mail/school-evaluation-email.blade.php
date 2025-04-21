<!DOCTYPE html>
<html lang="km">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $survey_title }}</title>
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
                {{ $survey_title }}
            </td>
        </tr>

        <!-- Body -->
        <tr>
            <td style="padding: 20px;">
                <p style="margin: 0 0 15px 0; font-size: 16px; color: #333;">
                    ជម្រាបសួរ,<br>
                    សូមជម្រាបជូនថា អ្នកបានទទួលការវាយតម្លៃពីសាលា <strong>{{ $school_type }}{{ $school_name }}</strong>
                    ខេត្ត<strong>{{ $province }}</strong>។
                </p>
                <p style="margin: 15px 0; font-size: 16px; color: #333;">
                    <strong>ព័ត៌មានសម្រាប់ការវាយតម្លៃ:</strong>
                </p>
                <ul>
                    <li><strong>ឈ្មោះសាលា:</strong> {{ $school_name }}</li>
                    <li><strong>ប្រភេទសាលា:</strong> {{ $school_type }}</li>
                    <li><strong>ក្រុង/ខេត្ត:</strong> {{ $province }}</li>
                    <li><strong>កាលបរិច្ឆេទចាប់ផ្តើម:</strong> {{ formatKhmerDate($start_date) }}
                    </li>
                    <li><strong>កាលបរិច្ឆេទបញ្ចប់:</strong> {{ formatKhmerDate($end_date) }}</li>
                    <li><strong>ពិន្ទុទទួលបាន:</strong> {{ $score }}</li>
                </ul>

                {{-- <p style="margin: 15px 0; font-size: 16px; color: #333;">
                    សូមបញ្ជាក់ថា អ្នកត្រូវបានអញ្ជើញឲ្យធ្វើការវាយតម្លៃការប្រឹងប្រែងរបស់សាលានេះ។<br>
                    សូមអរគុណសម្រាប់ការចូលរួមក្នុងការវាយតម្លៃនេះ។
                </p> --}}

                <!-- Optional Google Calendar Button -->
                {{-- 
                <a href="{{ $googleCalendarUrl }}" class="button">
                    បញ្ចូលទៅ Google Calendar
                </a>
                --}}
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
