<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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
        return Http::withHeaders($this->headers)->get('https://api.countrystatecity.in/v1/countries')->json();
    }

    public function states($country)
    {
        return Http::withHeaders($this->headers)->get("https://api.countrystatecity.in/v1/countries/{$country}/states")->json();
    }

    public function cities($country, $state)
    {
        return Http::withHeaders($this->headers)->get("https://api.countrystatecity.in/v1/countries/{$country}/states/{$state}/cities")->json();
    }
}
