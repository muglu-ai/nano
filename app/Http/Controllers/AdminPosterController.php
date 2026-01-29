<?php

namespace App\Http\Controllers;

use App\Models\PosterRegistration;
use App\Models\PosterAuthor;
use App\Exports\PosterRegistrationsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class AdminPosterController extends Controller
{
    /**
     * Check if user is authorized admin
     */
    private function validateAdminUser()
    {
        $user = auth()->user();
        if (!$user || !in_array($user->role, ['admin', 'super-admin'])) {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Poster Registration Analytics Dashboard
     */
    public function analytics(Request $request)
    {
        $this->validateAdminUser();

        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        // Base query
        $baseQuery = PosterRegistration::query();
        if ($dateFrom) {
            $baseQuery->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $baseQuery->whereDate('created_at', '<=', $dateTo);
        }

        // Total registrations
        $totalRegistrations = (clone $baseQuery)->count();

        // Paid vs Pending
        $paidRegistrations = (clone $baseQuery)->where('payment_status', 'paid')->count();
        $pendingRegistrations = (clone $baseQuery)->where('payment_status', 'pending')->count();
        $failedRegistrations = (clone $baseQuery)->where('payment_status', 'failed')->count();

        // Revenue by currency
        $revenueINR = (clone $baseQuery)
            ->where('payment_status', 'paid')
            ->where('currency', 'INR')
            ->sum('total_amount');

        $revenueUSD = (clone $baseQuery)
            ->where('payment_status', 'paid')
            ->where('currency', 'USD')
            ->sum('total_amount');

        // Sector-wise breakdown
        $sectorWiseData = DB::table('poster_registrations')
            ->select(
                'sector',
                DB::raw('COUNT(CASE WHEN payment_status = "paid" THEN 1 END) as paid_count'),
                DB::raw('COUNT(CASE WHEN payment_status = "pending" THEN 1 END) as pending_count'),
                DB::raw('COUNT(*) as total_count'),
                DB::raw('SUM(CASE WHEN payment_status = "paid" AND currency = "INR" THEN total_amount ELSE 0 END) as revenue_inr'),
                DB::raw('SUM(CASE WHEN payment_status = "paid" AND currency = "USD" THEN total_amount ELSE 0 END) as revenue_usd')
            )
            ->when($dateFrom, function($query) use ($dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($dateTo, function($query) use ($dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            })
            ->groupBy('sector')
            ->orderBy('total_count', 'desc')
            ->get();

        // Currency-wise breakdown (Indian vs International)
        $currencyBreakdown = DB::table('poster_registrations')
            ->select(
                'currency',
                DB::raw('COUNT(CASE WHEN payment_status = "paid" THEN 1 END) as paid_count'),
                DB::raw('COUNT(CASE WHEN payment_status = "pending" THEN 1 END) as pending_count'),
                DB::raw('COUNT(*) as total_count'),
                DB::raw('SUM(CASE WHEN payment_status = "paid" THEN total_amount ELSE 0 END) as revenue')
            )
            ->when($dateFrom, function($query) use ($dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($dateTo, function($query) use ($dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            })
            ->groupBy('currency')
            ->get();

        // Presentation mode breakdown
        $presentationModeData = DB::table('poster_registrations')
            ->select(
                'presentation_mode',
                DB::raw('COUNT(CASE WHEN payment_status = "paid" THEN 1 END) as paid_count'),
                DB::raw('COUNT(CASE WHEN payment_status = "pending" THEN 1 END) as pending_count'),
                DB::raw('COUNT(*) as total_count')
            )
            ->when($dateFrom, function($query) use ($dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($dateTo, function($query) use ($dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            })
            ->groupBy('presentation_mode')
            ->orderBy('total_count', 'desc')
            ->get();

        // Daily trends
        $dailyTrends = DB::table('poster_registrations')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total_count'),
                DB::raw('COUNT(CASE WHEN payment_status = "paid" THEN 1 END) as paid_count'),
                DB::raw('COUNT(CASE WHEN payment_status = "pending" THEN 1 END) as pending_count'),
                DB::raw('SUM(CASE WHEN payment_status = "paid" AND currency = "INR" THEN total_amount ELSE 0 END) as revenue_inr'),
                DB::raw('SUM(CASE WHEN payment_status = "paid" AND currency = "USD" THEN total_amount ELSE 0 END) as revenue_usd')
            )
            ->when($dateFrom, function($query) use ($dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($dateTo, function($query) use ($dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            })
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();

        // Payment gateway breakdown
        $gatewayBreakdown = DB::table('poster_registrations')
            ->select(
                'payment_method',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->where('payment_status', 'paid')
            ->whereNotNull('payment_method')
            ->when($dateFrom, function($query) use ($dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($dateTo, function($query) use ($dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            })
            ->groupBy('payment_method')
            ->get();

        // Total authors count
        $totalAuthors = PosterAuthor::whereHas('posterRegistration', function($q) use ($dateFrom, $dateTo) {
            if ($dateFrom) {
                $q->whereDate('created_at', '>=', $dateFrom);
            }
            if ($dateTo) {
                $q->whereDate('created_at', '<=', $dateTo);
            }
        })->count();

        // Attending authors count
        $attendingAuthors = PosterAuthor::where('will_attend', true)
            ->whereHas('posterRegistration', function($q) use ($dateFrom, $dateTo) {
                if ($dateFrom) {
                    $q->whereDate('created_at', '>=', $dateFrom);
                }
                if ($dateTo) {
                    $q->whereDate('created_at', '<=', $dateTo);
                }
            })->count();

        return view('poster.admin.analytics', compact(
            'totalRegistrations',
            'paidRegistrations',
            'pendingRegistrations',
            'failedRegistrations',
            'revenueINR',
            'revenueUSD',
            'sectorWiseData',
            'currencyBreakdown',
            'presentationModeData',
            'dailyTrends',
            'gatewayBreakdown',
            'totalAuthors',
            'attendingAuthors'
        ));
    }

    /**
     * List all poster registrations
     */
    public function list(Request $request)
    {
        $this->validateAdminUser();

        $query = PosterRegistration::with(['posterAuthors']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('tin_no', 'like', "%{$search}%")
                  ->orWhere('abstract_title', 'like', "%{$search}%")
                  ->orWhere('lead_author_name', 'like', "%{$search}%")
                  ->orWhere('lead_author_email', 'like', "%{$search}%");
            });
        }

        // Payment status filter
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Currency filter (Indian/International)
        if ($request->filled('currency')) {
            $query->where('currency', $request->currency);
        }

        // Sector filter
        if ($request->filled('sector')) {
            $query->where('sector', $request->sector);
        }

        // Presentation mode filter
        if ($request->filled('presentation_mode')) {
            $query->where('presentation_mode', $request->presentation_mode);
        }

        // Date filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $perPage = $request->get('per_page', 25);
        $registrations = $query->paginate($perPage)->withQueryString();

        // Get unique sectors for filter dropdown
        $sectors = PosterRegistration::select('sector')->distinct()->pluck('sector');

        // Get unique presentation modes for filter dropdown
        $presentationModes = PosterRegistration::select('presentation_mode')->distinct()->pluck('presentation_mode');

        return view('poster.admin.list', compact(
            'registrations',
            'sectors',
            'presentationModes'
        ));
    }

    /**
     * Show poster registration details
     */
    public function show($id)
    {
        $this->validateAdminUser();

        $registration = PosterRegistration::with(['posterAuthors.country', 'posterAuthors.state', 'posterAuthors.affiliationCountry'])
            ->findOrFail($id);

        return view('poster.admin.show', compact('registration'));
    }

    /**
     * Export poster registrations
     */
    public function export(Request $request)
    {
        $this->validateAdminUser();

        $filters = [
            'search' => $request->search,
            'payment_status' => $request->payment_status,
            'currency' => $request->currency,
            'sector' => $request->sector,
            'presentation_mode' => $request->presentation_mode,
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
        ];

        $filename = 'poster_registrations_' . date('Y-m-d_His') . '.xlsx';
        return Excel::download(new PosterRegistrationsExport($filters), $filename);
    }

    /**
     * Resend confirmation email
     */
    public function resendEmail(Request $request, $id)
    {
        $this->validateAdminUser();

        $registration = PosterRegistration::with(['posterAuthors'])->findOrFail($id);

        try {
            // Get lead author email
            $leadAuthor = $registration->posterAuthors->where('is_lead_author', true)->first();
            $recipientEmail = $leadAuthor ? $leadAuthor->email : $registration->lead_author_email;

            if (!$recipientEmail) {
                return redirect()->back()->with('error', 'No email address found for this registration.');
            }

            // Prepare email data
            $emailData = [
                'registration' => $registration,
                'authors' => $registration->posterAuthors,
                'leadAuthor' => $leadAuthor,
            ];

            // Determine which email to send based on payment status
            if ($registration->payment_status === 'paid') {
                // Send payment confirmation email
                Mail::send('emails.poster.payment-confirmation', $emailData, function ($message) use ($recipientEmail, $registration) {
                    $message->to($recipientEmail)
                        ->subject('Poster Registration Payment Confirmation - TIN: ' . $registration->tin_no);
                });
            } else {
                // Send registration confirmation email
                Mail::send('emails.poster.registration-confirmation', $emailData, function ($message) use ($recipientEmail, $registration) {
                    $message->to($recipientEmail)
                        ->subject('Poster Registration Confirmation - TIN: ' . $registration->tin_no);
                });
            }

            return redirect()->back()->with('success', 'Email sent successfully to ' . $recipientEmail);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }
}
