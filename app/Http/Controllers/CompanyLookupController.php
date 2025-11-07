<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Application;

class CompanyLookupController extends Controller
{
    /**
     * Return JSON list of company names from external applications DB.
     *
     * @param string $letter A-Z (case-insensitive) to filter by first letter, or '#' for non-alphabetic
     */
    public function index(Request $request, string $letter)
    {
        // Allow CORS preflight (OPTIONS) manually for https://bengalurutechsummit.com and https://www.bengalurutechsummit.com
        $allowedOrigins = [
            'https://bengalurutechsummit.com',
            'https://www.bengalurutechsummit.com'
        ];

        $origin = $request->headers->get('Origin');
        if ($request->getMethod() === 'OPTIONS') {
            $headers = [
                'Access-Control-Allow-Origin' => in_array($origin, $allowedOrigins) ? $origin : '',
                'Access-Control-Allow-Methods' => 'GET, POST, OPTIONS',
                'Access-Control-Allow-Headers' => 'Content-Type, Authorization',
            ];
            return response()->json('OK', 200, $headers);
        }

       

        $query = Application::select('company_name')
            ->whereNotNull('company_name')
            ->whereRaw('UPPER(TRIM(company_name)) <> ?', ['SCI']);

        // Normalize letter to uppercase
        $letter = strtoupper($letter);

        // Handle letter filtering
        if ($letter === '#') {
            // Filter for non-alphabetic first characters
            $query->whereRaw('UPPER(SUBSTRING(TRIM(company_name), 1, 1)) NOT REGEXP ?', ['^[A-Z]']);
        } elseif (preg_match('/^[A-Z]$/', $letter)) {
            // Filter by first letter (case-insensitive)
            $query->whereRaw('UPPER(SUBSTRING(TRIM(company_name), 1, 1)) = ?', [$letter]);
        } else {
            return response()->json(['error' => 'Invalid letter. Must be A-Z or #'], 422)
                ->header('Access-Control-Allow-Origin', in_array($origin, $allowedOrigins) ? $origin : '')
                ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        }

        $companies = $query
            ->distinct()
            ->orderBy('company_name')
            ->pluck('company_name');

        return response()->json($companies)
            ->header('Access-Control-Allow-Origin', in_array($origin, $allowedOrigins) ? $origin : '')
            ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    }
}


