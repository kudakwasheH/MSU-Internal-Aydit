<?php

namespace Database\Seeders;

use App\Models\ActionItem;
use App\Models\Finding;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActionItemsTableSeeder extends Seeder
{
    public function run(): void
    {
        $findings = Finding::all();
        $users = User::role('Auditor')->get();

        foreach ($findings as $finding) {
            $numActions = rand(1, 2);
            for ($i = 0; $i < $numActions; $i++) {
                $status = ['pending', 'in_progress', 'completed', 'overdue'][array_rand(['pending', 'in_progress', 'completed', 'overdue'])];
                ActionItem::create([
                    'finding_id' => $finding->id,
                    'action_description' => "Action item " . ($i+1) . " for: {$finding->title}",
                    'assigned_to' => $users->random()->id,
                    'due_date' => $status === 'overdue' ? now()->subDays(rand(1, 14)) : now()->addDays(rand(1, 30)),
                    'status' => $status,
                    'reminder_sent' => $status === 'overdue',
                    'completed_at' => $status === 'completed' ? now()->subDays(rand(1, 5)) : null,
                    'comments' => $status === 'completed' ? 'Action completed and verified.' : null,
                ]);
            }
        }
    }
}
