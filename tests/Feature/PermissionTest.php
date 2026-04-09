<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Audit;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // 1. Create permissions
        $permissions = [
            'view audits', 'edit audits', 'approve audits',
            'view risks', 'edit risks',
            'view findings', 'escalate findings',
            'view reports', 'generate reports',
            'view dashboad', // small typo in previous seeder but we'll follow logic or fix it
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p]);
        }

        // 2. Create roles and assign permissions
        $admin = Role::firstOrCreate(['name' => 'System Admin']);
        $admin->givePermissionTo(Permission::all());

        $auditor = Role::firstOrCreate(['name' => 'Auditor']);
        $auditor->givePermissionTo(['view audits', 'edit audits', 'view findings', 'view risks']);

        $committee = Role::firstOrCreate(['name' => 'Audit Committee']);
        $committee->givePermissionTo(['view audits', 'view reports', 'view findings', 'view risks']);
    }

    public function test_auditor_cannot_approve_audits()
    {
        $user = User::factory()->create();
        $user->assignRole('Auditor');
        
        $audit = Audit::factory()->create(['status' => 'draft']);

        $response = $this->actingAs($user)->post(route('audits.approve', $audit));
        $response->assertStatus(403);
    }

    public function test_committee_can_access_meeting_pack()
    {
        $user = User::factory()->create();
        $user->assignRole('Audit Committee');

        $response = $this->actingAs($user)->get(route('reports.meeting-pack'));
        $response->assertOk();
    }

    public function test_auditor_can_access_working_papers()
    {
        $user = User::factory()->create();
        $user->assignRole('Auditor');

        $response = $this->actingAs($user)->get(route('working-papers.index'));
        $response->assertOk();
    }

    public function test_unauthorized_user_cannot_view_risks()
    {
        // Define a role without risk permissions
        $role = Role::create(['name' => 'IT Department']);
        $role->givePermissionTo(['view audits']); // No 'view risks'

        $user = User::factory()->create();
        $user->assignRole('IT Department');

        $response = $this->actingAs($user)->get(route('risks.index'));
        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_any_dashboard()
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }
}
