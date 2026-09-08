<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RiskApiService
{
    protected $url;
    protected $secret;

    public function __construct()
    {
        $this->url = config('risk.api_url');
        $this->secret = config('risk.api_secret');
    }

    /**
     * Fetch all risks from the internal API.
     *
     * @return array
     */
    public function fetchRisks()
    {
        try {
            $response = Http::withHeaders([
                'X-Internal-Secret' => $this->secret,
                'Accept' => 'application/json',
            ])->get($this->url);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Risk API Request Failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [];
        } catch (\Exception $e) {
            Log::error('Risk API Exception: ' . $e->getMessage());
            return [];
        }
    }
}
