<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use App\Models\EnquiryFollowup;
use App\Models\EnquiryNote;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EnquiryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['admin']);
    }

    /**
     * Display a listing of the enquiries with search, filters and pagination.
     */
    public function index(Request $request)
    {
        $query = Enquiry::with(['interests', 'event', 'assignedTo', 'followups']);

        // Search functionality
        $search = $request->get('search', '');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('organisation', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('country', 'like', "%{$search}%")
                  ->orWhere('comments', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by event
        if ($request->has('event_id') && $request->event_id !== '') {
            $query->where('event_id', $request->event_id);
        }

        // Filter by assigned user
        if ($request->has('assigned_to') && $request->assigned_to !== '') {
            if ($request->assigned_to === 'unassigned') {
                $query->whereNull('assigned_to_user_id');
            } else {
                $query->where('assigned_to_user_id', $request->assigned_to);
            }
        }

        // Filter by interest type
        if ($request->has('interest_type') && $request->interest_type !== '') {
            $query->whereHas('interests', function($q) use ($request) {
                $q->where('interest_type', $request->interest_type);
            });
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        $allowedSortFields = ['created_at', 'full_name', 'email', 'organisation', 'status', 'city', 'country'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $enquiries = $query->paginate($perPage);
        $enquiries->appends($request->query());

        // Get filter options
        $statuses = ['new', 'contacted', 'qualified', 'converted', 'closed'];
        $users = User::where('role', 'admin')->orWhere('role', 'staff')->orderBy('name')->get();
        $interestTypes = \App\Models\EnquiryInterest::getInterestTypes();

        return view('enquiries.index', compact('enquiries', 'search', 'statuses', 'users', 'interestTypes'));
    }

    /**
     * Show the specified enquiry.
     */
    public function show($id)
    {
        $enquiry = Enquiry::with(['interests', 'event', 'assignedTo', 'followups', 'notes.createdBy'])
            ->findOrFail($id);

        $users = User::where('role', 'admin')->orWhere('role', 'staff')->orderBy('name')->get();
        $statuses = ['new', 'contacted', 'qualified', 'converted', 'closed'];
        $prospectLevels = ['hot', 'warm', 'cold'];

        return view('enquiries.show', compact('enquiry', 'users', 'statuses', 'prospectLevels'));
    }

    /**
     * Update enquiry status.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:new,contacted,qualified,converted,closed',
            'status_comment' => 'nullable|string|max:1000',
            'prospect_level' => 'nullable|in:hot,warm,cold',
        ]);

        $enquiry = Enquiry::findOrFail($id);
        $enquiry->update([
            'status' => $request->status,
            'status_comment' => $request->status_comment,
            'prospect_level' => $request->prospect_level,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Enquiry status updated successfully.']);
        }

        return redirect()->back()->with('success', 'Enquiry status updated successfully.');
    }

    /**
     * Assign enquiry to user.
     */
    public function assign(Request $request, $id)
    {
        $request->validate([
            'assigned_to_user_id' => 'nullable|exists:users,id',
        ]);

        $enquiry = Enquiry::findOrFail($id);
        
        $assignedTo = null;
        if ($request->assigned_to_user_id) {
            $assignedTo = User::find($request->assigned_to_user_id);
        }

        $enquiry->update([
            'assigned_to_user_id' => $request->assigned_to_user_id,
            'assigned_to_name' => $assignedTo ? $assignedTo->name : null,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Enquiry assigned successfully.']);
        }

        return redirect()->back()->with('success', 'Enquiry assigned successfully.');
    }

    /**
     * Add followup to enquiry.
     */
    public function addFollowup(Request $request, $id)
    {
        $request->validate([
            'followup_type' => 'nullable|string|max:50',
            'followup_status' => 'nullable|string|max:50',
            'followup_comment' => 'required|string',
            'followup_date' => 'nullable|date',
            'followup_time' => 'nullable',
            'prospect_level' => 'nullable|in:hot,warm,cold',
            'assigned_to_user_id' => 'nullable|exists:users,id',
        ]);

        $enquiry = Enquiry::findOrFail($id);

        $followupDatetime = null;
        if ($request->followup_date) {
            $followupDatetime = $request->followup_date;
            if ($request->followup_time) {
                $followupDatetime .= ' ' . $request->followup_time;
            }
        }

        $assignedTo = null;
        if ($request->assigned_to_user_id) {
            $assignedTo = User::find($request->assigned_to_user_id);
        }

        EnquiryFollowup::create([
            'enquiry_id' => $enquiry->id,
            'followup_type' => $request->followup_type,
            'followup_status' => $request->followup_status ?? 'pending',
            'followup_comment' => $request->followup_comment,
            'followup_date' => $request->followup_date,
            'followup_time' => $request->followup_time,
            'followup_datetime' => $followupDatetime,
            'assigned_to_user_id' => $request->assigned_to_user_id,
            'assigned_to_name' => $assignedTo ? $assignedTo->name : null,
            'prospect_level' => $request->prospect_level,
        ]);

        return redirect()->back()->with('success', 'Followup added successfully.');
    }

    /**
     * Add note to enquiry.
     */
    public function addNote(Request $request, $id)
    {
        $request->validate([
            'note' => 'required|string',
            'note_type' => 'nullable|in:general,internal,customer_response',
        ]);

        $enquiry = Enquiry::findOrFail($id);

        EnquiryNote::create([
            'enquiry_id' => $enquiry->id,
            'note' => $request->note,
            'note_type' => $request->note_type ?? 'general',
            'created_by_user_id' => auth()->id(),
            'created_by_name' => auth()->user()->name,
        ]);

        return redirect()->back()->with('success', 'Note added successfully.');
    }

    /**
     * Delete enquiry (soft delete).
     */
    public function destroy($id)
    {
        $enquiry = Enquiry::findOrFail($id);
        $enquiry->delete();

        return redirect()->route('enquiries.index')->with('success', 'Enquiry deleted successfully.');
    }
}
