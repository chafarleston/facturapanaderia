@extends('layouts.admin')
@section('title', 'Ver Reparto')
@section('page_title', 'Ver Reparto')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Reparto #{{ $delivery->id }}</h3>
                @php $badge = ['pendiente'=>'warning','asignado'=>'info','en_ruta'=>'primary','entregado'=>'success','cancelado'=>'danger'][$delivery->estado] ?? 'secondary'; @endphp
                <span class="badge badge-{{ $badge }} ml-2">{{ $delivery->estado }}</span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-info"><i class="fas fa-file-invoice"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Factura</span>
                                <span class="info-box-number">{{ $delivery->invoice->numero ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-primary"><i class="fas fa-map-marker-alt"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Zona</span>
                                <span class="info-box-number">{{ $delivery->zone->nombre ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-success"><i class="fas fa-motorcycle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Repartidor</span>
                                <span class="info-box-number">{{ $delivery->person->nombre ?? 'No asignado' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning"><i class="fas fa-dollar-sign"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Costo Envío</span>
                                <span class="info-box-number">S/ {{ number_format($delivery->costo_envio ?? 0, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <h5>Dirección</h5>
                        <p>{{ $delivery->direccion }}</p>
                    </div>
                    <div class="col-md-6">
                        <h5>Teléfono Contacto</h5>
                        <p>{{ $delivery->telefono_contacto ?? 'N/A' }}</p>
                    </div>
                </div>

                @if($delivery->referencia)
                <div class="mt-3">
                    <h5>Referencia</h5>
                    <p>{{ $delivery->referencia }}</p>
                </div>
                @endif

                @if($delivery->notas)
                <div class="mt-3">
                    <h5>Notas</h5>
                    <p>{{ $delivery->notas }}</p>
                </div>
                @endif
            </div>
            <div class="card-footer">
                @if($delivery->estado == 'pendiente')
                <form action="{{ route('deliveries.assign', $delivery) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-info"><i class="fas fa-user-check"></i> Asignar</button>
                </form>
                @endif
                @if($delivery->estado == 'asignado')
                <form action="{{ route('deliveries.start', $delivery) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary"><i class="fas fa-play"></i> Iniciar Ruta</button>
                </form>
                @endif
                @if($delivery->estado == 'en_ruta')
                <form action="{{ route('deliveries.complete', $delivery) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Completar</button>
                </form>
                @endif
                @if(!in_array($delivery->estado, ['entregado', 'cancelado']))
                <form action="{{ route('deliveries.cancel', $delivery) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger" onclick="return confirm('¿Cancelar este reparto?')"><i class="fas fa-times"></i> Cancelar</button>
                </form>
                @endif
                <a href="{{ route('deliveries.edit', $delivery) }}" class="btn btn-warning"><i class="fas fa-edit"></i> Editar</a>
                <a href="{{ route('deliveries.index') }}" class="btn btn-secondary">Volver</a>
            </div>
        </div>
    </div>
</div>
@endsection
