<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InfirmaryRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_dossier_listing_page_is_available(): void
    {
        $user = User::factory()->create();
        $user->assignRole('super-admin');

        $this->actingAs($user)
            ->get(route('infirmary.patients'))
            ->assertOk();
    }

    public function test_consultation_edit_route_is_registered_for_health_managers(): void
    {
        $user = User::factory()->create();
        $user->assignRole('super-admin');

        $this->actingAs($user)
            ->get(route('infirmary.edit', ['visit' => 1]))
            ->assertStatus(404);
    }
}
