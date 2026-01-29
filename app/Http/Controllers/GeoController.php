<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Country;
use App\Models\State;
use Illuminate\Support\Facades\Log;

class GeoController extends Controller
{
    private $headers;

    public function __construct()
    {
        $this->headers = [
            'X-CSCAPI-KEY' => env('CSC_API_KEY'),
        ];
    }

    public function countries()
    {
        // Prefer local DB (fallback to external if empty)
        try {
            $countries = Country::orderBy('name')->get(['id', 'name', 'code']);
            if ($countries->count() > 0) {
                // Map to a structure similar to external API minimal needs
                $data = $countries->map(function ($c) {
                    return [
                        'id' => $c->id,
                        'name' => $c->name,
                        'iso2' => $c->code, // external API uses iso2; our DB stores as code
                    ];
                });
                return response()->json($data)
                    ->header('Access-Control-Allow-Origin', '*')
                    ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
                    ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
                    ->header('Access-Control-Allow-Credentials', 'true')
                    ->header('Vary', 'Origin');
            }
        } catch (\Throwable $e) {
            Log::warning('countries(): local DB fetch failed, trying external API', ['error' => $e->getMessage()]);
        }
        $res = Http::withHeaders($this->headers)->get('https://api.countrystatecity.in/v1/countries')->json();
        return response()->json($res)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
            ->header('Access-Control-Allow-Credentials', 'true')
            ->header('Vary', 'Origin');
    }

    public function states($country)
    {
        // $country may be ISO2 code, numeric ID, or country name; support all
        try {
            $countryId = null;
            $countryCode = null;
            
            if (is_numeric($country)) {
                $countryId = (int) $country;
                $countryModel = Country::find($countryId);
                $countryCode = $countryModel ? $countryModel->code : null;
            } else {
                // Try to find by code first (most common case)
                $countryModel = Country::where('code', strtoupper($country))->first();
                if ($countryModel) {
                    $countryId = $countryModel->id;
                    $countryCode = $countryModel->code;
                } else {
                    // Try to find by name
                    $countryModel = Country::where('name', $country)->first();
                    if ($countryModel) {
                        $countryId = $countryModel->id;
                        $countryCode = $countryModel->code;
                    }
                }
            }
            
            if ($countryId) {
                $states = State::where('country_id', $countryId)->orderBy('name')->get(['id', 'name']);
                if ($states->count() > 0) {
                    $data = $states->map(function ($s) {
                        return [
                            'id' => $s->id,
                            'name' => $s->name,
                        ];
                    });
                    return response()->json($data)
                        ->header('Access-Control-Allow-Origin', '*')
                        ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
                        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
                        ->header('Access-Control-Allow-Credentials', 'true')
                        ->header('Vary', 'Origin');
                }
            }
            
            // If we found a country code but no states in DB, try external API with code
            if ($countryCode) {
                $res = Http::withHeaders($this->headers)->get("https://api.countrystatecity.in/v1/countries/{$countryCode}/states")->json();
                if ($res && is_array($res) && count($res) > 0) {
                    return response()->json($res)
                        ->header('Access-Control-Allow-Origin', '*')
                        ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
                        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
                        ->header('Access-Control-Allow-Credentials', 'true')
                        ->header('Vary', 'Origin');
                }
            }
        } catch (\Throwable $e) {
            Log::warning('states(): local DB fetch failed, trying external API', ['country' => $country, 'error' => $e->getMessage()]);
        }
        
        // Fallback to external API (may accept code or name)
        $res = Http::withHeaders($this->headers)->get("https://api.countrystatecity.in/v1/countries/{$country}/states")->json();
        return response()->json($res)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
            ->header('Access-Control-Allow-Credentials', 'true')
            ->header('Vary', 'Origin');
    }
    public function statesName($country)
    {
        // Mirror states() behavior, using local DB first
        try {
            $countryId = null;
            if (is_numeric($country)) {
                $countryId = (int) $country;
            } else {
                $countryModel = Country::where('code', $country)->first();
                $countryId = $countryModel ? $countryModel->id : null;
            }
            if ($countryId) {
                $states = State::where('country_id', $countryId)->orderBy('name')->get(['id', 'name']);
                if ($states->count() > 0) {
                    $data = $states->map(function ($s) {
                        return [
                            'id' => $s->id,
                            'name' => $s->name,
                        ];
                    });
                    return response()->json($data)
                        ->header('Access-Control-Allow-Origin', '*')
                        ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
                        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
                        ->header('Access-Control-Allow-Credentials', 'true')
                        ->header('Vary', 'Origin');
                }
            }
        } catch (\Throwable $e) {
            Log::warning('statesName(): local DB fetch failed, trying external API', ['country' => $country, 'error' => $e->getMessage()]);
        }
        $res = Http::withHeaders($this->headers)->get("https://api.countrystatecity.in/v1/countries/{$country}/states")->json();
        return response()->json($res)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
            ->header('Access-Control-Allow-Credentials', 'true')
            ->header('Vary', 'Origin');
    }

    public function cities($country, $state)
    {
        $res = Http::withHeaders($this->headers)->get("https://api.countrystatecity.in/v1/countries/{$country}/states/{$state}/cities")->json();
        return response()->json($res)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
            ->header('Access-Control-Allow-Credentials', 'true')
            ->header('Vary', 'Origin');
    }
}
