<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationAdministrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_creator_of_an_organization_becomes_its_administrator(): void
    {
        $this->post(route('register.store'), [
            'name' => 'Ana Admin',
            'email' => 'ana@example.com',
            'password' => 'password-seguro',
            'password_confirmation' => 'password-seguro',
            'organization_name' => 'Organización Ejemplo',
        ])->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'ana@example.com',
            'is_organization_admin' => true,
        ]);
    }

    public function test_member_cannot_access_administrator_dashboard(): void
    {
        $organization = Organization::create(['name' => 'Organización existente']);
        $member = User::factory()->for($organization)->create(['is_organization_admin' => false]);

        $this->actingAs($member)
            ->get(route('dashboard'))
            ->assertRedirect(route('member.home'));
    }
}
