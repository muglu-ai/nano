<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Models\Application;
use App\Models\Invoice;
use App\Models\StallManning;
use App\Models\ComplimentaryDelegate;
use App\Models\CoExhibitor;
use App\Models\ExhibitionParticipant;
use Illuminate\Support\Facades\Log;
use App\Models\RequirementsOrder;

class ActiveUserAnalytics extends ServiceProvider
{
    public function register(): void
    {
        ini_set('max_execution_time', 120); // 120 seconds
        $this->app->singleton('activeUserAnalytics', function () {
            $coExhibitorsWithInvoices = $this->getCoExhibitorsWithInvoices();
            $inactiveCoExhibitors = $this->inactiveCoExhibitor($coExhibitorsWithInvoices);
            $activeCoExhibitors = $this->activeCoExhibitor($coExhibitorsWithInvoices);

            return [
                'totalUsersWithApplications' => array_merge(
                    $this->getActiveUsersWithApplications(),
                    $coExhibitorsWithInvoices
                ),
                'totalUsers' => $this->getTotalUsers() + count($coExhibitorsWithInvoices),
                'totalApplications' => $this->getTotalApplications() + count($coExhibitorsWithInvoices),
                'applicationsWithExtraPlaces' => $this->getApplicationsWithExtraPlaces() + ($this->getCoExhibitorsWithExtraPlaces($coExhibitorsWithInvoices)),
                'inactiveUsersWithApplications' => array_merge(
                    $this->getInactiveUsersWithApplications(),
                    $inactiveCoExhibitors
                ),
                'countInactiveUsersWithApplications' => $this->countInactiveUsersWithApplications() + count($inactiveCoExhibitors),
                'activeUsersWithActivitiesCount' => $this->getActiveUsersWithActivitiesCount(),
                'activeUsersWithActivitiesList' => array_merge(
                    $this->getActiveUsersWithActivitiesList(),
                    $activeCoExhibitors
                ),
            ];
        });
    }

    public function boot(): void {}

    private function approvedApplicationsQuery()
    {
        return Application::where('submission_status', 'approved')
            ->whereHas('invoices', fn($q) => $q->whereIn('payment_status', ['paid', 'partial']))
            ->whereHas('eventContact');
    }

    private function applicationUserDetails($application): array
    {
        return [
            'user_id' => $application->user->id,
            'user_name' => $application->user->name,
            'user_email' => $application->user->email,
            'application_id' => $application->id,
            'application_status' => $application->submission_status,
            'company_name' => $application->company_name,
            'eventContact' => optional($application->eventContact)->only(['first_name', 'last_name', 'email', 'contact_number']),
        ];
    }

    public function getActiveUsersWithApplications(): array
    {
        return $this->approvedApplicationsQuery()
            ->with('user:id,name,email')
            ->get()
            ->map(fn($app) => $this->applicationUserDetails($app))
            ->toArray();
    }

    public function getTotalUsers(): int
    {
        return User::whereHas('applications', function ($q) {
            $q->where('submission_status', 'approved')
                ->whereHas('invoices', fn($iq) => $iq->whereIn('payment_status', ['paid', 'partial']));
        })->count();
    }

    public function getTotalApplications(): int
    {
        return $this->approvedApplicationsQuery()->count();
    }

    public function getApplicationsWithExtraPlaces(): array
    {
        return $this->approvedApplicationsQuery()
            ->whereHas('requirementsOrders')
            ->with(['requirementsOrders', 'user:id,name,email'])
            ->get()
            ->map(fn($app) => [
                'application_id' => $app->id,
                'extra_places' => $app->requirementsOrders->count(),
                'user_id' => $app->user->id,
                'user_name' => $app->user->name,
                'user_email' => $app->user->email,
            ])
            ->toArray();
    }

    public function getInactiveUsersWithApplications(): array
    {
        return $this->approvedApplicationsQuery()
            ->whereDoesntHave('requirementsOrders')
            ->whereDoesntHave('stallManning')
            ->whereDoesntHave('complimentaryDelegates')
            ->with('user:id,name,email')
            ->get()
            ->map(fn($app) => $this->applicationUserDetails($app))
            ->toArray();
    }

    public function countInactiveUsersWithApplications(): int
    {
        return $this->approvedApplicationsQuery()
            ->whereDoesntHave('requirementsOrders')
            ->whereDoesntHave('stallManning')
            ->whereDoesntHave('complimentaryDelegates')
            ->count();
    }

    public function getActiveUsersWithActivitiesCount(): int
    {
        return User::whereHas('applications', function ($q) {
            $q->where('submission_status', 'approved')
                ->whereHas('invoices', fn($iq) => $iq->whereIn('payment_status', ['paid', 'partial']))
                ->where(
                    fn($q) =>
                    $q->whereHas('requirementsOrders')
                        ->orWhereHas('stallManning')
                        ->orWhereHas('complimentaryDelegates')
                );
        })->count();
    }

    public function getActiveUsersWithActivitiesList(): array
    {
        return User::whereHas('applications', function ($q) {
            $q->where('submission_status', 'approved')
                ->whereHas('invoices', fn($iq) => $iq->whereIn('payment_status', ['paid', 'partial']))
                ->where(
                    fn($q) =>
                    $q->whereHas('requirementsOrders')
                        ->orWhereHas('stallManning')
                        ->orWhereHas('complimentaryDelegates')
                );
        })
            ->with('applications')
            ->get()
            ->map(fn($user) => [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
            ])
            ->toArray();
    }

    public function getCoExhibitorsWithInvoices(): array
    {
        return CoExhibitor::where('status', 'approved')
            ->where(function ($query) {
                $query->whereNull('pavilion_name')->whereHas('invoices', fn($q) => $q->whereIn('payment_status', ['paid', 'partial']))
                    ->orWhereNotNull('pavilion_name');
            })
            ->get()
            ->map(fn($c) => $c->only(['id', 'user_id', 'co_exhibitor_id', 'co_exhibitor_name', 'contact_person', 'email', 'phone']))
            ->toArray();
    }

    public function getCoExhibitorsWithExtraPlaces(array $coExhibitors): array
    {
        return array_filter($coExhibitors, function ($c) {
            return RequirementsOrder::where('user_id', $c['user_id'] ?? null)->exists();
        });
    }

    public function activeCoExhibitor(array $coExhibitors): array
    {
        return array_filter($coExhibitors, function ($c) {
            $participants = ExhibitionParticipant::where('coExhibitor_id', $c['id'])->get();
            foreach ($participants as $p) {
                if ($p->stallManning()->exists() || $p->complimentaryDelegates()->exists()) {
                    return true;
                }
            }
            return false;
        });
    }

    public function inactiveCoExhibitor(array $coExhibitors): array
    {
        return array_filter($coExhibitors, function ($c) {
            $participants = ExhibitionParticipant::where('coExhibitor_id', $c['id'])->get();
            if ($participants->isEmpty()) return true;

            foreach ($participants as $p) {
                if ($p->stallManning()->exists() || $p->complimentaryDelegates()->exists()) {
                    return false;
                }
            }
            return true;
        });
    }
}
