<?php

namespace App\Http\Controllers;

use App\Models\ProductionOrder;
use App\Models\Recipe;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductionOrderController extends Controller
{
    protected function companyId()
    {
        return Auth::user()->company_id ?? 1;
    }

    public function index()
    {
        $orders = ProductionOrder::where('company_id', $this->companyId())
            ->with('recipe', 'product', 'user')
            ->orderBy('fecha_produccion', 'desc')
            ->orderBy('id', 'desc')
            ->get();
        return view('production-orders.index', compact('orders'));
    }

    public function create()
    {
        $recipes = Recipe::where('company_id', $this->companyId())
            ->where('activa', true)
            ->with('resultProduct')
            ->orderBy('nombre')
            ->get();
        $products = Product::where('company_id', $this->companyId())
            ->orderBy('descripcion')->get();
        return view('production-orders.create', compact('recipes', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'recipe_id' => 'nullable|exists:recipes,id',
            'product_id' => 'nullable|exists:products,id',
            'fecha_produccion' => 'required|date',
            'cantidad_planificada' => 'required|numeric|min:0',
            'unidad' => 'required|string|max:50',
            'notas' => 'nullable|string',
        ]);

        $validated['company_id'] = $this->companyId();
        $validated['user_id'] = Auth::id();
        $validated['cantidad_producida'] = 0;
        $validated['estado'] = 'planificado';
        $validated['costo_total'] = 0;

        if ($request->filled('recipe_id')) {
            $recipe = Recipe::find($request->recipe_id);
            if ($recipe) {
                $validated['costo_total'] = $recipe->costo_estimado * $request->cantidad_planificada;
            }
        }

        ProductionOrder::create($validated);

        return redirect()->route('production-orders.index')
            ->with('success', 'Orden de producción creada exitosamente.');
    }

    public function show(ProductionOrder $productionOrder)
    {
        $productionOrder->load('recipe.ingredients.product', 'product', 'user');
        return view('production-orders.show', compact('productionOrder'));
    }

    public function edit(ProductionOrder $productionOrder)
    {
        $recipes = Recipe::where('company_id', $this->companyId())
            ->where('activa', true)->with('resultProduct')->orderBy('nombre')->get();
        $products = Product::where('company_id', $this->companyId())
            ->orderBy('descripcion')->get();
        return view('production-orders.edit', compact('productionOrder', 'recipes', 'products'));
    }

    public function update(Request $request, ProductionOrder $productionOrder)
    {
        $validated = $request->validate([
            'recipe_id' => 'nullable|exists:recipes,id',
            'product_id' => 'nullable|exists:products,id',
            'fecha_produccion' => 'required|date',
            'cantidad_planificada' => 'required|numeric|min:0',
            'notas' => 'nullable|string',
        ]);

        $productionOrder->update($validated);

        return redirect()->route('production-orders.index')
            ->with('success', 'Orden de producción actualizada.');
    }

    public function start(ProductionOrder $productionOrder)
    {
        $productionOrder->update(['estado' => 'en_proceso']);

        if ($productionOrder->recipe) {
            foreach ($productionOrder->recipe->ingredients as $ingredient) {
                $product = $ingredient->product;
                if ($product && $product->stock) {
                    $cantidadNecesaria = $ingredient->cantidad * $productionOrder->cantidad_planificada;
                    $merma = $cantidadNecesaria * ($ingredient->merma_porcentaje / 100);
                    $totalDescontar = $cantidadNecesaria + $merma;
                    $product->decrement('stock', $totalDescontar);
                }
            }
        }

        return redirect()->route('production-orders.index')
            ->with('success', 'Producción iniciada. Ingredientes descontados del inventario.');
    }

    public function complete(Request $request, ProductionOrder $productionOrder)
    {
        $request->validate([
            'cantidad_producida' => 'required|numeric|min:0',
        ]);

        $productionOrder->update([
            'estado' => 'completado',
            'cantidad_producida' => $request->cantidad_producida,
        ]);

        if ($productionOrder->product_id && $request->cantidad_producida > 0) {
            Product::where('id', $productionOrder->product_id)
                ->increment('stock', $request->cantidad_producida);
        }

        return redirect()->route('production-orders.index')
            ->with('success', 'Producción completada. Stock actualizado.');
    }

    public function cancel(ProductionOrder $productionOrder)
    {
        $productionOrder->update(['estado' => 'cancelado']);

        return redirect()->route('production-orders.index')
            ->with('success', 'Orden de producción cancelada.');
    }

    public function destroy(ProductionOrder $productionOrder)
    {
        $productionOrder->delete();

        return redirect()->route('production-orders.index')
            ->with('success', 'Orden de producción eliminada.');
    }
}
