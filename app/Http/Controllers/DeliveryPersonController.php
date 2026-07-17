<?php

namespace App\Http\Controllers;

use App\Models\DeliveryPerson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeliveryPersonController extends Controller
{
    protected function companyId()
    {
        return Auth::user()->company_id ?? 1;
    }

    public function index()
    {
        $persons = DeliveryPerson::where('company_id', $this->companyId())
            ->orderBy('nombre')->get();
        return view('delivery-persons.index', compact('persons'));
    }

    public function create()
    {
        return view('delivery-persons.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'vehiculo' => 'nullable|string|max:100',
            'activo' => 'boolean',
        ]);

        $validated['company_id'] = $this->companyId();
        DeliveryPerson::create($validated);

        return redirect()->route('delivery-persons.index')
            ->with('success', 'Repartidor creado exitosamente.');
    }

    public function edit(DeliveryPerson $deliveryPerson)
    {
        return view('delivery-persons.edit', compact('deliveryPerson'));
    }

    public function update(Request $request, DeliveryPerson $deliveryPerson)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'vehiculo' => 'nullable|string|max:100',
            'activo' => 'boolean',
        ]);

        $deliveryPerson->update($validated);

        return redirect()->route('delivery-persons.index')
            ->with('success', 'Repartidor actualizado.');
    }

    public function destroy(DeliveryPerson $deliveryPerson)
    {
        $deliveryPerson->delete();

        return redirect()->route('delivery-persons.index')
            ->with('success', 'Repartidor eliminado.');
    }
}
