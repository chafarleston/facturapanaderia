@extends('layouts.admin')
@section('title', 'Pedidos Programados')
@section('page_title', 'Pedidos Programados')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lista de Pedidos Programados</h3>
                <div class="card-tools">
                    <a href="{{ route('scheduled-orders.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Nuevo Pedido
                    </a>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Número</th>
                            <th>Cliente</th>
                            <th>Fecha Pedido</th>
                            <th>Fecha Entrega</th>
                            <th>Total</th>
                            <th>Saldo</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td>{{ $order->numero ?? '#'.$order->id }}</td>
                            <td>{{ $order->customer->nombre ?? 'N/A' }}</td>
                            <td>{{ $order->fecha_pedido }}</td>
                            <td>{{ $order->fecha_entrega }}</td>
                            <td>S/ {{ number_format($order->total ?? 0, 2) }}</td>
                            <td>S/ {{ number_format(($order->total ?? 0) - ($order->anticipo ?? 0), 2) }}</td>
                            <td>
                                @php $badge = ['pendiente'=>'warning','confirmado'=>'info','en_produccion'=>'primary','listo'=>'success','entregado'=>'success','cancelado'=>'danger'][$order->estado] ?? 'secondary'; @endphp
                                <span class="badge badge-{{ $badge }}">{{ $order->estado }}</span>
                            </td>
                            <td>
                                <a href="{{ route('scheduled-orders.show', $order) }}" class="btn btn-info btn-xs" title="Ver"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('scheduled-orders.edit', $order) }}" class="btn btn-warning btn-xs" title="Editar"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('scheduled-orders.destroy', $order) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-xs" title="Eliminar" onclick="return confirm('¿Eliminar este pedido?')"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center">No hay pedidos programados</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($orders instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
            <div class="card-footer">{{ $orders->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
