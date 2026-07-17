@extends('layouts.admin')
@section('title', 'Ver Pedido Programado')
@section('page_title', 'Ver Pedido Programado')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Pedido {{ $scheduledOrder->order_number ?? '#'.$scheduledOrder->id }}</h3>
                @php $badge = ['pendiente'=>'warning','confirmado'=>'info','en_produccion'=>'primary','listo'=>'success','entregado'=>'success','cancelado'=>'danger'][$scheduledOrder->estado] ?? 'secondary'; @endphp
                <span class="badge badge-{{ $badge }} ml-2">{{ $scheduledOrder->estado }}</span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-info"><i class="fas fa-user"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Cliente</span>
                                <span class="info-box-number">{{ $scheduledOrder->customer->nombre ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-primary"><i class="fas fa-calendar"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Fecha Entrega</span>
                                <span class="info-box-number">{{ $scheduledOrder->fecha_entrega }} {{ $scheduledOrder->hora_entrega ? ' - '.$scheduledOrder->hora_entrega : '' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-success"><i class="fas fa-dollar-sign"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total / Saldo</span>
                                <span class="info-box-number">S/ {{ number_format($scheduledOrder->total ?? 0, 2) }} / S/ {{ number_format(($scheduledOrder->total ?? 0) - ($scheduledOrder->anticipo ?? 0), 2) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-{{ ($scheduledOrder->anticipo ?? 0) > 0 ? 'success' : 'warning' }}"><i class="fas fa-hand-holding-usd"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Anticipo</span>
                                <span class="info-box-number">S/ {{ number_format($scheduledOrder->anticipo ?? 0, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                @if($scheduledOrder->descripcion)
                <div class="mt-3">
                    <h5>Descripción</h5>
                    <p>{{ $scheduledOrder->descripcion }}</p>
                </div>
                @endif

                @if($scheduledOrder->notas)
                <div class="mt-3">
                    <h5>Notas</h5>
                    <p>{{ $scheduledOrder->notas }}</p>
                </div>
                @endif

                <hr>
                <h5><i class="fas fa-list-ul"></i> Items</h5>
                @if(isset($scheduledOrder->items) && $scheduledOrder->items->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="thead-light">
                            <tr>
                                <th>Producto</th>
                                <th>Descripción</th>
                                <th>Cantidad</th>
                                <th>Precio Unit.</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($scheduledOrder->items as $item)
                            <tr>
                                <td>{{ $item->product->descripcion ?? 'N/A' }}</td>
                                <td>{{ $item->descripcion_personalizada }}</td>
                                <td>{{ number_format($item->cantidad, 2) }}</td>
                                <td>S/ {{ number_format($item->precio_unitario ?? 0, 2) }}</td>
                                <td>S/ {{ number_format(($item->precio_unitario ?? 0) * $item->cantidad, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted">No hay items registrados.</p>
                @endif
            </div>
            <div class="card-footer">
                @if($scheduledOrder->estado == 'pendiente')
                <form action="{{ route('scheduled-orders.confirm', $scheduledOrder) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-info"><i class="fas fa-check"></i> Confirmar</button>
                </form>
                @endif
                @if($scheduledOrder->estado == 'confirmado')
                <form action="{{ route('scheduled-orders.startProduction', $scheduledOrder) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary"><i class="fas fa-industry"></i> Iniciar Producción</button>
                </form>
                @endif
                @if($scheduledOrder->estado == 'en_produccion')
                <form action="{{ route('scheduled-orders.markReady', $scheduledOrder) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success"><i class="fas fa-check-circle"></i> Marcar Listo</button>
                </form>
                @endif
                @if($scheduledOrder->estado == 'listo')
                <form action="{{ route('scheduled-orders.deliver', $scheduledOrder) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success"><i class="fas fa-truck"></i> Entregar</button>
                </form>
                @endif
                @if(!in_array($scheduledOrder->estado, ['entregado', 'cancelado']))
                <form action="{{ route('scheduled-orders.cancel', $scheduledOrder) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger" onclick="return confirm('¿Cancelar este pedido?')"><i class="fas fa-times"></i> Cancelar</button>
                </form>
                @endif
                <a href="{{ route('scheduled-orders.printComanda', $scheduledOrder) }}" class="btn btn-secondary"><i class="fas fa-print"></i> Imprimir Comanda</a>
                <a href="{{ route('scheduled-orders.edit', $scheduledOrder) }}" class="btn btn-warning"><i class="fas fa-edit"></i> Editar</a>
                <a href="{{ route('scheduled-orders.index') }}" class="btn btn-secondary">Volver</a>
            </div>
        </div>
    </div>
</div>
@endsection
