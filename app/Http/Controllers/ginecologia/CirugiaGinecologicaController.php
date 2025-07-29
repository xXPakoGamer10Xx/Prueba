<?php

namespace App\Http\Controllers\Ginecologia;

use App\Http\Controllers\Controller;
use App\Models\Ginecologia\CirugiaGinecologica;
use Illuminate\Http\Request;

class CirugiaGinecologicaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([ 'id_cirugia_ginecologica' => 'required|string|max:10|unique:cirugiasginecologica', /* ... */ ]);
        CirugiaGinecologica::create($request->all());
        return redirect()->route('cirugia.index')->with('success', 'Cirugía Ginecológica registrada.');
    }

    public function update(Request $request, CirugiaGinecologica $cirugiaginecologica)
    {
        $request->validate([/* ... */]);
        $cirugiaginecologica->update($request->all());
        return redirect()->route('cirugia.index')->with('success', 'Cirugía Ginecológica actualizada.');
    }

    public function destroy(CirugiaGinecologica $cirugiaginecologica)
    {
        $cirugiaginecologica->delete();
        return redirect()->route('cirugia.index')->with('success', 'Cirugía Ginecológica eliminada.');
    }
}