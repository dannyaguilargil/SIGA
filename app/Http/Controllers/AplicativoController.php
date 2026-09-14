<?php

namespace App\Http\Controllers;

use App\Models\Aplicativo;
use App\Models\Modulo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AplicativoController extends Controller
{
    public function index(Request $request): View
    {
        return view('aplicativos.index', [
            'aplicativos' => Aplicativo::query()
                ->where('organization_id', $request->user()->organization_id)
                ->with('modulos')
                ->orderBy('nombre_aplicativo')
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['nombre_aplicativo' => ['required', 'string', 'max:255', 'unique:aplicativos,nombre_aplicativo,NULL,id,organization_id,'.$request->user()->organization_id]]);
        Aplicativo::create(['organization_id' => $request->user()->organization_id, 'nombre_aplicativo' => $data['nombre_aplicativo']]);

        return back()->with('status', 'Aplicativo creado correctamente.');
    }

    public function storeModulo(Request $request, Aplicativo $aplicativo): RedirectResponse
    {
        abort_unless($aplicativo->organization_id === $request->user()->organization_id, 404);
        $data = $request->validate([
            'nombre_modulo' => ['required', 'string', 'max:255'],
            'tipo' => ['required', 'in:modulo,rol'],
        ]);
        $aplicativo->modulos()->create($data);

        return back()->with('status', ucfirst($data['tipo']).' agregado correctamente.');
    }

    public function destroy(Request $request, Aplicativo $aplicativo): RedirectResponse
    {
        abort_unless($aplicativo->organization_id === $request->user()->organization_id, 404);
        $aplicativo->delete();

        return back()->with('status', 'Aplicativo eliminado correctamente.');
    }

    public function destroyModulo(Request $request, Aplicativo $aplicativo, Modulo $modulo): RedirectResponse
    {
        abort_unless(
            $aplicativo->organization_id === $request->user()->organization_id && $modulo->aplicativo_id === $aplicativo->id,
            404,
        );
        $modulo->delete();

        return back()->with('status', 'Módulo eliminado correctamente.');
    }
}
