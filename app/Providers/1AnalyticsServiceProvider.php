<?php

namespace App\Providers;

use App\Models\Sponsorship;
use Illuminate\Support\ServiceProvider;
use App\Models\Application;
use App\Models\CoExhibitor;
use App\Models\User;
use App\Models\Invoice;



class AnalyticsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('analytics', function () {
            return [
               'totalApplications' => Application::where('application_type', 'exhibitor')->count(),
                'totalCoExhibitors' => CoExhibitor::count(),
                'totalUsers' => User::count(),
                'totalInvoices' => Invoice::count(),
                'applicationsByStatus' => Application::select('submission_status', \DB::raw('count(*) as count'))
                                        ->where('application_type', 'exhibitor')
                                        ->whereDoesntHave('sponsorships')
                                        ->groupBy('submission_status')
                                        ->pluck('count', 'submission_status')
                                        ->toArray(),
                'sponsors_count' => Sponsorship::whereHas('application')->count(),
                'sponsorshipByStatus' => Sponsorship::select('status', \DB::raw('count(*) as count'))
                    ->groupBy('status')
                    ->pluck('count', 'status')
                    ->toArray(),
                'payments' => \DB::table('payments')->select(\DB::raw('count(*) as count'))->pluck('count')->toArray(),
//                'invoices' => Invoice::select('type', \DB::raw('count(*) as count'))
//                    ->pluck('count', 'type')
//                    ->toArray(),

            ];
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
