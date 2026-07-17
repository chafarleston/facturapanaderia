@extends('layouts.admin')
@section('title', 'Nueva Merma')
@section('page_title', 'Nueva Merma')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Registrar Merma</h3></div>
            <form method="POST" action="{{ route('waste.store') }}">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Producto <span class="text-danger">*</span></label>
                                <select name="product_id" class="form-control" required>
                                    <option value="">Seleccionar producto...</option>
                                    @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Fecha <span class="text-danger">*</span></label>
                                <input type="date" name="fecha" class="form-control" required value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Cantidad <span class="text-danger">*</span></label>
                                <input type="number" name="cantidad" class="form-control" step="0.01" min="0.01" required>
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
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Motivo <span class="text-danger">*</span></label>
                                <select name="motivo" class="form-control" required>
                                    <option value="">Seleccionar motivo...</option>
                                    <option value="vencido">Vencido</option>
                                    <option value="danado">Dañado</option>
                                    <option value="devolucion">Devolución</option>
                                    <option value="no_vendido">No Vendido</option>
                                    <option value="produccion">Error de Producción</option>
                                    <option value="otro">Otro</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Notas</label>
                        <textarea name="notas" class="form-control" rows="3" placeholder="Detalles adicionales..."></textarea>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
                    <a href="{{ route('waste.index') }}" class="btn btn-default">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
