<?php

namespace Tests\Feature;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // Standard BRD Roles
        $roles = [
            'System Admin', 'Auditor', 'Audit Manager', 'Risk Officer',
            'Audit Committee', 'Council / Board', 'IT Department',
            'Finance Department', 'Executive Management'
        ];

        foreach ($roles as $r) {
            Role::firstOrCreate(['name' => $r]);
        }
    }

    public function test_guests_are_redirected_to_the_login_page()
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_admin_sees_admin_dashboard()
    {
        $user = User::factory()->create();
        $user->assignRole('System Admin');
        
        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertOk();
        $response->assertViewIs('dashboard.index');
    }

    public function test_auditor_sees_auditor_dashboard()
    {
        $user = User::factory()->create();
        $user->assignRole('Auditor');
        
        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertOk();
        $response->assertViewIs('dashboard.auditor');
    }

    public function test_risk_officer_sees_risk_dashboard()
    {
        $user = User::factory()->create();
        $user->assignRole('Risk Officer');
        
        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertOk();
        $response->assertViewIs('dashboard.risk_officer');
    }

    public function test_committee_sees_committee_dashboard()
    {
        $user = User::factory()->create();
        $user->assignRole('Audit Committee');
        
        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertOk();
        $response->assertViewIs('dashboard.committee');
    }

    public function test_executive_sees_executive_dashboard()
    {
        $user = User::factory()->create();
        $user->assignRole('Executive Management');
        
        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertOk();
        $response->assertViewIs('dashboard.executive');
    }

    public function test_board_sees_executive_dashboard()
    {
        $user = User::factory()->create();
        $user->assignRole('Council / Board');
        
        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertOk();
        $response->assertViewIs('dashboard.executive');
    }
}
