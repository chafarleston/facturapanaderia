<?php

namespace App\Http\Controllers;

use App\Models\WasteRecord;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WasteRecordController extends Controller
{
    protected function companyId()
    {
        return Auth::user()->company_id ?? 1;
    }

    public function index()
    {
        $records = WasteRecord::where('company_id', $this->companyId())
            ->with('product', 'user')
            ->orderBy('fecha', 'desc')
            ->orderBy('id', 'desc')
            ->get();
        return view('waste.index', compact('records'));
    }

    public function create()
    {
        $products = Product::where('company_id', $this->companyId())
            ->where('stock', '>', 0)
            ->orderBy('descripcion')->get();
        return view('waste.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'fecha' => 'required|date',
            'cantidad' => 'required|numeric|min:0.0001',
            'unidad' => 'required|string|max:50',
            'motivo' => 'required|in:vencido,danado,devolucion,no_vendido,produccion,otro',
            'notas' => 'nullable|string',
        ]);

        $validated['company_id'] = $this->companyId();
        $validated['user_id'] = Auth::id();

        $product = Product::find($request->product_id);
        $costoUnitario = $product ? $product->precio_compra : 0;
        $validated['costo_perdida'] = $costoUnitario * $request->cantidad;

        $waste = WasteRecord::create($validated);

        $product->decrement('stock', $request->cantidad);

        return redirect()->route('waste.index')
            ->with('success', 'Merma registrada exitosamente.');
    }

    public function show(WasteRecord $waste)
    {
        $waste->load('product', 'user');
        return view('waste.show', compact('waste'));
    }

    public function edit(WasteRecord $waste)
    {
        $products = Product::where('company_id', $this->companyId())
            ->orderBy('descripcion')->get();
        return view('waste.edit', compact('waste', 'products'));
    }

    public function update(Request $request, WasteRecord $waste)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'fecha' => 'required|date',
            'cantidad' => 'required|numeric|min:0.0001',
            'unidad' => 'required|string|max:50',
            'motivo' => 'required|in:vencido,danado,devolucion,no_vendido,produccion,otro',
            'notas' => 'nullable|string',
        ]);

        $waste->update($validated);

        return redirect()->route('waste.index')
            ->with('success', 'Registro de merma actualizado.');
    }

    public function destroy(WasteRecord $waste)
    {
        $product = Product::find($waste->product_id);
        if ($product) {
            $product->increment('stock', $waste->cantidad);
        }

        $waste->delete();

        return redirect()->route('waste.index')
            ->with('success', 'Registro de merma eliminado. Stock restaurado.');
    }
}
