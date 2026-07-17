@extends('layouts.admin')
@section('title', 'Recetas')
@section('page_title', 'Recetas')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lista de Recetas</h3>
                <div class="card-tools">
                    <a href="{{ route('recipes.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Nueva Receta
                    </a>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Producto Resultante</th>
                            <th>Cant Producida</th>
                            <th>Costo Estimado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recipes as $recipe)
                        <tr>
                            <td>{{ $recipe->nombre }}</td>
                            <td>{{ $recipe->resultProduct->descripcion ?? 'N/A' }}</td>
                            <td>{{ number_format($recipe->cantidad_producida, 2) }} {{ $recipe->unidad }}</td>
                            <td>S/ {{ number_format($recipe->costo_estimado ?? 0, 2) }}</td>
                            <td>
                                <a href="{{ route('recipes.show', $recipe) }}" class="btn btn-info btn-xs" title="Ver"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('recipes.edit', $recipe) }}" class="btn btn-warning btn-xs" title="Editar"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('recipes.destroy', $recipe) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-xs" title="Eliminar" onclick="return confirm('¿Eliminar esta receta?')"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center">No hay recetas registradas</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($recipes instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
            <div class="card-footer">{{ $recipes->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
