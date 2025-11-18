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
                return $countries->map(function ($c) {
                    return [
                        'id' => $c->id,
                        'name' => $c->name,
                        'iso2' => $c->code, // external API uses iso2; our DB stores as code
                    ];
                });
            }
        } catch (\Throwable $e) {
            Log::warning('countries(): local DB fetch failed, trying external API', ['error' => $e->getMessage()]);
        }
        return Http::withHeaders($this->headers)->get('https://api.countrystatecity.in/v1/countries')->json();
    }

    public function states($country)
    {
        // $country may be ISO2 code or numeric ID; support both
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
                    return $states->map(function ($s) {
                        return [
                            'id' => $s->id,
                            'name' => $s->name,
                        ];
                    });
                }
            }
        } catch (\Throwable $e) {
            Log::warning('states(): local DB fetch failed, trying external API', ['country' => $country, 'error' => $e->getMessage()]);
        }
        return Http::withHeaders($this->headers)->get("https://api.countrystatecity.in/v1/countries/{$country}/states")->json();
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
                    return $states->map(function ($s) {
                        return [
                            'id' => $s->id,
                            'name' => $s->name,
                        ];
                    });
                }
            }
        } catch (\Throwable $e) {
            Log::warning('statesName(): local DB fetch failed, trying external API', ['country' => $country, 'error' => $e->getMessage()]);
        }
        return Http::withHeaders($this->headers)->get("https://api.countrystatecity.in/v1/countries/{$country}/states")->json();
    }

    public function cities($country, $state)
    {
        return Http::withHeaders($this->headers)->get("https://api.countrystatecity.in/v1/countries/{$country}/states/{$state}/cities")->json();
    }
}
