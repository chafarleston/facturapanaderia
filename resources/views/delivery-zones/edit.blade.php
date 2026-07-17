@extends('layouts.admin')
@section('title', 'Editar Zona de Reparto')
@section('page_title', 'Editar Zona de Reparto')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Editar Zona: {{ $zone->nombre }}</h3></div>
            <form method="POST" action="{{ route('delivery-zones.update', $zone) }}">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group">
                        <label>Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control" required value="{{ old('nombre', $zone->nombre) }}">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Precio Envío (S/) <span class="text-danger">*</span></label>
                                <input type="number" name="precio_envio" class="form-control" step="0.01" min="0" required value="{{ old('precio_envio', $zone->precio_envio) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tiempo Estimado (min)</label>
                                <input type="number" name="tiempo_estimado_min" class="form-control" min="0" value="{{ old('tiempo_estimado_min', $zone->tiempo_estimado_min) }}">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="icheck-primary d-inline">
                            <input type="checkbox" name="activa" id="activa" value="1" {{ old('activa', $zone->activa) ? 'checked' : '' }}>
                            <label for="activa">Zona Activa</label>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Actualizar</button>
                    <a href="{{ route('delivery-zones.index') }}" class="btn btn-default">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
