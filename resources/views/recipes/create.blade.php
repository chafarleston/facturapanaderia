@extends('layouts.admin')
@section('title', 'Nueva Receta')
@section('page_title', 'Nueva Receta')

@push('styles')
<style>
    .ingredient-row { margin-bottom: 10px; padding: 10px; background: #f8f9fa; border-radius: 5px; }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Formulario de Receta</h3></div>
            <form method="POST" action="{{ route('recipes.store') }}" id="recipeForm">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nombre <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" class="form-control" required placeholder="Ej: Pan Ciabatta">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Producto Resultante <span class="text-danger">*</span></label>
                                <select name="result_product_id" class="form-control" required>
                                    <option value="">Seleccionar producto...</option>
                                    @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="2" placeholder="Descripción breve de la receta"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Cantidad Producida <span class="text-danger">*</span></label>
                                <input type="number" name="cantidad_producida" class="form-control" step="0.01" min="0.01" required placeholder="100">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Unidad <span class="text-danger">*</span></label>
                                <select name="unidad" class="form-control" required>
                                    <option value="UNIDAD">UNIDAD</option>
                                    <option value="KG">KG</option>
                                    <option value="LT">LT</option>
                                    <option value="DOCENA">DOCENA</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tiempo Prep. (min)</label>
                                <input type="number" name="tiempo_preparacion_min" class="form-control" min="0" placeholder="60">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Instrucciones</label>
                        <textarea name="instrucciones" class="form-control" rows="3" placeholder="Pasos de preparación..."></textarea>
                    </div>
                </div>

                <div class="card card-secondary m-3">
                    <div class="card-header">
                        <h3 class="card-title">Ingredientes</h3>
                        <button type="button" class="btn btn-success btn-sm float-right" onclick="addIngredientRow()">
                            <i class="fas fa-plus"></i> Agregar Ingrediente
                        </button>
                    </div>
                    <div class="card-body" id="ingredientsContainer">
                        <p class="text-muted text-center" id="noIngredients">Sin ingredientes. Haga clic en "Agregar Ingrediente".</p>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
                    <a href="{{ route('recipes.index') }}" class="btn btn-default">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let ingredientIndex = 0;

function addIngredientRow(data = null) {
    document.getElementById('noIngredients').style.display = 'none';
    const idx = ingredientIndex++;
    const html = `
        <div class="ingredient-row" id="ingredient-row-${idx}">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group mb-0">
                        <label>Producto <span class="text-danger">*</span></label>
                        <select name="ingredients[${idx}][product_id]" class="form-control" required>
                            <option value="">Seleccionar...</option>
                            @foreach($products as $product)
                            <option value="{{ $product->id }}" ${data && data.product_id == {{ $product->id }} ? 'selected' : ''}>{{ $product->descripcion }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <label>Cantidad <span class="text-danger">*</span></label>
                        <input type="number" name="ingredients[${idx}][cantidad]" class="form-control" step="0.01" min="0.01" required value="${data ? data.cantidad : ''}">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <label>Unidad</label>
                        <select name="ingredients[${idx}][unidad]" class="form-control">
                            <option value="UNIDAD">UNIDAD</option>
                            <option value="KG" ${data && data.unidad == 'KG' ? 'selected' : ''}>KG</option>
                            <option value="LT" ${data && data.unidad == 'LT' ? 'selected' : ''}>LT</option>
                            <option value="GR" ${data && data.unidad == 'GR' ? 'selected' : ''}>GR</option>
                            <option value="ML" ${data && data.unidad == 'ML' ? 'selected' : ''}>ML</option>
                            <option value="DOCENA" ${data && data.unidad == 'DOCENA' ? 'selected' : ''}>DOCENA</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <label>Merma %</label>
                        <input type="number" name="ingredients[${idx}][merma_porcentaje]" class="form-control" step="0.01" min="0" max="100" value="${data ? data.merma_porcentaje : '0'}">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <label>Costo Unit.</label>
                        <input type="number" name="ingredients[${idx}][costo_unitario]" class="form-control" step="0.01" min="0" value="${data ? data.costo_unitario : ''}">
                    </div>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-sm" onclick="document.getElementById('ingredient-row-${idx}').remove(); if(!document.querySelectorAll('.ingredient-row').length) document.getElementById('noIngredients').style.display='';">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>`;
    document.getElementById('ingredientsContainer').insertAdjacentHTML('beforeend', html);
}
</script>
@endsection
