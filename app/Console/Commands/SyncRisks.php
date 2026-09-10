<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RiskApiService;
use App\Models\RiskRegister;
use App\Models\User;

class SyncRisks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'risk:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync risk registers from the external master API';

    /**
     * Execute the console command.
     */
    public function handle(RiskApiService $apiService)
    {
        $this->info('Fetching risks from API...');
        $apiRisks = $apiService->fetchRisks();

        if (empty($apiRisks)) {
            $this->error('No risks found or API request failed.');
            return 1;
        }

        $this->info('Processing ' . count($apiRisks) . ' risks...');

        $syncedCount = 0;
        $errorCount = 0;

        foreach ($apiRisks as $apiRisk) {
            try {
                $owner = null;
                if (!empty($apiRisk['owner_email'])) {
                    $owner = User::where('email', $apiRisk['owner_email'])->first();
                }
                if (!$owner && !empty($apiRisk['owner'])) {
                    $owner = User::where('department', $apiRisk['owner'])->first();
                }
                if (!$owner) {
                    $owner = User::first();
                }

                if (!$owner) {
                    $this->warn('No owner found for risk: ' . ($apiRisk['risk_code'] ?? 'Unknown'));
                    $errorCount++;
                    continue;
                }

                RiskRegister::updateOrCreate(
                    ['risk_code' => $apiRisk['risk_code']],
                    [
                        'title' => $apiRisk['title'] ?? 'Untitled risk',
                        'description' => $apiRisk['description'] ?? 'No description provided',
                        'category' => $apiRisk['category'] ?? 'operational',
                        'inherent_likelihood' => $apiRisk['inherent_likelihood'] ?? 3,
                        'inherent_impact' => $apiRisk['inherent_impact'] ?? 3,
                        'residual_likelihood' => $apiRisk['residual_likelihood'] ?? 2,
                        'residual_impact' => $apiRisk['residual_impact'] ?? 2,
                        'status' => $apiRisk['status'] ?? 'active',
                        'owner_id' => $owner->id,
                    ]
                );

                $syncedCount++;
            } catch (\Exception $e) {
                $this->warn('Failed to sync risk: ' . ($apiRisk['risk_code'] ?? 'Unknown'));
                $errorCount++;
            }
        }

        $this->info("Sync completed! $syncedCount synced, $errorCount errors.");
        return 0;
    }
}
