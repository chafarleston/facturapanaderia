@extends('layouts.admin')
@section('title', 'Nueva Orden de Producción')
@section('page_title', 'Nueva Orden de Producción')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Formulario</h3></div>
            <form method="POST" action="{{ route('production-orders.store') }}">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Receta</label>
                                <select name="recipe_id" id="recipe_id" class="form-control">
                                    <option value="">Seleccionar receta...</option>
                                    @foreach($recipes as $recipe)
                                    <option value="{{ $recipe->id }}" data-product="{{ $recipe->result_product_id }}" data-cantidad="{{ $recipe->cantidad_producida }}" data-unidad="{{ $recipe->unidad }}">{{ $recipe->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Producto <span class="text-danger">*</span></label>
                                <select name="product_id" id="product_id" class="form-control" required>
                                    <option value="">Seleccionar producto...</option>
                                    @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Fecha Producción <span class="text-danger">*</span></label>
                                <input type="date" name="fecha_produccion" class="form-control" required value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Cantidad Planificada <span class="text-danger">*</span></label>
                                <input type="number" name="cantidad_planificada" id="cantidad_planificada" class="form-control" step="0.01" min="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Unidad <span class="text-danger">*</span></label>
                                <select name="unidad" id="unidad" class="form-control" required>
                                    <option value="UNIDAD">UNIDAD</option>
                                    <option value="KG">KG</option>
                                    <option value="LT">LT</option>
                                    <option value="DOCENA">DOCENA</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Notas</label>
                        <textarea name="notas" class="form-control" rows="3" placeholder="Notas adicionales..."></textarea>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
                    <a href="{{ route('production-orders.index') }}" class="btn btn-default">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('recipe_id').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    if (opt.value) {
        document.getElementById('product_id').value = opt.dataset.product;
        document.getElementById('cantidad_planificada').value = opt.dataset.cantidad;
        document.getElementById('unidad').value = opt.dataset.unidad;
    }
});
</script>
@endsection
