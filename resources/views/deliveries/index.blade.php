@extends('layouts.admin')
@section('title', 'Repartos')
@section('page_title', 'Repartos')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lista de Repartos</h3>
                <div class="card-tools">
                    <a href="{{ route('deliveries.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Nuevo Reparto
                    </a>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Factura</th>
                            <th>Zona</th>
                            <th>Repartidor</th>
                            <th>Dirección</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($deliveries as $delivery)
                        <tr>
                            <td>{{ $delivery->id }}</td>
                            <td>{{ $delivery->invoice->numero ?? 'N/A' }}</td>
                            <td>{{ $delivery->zone->nombre ?? 'N/A' }}</td>
                            <td>{{ $delivery->person->nombre ?? 'N/A' }}</td>
                            <td>{{ $delivery->direccion }}</td>
                            <td>
                                @php $badge = ['pendiente'=>'warning','asignado'=>'info','en_ruta'=>'primary','entregado'=>'success','cancelado'=>'danger'][$delivery->estado] ?? 'secondary'; @endphp
                                <span class="badge badge-{{ $badge }}">{{ $delivery->estado }}</span>
                            </td>
                            <td>
                                <a href="{{ route('deliveries.show', $delivery) }}" class="btn btn-info btn-xs" title="Ver"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('deliveries.edit', $delivery) }}" class="btn btn-warning btn-xs" title="Editar"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('deliveries.destroy', $delivery) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-xs" title="Eliminar" onclick="return confirm('¿Eliminar este reparto?')"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center">No hay repartos registrados</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($deliveries instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
            <div class="card-footer">{{ $deliveries->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
