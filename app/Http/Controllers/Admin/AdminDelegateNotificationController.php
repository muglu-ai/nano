<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket\DelegateNotification;
use App\Models\Ticket\TicketDelegate;
use App\Models\Ticket\TicketContact;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;

class AdminDelegateNotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->middleware(['auth']);
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if (!in_array($user->role, ['admin', 'super-admin'])) {
                abort(403, 'Unauthorized access');
            }
            return $next($request);
        });
        $this->notificationService = $notificationService;
    }

    /**
     * List all notifications
     */
    public function index(Request $request)
    {
        $query = DelegateNotification::with(['delegate', 'contact', 'creator']);

        // Filters
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        if ($request->has('is_read') && $request->is_read !== '') {
            $query->where('is_read', $request->is_read);
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(50);

        return view('admin.delegate-notifications.index', compact('notifications'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('admin.delegate-notifications.create');
    }

    /**
     * Store notification
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:info,warning,important',
            'target_type' => 'required|in:all,contact,delegate',
            'contact_id' => 'required_if:target_type,contact|exists:ticket_contacts,id',
            'delegate_id' => 'required_if:target_type,delegate|exists:ticket_delegates,id',
            'send_email' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $adminId = auth()->id();

            if ($request->target_type === 'all') {
                // Send to all delegates
                $this->notificationService->sendToAll(
                    $request->title,
                    $request->message,
                    $request->type,
                    $adminId,
                    $request->boolean('send_email')
                );
            } elseif ($request->target_type === 'contact') {
                // Send to all delegates in contact's registrations
                $this->notificationService->sendToContact(
                    $request->contact_id,
                    $request->title,
                    $request->message,
                    $request->type,
                    $adminId,
                    $request->boolean('send_email')
                );
            } elseif ($request->target_type === 'delegate') {
                // Send to specific delegate
                $this->notificationService->sendToDelegate(
                    $request->delegate_id,
                    $request->title,
                    $request->message,
                    $request->type,
                    $adminId,
                    $request->boolean('send_email')
                );
            }

            DB::commit();

            return redirect()->route('admin.delegate-notifications.index')
                ->with('success', 'Notification sent successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to send notification: ' . $e->getMessage()]);
        }
    }

    /**
     * Send notification (trigger send)
     */
    public function send($id)
    {
        $notification = DelegateNotification::findOrFail($id);

        try {
            $this->notificationService->sendEmail($notification);
            return response()->json(['success' => true, 'message' => 'Email sent successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to send email: ' . $e->getMessage()], 500);
        }
    }
}
