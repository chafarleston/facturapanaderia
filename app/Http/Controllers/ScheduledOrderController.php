<?php

namespace App\Http\Controllers;

use App\Models\ScheduledOrder;
use App\Models\ScheduledOrderItem;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduledOrderController extends Controller
{
    protected function companyId()
    {
        return Auth::user()->company_id ?? 1;
    }

    protected function generateOrderNumber()
    {
        $prefix = 'P-';
        $lastOrder = ScheduledOrder::where('company_id', $this->companyId())
            ->orderBy('id', 'desc')->first();
        $next = $lastOrder ? intval(substr($lastOrder->order_number, 2)) + 1 : 1;
        return $prefix . str_pad($next, 6, '0', STR_PAD_LEFT);
    }

    public function index()
    {
        $orders = ScheduledOrder::where('company_id', $this->companyId())
            ->with('customer', 'user')
            ->orderBy('fecha_entrega', 'desc')
            ->orderBy('id', 'desc')
            ->get();
        return view('scheduled-orders.index', compact('orders'));
    }

    public function create()
    {
        $products = Product::where('company_id', $this->companyId())
            ->orderBy('descripcion')->get();
        $customers = Customer::where('company_id', $this->companyId())
            ->orderBy('nombre')->get();
        return view('scheduled-orders.create', compact('products', 'customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'fecha_pedido' => 'required|date',
            'fecha_entrega' => 'required|date',
            'hora_entrega' => 'nullable',
            'descripcion' => 'nullable|string',
            'notas' => 'nullable|string',
            'telefono_contacto' => 'nullable|string|max:20',
            'anticipo' => 'nullable|numeric|min:0',
            'para_delivery' => 'boolean',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.descripcion_personalizada' => 'nullable|string',
            'items.*.cantidad' => 'required|numeric|min:0.0001',
            'items.*.precio_unitario' => 'required|numeric|min:0',
        ]);

        $subtotal = 0;
        foreach ($request->items as $item) {
            $subtotal += $item['cantidad'] * $item['precio_unitario'];
        }

        $igvPercent = 0.18;
        $igv = $subtotal * $igvPercent / (1 + $igvPercent);
        $total = $subtotal;
        $anticipo = $request->anticipo ?? 0;
        $saldo = $total - $anticipo;

        $order = ScheduledOrder::create([
            'company_id' => $this->companyId(),
            'customer_id' => $request->customer_id,
            'user_id' => Auth::id(),
            'order_number' => $this->generateOrderNumber(),
            'fecha_pedido' => $request->fecha_pedido,
            'fecha_entrega' => $request->fecha_entrega,
            'hora_entrega' => $request->hora_entrega,
            'estado' => 'pendiente',
            'subtotal' => $subtotal,
            'igv' => $igv,
            'total' => $total,
            'anticipo' => $anticipo,
            'saldo' => $saldo,
            'descripcion' => $request->descripcion,
            'notas' => $request->notas,
            'telefono_contacto' => $request->telefono_contacto,
            'para_delivery' => $request->para_delivery ?? false,
        ]);

        foreach ($request->items as $item) {
            ScheduledOrderItem::create([
                'scheduled_order_id' => $order->id,
                'product_id' => $item['product_id'] ?? null,
                'descripcion_personalizada' => $item['descripcion_personalizada'] ?? null,
                'cantidad' => $item['cantidad'],
                'precio_unitario' => $item['precio_unitario'],
                'subtotal' => $item['cantidad'] * $item['precio_unitario'],
                'notas' => $item['notas'] ?? null,
            ]);
        }

        return redirect()->route('scheduled-orders.index')
            ->with('success', 'Pedido programado creado exitosamente.');
    }

    public function show(ScheduledOrder $scheduledOrder)
    {
        $scheduledOrder->load('items.product', 'customer', 'user');
        return view('scheduled-orders.show', compact('scheduledOrder'));
    }

    public function edit(ScheduledOrder $scheduledOrder)
    {
        $products = Product::where('company_id', $this->companyId())
            ->orderBy('descripcion')->get();
        $customers = Customer::where('company_id', $this->companyId())
            ->orderBy('nombre')->get();
        $scheduledOrder->load('items.product');
        return view('scheduled-orders.edit', compact('scheduledOrder', 'products', 'customers'));
    }

    public function update(Request $request, ScheduledOrder $scheduledOrder)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'fecha_entrega' => 'required|date',
            'hora_entrega' => 'nullable',
            'descripcion' => 'nullable|string',
            'notas' => 'nullable|string',
            'telefono_contacto' => 'nullable|string|max:20',
            'anticipo' => 'nullable|numeric|min:0',
            'para_delivery' => 'boolean',
        ]);

        $anticipo = $request->anticipo ?? $scheduledOrder->anticipo;
        $saldo = $scheduledOrder->total - $anticipo;

        $scheduledOrder->update([
            'customer_id' => $request->customer_id,
            'fecha_entrega' => $request->fecha_entrega,
            'hora_entrega' => $request->hora_entrega,
            'descripcion' => $request->descripcion,
            'notas' => $request->notas,
            'telefono_contacto' => $request->telefono_contacto,
            'anticipo' => $anticipo,
            'saldo' => $saldo,
            'para_delivery' => $request->para_delivery ?? false,
        ]);

        return redirect()->route('scheduled-orders.index')
            ->with('success', 'Pedido actualizado exitosamente.');
    }

    public function confirm(ScheduledOrder $scheduledOrder)
    {
        $scheduledOrder->update(['estado' => 'confirmado']);
        return redirect()->route('scheduled-orders.index')
            ->with('success', 'Pedido confirmado.');
    }

    public function startProduction(ScheduledOrder $scheduledOrder)
    {
        $scheduledOrder->update(['estado' => 'en_produccion']);
        return redirect()->route('scheduled-orders.index')
            ->with('success', 'Pedido en producción.');
    }

    public function markReady(ScheduledOrder $scheduledOrder)
    {
        $scheduledOrder->update(['estado' => 'listo']);
        return redirect()->route('scheduled-orders.index')
            ->with('success', 'Pedido listo para entrega.');
    }

    public function deliver(ScheduledOrder $scheduledOrder)
    {
        $scheduledOrder->update(['estado' => 'entregado']);
        return redirect()->route('scheduled-orders.index')
            ->with('success', 'Pedido entregado.');
    }

    public function cancel(ScheduledOrder $scheduledOrder)
    {
        $scheduledOrder->update(['estado' => 'cancelado']);
        return redirect()->route('scheduled-orders.index')
            ->with('success', 'Pedido cancelado.');
    }

    public function printComanda(ScheduledOrder $scheduledOrder)
    {
        $scheduledOrder->load('items.product', 'customer');

        return redirect()->route('scheduled-orders.show', $scheduledOrder)
            ->with('success', 'Comanda enviada a impresión.');
    }

    public function destroy(ScheduledOrder $scheduledOrder)
    {
        $scheduledOrder->items()->delete();
        $scheduledOrder->delete();

        return redirect()->route('scheduled-orders.index')
            ->with('success', 'Pedido programado eliminado.');
    }
}
