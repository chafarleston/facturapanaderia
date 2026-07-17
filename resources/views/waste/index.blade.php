@extends('layouts.admin')
@section('title', 'Mermas')
@section('page_title', 'Mermas')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lista de Mermas</h3>
                <div class="card-tools">
                    <a href="{{ route('waste.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Nueva Merma
                    </a>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Motivo</th>
                            <th>Costo Pérdida</th>
                            <th>Usuario</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalPerdida = 0; @endphp
                        @forelse($wasteRecords as $waste)
                        @php $totalPerdida += ($waste->costo_perdida ?? 0); @endphp
                        <tr>
                            <td>{{ $waste->fecha }}</td>
                            <td>{{ $waste->product->descripcion ?? 'N/A' }}</td>
                            <td>{{ number_format($waste->cantidad, 2) }} {{ $waste->unidad }}</td>
                            <td><span class="badge badge-warning">{{ $waste->motivo }}</span></td>
                            <td>S/ {{ number_format($waste->costo_perdida ?? 0, 2) }}</td>
                            <td>{{ $waste->user->name ?? 'N/A' }}</td>
                            <td>
                                <a href="{{ route('waste.show', $waste) }}" class="btn btn-info btn-xs" title="Ver"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('waste.edit', $waste) }}" class="btn btn-warning btn-xs" title="Editar"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('waste.destroy', $waste) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-xs" title="Eliminar" onclick="return confirm('¿Eliminar este registro?')"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center">No hay registros de merma</td></tr>
                        @endforelse
                    </tbody>
                    @if($wasteRecords->count() > 0)
                    <tfoot>
                        <tr class="bg-light font-weight-bold">
                            <td colspan="4" class="text-right">Total Pérdida:</td>
                            <td><strong>S/ {{ number_format($totalPerdida, 2) }}</strong></td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
            @if($wasteRecords instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
            <div class="card-footer">{{ $wasteRecords->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
