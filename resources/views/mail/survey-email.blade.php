<!DOCTYPE html>
<html lang="km">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .email-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            box-sizing: border-box;
            border-radius: 8px;
            overflow: hidden;
            /* Ensures no scrollbars */
        }

        .email-header {
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 8px 8px 0 0;
            /* Rounded corners on top */
        }

        .email-body {
            padding: 20px;
            font-size: 16px;
            line-height: 1.5;
            color: #333;
            text-align: left;
        }

        .google-calendar-button {
            display: inline-block;
            background-color: #4285F4;
            color: white;
            padding: 12px 24px;
            text-align: center;
            border-radius: 5px;
            font-size: 16px;
            text-decoration: none;
            margin-top: 20px;
        }

        .google-calendar-button:hover {
            background-color: #357AE8;
        }

        .email-footer {
            margin-top: 20px;
            font-size: 12px;
            text-align: center;
            color: #888;
        }

        .important-info {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            <h2>{{ $title }}</h2>
        </div>
        <div class="email-body">
            <p><strong>សូមជូនដំណឹង:</strong></p>
            <p>ការវាយតម្លៃសម្រាប់សាលារៀនថ្មីបានដាក់ឲ្យបំពេញនៅ <span
                    class="important-info">{{ formatKhmerDate($start_date) }}</span>
                ដោយមានកាលបរិច្ឆេទបញ្ចប់នៅ <span class="important-info">{{ formatKhmerDate($end_date) }}</span>។</p>
            <p><strong>ប្រភេទសាលារៀន:</strong> {{ $school_type }}</p>
            <p><strong>ពិពណ៌នាសម្រាប់ការវាយតម្លៃ:</strong> {{ $description }}</p>
            <!-- Google Calendar Add Event Button -->
            @php
                $googleCalendarUrl =
                    'https://www.google.com/calendar/render?action=TEMPLATE&text=' .
                    urlencode($title) .
                    '&dates=' .
                    Carbon\Carbon::parse($start_date)->format('Ymd\THis\Z') .
                    '/' .
                    Carbon\Carbon::parse($end_date)->format('Ymd\THis\Z') .
                    '&details=' .
                    urlencode($description) .
                    '&location=' .
                    urlencode($school_type);
            @endphp
            <a href="{{ $googleCalendarUrl }}" class="google-calendar-button" target="_blank"
                onclick="handleClickEvent()">
                បញ្ចូលទៅ Google Calendar
            </a>
        </div>

        <div class="email-footer">
            <p>សូមអរគុណសម្រាប់ការចូលរួម!</p>
        </div>
    </div>
</body>

</html>
