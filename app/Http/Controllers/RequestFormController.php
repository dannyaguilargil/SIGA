<?php

namespace App\Http\Controllers;

use App\Models\CredentialRequestForm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RequestFormController extends Controller
{
    private const FIELD_TYPES = ['text', 'textarea', 'select', 'number', 'date', 'aplicativo', 'modulo'];

    public function index(Request $request): View
    {
        return view('forms.index', [
            'forms' => CredentialRequestForm::query()
                ->where('organization_id', $request->user()->organization_id)
                ->withCount('requests')
                ->latest()
                ->get(),
        ]);
    }

    public function create(): View
    {
        return view('forms.create', ['form' => null, 'formFields' => []]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        DB::transaction(function () use ($request, $data): void {
            $form = CredentialRequestForm::create([
                'organization_id' => $request->user()->organization_id,
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
            ]);

            foreach ($data['fields'] as $position => $field) {
                $options = $field['type'] === 'select'
                    ? collect(explode(',', $field['options']))->map(fn (string $option) => trim($option))->filter()->values()->all()
                    : null;

                $form->fields()->create([
                    'label' => $field['label'],
                    'type' => $field['type'],
                    'options' => $options,
                    'is_required' => (bool) ($field['required'] ?? false),
                    'position' => $position,
                ]);
            }
        });

        return redirect()->route('forms.index')->with('status', 'Formulario creado y disponible para los miembros de tu organización.');
    }

    public function edit(Request $request, CredentialRequestForm $form): View
    {
        $this->ensureBelongsToOrganization($request, $form);

        $form->load('fields');

        return view('forms.create', [
            'form' => $form,
            'formFields' => $form->fields->map(fn ($field) => [
                'label' => $field->label,
                'type' => $field->type,
                'required' => $field->is_required,
                'options' => implode(', ', $field->options ?? []),
            ])->values(),
        ]);
    }

    public function update(Request $request, CredentialRequestForm $form): RedirectResponse
    {
        $this->ensureBelongsToOrganization($request, $form);
        $data = $this->validatedData($request);

        DB::transaction(function () use ($form, $data): void {
            $form->update(['name' => $data['name'], 'description' => $data['description'] ?? null]);
            $form->fields()->delete();

            foreach ($data['fields'] as $position => $field) {
                $form->fields()->create([
                    'label' => $field['label'],
                    'type' => $field['type'],
                    'options' => $field['type'] === 'select' ? collect(explode(',', $field['options']))->map(fn (string $option) => trim($option))->filter()->values()->all() : null,
                    'is_required' => (bool) ($field['required'] ?? false),
                    'position' => $position,
                ]);
            }
        });

        return redirect()->route('forms.index')->with('status', 'Formulario actualizado correctamente.');
    }

    public function destroy(Request $request, CredentialRequestForm $form): RedirectResponse
    {
        $this->ensureBelongsToOrganization($request, $form);
        $form->delete();

        return redirect()->route('forms.index')->with('status', 'Formulario eliminado correctamente.');
    }

    private function validatedData(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:1000'],
            'fields' => ['required', 'array', 'min:1', 'max:20'],
            'fields.*.label' => ['required', 'string', 'max:120'],
            'fields.*.type' => ['required', 'in:'.implode(',', self::FIELD_TYPES)],
            'fields.*.required' => ['nullable', 'boolean'],
            'fields.*.options' => ['nullable', 'string', 'max:1000'],
        ]);

        foreach ($data['fields'] as $field) {
            if ($field['type'] === 'select' && blank($field['options'] ?? null)) {
                throw ValidationException::withMessages([
                    'fields' => 'Cada campo de lista debe incluir al menos una opción.',
                ]);
            }
        }

        return $data;
    }

    private function ensureBelongsToOrganization(Request $request, CredentialRequestForm $form): void
    {
        abort_unless($form->organization_id === $request->user()->organization_id, 404);
    }
}
