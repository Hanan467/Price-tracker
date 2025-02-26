<?php
namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function () {
            \App\Http\Controllers\ScrapeController::checkPriceDrop();
        })->everyTwoHours();
    }

    protected function commands(): void
    {
        require base_path('routes/console.php');
    }
}
