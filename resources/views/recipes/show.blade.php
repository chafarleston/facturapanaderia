@extends('layouts.admin')
@section('title', 'Ver Receta')
@section('page_title', 'Ver Receta')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Receta: {{ $recipe->nombre }}</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-info"><i class="fas fa-box"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Producto Resultante</span>
                                <span class="info-box-number">{{ $recipe->resultProduct->descripcion ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-success"><i class="fas fa-cubes"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Cantidad Producida</span>
                                <span class="info-box-number">{{ number_format($recipe->cantidad_producida, 2) }} {{ $recipe->unidad }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning"><i class="fas fa-dollar-sign"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Costo Estimado</span>
                                <span class="info-box-number">S/ {{ number_format($recipe->costo_estimado ?? 0, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-secondary"><i class="fas fa-clock"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Tiempo Preparación</span>
                                <span class="info-box-number">{{ $recipe->tiempo_preparacion_min ?? 0 }} min</span>
                            </div>
                        </div>
                    </div>
                </div>

                @if($recipe->descripcion)
                <div class="mt-3">
                    <h5>Descripción</h5>
                    <p>{{ $recipe->descripcion }}</p>
                </div>
                @endif

                @if($recipe->instrucciones)
                <div class="mt-3">
                    <h5>Instrucciones</h5>
                    <p>{{ $recipe->instrucciones }}</p>
                </div>
                @endif

                <hr>
                <h5><i class="fas fa-list-ul"></i> Ingredientes</h5>
                @if(isset($recipe->ingredients) && $recipe->ingredients->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="thead-light">
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Unidad</th>
                                <th>Merma %</th>
                                <th>Costo Unit.</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recipe->ingredients as $ingredient)
                            <tr>
                                <td>{{ $ingredient->product->descripcion ?? 'N/A' }}</td>
                                <td>{{ number_format($ingredient->cantidad, 2) }}</td>
                                <td>{{ $ingredient->unidad }}</td>
                                <td>{{ number_format($ingredient->merma_porcentaje ?? 0, 1) }}%</td>
                                <td>S/ {{ number_format($ingredient->costo_unitario ?? 0, 2) }}</td>
                                <td>S/ {{ number_format(($ingredient->costo_unitario ?? 0) * $ingredient->cantidad, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted">No hay ingredientes registrados.</p>
                @endif
            </div>
            <div class="card-footer">
                <a href="{{ route('recipes.edit', $recipe) }}" class="btn btn-warning"><i class="fas fa-edit"></i> Editar</a>
                <a href="{{ route('recipes.index') }}" class="btn btn-secondary">Volver</a>
            </div>
        </div>
    </div>
</div>
@endsection
