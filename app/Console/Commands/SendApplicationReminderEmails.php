<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Application;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Mail\ApplicationReminderEmail;

class SendApplicationReminderEmails extends Command
{
    protected $signature = 'applications:send-reminders';
    protected $description = 'Send reminder emails for applications in progress for 3 days';

    public function handle()
    {
        $threeDaysAgo = Carbon::now()->subDays(3)->toDateString();
        $this->info('Fetching applications with status "in progress" submitted 3 days ago...'. $threeDaysAgo);

        // Fetch applications with 'in progress' status submitted 3 days ago
        $applications = Application::where('submission_status', 'in progress')
            ->whereDate('created_at', '<=', $threeDaysAgo)
            ->limit(1)
            ->get();

        $this->info('Found ' . $applications->count() . ' applications to send reminder emails.');

        Log::info('Found ' . $applications->count() . ' applications to send reminder emails.');

       foreach ($applications as $application) {
           // Send email to company email and BCC to interlinks@semi.org and test.interlinks@gmail.com
           Mail::to($application->company_email)
               ->bcc(['interlinks@semi.org', 'test.interlinks@gmail.com'])
               ->send(new ApplicationReminderEmail($application));
       }

        $this->info('Reminder emails sent successfully.');
    }
}
