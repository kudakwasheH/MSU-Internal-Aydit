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
                $data = $response->json() ?? [];
                return collect($data)->map(fn ($risk) => $this->normalizeRisk((array) $risk))->values()->all();
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

    private function normalizeRisk(array $risk): array
    {
        $likelihoodMap = [
            'rare' => 1,
            'unlikely' => 2,
            'possible' => 3,
            'likely' => 4,
            'almost certain' => 5,
            'almost_certain' => 5,
        ];

        $impactMap = [
            'insignificant' => 1,
            'minor' => 2,
            'moderate' => 3,
            'major' => 4,
            'catastrophic' => 5,
        ];

        $categoryMap = [
            'operational' => 'operational',
            'financial' => 'financial',
            'compliance' => 'compliance',
            'legal & compliance' => 'compliance',
            'legal and compliance' => 'compliance',
            'strategic' => 'strategic',
            'technological' => 'it',
            'it' => 'it',
        ];

        $statusMap = [
            'open' => 'active',
            'closed' => 'mitigated',
            'resolved' => 'mitigated',
        ];

        $risk['risk_code'] = $risk['sn'] ?? $risk['risk_code'] ?? $risk['id'] ?? null;
        $risk['title'] = $risk['risk_description'] ?? $risk['title'] ?? 'Untitled risk';
        $risk['description'] = $risk['causes'] ?? $risk['consequence'] ?? $risk['risk_description'] ?? 'No description provided';
        $risk['inherent_likelihood'] = $this->mapScale($risk['inherent_likelihood'] ?? null, $likelihoodMap, 3);
        $risk['inherent_impact'] = $this->mapScale($risk['inherent_consequence'] ?? null, $impactMap, 3);
        $risk['residual_likelihood'] = $this->mapScale($risk['residual_likelihood'] ?? null, $likelihoodMap, 2);
        $risk['residual_impact'] = $this->mapScale($risk['residual_consequence'] ?? null, $impactMap, 2);
        $risk['status'] = $statusMap[strtolower($risk['status'] ?? '')] ?? 'active';
        $risk['category'] = $categoryMap[strtolower($risk['category'] ?? '')] ?? ($risk['category'] ?? 'operational');
        $risk['last_reviewed_at'] = $risk['date_reviewed'] ?? $risk['last_reviewed_at'] ?? null;
        $risk['kra_at_risk'] = $risk['kra_at_risk'] ?? null;
        $risk['cause'] = $risk['causes'] ?? $risk['cause'] ?? null;
        $risk['consequence'] = $risk['consequence'] ?? null;

        return $risk;
    }

    private function mapScale($value, array $map, int $default): int
    {
        if (is_numeric($value)) {
            $int = (int) $value;
            return $int >= 1 && $int <= 5 ? $int : $default;
        }

        $key = strtolower((string) $value);
        return $map[$key] ?? $default;
    }
}
