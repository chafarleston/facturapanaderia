@extends('layouts.admin')
@section('title', 'Zonas de Reparto')
@section('page_title', 'Zonas de Reparto')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lista de Zonas</h3>
                <div class="card-tools">
                    <a href="{{ route('delivery-zones.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Nueva Zona
                    </a>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Precio Envío</th>
                            <th>Tiempo Est.</th>
                            <th>Activa</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($zones as $zone)
                        <tr>
                            <td>{{ $zone->nombre }}</td>
                            <td>S/ {{ number_format($zone->precio_envio, 2) }}</td>
                            <td>{{ $zone->tiempo_estimado_min ?? 0 }} min</td>
                            <td>
                                <span class="badge badge-{{ $zone->activa ? 'success' : 'danger' }}">
                                    {{ $zone->activa ? 'Sí' : 'No' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('delivery-zones.edit', $zone) }}" class="btn btn-warning btn-xs" title="Editar"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('delivery-zones.destroy', $zone) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-xs" title="Eliminar" onclick="return confirm('¿Eliminar esta zona?')"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center">No hay zonas registradas</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($zones instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
            <div class="card-footer">{{ $zones->links('pagination::bootstrap-4') }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
