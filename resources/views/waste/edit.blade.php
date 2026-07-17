@extends('layouts.admin')
@section('title', 'Editar Merma')
@section('page_title', 'Editar Merma')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Editar Registro de Merma</h3></div>
            <form method="POST" action="{{ route('waste.update', $wasteRecord) }}">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Producto <span class="text-danger">*</span></label>
                                <select name="product_id" class="form-control" required>
                                    <option value="">Seleccionar producto...</option>
                                    @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ old('product_id', $wasteRecord->product_id) == $product->id ? 'selected' : '' }}>{{ $product->descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Fecha <span class="text-danger">*</span></label>
                                <input type="date" name="fecha" class="form-control" required value="{{ old('fecha', $wasteRecord->fecha) }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Cantidad <span class="text-danger">*</span></label>
                                <input type="number" name="cantidad" class="form-control" step="0.01" min="0.01" required value="{{ old('cantidad', $wasteRecord->cantidad) }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Unidad <span class="text-danger">*</span></label>
                                <select name="unidad" class="form-control" required>
                                    <option value="UNIDAD" {{ old('unidad', $wasteRecord->unidad) == 'UNIDAD' ? 'selected' : '' }}>UNIDAD</option>
                                    <option value="KG" {{ old('unidad', $wasteRecord->unidad) == 'KG' ? 'selected' : '' }}>KG</option>
                                    <option value="LT" {{ old('unidad', $wasteRecord->unidad) == 'LT' ? 'selected' : '' }}>LT</option>
                                    <option value="DOCENA" {{ old('unidad', $wasteRecord->unidad) == 'DOCENA' ? 'selected' : '' }}>DOCENA</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Motivo <span class="text-danger">*</span></label>
                                <select name="motivo" class="form-control" required>
                                    <option value="">Seleccionar motivo...</option>
                                    @php $motivos = ['vencido','danado','devolucion','no_vendido','produccion','otro']; @endphp
                                    @foreach($motivos as $m)
                                    <option value="{{ $m }}" {{ old('motivo', $wasteRecord->motivo) == $m ? 'selected' : '' }}>{{ ucfirst($m) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Notas</label>
                        <textarea name="notas" class="form-control" rows="3">{{ old('notas', $wasteRecord->notas) }}</textarea>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Actualizar</button>
                    <a href="{{ route('waste.index') }}" class="btn btn-default">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
