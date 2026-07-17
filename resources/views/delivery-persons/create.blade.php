@extends('layouts.admin')
@section('title', 'Nuevo Repartidor')
@section('page_title', 'Nuevo Repartidor')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Formulario</h3></div>
            <form method="POST" action="{{ route('delivery-persons.store') }}">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label>Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control" required placeholder="Ej: Juan Pérez">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Teléfono</label>
                                <input type="text" name="telefono" class="form-control" placeholder="999888777">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Vehículo</label>
                                <input type="text" name="vehiculo" class="form-control" placeholder="Ej: Motocicleta, Bicicleta">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="icheck-primary d-inline">
                            <input type="checkbox" name="activo" id="activo" value="1" checked>
                            <label for="activo">Repartidor Activo</label>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
                    <a href="{{ route('delivery-persons.index') }}" class="btn btn-default">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
