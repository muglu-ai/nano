<?php

namespace App\Providers;

use App\Models\Sponsorship;
use Illuminate\Support\ServiceProvider;
use App\Models\Application;
use App\Models\CoExhibitor;
use App\Models\User;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;



class AnalyticsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('analytics', function () {
            // Exhibition (exhibitor) applications
            $exhibitorApplicationsByStatus = Application::select('submission_status', DB::raw('count(*) as count'))
                ->where('application_type', 'exhibitor')
                ->groupBy('submission_status')
                ->pluck('count', 'submission_status')
                ->toArray();
            
            // Startup Zone applications
            $startupZoneApplicationsByStatus = Application::select('submission_status', DB::raw('count(*) as count'))
                ->where('application_type', 'startup-zone')
                ->groupBy('submission_status')
                ->pluck('count', 'submission_status')
                ->toArray();
            
            // Startup Zone payment statistics
            $startupZoneSubmitted = Application::where('application_type', 'startup-zone')
                ->where('submission_status', 'submitted')
                ->count();
            
            $startupZonePaid = Application::where('application_type', 'startup-zone')
                ->where('submission_status', 'submitted')
                ->whereHas('invoices', function($query) {
                    $query->where('payment_status', 'paid');
                })
                ->count();
            
            $startupZoneNotPaid = $startupZoneSubmitted - $startupZonePaid;
            
            // Exhibitor payment statistics
            $exhibitorSubmitted = Application::where('application_type', 'exhibitor')
                ->where('submission_status', 'submitted')
                ->count();
            
            $exhibitorPaid = Application::where('application_type', 'exhibitor')
                ->where('submission_status', 'submitted')
                ->whereHas('invoices', function($query) {
                    $query->where('payment_status', 'paid');
                })
                ->count();
            
            $exhibitorNotPaid = $exhibitorSubmitted - $exhibitorPaid;
            
            // Total counts
            $totalExhibitorRegistrations = Application::where('application_type', 'exhibitor')->count();
            $totalStartupZoneRegistrations = Application::where('application_type', 'startup-zone')->count();
            
            return [
                'totalApplications' => Application::whereIn('application_type', ['exhibitor', 'sponsor', 'exhibitor+sponsor'])->count(),
                'totalCoExhibitors' => CoExhibitor::count(),
                'totalUsers' => User::count(),
                'totalInvoices' => Invoice::count(),
                'applicationsByStatus' => $exhibitorApplicationsByStatus,
                // Exhibitor payment statistics
                'exhibitor' => [
                    'total' => $totalExhibitorRegistrations,
                    'paid' => $exhibitorPaid,
                    'unpaid' => $exhibitorNotPaid,
                    'byStatus' => $exhibitorApplicationsByStatus,
                ],
                // Startup Zone specific statistics
                'startupZone' => [
                    'total' => $totalStartupZoneRegistrations,
                    'initiated' => $startupZoneApplicationsByStatus['in progress'] ?? 0,
                    'submitted' => $startupZoneSubmitted,
                    'approved' => $startupZoneApplicationsByStatus['approved'] ?? 0,
                    'paid' => $startupZonePaid,
                    'unpaid' => $startupZoneNotPaid,
                    'byStatus' => $startupZoneApplicationsByStatus,
                ],
                'sponsors_count' => Sponsorship::whereHas('application')->count(),
                'sponsorshipByStatus' => Sponsorship::select('status', DB::raw('count(*) as count'))
                    ->groupBy('status')
                    ->pluck('count', 'status')
                    ->toArray(),
                'payments' => DB::table('payments')->select(DB::raw('count(*) as count'))->pluck('count')->toArray(),
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
