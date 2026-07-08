<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubjectsIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_subjects_page_shows_section_filter_in_section_tab(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->givePermissionTo('view-subjects');

        $response = $this->actingAs($user)
            ->get(route('subjects.index', ['tab' => 'section']));

        $response->assertOk();
        $response->assertSee('Filtrer par :');
        $response->assertSee('Toutes les sections');
    }
}
