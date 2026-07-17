<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\DeliveryZone;
use App\Models\DeliveryPerson;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeliveryController extends Controller
{
    protected function companyId()
    {
        return Auth::user()->company_id ?? 1;
    }

    public function index()
    {
        $deliveries = Delivery::where('company_id', $this->companyId())
            ->with('invoice', 'deliveryZone', 'deliveryPerson')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('deliveries.index', compact('deliveries'));
    }

    public function create()
    {
        $zones = DeliveryZone::where('company_id', $this->companyId())
            ->where('activa', true)->orderBy('nombre')->get();
        $persons = DeliveryPerson::where('company_id', $this->companyId())
            ->where('activo', true)->orderBy('nombre')->get();
        $invoices = Invoice::where('company_id', $this->companyId())
            ->whereIn('tipo_documento', ['01', '03'])
            ->orderBy('fecha_emision', 'desc')
            ->orderBy('id', 'desc')
            ->take(200)
            ->get();
        return view('deliveries.create', compact('zones', 'persons', 'invoices'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'nullable|exists:invoices,id',
            'delivery_zone_id' => 'nullable|exists:delivery_zones,id',
            'delivery_person_id' => 'nullable|exists:delivery_persons,id',
            'direccion' => 'required|string|max:500',
            'referencia' => 'nullable|string|max:500',
            'telefono_contacto' => 'nullable|string|max:20',
            'costo_envio' => 'nullable|numeric|min:0',
            'notas' => 'nullable|string',
        ]);

        $validated['company_id'] = $this->companyId();
        $validated['estado'] = 'pendiente';

        Delivery::create($validated);

        return redirect()->route('deliveries.index')
            ->with('success', 'Reparto creado exitosamente.');
    }

    public function show(Delivery $delivery)
    {
        $delivery->load('invoice.customer', 'deliveryZone', 'deliveryPerson');
        return view('deliveries.show', compact('delivery'));
    }

    public function edit(Delivery $delivery)
    {
        $zones = DeliveryZone::where('company_id', $this->companyId())
            ->where('activa', true)->orderBy('nombre')->get();
        $persons = DeliveryPerson::where('company_id', $this->companyId())
            ->where('activo', true)->orderBy('nombre')->get();
        return view('deliveries.edit', compact('delivery', 'zones', 'persons'));
    }

    public function update(Request $request, Delivery $delivery)
    {
        $validated = $request->validate([
            'delivery_zone_id' => 'nullable|exists:delivery_zones,id',
            'delivery_person_id' => 'nullable|exists:delivery_persons,id',
            'direccion' => 'required|string|max:500',
            'referencia' => 'nullable|string|max:500',
            'telefono_contacto' => 'nullable|string|max:20',
            'costo_envio' => 'nullable|numeric|min:0',
            'notas' => 'nullable|string',
        ]);

        $delivery->update($validated);

        return redirect()->route('deliveries.index')
            ->with('success', 'Reparto actualizado.');
    }

    public function assign(Request $request, Delivery $delivery)
    {
        $request->validate([
            'delivery_person_id' => 'required|exists:delivery_persons,id',
        ]);

        $delivery->update([
            'delivery_person_id' => $request->delivery_person_id,
        ]);

        return redirect()->route('deliveries.index')
            ->with('success', 'Repartidor asignado.');
    }

    public function startRoute(Delivery $delivery)
    {
        $delivery->update(['estado' => 'en_ruta']);
        return redirect()->route('deliveries.index')
            ->with('success', 'Reparto en ruta.');
    }

    public function complete(Request $request, Delivery $delivery)
    {
        $delivery->update([
            'estado' => 'entregado',
            'fecha_entrega' => now(),
        ]);

        return redirect()->route('deliveries.index')
            ->with('success', 'Reparto completado.');
    }

    public function cancel(Delivery $delivery)
    {
        $delivery->update(['estado' => 'cancelado']);
        return redirect()->route('deliveries.index')
            ->with('success', 'Reparto cancelado.');
    }

    public function destroy(Delivery $delivery)
    {
        $delivery->delete();

        return redirect()->route('deliveries.index')
            ->with('success', 'Reparto eliminado.');
    }
}
