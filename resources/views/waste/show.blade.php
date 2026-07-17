@extends('layouts.admin')
@section('title', 'Ver Merma')
@section('page_title', 'Ver Merma')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Registro de Merma</h3></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-info"><i class="fas fa-box"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Producto</span>
                                <span class="info-box-number">{{ $waste->product->descripcion ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-primary"><i class="fas fa-calendar"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Fecha</span>
                                <span class="info-box-number">{{ $waste->fecha }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-danger"><i class="fas fa-trash"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Cantidad</span>
                                <span class="info-box-number">{{ number_format($waste->cantidad, 2) }} {{ $waste->unidad }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning"><i class="fas fa-dollar-sign"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Costo Pérdida</span>
                                <span class="info-box-number">S/ {{ number_format($waste->costo_perdida ?? 0, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <h5>Motivo</h5>
                        <p><span class="badge badge-warning">{{ $waste->motivo }}</span></p>
                    </div>
                    <div class="col-md-6">
                        <h5>Registrado por</h5>
                        <p>{{ $waste->user->name ?? 'N/A' }}</p>
                    </div>
                </div>
                @if($waste->notas)
                <div class="mt-3">
                    <h5>Notas</h5>
                    <p>{{ $waste->notas }}</p>
                </div>
                @endif
            </div>
            <div class="card-footer">
                <a href="{{ route('waste.edit', $waste) }}" class="btn btn-warning"><i class="fas fa-edit"></i> Editar</a>
                <a href="{{ route('waste.index') }}" class="btn btn-secondary">Volver</a>
            </div>
        </div>
    </div>
</div>
@endsection
