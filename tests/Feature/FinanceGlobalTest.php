<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FinanceGlobalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed roles
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        
        // Seed active year
        AcademicYear::create([
            'label' => '2025-2026',
            'start_date' => '2025-09-01',
            'end_date' => '2026-06-30',
            'is_active' => true,
        ]);
    }

    /**
     * Test that directeur can access the global finances page.
     */
    public function test_directeur_can_access_global_finances()
    {
        $directeur = User::factory()->create();
        $directeur->assignRole('directeur');

        $response = $this->actingAs($directeur)->get(route('finances.global'));

        $response->assertStatus(200);
        $response->assertSee('Gestion Globale des Frais');
    }

    /**
     * Test that teacher cannot access the global finances page.
     */
    public function test_teacher_cannot_access_global_finances()
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('enseignant');

        $response = $this->actingAs($teacher)->get(route('finances.global'));

        $response->assertStatus(403);
    }
}
