<?php

namespace App\Http\Controllers;

use App\Models\ExhibitorFeedback;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminFeedbackController extends Controller
{
    /**
     * Display exhibitor feedback with analytics for admins.
     */
    public function index(Request $request)
    {
        $this->authorizeAdmin();

        $query = ExhibitorFeedback::query();

        // Filters
        if ($search = trim($request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        if ($rating = $request->input('event_rating')) {
            $query->where('event_rating', (int) $rating);
        }

        if ($recommend = $request->input('would_recommend')) {
            $query->where('would_recommend', $recommend);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        $perPage = (int) $request->input('per_page', 15);

        $feedback = $query->latest()->paginate($perPage);
        $feedback->appends($request->query());

        $averages = ExhibitorFeedback::selectRaw('
            AVG(event_rating) as avg_event,
            AVG(portal_rating) as avg_portal,
            AVG(overall_experience_rating) as avg_overall
        ')->first();

        $recommendationStats = ExhibitorFeedback::select('would_recommend', DB::raw('COUNT(*) as total'))
            ->groupBy('would_recommend')
            ->pluck('total', 'would_recommend');

        $ratingDistribution = ExhibitorFeedback::select('event_rating', DB::raw('COUNT(*) as total'))
            ->groupBy('event_rating')
            ->orderBy('event_rating')
            ->pluck('total', 'event_rating')
            ->toArray();

        $trendData = ExhibitorFeedback::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as total')
        )
            ->where('created_at', '>=', Carbon::now()->subDays(14))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $stats = [
            'total_submissions' => ExhibitorFeedback::count(),
            'avg_event_rating' => round($averages?->avg_event ?? 0, 1),
            'avg_portal_rating' => round($averages?->avg_portal ?? 0, 1),
            'avg_overall_rating' => round($averages?->avg_overall ?? 0, 1),
            'recommend_yes' => $recommendationStats['yes'] ?? 0,
            'recommend_no' => $recommendationStats['no'] ?? 0,
            'recommend_maybe' => $recommendationStats['maybe'] ?? 0,
            'latest_submission' => ExhibitorFeedback::latest()->first(),
        ];

        return view('admin.feedback.index', [
            'feedback' => $feedback,
            'stats' => $stats,
            'ratingDistribution' => $ratingDistribution,
            'trendData' => $trendData,
            'filters' => $request->all(),
        ]);
    }

    protected function authorizeAdmin(): void
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }
    }
}


