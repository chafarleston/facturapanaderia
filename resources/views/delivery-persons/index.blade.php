@extends('layouts.admin')
@section('title', 'Repartidores')
@section('page_title', 'Repartidores')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lista de Repartidores</h3>
                <div class="card-tools">
                    <a href="{{ route('delivery-persons.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Nuevo Repartidor
                    </a>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th>Vehículo</th>
                            <th>Activo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($persons as $person)
                        <tr>
                            <td>{{ $person->nombre }}</td>
                            <td>{{ $person->telefono }}</td>
                            <td>{{ $person->vehiculo }}</td>
                            <td>
                                <span class="badge badge-{{ $person->activo ? 'success' : 'danger' }}">
                                    {{ $person->activo ? 'Sí' : 'No' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('delivery-persons.edit', $person) }}" class="btn btn-warning btn-xs" title="Editar"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('delivery-persons.destroy', $person) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-xs" title="Eliminar" onclick="return confirm('¿Eliminar este repartidor?')"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center">No hay repartidores registrados</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($persons instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
            <div class="card-footer">{{ $persons->links('pagination::bootstrap-4') }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
