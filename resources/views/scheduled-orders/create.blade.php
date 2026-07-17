@extends('layouts.admin')
@section('title', 'Nuevo Pedido Programado')
@section('page_title', 'Nuevo Pedido Programado')

@push('styles')
<style>
    .item-row { margin-bottom: 10px; padding: 10px; background: #f8f9fa; border-radius: 5px; }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Formulario de Pedido</h3></div>
            <form method="POST" action="{{ route('scheduled-orders.store') }}" id="orderForm">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Cliente <span class="text-danger">*</span></label>
                                <select name="customer_id" class="form-control" required>
                                    <option value="">Seleccionar cliente...</option>
                                    @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Fecha Pedido <span class="text-danger">*</span></label>
                                <input type="date" name="fecha_pedido" class="form-control" required value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Teléfono Contacto</label>
                                <input type="text" name="telefono_contacto" class="form-control" placeholder="999888777">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Fecha Entrega <span class="text-danger">*</span></label>
                                <input type="date" name="fecha_entrega" class="form-control" required value="{{ date('Y-m-d', strtotime('+1 day')) }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Hora Entrega</label>
                                <input type="time" name="hora_entrega" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Anticipo (S/)</label>
                                <input type="number" name="anticipo" id="anticipo" class="form-control" step="0.01" min="0" value="0">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div class="icheck-primary d-block mt-2">
                                    <input type="checkbox" name="para_delivery" id="para_delivery" value="1">
                                    <label for="para_delivery">Para Delivery</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="2" placeholder="Descripción del pedido..."></textarea>
                    </div>
                    <div class="form-group">
                        <label>Notas</label>
                        <textarea name="notas" class="form-control" rows="2" placeholder="Notas internas..."></textarea>
                    </div>
                </div>

                <div class="card card-secondary m-3">
                    <div class="card-header">
                        <h3 class="card-title">Items del Pedido</h3>
                        <button type="button" class="btn btn-success btn-sm float-right" onclick="addItemRow()">
                            <i class="fas fa-plus"></i> Agregar Item
                        </button>
                    </div>
                    <div class="card-body" id="itemsContainer">
                        <p class="text-muted text-center" id="noItems">Sin items. Haga clic en "Agregar Item".</p>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-8 text-right">
                                <strong>Total:</strong>
                            </div>
                            <div class="col-md-4">
                                <span id="totalDisplay">S/ 0.00</span>
                                <input type="hidden" name="total_calculado" id="totalCalculado" value="0">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
                    <a href="{{ route('scheduled-orders.index') }}" class="btn btn-default">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let itemIndex = 0;

function addItemRow(data = null) {
    document.getElementById('noItems').style.display = 'none';
    const idx = itemIndex++;
    const html = `
        <div class="item-row" id="item-row-${idx}">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group mb-0">
                        <label>Producto <span class="text-danger">*</span></label>
                        <select name="items[${idx}][product_id]" class="form-control item-product" required onchange="updateTotal()">
                            <option value="">Seleccionar...</option>
                            @foreach($products as $product)
                            <option value="{{ $product->id }}" data-precio="{{ $product->precio }}" ${data && data.product_id == {{ $product->id }} ? 'selected' : ''}>{{ $product->descripcion }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mb-0">
                        <label>Descripción Personalizada</label>
                        <input type="text" name="items[${idx}][descripcion_personalizada]" class="form-control" value="${data ? data.descripcion_personalizada || '' : ''}">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <label>Cantidad <span class="text-danger">*</span></label>
                        <input type="number" name="items[${idx}][cantidad]" class="form-control item-cantidad" step="1" min="1" required value="${data ? data.cantidad : '1'}" onchange="updateTotal()">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <label>Precio Unit. <span class="text-danger">*</span></label>
                        <input type="number" name="items[${idx}][precio_unitario]" class="form-control item-precio" step="0.01" min="0" required value="${data ? data.precio_unitario : ''}" onchange="updateTotal()">
                    </div>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-sm" onclick="document.getElementById('item-row-${idx}').remove(); updateTotal(); if(!document.querySelectorAll('.item-row').length) document.getElementById('noItems').style.display='';">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>`;
    document.getElementById('itemsContainer').insertAdjacentHTML('beforeend', html);

    // If we have product data, set the precio from the product
    if (data && data.product_id) {
        const sel = document.querySelector(`#item-row-${idx} .item-product`);
        if (sel) {
            const opt = sel.options[sel.selectedIndex];
            if (opt && opt.dataset.precio && !data.precio_unitario) {
                document.querySelector(`#item-row-${idx} .item-precio`).value = opt.dataset.precio;
            }
        }
    }

    updateTotal();
}

function updateTotal() {
    let total = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const cantidad = parseFloat(row.querySelector('.item-cantidad')?.value) || 0;
        const precio = parseFloat(row.querySelector('.item-precio')?.value) || 0;
        total += cantidad * precio;
    });
    document.getElementById('totalDisplay').innerText = 'S/ ' + total.toFixed(2);
    document.getElementById('totalCalculado').value = total.toFixed(2);
}

// Auto-fill precio when product is selected
document.getElementById('itemsContainer').addEventListener('change', function(e) {
    if (e.target.classList.contains('item-product')) {
        const opt = e.target.options[e.target.selectedIndex];
        if (opt && opt.dataset.precio) {
            const row = e.target.closest('.item-row');
            row.querySelector('.item-precio').value = opt.dataset.precio;
        }
        updateTotal();
    }
});
</script>
@endsection
