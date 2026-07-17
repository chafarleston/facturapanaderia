<?php

namespace App\Http\Controllers;

use App\Models\DeliveryZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeliveryZoneController extends Controller
{
    protected function companyId()
    {
        return Auth::user()->company_id ?? 1;
    }

    public function index()
    {
        $zones = DeliveryZone::where('company_id', $this->companyId())
            ->orderBy('nombre')->get();
        return view('delivery-zones.index', compact('zones'));
    }

    public function create()
    {
        return view('delivery-zones.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'precio_envio' => 'required|numeric|min:0',
            'tiempo_estimado_min' => 'nullable|integer|min:0',
            'activa' => 'boolean',
        ]);

        $validated['company_id'] = $this->companyId();
        DeliveryZone::create($validated);

        return redirect()->route('delivery-zones.index')
            ->with('success', 'Zona de reparto creada exitosamente.');
    }

    public function edit(DeliveryZone $deliveryZone)
    {
        return view('delivery-zones.edit', compact('deliveryZone'));
    }

    public function update(Request $request, DeliveryZone $deliveryZone)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'precio_envio' => 'required|numeric|min:0',
            'tiempo_estimado_min' => 'nullable|integer|min:0',
            'activa' => 'boolean',
        ]);

        $deliveryZone->update($validated);

        return redirect()->route('delivery-zones.index')
            ->with('success', 'Zona de reparto actualizada.');
    }

    public function destroy(DeliveryZone $deliveryZone)
    {
        $deliveryZone->delete();

        return redirect()->route('delivery-zones.index')
            ->with('success', 'Zona de reparto eliminada.');
    }
}
