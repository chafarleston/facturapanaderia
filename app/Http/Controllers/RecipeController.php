<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\RecipeIngredient;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecipeController extends Controller
{
    protected function companyId()
    {
        return Auth::user()->company_id ?? 1;
    }

    public function index()
    {
        $recipes = Recipe::where('company_id', $this->companyId())
            ->with('resultProduct')
            ->orderBy('nombre')
            ->get();
        return view('recipes.index', compact('recipes'));
    }

    public function create()
    {
        $products = Product::where('company_id', $this->companyId())
            ->orderBy('descripcion')->get();
        return view('recipes.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'result_product_id' => 'nullable|exists:products,id',
            'cantidad_producida' => 'required|numeric|min:0',
            'unidad' => 'required|string|max:50',
            'tiempo_preparacion_min' => 'nullable|integer|min:0',
            'instrucciones' => 'nullable|string',
            'ingredients' => 'nullable|array',
            'ingredients.*.product_id' => 'required|exists:products,id',
            'ingredients.*.cantidad' => 'required|numeric|min:0',
            'ingredients.*.unidad' => 'required|string|max:50',
            'ingredients.*.merma_porcentaje' => 'nullable|numeric|min:0|max:100',
            'ingredients.*.costo_unitario' => 'nullable|numeric|min:0',
        ]);

        $validated['company_id'] = $this->companyId();
        $validated['costo_estimado'] = 0;

        $recipe = Recipe::create($validated);

        $costoTotal = 0;
        if ($request->has('ingredients')) {
            foreach ($request->ingredients as $ing) {
                $costo = ($ing['costo_unitario'] ?? 0) * $ing['cantidad'];
                $costoTotal += $costo;
                RecipeIngredient::create([
                    'recipe_id' => $recipe->id,
                    'product_id' => $ing['product_id'],
                    'cantidad' => $ing['cantidad'],
                    'unidad' => $ing['unidad'],
                    'merma_porcentaje' => $ing['merma_porcentaje'] ?? 0,
                    'costo_unitario' => $ing['costo_unitario'] ?? 0,
                ]);
            }
        }

        $recipe->update(['costo_estimado' => $costoTotal]);

        return redirect()->route('recipes.index')
            ->with('success', 'Receta creada exitosamente.');
    }

    public function show(Recipe $recipe)
    {
        $recipe->load('ingredients.product', 'resultProduct');
        return view('recipes.show', compact('recipe'));
    }

    public function edit(Recipe $recipe)
    {
        $products = Product::where('company_id', $this->companyId())
            ->orderBy('descripcion')->get();
        $recipe->load('ingredients.product');
        return view('recipes.edit', compact('recipe', 'products'));
    }

    public function update(Request $request, Recipe $recipe)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'result_product_id' => 'nullable|exists:products,id',
            'cantidad_producida' => 'required|numeric|min:0',
            'unidad' => 'required|string|max:50',
            'tiempo_preparacion_min' => 'nullable|integer|min:0',
            'instrucciones' => 'nullable|string',
            'ingredients' => 'nullable|array',
            'ingredients.*.product_id' => 'required|exists:products,id',
            'ingredients.*.cantidad' => 'required|numeric|min:0',
            'ingredients.*.unidad' => 'required|string|max:50',
            'ingredients.*.merma_porcentaje' => 'nullable|numeric|min:0|max:100',
            'ingredients.*.costo_unitario' => 'nullable|numeric|min:0',
        ]);

        $recipe->update($validated);

        $recipe->ingredients()->delete();
        $costoTotal = 0;
        if ($request->has('ingredients')) {
            foreach ($request->ingredients as $ing) {
                $costo = ($ing['costo_unitario'] ?? 0) * $ing['cantidad'];
                $costoTotal += $costo;
                RecipeIngredient::create([
                    'recipe_id' => $recipe->id,
                    'product_id' => $ing['product_id'],
                    'cantidad' => $ing['cantidad'],
                    'unidad' => $ing['unidad'],
                    'merma_porcentaje' => $ing['merma_porcentaje'] ?? 0,
                    'costo_unitario' => $ing['costo_unitario'] ?? 0,
                ]);
            }
        }

        $recipe->update(['costo_estimado' => $costoTotal]);

        return redirect()->route('recipes.index')
            ->with('success', 'Receta actualizada exitosamente.');
    }

    public function destroy(Recipe $recipe)
    {
        $recipe->ingredients()->delete();
        $recipe->delete();

        return redirect()->route('recipes.index')
            ->with('success', 'Receta eliminada exitosamente.');
    }
}
