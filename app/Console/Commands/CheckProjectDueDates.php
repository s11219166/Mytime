<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckProjectDueDates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'projects:check-due-dates';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Check project due dates and send notifications';

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notificationService)
    {
        $this->info('Checking project due dates...');
        
        try {
            $notificationService->checkProjectDueDates();
            $this->info('Project due date check completed successfully.');
            Log::info('Project due date check completed successfully.');
        } catch (\Exception $e) {
            $this->error('Error checking project due dates: ' . $e->getMessage());
            Log::error('Error checking project due dates: ' . $e->getMessage());
        }
    }
}
