@extends('layouts.admin')
@section('title', 'Nuevo Reparto')
@section('page_title', 'Nuevo Reparto')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Formulario de Reparto</h3></div>
            <form method="POST" action="{{ route('deliveries.store') }}">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Factura <span class="text-danger">*</span></label>
                                <select name="invoice_id" class="form-control" required>
                                    <option value="">Seleccionar factura...</option>
                                    @foreach($invoices as $invoice)
                                    <option value="{{ $invoice->id }}">{{ $invoice->numero ?? '#'.$invoice->id }} - {{ $invoice->customer->nombre ?? 'N/A' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Zona <span class="text-danger">*</span></label>
                                <select name="delivery_zone_id" class="form-control" required>
                                    <option value="">Seleccionar zona...</option>
                                    @foreach($zones as $zone)
                                    <option value="{{ $zone->id }}">{{ $zone->nombre }} (S/ {{ number_format($zone->precio_envio, 2) }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Repartidor</label>
                                <select name="delivery_person_id" class="form-control">
                                    <option value="">Seleccionar repartidor...</option>
                                    @foreach($persons as $person)
                                    <option value="{{ $person->id }}">{{ $person->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Teléfono Contacto</label>
                                <input type="text" name="telefono_contacto" class="form-control" placeholder="999888777">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Dirección <span class="text-danger">*</span></label>
                        <input type="text" name="direccion" class="form-control" required placeholder="Av. Principal 123">
                    </div>
                    <div class="form-group">
                        <label>Referencia</label>
                        <input type="text" name="referencia" class="form-control" placeholder="Cerca al parque...">
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Costo Envío (S/)</label>
                                <input type="number" name="costo_envio" class="form-control" step="0.01" min="0" value="0">
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
                    <a href="{{ route('deliveries.index') }}" class="btn btn-default">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
