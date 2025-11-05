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
//                    ->where('interested_sqm', '!=', 0)
//                    ->where('company_name', '!=', 'SCI Knowledge Interlinks Pvt. Ltd.')
                    ->groupBy('submission_status')
                    ->pluck('count', 'submission_status')
                    ->toArray(),
                'sponsors_count' => Sponsorship::whereHas('application')->count(),
                'sponsorshipByStatus' => Sponsorship::select('status', \DB::raw('count(*) as count'))
                    ->groupBy('status')
                    ->pluck('count', 'status')
                    ->toArray(),
                'payments' => \DB::table('payments')->select(\DB::raw('count(*) as count'))->pluck('count')->toArray(),
                // Declaration form statistics
                'declarationsFilled' => Application::where('application_type', 'exhibitor')
                    ->where('declarationStatus', 1)
                    ->count(),
                'declarationsNotFilled' => Application::where('application_type', 'exhibitor')
                    ->where(function($query) {
                        $query->where('declarationStatus', 0)
                              ->orWhereNull('declarationStatus');
                    })
                    ->count(),
                'req_sqm_sum' => Application::where('application_type', 'exhibitor')->where('submission_status', 'submitted')->sum('interested_sqm'),
                'approved_sqm_sum' => Application::where('application_type', 'exhibitor')
                    ->where('submission_status', 'approved')
                    ->where('id', '!=', 240)
                    ->sum('allocated_sqm'),
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
