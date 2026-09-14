<?php

namespace App\Http\Controllers;

use App\Models\Aplicativo;
use App\Models\CredentialRequestForm;
use App\Models\Modulo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CredentialRequestController extends Controller
{
    public function index(Request $request): View
    {
        return view('requests.index', [
            'forms' => CredentialRequestForm::query()
                ->where('organization_id', $request->user()->organization_id)
                ->where('is_active', true)
                ->with('fields')
                ->get(),
        ]);
    }

    public function create(Request $request, CredentialRequestForm $form): View
    {
        $this->ensureBelongsToUserOrganization($request, $form);
        abort_unless($form->is_active, 404);

        $aplicativos = Aplicativo::query()
            ->where('organization_id', $request->user()->organization_id)
            ->where('activo', true)
            ->with(['modulos' => fn ($query) => $query->where('activo', true)->orderBy('nombre_modulo')])
            ->orderBy('nombre_aplicativo')
            ->get();

        return view('requests.create', [
            'form' => $form->load('fields'),
            'aplicativos' => $aplicativos,
            'applicationOptions' => $aplicativos->map(fn ($app) => [
                'id' => $app->id,
                'modulos' => $app->modulos->map(fn ($module) => ['id' => $module->id, 'name' => $module->nombre_modulo]),
            ])->values(),
        ]);
    }

    public function store(Request $request, CredentialRequestForm $form): RedirectResponse
    {
        $this->ensureBelongsToUserOrganization($request, $form);
        abort_unless($form->is_active, 404);
        $form->load('fields');

        $rules = [];
        foreach ($form->fields as $field) {
            $fieldRules = [$field->is_required ? 'required' : 'nullable'];
            $fieldRules[] = match ($field->type) {
                'number' => 'numeric',
                'date' => 'date',
                'select' => Rule::in($field->options),
                'aplicativo' => Rule::exists('aplicativos', 'id')->where(fn ($query) => $query->where('organization_id', $request->user()->organization_id)->where('activo', true)),
                'modulo' => Rule::exists('modulos', 'id')->where('activo', true),
                default => 'string',
            };
            $fieldRules[] = 'max:1000';
            $rules['answers.'.$field->id] = $fieldRules;
        }

        $answers = $request->validate($rules)['answers'] ?? [];
        $selectedAplicativo = $form->fields->firstWhere('type', 'aplicativo');
        foreach ($form->fields->where('type', 'modulo') as $moduleField) {
            $moduleId = $answers[$moduleField->id] ?? null;
            $applicationId = $selectedAplicativo ? ($answers[$selectedAplicativo->id] ?? null) : null;
            if ($moduleId && (! $applicationId || ! Modulo::where('id', $moduleId)->where('aplicativo_id', $applicationId)->exists())) {
                return back()->withErrors(['answers.'.$moduleField->id => 'El módulo seleccionado no pertenece al aplicativo indicado.'])->withInput();
            }
        }
        $form->requests()->create([
            'user_id' => $request->user()->id,
            'answers' => collect($answers)->mapWithKeys(fn ($answer, $id) => [$id => ['label' => $form->fields->firstWhere('id', $id)?->label, 'value' => $answer]])->all(),
        ]);

        return redirect()->route('requests.index')->with('status', 'Tu solicitud fue enviada correctamente.');
    }

    private function ensureBelongsToUserOrganization(Request $request, CredentialRequestForm $form): void
    {
        abort_unless($form->organization_id === $request->user()->organization_id, 404);
    }
}
