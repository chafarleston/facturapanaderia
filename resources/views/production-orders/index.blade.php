@extends('layouts.admin')
@section('title', 'Órdenes de Producción')
@section('page_title', 'Órdenes de Producción')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lista de Órdenes</h3>
                <div class="card-tools">
                    <a href="{{ route('production-orders.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Nueva Orden
                    </a>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Receta / Producto</th>
                            <th>Fecha</th>
                            <th>Cant Planificada</th>
                            <th>Cant Producida</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($productionOrders as $order)
                        <tr>
                            <td>{{ $order->id }}</td>
                            <td>
                                @if($order->recipe)
                                    {{ $order->recipe->nombre }}
                                @elseif($order->product)
                                    {{ $order->product->descripcion }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ $order->fecha_produccion }}</td>
                            <td>{{ number_format($order->cantidad_planificada, 2) }} {{ $order->unidad }}</td>
                            <td>{{ number_format($order->cantidad_producida ?? 0, 2) }} {{ $order->unidad }}</td>
                            <td>
                                @php $badge = ['planificado'=>'secondary','en_proceso'=>'primary','completado'=>'success','cancelado'=>'danger'][$order->estado] ?? 'secondary'; @endphp
                                <span class="badge badge-{{ $badge }}">{{ $order->estado }}</span>
                            </td>
                            <td>
                                <a href="{{ route('production-orders.show', $order) }}" class="btn btn-info btn-xs" title="Ver"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('production-orders.edit', $order) }}" class="btn btn-warning btn-xs" title="Editar"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('production-orders.destroy', $order) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-xs" title="Eliminar" onclick="return confirm('¿Eliminar esta orden?')"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center">No hay órdenes de producción</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($productionOrders instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
            <div class="card-footer">{{ $productionOrders->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
