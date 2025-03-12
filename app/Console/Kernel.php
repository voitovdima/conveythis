<?php

namespace App\Console;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\DeleteExpiredFiles::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('files:delete-expired')->everyMinute(); // Перевірка щохвилини
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
