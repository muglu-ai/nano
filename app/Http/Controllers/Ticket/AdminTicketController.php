<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\Controller;
use App\Models\Events;
use App\Models\Ticket\TicketRegistration;
use App\Models\Ticket\TicketOrder;
use App\Models\Ticket\TicketDelegate;
use App\Models\Ticket\TicketContact;
use App\Models\Ticket\TicketPayment;
use App\Models\Ticket\TicketRegistrationTracking;
use App\Models\Country;
use App\Models\State;
use App\Mail\TicketRegistrationMail;
use App\Exports\TicketRegistrationsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class AdminTicketController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if (!in_array($user->role, ['admin', 'super-admin'])) {
                abort(403, 'Unauthorized access');
            }
            return $next($request);
        });
    }

    /**
     * List all registrations with filters
     */
    public function registrations(Request $request)
    {
        $query = TicketRegistration::with(['event', 'contact', 'order', 'delegates', 'registrationCategory']);

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('company_phone', 'like', "%{$search}%")
                  ->orWhere('gstin', 'like', "%{$search}%")
                  ->orWhereHas('contact', function($contactQuery) use ($search) {
                      $contactQuery->where('email', 'like', "%{$search}%")
                                    ->orWhere('name', 'like', "%{$search}%")
                                    ->orWhere('phone', 'like', "%{$search}%");
                  })
                  ->orWhereHas('order', function($orderQuery) use ($search) {
                      $orderQuery->where('order_no', 'like', "%{$search}%");
                  })
                  ->orWhereHas('delegates', function($delegateQuery) use ($search) {
                      $delegateQuery->where('email', 'like', "%{$search}%")
                                    ->orWhere('first_name', 'like', "%{$search}%")
                                    ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by event
        if ($request->has('event_id') && !empty($request->event_id)) {
            $query->where('event_id', $request->event_id);
        }

        // Filter by nationality
        if ($request->has('nationality') && !empty($request->nationality)) {
            $query->where('nationality', $request->nationality);
        }

        // Filter by order status
        if ($request->has('status') && !empty($request->status)) {
            $query->whereHas('order', function($orderQuery) use ($request) {
                $orderQuery->where('status', $request->status);
            });
        }

        // Filter by payment gateway
        if ($request->has('gateway') && !empty($request->gateway)) {
            // Get order IDs that have payments from this gateway
            $paymentOrderIds = TicketPayment::where('gateway_name', $request->gateway)
                ->where('status', 'completed')
                ->get()
                ->flatMap(function($payment) {
                    return $payment->order_ids_json ?? [];
                })
                ->unique()
                ->toArray();
            
            if (!empty($paymentOrderIds)) {
                $query->whereHas('order', function($orderQuery) use ($paymentOrderIds) {
                    $orderQuery->whereIn('id', $paymentOrderIds);
                });
            } else {
                // No orders found with this gateway, return empty result
                $query->whereRaw('1 = 0');
            }
        }

        // Filter by date range
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        $allowedSortFields = ['created_at', 'company_name', 'nationality'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Pagination
        $perPage = $request->get('per_page', 25);
        $registrations = $query->paginate($perPage);
        $registrations->appends($request->query());

        // Get events for filter dropdown
        $events = Events::orderBy('event_year', 'desc')->orderBy('event_name', 'asc')->get();

        return view('tickets.admin.registrations.index', compact('registrations', 'events'));
    }

    /**
     * Show registration details
     */
    public function showRegistration($id)
    {
        $registration = TicketRegistration::with([
            'event',
            'contact',
            'order.items.ticketType',
            'delegates.assignment.ticketType',
            'registrationCategory'
        ])->findOrFail($id);

        // Get payment information
        $payment = null;
        if ($registration->order) {
            $payment = TicketPayment::whereJsonContains('order_ids_json', $registration->order->id)
                ->where('status', 'completed')
                ->orderBy('paid_at', 'desc')
                ->first();
        }

        return view('tickets.admin.registrations.show', compact('registration', 'payment'));
    }

    /**
     * Show edit form
     */
    public function editRegistration($id)
    {
        $registration = TicketRegistration::with([
            'event',
            'contact',
            'order',
            'delegates',
            'registrationCategory'
        ])->findOrFail($id);

        $countries = Country::orderBy('name')->get();
        $states = State::where('country_id', $registration->company_country ?? 1)->orderBy('name')->get();

        return view('tickets.admin.registrations.edit', compact('registration', 'countries', 'states'));
    }

    /**
     * Update registration
     */
    public function updateRegistration(Request $request, $id)
    {
        $registration = TicketRegistration::with(['contact', 'order'])->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'company_country' => 'nullable|string|max:100',
            'company_state' => 'nullable|string|max:100',
            'company_city' => 'nullable|string|max:100',
            'company_phone' => 'nullable|string|max:20',
            'contact_name' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'gstin' => 'nullable|string|max:15',
            'gst_legal_name' => 'nullable|string|max:255',
            'gst_address' => 'nullable|string|max:500',
            'gst_state' => 'nullable|string|max:100',
            'nationality' => 'required|in:Indian,International',
            'delegates' => 'required|array|min:1',
            'delegates.*.salutation' => 'required|string|max:10',
            'delegates.*.first_name' => 'required|string|max:255',
            'delegates.*.last_name' => 'required|string|max:255',
            'delegates.*.email' => 'required|email|max:255',
            'delegates.*.phone' => 'nullable|string|max:20',
            'delegates.*.job_title' => 'nullable|string|max:255',
            'order_status' => 'nullable|in:pending,paid,cancelled,refunded',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            // Update registration
            $gstRequired = $request->has('gst_required') && $request->gst_required == '1';
            $registration->update([
                'company_name' => $request->company_name,
                'company_country' => $request->company_country,
                'company_state' => $request->company_state,
                'company_city' => $request->company_city,
                'company_phone' => $request->company_phone,
                'gst_required' => $gstRequired,
                'gstin' => $gstRequired ? $request->gstin : null,
                'gst_legal_name' => $gstRequired ? $request->gst_legal_name : null,
                'gst_address' => $gstRequired ? $request->gst_address : null,
                'gst_state' => $gstRequired ? $request->gst_state : null,
                'nationality' => $request->nationality,
            ]);

            // Update contact
            if ($registration->contact) {
                $registration->contact->update([
                    'name' => $request->contact_name,
                    'email' => $request->contact_email,
                    'phone' => $request->contact_phone,
                ]);
            }

            // Update order status if provided
            if ($request->order_status && $registration->order) {
                $registration->order->update([
                    'status' => $request->order_status,
                ]);
            }

            // Update delegates
            $delegateIds = [];
            foreach ($request->delegates as $delegateData) {
                if (isset($delegateData['id'])) {
                    // Update existing delegate
                    $delegate = TicketDelegate::find($delegateData['id']);
                    if ($delegate && $delegate->registration_id == $registration->id) {
                        $delegate->update([
                            'salutation' => $delegateData['salutation'],
                            'first_name' => $delegateData['first_name'],
                            'last_name' => $delegateData['last_name'],
                            'email' => $delegateData['email'],
                            'phone' => $delegateData['phone'] ?? null,
                            'job_title' => $delegateData['job_title'] ?? null,
                        ]);
                        $delegateIds[] = $delegate->id;
                    }
                } else {
                    // Create new delegate
                    $delegate = TicketDelegate::create([
                        'registration_id' => $registration->id,
                        'salutation' => $delegateData['salutation'],
                        'first_name' => $delegateData['first_name'],
                        'last_name' => $delegateData['last_name'],
                        'email' => $delegateData['email'],
                        'phone' => $delegateData['phone'] ?? null,
                        'job_title' => $delegateData['job_title'] ?? null,
                    ]);
                    $delegateIds[] = $delegate->id;
                }
            }

            // Delete delegates that are no longer in the list
            TicketDelegate::where('registration_id', $registration->id)
                ->whereNotIn('id', $delegateIds)
                ->delete();

            DB::commit();

            Log::info('Ticket registration updated by admin', [
                'registration_id' => $registration->id,
                'admin_id' => auth()->id(),
                'changes' => $request->all(),
            ]);

            return redirect()->route('admin.tickets.registrations.show', $registration->id)
                ->with('success', 'Registration updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating ticket registration', [
                'registration_id' => $registration->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to update registration. Please try again.')
                ->withInput();
        }
    }

    /**
     * Resend email
     */
    public function resendEmail(Request $request, $id)
    {
        $registration = TicketRegistration::with(['order', 'event', 'contact'])->findOrFail($id);

        if (!$registration->order) {
            return back()->with('error', 'No order found for this registration.');
        }

        $emailType = $request->input('email_type', 'auto'); // 'auto', 'registration', 'payment'

        try {
            $isPaymentSuccessful = false;
            if ($emailType === 'auto') {
                $isPaymentSuccessful = $registration->order->status === 'paid';
            } elseif ($emailType === 'payment') {
                $isPaymentSuccessful = true;
            }

            $event = $registration->event ?? Events::first();

            Mail::to($registration->contact->email)->send(
                new TicketRegistrationMail($registration->order, $event, $isPaymentSuccessful)
            );

            Log::info('Ticket registration email resent by admin', [
                'registration_id' => $registration->id,
                'order_id' => $registration->order->id,
                'email' => $registration->contact->email,
                'email_type' => $emailType,
                'admin_id' => auth()->id(),
            ]);

            return back()->with('success', 'Email sent successfully to ' . $registration->contact->email);
        } catch (\Exception $e) {
            Log::error('Error resending ticket registration email', [
                'registration_id' => $registration->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to send email. Please try again.');
        }
    }

    /**
     * Export registrations
     */
    public function exportRegistrations(Request $request)
    {
        $filters = [
            'event_id' => $request->event_id,
            'status' => $request->status,
            'nationality' => $request->nationality,
            'gateway' => $request->gateway,
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'search' => $request->search,
        ];

        $filename = 'ticket_registrations_' . date('Y-m-d_His') . '.xlsx';

        return Excel::download(new TicketRegistrationsExport($filters), $filename);
    }

    /**
     * List all orders
     */
    public function orders(Request $request)
    {
        $query = TicketOrder::with(['registration.event', 'registration.contact', 'items.ticketType']);

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_no', 'like', "%{$search}%")
                  ->orWhereHas('registration', function($regQuery) use ($search) {
                      $regQuery->where('company_name', 'like', "%{$search}%")
                               ->orWhereHas('contact', function($contactQuery) use ($search) {
                                   $contactQuery->where('email', 'like', "%{$search}%");
                               });
                  });
            });
        }

        // Filter by status
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Filter by event
        if ($request->has('event_id') && !empty($request->event_id)) {
            $query->whereHas('registration', function($regQuery) use ($request) {
                $regQuery->where('event_id', $request->event_id);
            });
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        $allowedSortFields = ['created_at', 'order_no', 'total', 'status'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Pagination
        $perPage = $request->get('per_page', 25);
        $orders = $query->paginate($perPage);
        $orders->appends($request->query());

        $events = Events::orderBy('event_year', 'desc')->orderBy('event_name', 'asc')->get();

        return view('tickets.admin.orders.index', compact('orders', 'events'));
    }

    /**
     * Show order details
     */
    public function showOrder($id)
    {
        $order = TicketOrder::with([
            'registration.event',
            'registration.contact',
            'registration.delegates',
            'items.ticketType',
            'promoCode'
        ])->findOrFail($id);

        $payment = TicketPayment::whereJsonContains('order_ids_json', $order->id)
            ->where('status', 'completed')
            ->orderBy('paid_at', 'desc')
            ->first();

        return view('tickets.admin.orders.show', compact('order', 'payment'));
    }

    /**
     * Registration Analytics Dashboard
     */
    public function registrationAnalytics(Request $request)
    {
        $eventId = $request->get('event_id');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        // Base query
        $baseQuery = TicketRegistration::with(['order', 'registrationCategory']);
        if ($eventId) {
            $baseQuery->where('event_id', $eventId);
        }
        if ($dateFrom) {
            $baseQuery->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $baseQuery->whereDate('created_at', '<=', $dateTo);
        }

        // Total registrations
        $totalRegistrations = (clone $baseQuery)->count();

        // Paid vs Not Paid
        $paidRegistrations = (clone $baseQuery)
            ->whereHas('order', function($q) {
                $q->where('status', 'paid');
            })
            ->count();
        
        $notPaidRegistrations = $totalRegistrations - $paidRegistrations;

        // Category-wise Registration with Nationality (Paid & Not Paid)
        $categoryWiseData = DB::table('ticket_registrations as tr')
            ->leftJoin('ticket_registration_categories as trc', 'tr.registration_category_id', '=', 'trc.id')
            ->leftJoin('ticket_orders as to', 'tr.id', '=', 'to.registration_id')
            ->select(
                'tr.registration_category_id as category_id',
                'trc.name as category_name',
                'tr.nationality',
                DB::raw('COUNT(CASE WHEN to.status = "paid" THEN 1 END) as paid_count'),
                DB::raw('COUNT(CASE WHEN to.status != "paid" OR to.status IS NULL THEN 1 END) as not_paid_count'),
                DB::raw('COUNT(*) as total_count')
            )
            ->when($eventId, function($query) use ($eventId) {
                $query->where('tr.event_id', $eventId);
            })
            ->when($dateFrom, function($query) use ($dateFrom) {
                $query->whereDate('tr.created_at', '>=', $dateFrom);
            })
            ->when($dateTo, function($query) use ($dateTo) {
                $query->whereDate('tr.created_at', '<=', $dateTo);
            })
            ->groupBy('tr.registration_category_id', 'trc.name', 'tr.nationality')
            ->orderBy('trc.name')
            ->orderBy('tr.nationality')
            ->get();

        // Registration trends for paid (daily)
        $paidTrends = DB::table('ticket_registrations as tr')
            ->join('ticket_orders as to', 'tr.id', '=', 'to.registration_id')
            ->select(
                DB::raw('DATE(tr.created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(CASE WHEN to.status = "paid" THEN 1 ELSE 0 END) as paid_count'),
                DB::raw('SUM(CASE WHEN to.status = "paid" AND tr.nationality = "International" THEN to.total ELSE 0 END) as revenue_usd'),
                DB::raw('SUM(CASE WHEN to.status = "paid" AND tr.nationality = "Indian" THEN to.total ELSE 0 END) as revenue_inr')
            )
            ->when($eventId, function($query) use ($eventId) {
                $query->where('tr.event_id', $eventId);
            })
            ->when($dateFrom, function($query) use ($dateFrom) {
                $query->whereDate('tr.created_at', '>=', $dateFrom);
            })
            ->when($dateTo, function($query) use ($dateTo) {
                $query->whereDate('tr.created_at', '<=', $dateTo);
            })
            ->groupBy(DB::raw('DATE(tr.created_at)'))
            ->orderBy('date', 'asc')
            ->get();

        // Registration trends for not paid (daily)
        $notPaidTrends = DB::table('ticket_registrations as tr')
            ->leftJoin('ticket_orders as to', 'tr.id', '=', 'to.registration_id')
            ->select(
                DB::raw('DATE(tr.created_at) as date'),
                DB::raw('COUNT(CASE WHEN to.status != "paid" OR to.status IS NULL THEN 1 END) as not_paid_count')
            )
            ->when($eventId, function($query) use ($eventId) {
                $query->where('tr.event_id', $eventId);
            })
            ->when($dateFrom, function($query) use ($dateFrom) {
                $query->whereDate('tr.created_at', '>=', $dateFrom);
            })
            ->when($dateTo, function($query) use ($dateTo) {
                $query->whereDate('tr.created_at', '<=', $dateTo);
            })
            ->groupBy(DB::raw('DATE(tr.created_at)'))
            ->orderBy('date', 'asc')
            ->get();

        // Revenue by nationality
        $revenueByNationality = (clone $baseQuery)
            ->join('ticket_orders', 'ticket_registrations.id', '=', 'ticket_orders.registration_id')
            ->where('ticket_orders.status', 'paid')
            ->select('ticket_registrations.nationality', DB::raw('sum(ticket_orders.total) as total'))
            ->groupBy('ticket_registrations.nationality')
            ->pluck('total', 'nationality')
            ->toArray();

        // Registration by nationality (paid vs not paid)
        $nationalityBreakdown = DB::table('ticket_registrations as tr')
            ->leftJoin('ticket_orders as to', 'tr.id', '=', 'to.registration_id')
            ->select(
                'tr.nationality',
                DB::raw('COUNT(CASE WHEN to.status = "paid" THEN 1 END) as paid_count'),
                DB::raw('COUNT(CASE WHEN to.status != "paid" OR to.status IS NULL THEN 1 END) as not_paid_count'),
                DB::raw('COUNT(*) as total_count')
            )
            ->when($eventId, function($query) use ($eventId) {
                $query->where('tr.event_id', $eventId);
            })
            ->when($dateFrom, function($query) use ($dateFrom) {
                $query->whereDate('tr.created_at', '>=', $dateFrom);
            })
            ->when($dateTo, function($query) use ($dateTo) {
                $query->whereDate('tr.created_at', '<=', $dateTo);
            })
            ->whereNotNull('tr.nationality')
            ->groupBy('tr.nationality')
            ->get();

        // Payment gateway breakdown
        $gatewayBreakdown = [];
        $payments = TicketPayment::where('status', 'completed')->get();
        
        foreach ($payments as $payment) {
            $orderIds = $payment->order_ids_json ?? [];
            if (empty($orderIds)) {
                continue;
            }
            
            $orders = TicketOrder::whereIn('id', $orderIds)
                ->whereHas('registration', function($regQuery) use ($eventId, $dateFrom, $dateTo) {
                    if ($eventId) {
                        $regQuery->where('event_id', $eventId);
                    }
                    if ($dateFrom) {
                        $regQuery->whereDate('created_at', '>=', $dateFrom);
                    }
                    if ($dateTo) {
                        $regQuery->whereDate('created_at', '<=', $dateTo);
                    }
                })
                ->get();
            
            if ($orders->count() > 0) {
                $gateway = $payment->gateway_name ?? 'unknown';
                if (!isset($gatewayBreakdown[$gateway])) {
                    $gatewayBreakdown[$gateway] = [
                        'count' => 0,
                        'revenue' => 0
                    ];
                }
                $gatewayBreakdown[$gateway]['count'] += $orders->count();
                $gatewayBreakdown[$gateway]['revenue'] += $payment->amount;
            }
        }

        $events = Events::orderBy('event_year', 'desc')->orderBy('event_name', 'asc')->get();

        return view('tickets.admin.registration.analytics', compact(
            'totalRegistrations',
            'paidRegistrations',
            'notPaidRegistrations',
            'categoryWiseData',
            'paidTrends',
            'notPaidTrends',
            'revenueByNationality',
            'nationalityBreakdown',
            'gatewayBreakdown',
            'events'
        ));
    }

    /**
     * Registration List with filters
     */
    public function registrationList(Request $request)
    {
        $query = TicketRegistration::with(['event', 'contact', 'order', 'delegates', 'registrationCategory']);

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('company_phone', 'like', "%{$search}%")
                  ->orWhere('gstin', 'like', "%{$search}%")
                  ->orWhereHas('contact', function($contactQuery) use ($search) {
                      $contactQuery->where('email', 'like', "%{$search}%")
                                    ->orWhere('name', 'like', "%{$search}%")
                                    ->orWhere('phone', 'like', "%{$search}%");
                  })
                  ->orWhereHas('order', function($orderQuery) use ($search) {
                      $orderQuery->where('order_no', 'like', "%{$search}%");
                  })
                  ->orWhereHas('delegates', function($delegateQuery) use ($search) {
                      $delegateQuery->where('email', 'like', "%{$search}%")
                                    ->orWhere('first_name', 'like', "%{$search}%")
                                    ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by event
        if ($request->has('event_id') && !empty($request->event_id)) {
            $query->where('event_id', $request->event_id);
        }

        // Filter by nationality
        if ($request->has('nationality') && !empty($request->nationality)) {
            $query->where('nationality', $request->nationality);
        }

        // Filter by registration category
        if ($request->has('category_id') && !empty($request->category_id)) {
            $query->where('registration_category_id', $request->category_id);
        }

        // Filter by payment status (paid/not paid)
        if ($request->has('payment_status') && !empty($request->payment_status)) {
            if ($request->payment_status === 'paid') {
                $query->whereHas('order', function($orderQuery) {
                    $orderQuery->where('status', 'paid');
                });
            } elseif ($request->payment_status === 'not_paid') {
                $query->where(function($q) {
                    $q->whereDoesntHave('order')
                      ->orWhereHas('order', function($orderQuery) {
                          $orderQuery->where('status', '!=', 'paid');
                      });
                });
            }
        }

        // Filter by date range
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        $allowedSortFields = ['created_at', 'company_name', 'nationality'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Pagination
        $perPage = $request->get('per_page', 25);
        $registrations = $query->paginate($perPage);
        $registrations->appends($request->query());

        // Get events and categories for filter dropdowns
        $events = Events::orderBy('event_year', 'desc')->orderBy('event_name', 'asc')->get();
        $categories = \App\Models\Ticket\TicketRegistrationCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('tickets.admin.registration.list', compact('registrations', 'events', 'categories'));
    }

    /**
     * Analytics dashboard
     */
    public function analytics(Request $request)
    {
        $eventId = $request->get('event_id');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        // Base query
        $baseQuery = TicketRegistration::query();
        if ($eventId) {
            $baseQuery->where('event_id', $eventId);
        }
        if ($dateFrom) {
            $baseQuery->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $baseQuery->whereDate('created_at', '<=', $dateTo);
        }

        // Total registrations
        $totalRegistrations = (clone $baseQuery)->count();

        // Registrations by status
        $registrationsByStatus = (clone $baseQuery)
            ->join('ticket_orders', 'ticket_registrations.id', '=', 'ticket_orders.registration_id')
            ->select('ticket_orders.status', DB::raw('count(*) as count'))
            ->groupBy('ticket_orders.status')
            ->pluck('count', 'status')
            ->toArray();

        // Registrations by nationality
        $registrationsByNationality = (clone $baseQuery)
            ->select('nationality', DB::raw('count(*) as count'))
            ->groupBy('nationality')
            ->pluck('count', 'nationality')
            ->toArray();

        // Revenue by nationality
        $revenueByNationality = (clone $baseQuery)
            ->join('ticket_orders', 'ticket_registrations.id', '=', 'ticket_orders.registration_id')
            ->where('ticket_orders.status', 'paid')
            ->select('ticket_registrations.nationality', DB::raw('sum(ticket_orders.total) as total'))
            ->groupBy('ticket_registrations.nationality')
            ->pluck('total', 'nationality')
            ->toArray();

        // Revenue by payment gateway
        $revenueByGateway = [];
        $payments = TicketPayment::where('status', 'completed')
            ->get();
        
        foreach ($payments as $payment) {
            $orderIds = $payment->order_ids_json ?? [];
            if (empty($orderIds)) {
                continue;
            }
            
            $orders = TicketOrder::whereIn('id', $orderIds)
                ->whereHas('registration', function($regQuery) use ($eventId, $dateFrom, $dateTo) {
                    if ($eventId) {
                        $regQuery->where('event_id', $eventId);
                    }
                    if ($dateFrom) {
                        $regQuery->whereDate('created_at', '>=', $dateFrom);
                    }
                    if ($dateTo) {
                        $regQuery->whereDate('created_at', '<=', $dateTo);
                    }
                })
                ->get();
            
            if ($orders->count() > 0) {
                $gateway = $payment->gateway_name ?? 'unknown';
                if (!isset($revenueByGateway[$gateway])) {
                    $revenueByGateway[$gateway] = 0;
                }
                $revenueByGateway[$gateway] += $payment->amount;
            }
        }

        // Tracking analytics
        $trackingQuery = TicketRegistrationTracking::query();
        if ($eventId) {
            $trackingQuery->where('event_id', $eventId);
        }
        if ($dateFrom) {
            $trackingQuery->whereDate('started_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $trackingQuery->whereDate('started_at', '<=', $dateTo);
        }

        $totalStarted = (clone $trackingQuery)->count();
        $totalCompleted = (clone $trackingQuery)->whereNotNull('payment_completed_at')->count();
        $totalAbandoned = (clone $trackingQuery)->whereNotNull('abandoned_at')->count();

        // Daily registration trends
        $dailyTrends = (clone $baseQuery)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'asc')
            ->get();

        $events = Events::orderBy('event_year', 'desc')->orderBy('event_name', 'asc')->get();

        return view('tickets.admin.analytics.index', compact(
            'totalRegistrations',
            'registrationsByStatus',
            'registrationsByNationality',
            'revenueByNationality',
            'revenueByGateway',
            'totalStarted',
            'totalCompleted',
            'totalAbandoned',
            'dailyTrends',
            'events'
        ));
    }
}
