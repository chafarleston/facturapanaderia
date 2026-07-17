@extends('layouts.admin')
@section('title', 'Ver Orden de Producción')
@section('page_title', 'Ver Orden de Producción')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Orden #{{ $productionOrder->id }}</h3>
                @php $badge = ['planificado'=>'secondary','en_proceso'=>'primary','completado'=>'success','cancelado'=>'danger'][$productionOrder->estado] ?? 'secondary'; @endphp
                <span class="badge badge-{{ $badge }} ml-2">{{ $productionOrder->estado }}</span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-info"><i class="fas fa-box"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Producto</span>
                                <span class="info-box-number">{{ $productionOrder->product->descripcion ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-primary"><i class="fas fa-calendar"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Fecha Producción</span>
                                <span class="info-box-number">{{ $productionOrder->fecha_produccion }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-success"><i class="fas fa-cubes"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Cant Planificada</span>
                                <span class="info-box-number">{{ number_format($productionOrder->cantidad_planificada, 2) }} {{ $productionOrder->unidad }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-{{ $productionOrder->cantidad_producida ? 'success' : 'warning' }}"><i class="fas fa-check-circle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Cant Producida</span>
                                <span class="info-box-number">{{ number_format($productionOrder->cantidad_producida ?? 0, 2) }} {{ $productionOrder->unidad }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                @if($productionOrder->recipe)
                <h5 class="mt-3">Receta</h5>
                <p>{{ $productionOrder->recipe->nombre }}</p>
                @endif

                @if($productionOrder->notas)
                <h5 class="mt-3">Notas</h5>
                <p>{{ $productionOrder->notas }}</p>
                @endif
            </div>
            <div class="card-footer">
                @if($productionOrder->estado == 'planificado')
                <form action="{{ route('production-orders.start', $productionOrder) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary"><i class="fas fa-play"></i> Iniciar</button>
                </form>
                @endif
                @if($productionOrder->estado == 'en_proceso')
                <form action="{{ route('production-orders.complete', $productionOrder) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Completar</button>
                </form>
                @endif
                @if(in_array($productionOrder->estado, ['planificado', 'en_proceso']))
                <form action="{{ route('production-orders.cancel', $productionOrder) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger" onclick="return confirm('¿Cancelar esta orden?')"><i class="fas fa-times"></i> Cancelar</button>
                </form>
                @endif
                <a href="{{ route('production-orders.edit', $productionOrder) }}" class="btn btn-warning"><i class="fas fa-edit"></i> Editar</a>
                <a href="{{ route('production-orders.index') }}" class="btn btn-secondary">Volver</a>
            </div>
        </div>
    </div>
</div>
@endsection
