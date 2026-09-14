<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CredentialRequestFormsTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrator_can_create_a_dynamic_form_and_member_can_submit_it(): void
    {
        $organization = Organization::create(['name' => 'Organización de prueba']);
        $admin = User::factory()->for($organization)->create(['is_organization_admin' => true]);
        $member = User::factory()->for($organization)->create(['is_organization_admin' => false]);

        $this->actingAs($admin)->post(route('forms.store'), [
            'name' => 'Solicitud de identidad',
            'description' => 'Información laboral.',
            'fields' => [
                ['label' => 'Tipo de vinculación', 'type' => 'select', 'options' => 'Empleado, Contratista', 'required' => true],
            ],
        ])->assertRedirect(route('forms.index'));

        $form = $organization->credentialRequestForms()->first();

        $this->actingAs($admin)
            ->put(route('forms.update', $form), [
                'name' => 'Solicitud de identidad',
                'description' => 'Información laboral y de acceso.',
                'fields' => [
                    ['label' => 'Tipo de vinculación', 'type' => 'select', 'options' => 'Empleado, Contratista', 'required' => true],
                    ['label' => 'Fecha de inicio', 'type' => 'date', 'required' => true],
                ],
            ])
            ->assertRedirect(route('forms.index'));

        $this->assertSame(2, $form->fresh()->fields()->count());

        $this->actingAs($member)->post(route('requests.store', $form), [
            'answers' => [
                $form->fields()->first()->id => 'Empleado',
                $form->fields()->skip(1)->first()->id => '2026-09-13',
            ],
        ])->assertRedirect(route('requests.index'));

        $this->assertDatabaseHas('credential_requests', [
            'credential_request_form_id' => $form->id,
            'user_id' => $member->id,
            'status' => 'pending',
        ]);
    }

    public function test_administrator_can_delete_a_form(): void
    {
        $organization = Organization::create(['name' => 'Organización para eliminar']);
        $admin = User::factory()->for($organization)->create(['is_organization_admin' => true]);
        $form = $organization->credentialRequestForms()->create(['name' => 'Formulario temporal']);

        $this->actingAs($admin)
            ->delete(route('forms.destroy', $form))
            ->assertRedirect(route('forms.index'));

        $this->assertDatabaseMissing('credential_request_forms', ['id' => $form->id]);
    }
}
