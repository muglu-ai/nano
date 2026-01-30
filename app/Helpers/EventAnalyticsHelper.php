<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class EventAnalyticsHelper
{
    public static function getEventAnalytics()
    {
        // Replace with actual database queries based on your table structure
        return [
            
            'total_event_delegates' => self::getTotalEventDelegates(),
            'total_normal_registered' => self::getTotalNormalRegistered(),
            'total_sponsors_registered' => self::getTotalSponsorsRegistered(),
            'total_exhibitor_registered' => self::getTotalExhibitorRegistered(),
            'total_speaker_registered' => self::getTotalSpeakerRegistered(),
            'total_invitee_registered' => self::getTotalInviteeRegistered(),
            'total_complimentary' => self::getTotalComplimentary(),
            'total_unpaid' => self::getTotalUnpaid(),
            'total_paid' => self::getTotalPaid(),
            'total_high_risk' => self::getTotalHighRisk(),
            'total_visitor_pass' => self::getTotalVisitorPass(),
            'total_enquiries' => self::getTotalEnquiries(),
        ];
    }

    private static function getTotalEventDelegates()
    {
        // Total delegates across all types - sum of all delegate categories
        $normalDelegatesArray = self::getTotalNormalRegistered();
        $normalDelegatesTotal = array_sum($normalDelegatesArray);
        $sponsorDelegates = self::getTotalSponsorsRegistered();
        $exhibitorDelegates = self::getTotalExhibitorRegistered();
        $speakerDelegates = self::getTotalSpeakerRegistered();
        $inviteeDelegates = self::getTotalInviteeRegistered();
        $complimentaryDelegates = self::getTotalComplimentary();
        $visitorPassDelegates = self::getTotalVisitorPass();
        
        return $normalDelegatesTotal + $sponsorDelegates + $exhibitorDelegates + 
               $speakerDelegates + $inviteeDelegates + $complimentaryDelegates + 
               $visitorPassDelegates;
    }

    private static function getTotalNormalRegistered()
    {
        // Get registration categories and count delegates for each category
        $results = [];
        
        // Get all registration categories with their delegate counts
        $categories = DB::table('ticket_registration_categories as trc')
            ->leftJoin('ticket_registrations as tr', 'trc.id', '=', 'tr.registration_category_id')
            ->leftJoin('ticket_delegates as td', 'tr.id', '=', 'td.registration_id')
            ->where('trc.is_active', 1)
            ->select('trc.name', DB::raw('COUNT(td.id) as delegate_count'))
            ->groupBy('trc.id', 'trc.name')
            ->orderBy('trc.sort_order')
            ->get();
            
        foreach ($categories as $category) {
            $results[$category->name] = $category->delegate_count;
        }
        
        // If no categories found, return default
        if (empty($results)) {
            $totalDelegates = DB::table('ticket_delegates')->count();
            $results = ['Total Delegate Registration' => $totalDelegates];
        }
        
        return $results;
    }

    private static function getTotalSponsorsRegistered()
    {
        return DB::table('attendees')
            ->where('badge_category', 'sponsor')
            ->where('status', 'approved')
            ->count();
    }

    private static function getTotalExhibitorRegistered()
    {
        // Count approved applications from both exhibitor-registration and startup-zone
        $exhibitorCount = DB::table('applications')
            ->where('application_type', 'exhibitor-registration')
            ->where('submission_status', 'approved')
            ->count();
            
        $startupCount = DB::table('applications')
            ->where('application_type', 'startup-zone')
            ->where('submission_status', 'approved')
            ->count();
            
        return $exhibitorCount + $startupCount;
    }

    private static function getTotalSpeakerRegistered()
    {
        return DB::table('attendees')
            ->where('badge_category', 'speaker')
            ->where('status', 'approved')
            ->count();
    }

    private static function getTotalInviteeRegistered()
    {
        return DB::table('attendees')
            ->where('badge_category', 'invitee')
            ->where('status', 'approved')
            ->count();
    }

    private static function getTotalComplimentary()
    {
        return DB::table('complimentary_delegates')->count();
    }

    private static function getTotalUnpaid()
    {
        // Count invoices that don't have successful payments
        return DB::table('invoices as i')
            ->leftJoin('payments as p', 'i.id', '=', 'p.invoice_id')
            ->where(function($query) {
                $query->whereNull('p.id')
                      ->orWhere('p.status', '!=', 'successful');
            })
            ->count();
    }

    private static function getTotalPaid()
    {
        // Count invoices with successful payments
        return DB::table('invoices as i')
            ->join('payments as p', 'i.id', '=', 'p.invoice_id')
            ->where('p.status', 'successful')
            ->count();
    }

    private static function getTotalHighRisk()
    {
        // Return 0 as high risk column may not exist in attendees table
        return 0;
    }

    private static function getTotalVisitorPass()
    {
        return DB::table('attendees')
            ->where('registration_type', 'visitor')
            ->orWhere('badge_category', 'visitor')
            ->count();
    }

    private static function getTotalEnquiries()
    {
        return DB::table('enquiries')->count();
    }
}