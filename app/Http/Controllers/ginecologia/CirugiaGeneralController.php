<?php

namespace App\Http\Controllers\Ginecologia;

use App\Http\Controllers\Controller;
use App\Models\Ginecologia\CirugiaGeneral;
use Illuminate\Http\Request;

class CirugiaGeneralController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([ 'id_cirugia_general' => 'required|string|max:10|unique:cirugiasgeneral', /* ... */ ]);
        CirugiaGeneral::create($request->all());
        return redirect()->route('cirugia.index')->with('success', 'Cirugía General registrada.');
    }

    public function update(Request $request, CirugiaGeneral $cirugiageneral)
    {
        $request->validate([/* ... */]);
        $cirugiageneral->update($request->all());
        return redirect()->route('cirugia.index')->with('success', 'Cirugía General actualizada.');
    }

    public function destroy(CirugiaGeneral $cirugiageneral)
    {
        $cirugiageneral->delete();
        return redirect()->route('cirugia.index')->with('success', 'Cirugía General eliminada.');
    }
}