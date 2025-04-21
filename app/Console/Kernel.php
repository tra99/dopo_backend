<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Config;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function () {
            Config::create([
                'name' => 'hello',
                'options' => json_encode([
                    'key'   => 'days',
                    'value' => 21,
                ]),
            ]);
        })->everyMinute(); // Use Laravel’s built-in everyMinute()
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
