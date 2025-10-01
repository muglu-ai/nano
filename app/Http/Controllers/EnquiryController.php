<?php

namespace App\Http\Controllers;

use App\Models\ItEnq;
use Illuminate\Http\Request;

class EnquiryController extends Controller
{
    /**
     * Display a listing of the enquiries with search and pagination.
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $perPage = $request->get('per_page', 15);
        $query = ItEnq::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('fname', 'like', "%{$search}%")
                  ->orWhere('lname', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('org', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('country', 'like', "%{$search}%")
                  ->orWhere('cellno', 'like', "%{$search}%");
            });
        }

        $enquiries = $query->orderByDesc('del_srno')->paginate($perPage);

        return view('enquiries.index', compact('enquiries', 'search'));
    }
}

