<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $purpose }}</title>
</head>

<body style="margin:0; padding:0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="600"
        style="border-collapse: collapse; background-color: #ffffff; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <!-- Header -->
        <tr>
            <td align="center"
                style="padding: 20px; background-color: #4285F4; color: #ffffff; font-size: 24px; font-weight: bold;">
                {{ $purpose }}
            </td>
        </tr>
        <!-- Body -->
        <tr>
            <td style="padding: 20px;">
                <p style="margin: 0 0 15px 0; font-size: 16px; color: #333;">
                    ជម្រាបសួរ,<br>
                    លោកអ្នកត្រូវបានអញ្ជើញឲ្យចូលរួមក្នុងបេសកកម្មមួយក្រោមប្រធានបទ <strong>{{ $purpose }}</strong>.
                </p>
                <p style="margin: 15px 0; font-size: 16px; color: #333;">
                    <strong>ថ្ងៃចាប់ផ្តើមបេសកកម្ម:</strong> {{ formatKhmerDate($start_date) }}
                </p>
                <p style="margin: 15px 0; font-size: 16px; color: #333;">
                    <strong>ថ្ងៃបញ្ចប់បេសកកម្ម:</strong> {{ formatKhmerDate($end_date) }}
                </p>
                <p style="margin: 15px 0; font-size: 16px; color: #333;">
                    <strong>សាលារៀនដែលត្រូវចុះទៅ:</strong>
                </p>
                <ul style="margin: 0 0 15px 20px; font-size: 16px; color: #333;">
                    @foreach ($schools as $school)
                        <li>{{ $school }}</li>
                    @endforeach
                </ul>
                <p style="margin: 15px 0; font-size: 16px; color: #333;">
                    <strong>ពណ៌នាអំពីបេសកកម្ម:</strong> {{ $description }}
                </p>

                @php
                    // Format dates for Google Calendar: remove hyphens for the start date.
                    $startDateForCalendar = str_replace('-', '', $start_date);
                    // For all-day events, the end date should be set to the day after the event.
                    $endDateForCalendar = \Carbon\Carbon::parse($end_date)->addDay()->format('Ymd');
                    // Build the Google Calendar URL.
                    $googleCalendarUrl = 'https://calendar.google.com/calendar/render?action=TEMPLATE';
                    $googleCalendarUrl .= '&text=' . urlencode($purpose);
                    $googleCalendarUrl .= '&dates=' . $startDateForCalendar . '/' . $endDateForCalendar;
                    $googleCalendarUrl .= '&details=' . urlencode($description);
                @endphp

                <p style="text-align: center; margin: 30px 0;">
                    <a href="{{ $googleCalendarUrl }}"
                        style="background-color: #34A853; color: #ffffff; padding: 12px 20px; text-decoration: none; border-radius: 5px; font-size: 16px; font-weight: bold;">
                        បញ្ចូលទៅ Google Calendar
                    </a>
                </p>
            </td>
        </tr>
        <!-- Footer -->
        <tr>
            <td align="center" style="padding: 10px; background-color: #f4f4f4; font-size: 12px; color: #777;">
                © {{ date('Y') }} Your Company Name. All rights reserved.
            </td>
        </tr>
    </table>
</body>

</html>
