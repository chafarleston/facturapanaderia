@extends('layouts.admin')
@section('title', 'Punto de Venta')
@section('page_title', 'Punto de Venta')

@push('styles')
<style>
    body { overflow: hidden; }
    .main-footer, .content-header { display: none !important; }
    .content-wrapper { padding-top: 0 !important; }
    
    .pos-container {
        display: flex;
        height: calc(100vh - 60px);
        width: 100%;
        gap: 10px;
        padding: 10px;
        box-sizing: border-box;
    }
    
    /* Columna 1: 65% Categorías/Productos */
    .categories-section {
        width: 65%;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    
    .categories-header {
        padding: 15px;
        border-bottom: 2px solid #eee;
        flex-shrink: 0;
    }
    
    .categories-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 15px;
        padding: 15px;
        overflow-y: auto;
        height: 100%;
    }
    
    .category-card {
        background: #fff;
        border-radius: 15px;
        padding: 25px 15px;
        cursor: pointer;
        transition: all 0.3s;
        text-align: center;
        border: 3px solid transparent;
        min-height: 100px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    .category-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }
    .category-card i {
        font-size: 32px;
        margin-bottom: 10px;
    }
    .category-card h5 {
        margin: 0;
        font-size: 13px;
        font-weight: bold;
    }
    .category-card small {
        font-size: 10px;
        color: #666;
        margin-top: 5px;
    }
    
    /* Productos */
    .products-section {
        display: none;
        flex-direction: column;
        height: 100%;
    }
    
    .products-header {
        padding: 10px 15px;
        background: #f8f9fa;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .products-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        padding: 15px 15px 30px 15px;
        overflow-y: auto;
        flex: 1;
        align-content: start;
        min-height: 0;
    }
    
    .product-card {
        background: #fff;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 12px;
        cursor: pointer;
        transition: all 0.2s;
        text-align: center;
        min-height: 85px;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
    }
    .product-card:hover {
        border-color: #007bff;
        transform: scale(1.03);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .product-name {
        font-size: 11px;
        font-weight: bold;
        margin-bottom: 6px;
        height: 32px;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        line-height: 1.2;
    }
    .product-price {
        font-size: 14px;
        color: #28a745;
        font-weight: bold;
    }
    .product-stock {
        font-size: 10px;
        color: #666;
        margin-top: 4px;
    }
    
    /* Espaciador para la última fila */
    .products-grid::after {
        content: '';
        height: 20px;
    }
    
    /* Columna 2: 35% Venta */
    .sale-section {
        width: 35%;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    
    .sale-items-panel {
        flex: 1.5;
        background: #fff;
        border-radius: 10px;
        padding: 12px;
        display: flex;
        flex-direction: column;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        overflow: hidden;
        min-height: 250px;
    }
    
    .sale-data-panel {
        flex: 0;
        background: #fff;
        border-radius: 10px;
        padding: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        min-height: 280px;
        max-height: 350px;
        overflow-y: auto;
    }
    
    .panel-title {
        font-weight: bold;
        font-size: 15px;
        margin-bottom: 10px;
        padding-bottom: 8px;
        border-bottom: 2px solid #eee;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .sale-items-list {
        flex: 1;
        overflow-y: auto;
    }
    
    .sale-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 6px;
    }
    .sale-item-info { flex: 1; }
    .sale-item-name { font-weight: bold; font-size: 13px; }
    .sale-item-price { font-size: 12px; color: #666; }
    .sale-item-actions { display: flex; align-items: center; gap: 6px; }
    .qty-btn {
        width: 26px;
        height: 26px;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        font-weight: bold;
        font-size: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .qty-minus { background: #dc3545; color: white; }
    .qty-plus { background: #28a745; color: white; }
    .sale-item-qty { font-weight: bold; min-width: 25px; text-align: center; font-size: 13px; }
    .remove-item { color: #dc3545; cursor: pointer; margin-left: 5px; }
    .remove-item:hover { color: #c82333; }
    
    .empty-sale { text-align: center; color: #999; padding: 30px; }
    .empty-sale i { font-size: 40px; margin-bottom: 10px; }
    
    .customer-dropdown {
        position: absolute;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        max-height: 250px;
        overflow-y: auto;
        z-index: 1000;
        width: calc(100% - 30px);
    }
    .customer-option {
        padding: 10px 12px;
        cursor: pointer;
        border-bottom: 1px solid #eee;
    }
    .customer-option:hover { background: #f8f9fa; }
    .customer-option:last-child { border-bottom: none; }
    .customer-option-name { font-weight: bold; font-size: 13px; }
    .customer-option-doc { font-size: 11px; color: #666; }
    
    .sale-totals {
        border-top: 2px solid #007bff;
        padding-top: 10px;
        margin: 12px 0;
    }
    .sale-total-row { 
        display: flex; 
        justify-content: space-between; 
        padding: 4px 0; 
        font-size: 13px; 
    }
    .sale-total-row.grand-total {
        font-size: 20px;
        font-weight: bold;
        color: #007bff;
        border-top: 2px solid #ddd;
        padding-top: 10px;
        margin-top: 8px;
    }
    
    .btn-pay {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
        border: none;
        padding: 15px;
        font-size: 18px;
        font-weight: bold;
        border-radius: 10px;
        cursor: pointer;
        width: 100%;
        transition: all 0.3s;
    }
    .btn-pay:hover {
        transform: scale(1.02);
        box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
    }
    .btn-pay:disabled { background: #ccc; cursor: not-allowed; transform: none; box-shadow: none; }
    
    .btn-cancel {
        background: #dc3545;
        color: white;
        border: none;
        padding: 10px;
        font-size: 13px;
        border-radius: 8px;
        cursor: pointer;
        flex: 0 0 auto;
    }

    /* Tabs multi-venta */
    .sale-tabs { display: flex; background: #f0f0f0; border-bottom: 1px solid #ddd; flex-shrink: 0; overflow-x: auto; min-height: 38px; align-items: flex-end; }
    .sale-tab { padding: 6px 12px; cursor: pointer; border-radius: 6px 6px 0 0; font-size: 12px; white-space: nowrap; display: flex; align-items: center; gap: 4px; border: 1px solid transparent; border-bottom: none; color: #666; margin: 0 1px; background: #e0e0e0; max-width: 180px; }
    .sale-tab:hover { background: #d5d5d5; }
    .sale-tab.active { background: #fff; color: #007bff; font-weight: bold; border-color: #ddd; }
    .sale-tab .tab-total { font-size: 10px; color: #28a745; }
    .sale-tab .close-tab { font-size: 14px; color: #999; margin-left: 2px; padding: 0 3px; line-height: 1; border-radius: 50%; }
    .sale-tab .close-tab:hover { color: #dc3545; background: #f8d7da; }
    .sale-tab-add { padding: 4px 10px; cursor: pointer; font-size: 16px; font-weight: bold; color: #28a745; border: 1px dashed #ccc; border-radius: 6px 6px 0 0; margin: 0 1px; background: transparent; border-bottom: none; line-height: 1; }
    .sale-tab-add:hover { background: #d4edda; border-color: #28a745; }
</style>
@endpush

@section('content')
<div class="pos-container">
    <div class="categories-section">
        <div class="sale-tabs" id="saleTabs"><div class="sale-tab-add" onclick="addTab()" title="Nueva venta">+</div></div>
        <div class="categories-header">
            <h5 class="panel-title" style="margin:0;"><i class="fas fa-th-large"></i> Seleccionar Categoria</h5>
        </div>

        <div style="padding:8px 12px;">
            <div style="position:relative;">
                <i class="fas fa-search" style="position:absolute; left:10px; top:50%; transform:translateY(-50%); color:#999; font-size:13px;"></i>
                <input type="text" id="posSearch" placeholder="Buscar producto por nombre o código de barras..." oninput="searchPOSProducts(this.value)" style="width:100%; padding:7px 10px 7px 30px; border:1px solid #ddd; border-radius:6px; font-size:13px; outline:none; box-sizing:border-box;">
            </div>
        </div>
        
        <div class="categories-grid" id="categoriesGrid">
            @foreach($categories as $category)
            <div class="category-card" style="border-color: {{ $category->color ?? '#007bff' }};"
                 onclick="showProducts({{ $category->id }}, '{{ $category->nombre }}')">
                <i class="{{ $category->icon ?? 'fas fa-tag' }}" style="color: {{ $category->color ?? '#007bff' }};"></i>
                <h5 style="color: #333;">{{ $category->nombre }}</h5>
                <small>{{ $category->products_count ?? 0 }} productos</small>
            </div>
            @endforeach
        </div>
        
        <div class="products-section" id="productsSection">
            <div class="products-header">
                <button class="btn btn-sm btn-secondary" onclick="backToCategories()">
                    <i class="fas fa-arrow-left"></i> Volver
                </button>
                <span class="ml-3 font-weight-bold" id="categoryTitle"></span>
            </div>
            <div class="products-grid" id="productsGrid"></div>
        </div>
    </div>
    
    <div class="sale-section">
        <div class="sale-items-panel">
            <div class="panel-title"><i class="fas fa-shopping-cart"></i> Productos <span id="cartCount" style="display:none;" class="badge badge-warning ml-1"></span></div>
            <div class="sale-items-list" id="saleItems">
                <div class="empty-sale">
                    <i class="fas fa-shopping-basket"></i>
                    <p>Agrega productos</p>
                </div>
            </div>
        </div>
        
        <div class="sale-data-panel">
            <div style="display: flex; gap: 8px; margin-bottom: 10px;">
                <div style="flex: 1; position: relative;">
                    <label><i class="fas fa-user"></i> Cliente</label>
                    <div style="display: flex; gap: 5px;">
                        <input type="text" id="customerSearch" class="form-control form-control-sm" placeholder="Buscar..." autocomplete="off" style="flex: 1;">
                        <button type="button" class="btn btn-sm btn-success" onclick="openCustomerModal()" title="Nuevo cliente" style="padding: 4px 10px;">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <input type="hidden" id="customerId" value="">
                    <div id="customerDropdown" class="customer-dropdown" style="display: none;"></div>
                </div>
            </div>
            
            <div style="display: flex; gap: 8px; margin-bottom: 10px;">
                <div style="flex: 1;">
                    <label>Tipo Documento</label>
                    <select id="documentType" class="form-control form-control-sm" onchange="updateSerieByType()">
                        <option value="03">BOLETA</option>
                        <option value="01">FACTURA</option>
                        <option value="NV">NOTA DE VENTA</option>
                    </select>
                </div>
                <div style="flex: 1;">
                    <label>Serie</label>
                    <input type="text" id="serieDisplay" class="form-control form-control-sm" readonly disabled>
                </div>
            </div>
            
            <div style="margin-bottom: 10px;">
                <label><i class="fas fa-money-bill"></i> Pagos</label>
                <div id="paymentsContainer">
                    <div class="payment-row" style="display: flex; gap: 5px; margin-bottom: 4px; align-items: center;">
                        <select class="form-control form-control-sm payment-method" style="flex: 1.5;" onchange="updateAllPaymentAmounts()">
                            <option value="EFECTIVO">EFECTIVO</option>
                            <option value="TARJETA">TARJETA</option>
                            <option value="TRANSFERENCIA">TRANSFERENCIA</option>
                            <option value="YAPE">YAPE</option>
                            <option value="PLIN">PLIN</option>
                        </select>
                        <input type="text" class="form-control form-control-sm payment-amount" placeholder="Monto" style="flex: 1;" oninput="updatePaymentTotals()" value="0.00">
                        <input type="text" class="form-control form-control-sm payment-ref" placeholder="Ref" style="flex: 0.8;">
                        <button type="button" class="btn btn-sm btn-danger remove-payment-btn" onclick="removePayment(this)" style="padding: 2px 6px; display: none;" title="Quitar pago">&times;</button>
                    </div>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 4px;">
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addPayment()" style="font-size: 11px; padding: 2px 10px;">
                            <i class="fas fa-plus"></i> Agregar
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="updateAllPaymentAmounts()" style="font-size: 11px; padding: 2px 8px; margin-left: 4px;" title="Distribuir el total entre todos los pagos">
                            <i class="fas fa-balance-scale"></i>
                        </button>
                    </div>
                    <span style="font-size: 11px;">
                        Pagado: <strong id="paidTotal" style="color: #dc3545;">S/ 0.00</strong>
                        &nbsp;|&nbsp; Pendiente: <strong id="pendingBalance" style="color: #dc3545;">S/ 0.00</strong>
                    </span>
                </div>
            </div>
            
            <div class="sale-totals">
                <div class="sale-total-row">
                    <span>Subtotal:</span>
                    <span id="subtotal">S/ 0.00</span>
                </div>
                <div class="sale-total-row">
                    <span>IGV ({{ $mainCompany->getActiveIgvPercent() }}%):</span>
                    <span id="igv">S/ 0.00</span>
                </div>
                <div class="sale-total-row grand-total">
                    <span>TOTAL:</span>
                    <span id="total">S/ 0.00</span>
                </div>
            </div>
            
            <div style="display: flex; gap: 8px; margin-top: 10px;">
                <button class="btn-cancel" onclick="cancelSale()" style="flex: 0 0 100px;">
                    <i class="fas fa-trash"></i> Cancelar
                </button>
                <button class="btn-pay" id="btnPay" onclick="processSale()" disabled style="flex: 1;">
                    <i class="fas fa-credit-card"></i> COBRAR
                </button>
            </div>

            <div style="margin-top: 8px;">
                <button class="btn-pay" id="btnDespacho" onclick="printDespacho()" disabled
                    style="background: linear-gradient(135deg, #ff8f00, #ff6d00); font-size: 14px; padding: 10px;">
                    <i class="fas fa-print"></i> Imprimir Despacho
                </button>
            </div>
        </div>
    </div>
</div>

<form id="saleForm" method="POST" action="{{ route('pos.store') }}" style="display: none;">
    @csrf
    <input type="hidden" name="customer_id" id="customerIdInput">
    <input type="hidden" name="document_type" id="documentTypeInput">
    <input type="hidden" name="payments_json" id="paymentsJson">
    <input type="hidden" name="items_json" id="itemsJson">
    <input type="hidden" name="total" id="totalInput">
    <input type="hidden" name="sale_tab_id" id="saleTabId">
</form>

<div class="modal fade" id="successModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-check-circle"></i> Venta Procesada</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body text-center">
                <h4 id="invoiceNumberSuccess"></h4>
                <h3>Total: <span id="saleTotalSuccess"></span></h3>
                <input type="hidden" id="lastInvoiceId" value="">
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-primary" onclick="sendToSunat()">
                    <i class="fas fa-paper-plane"></i> Enviar a SUNAT
                </button>
                <button type="button" class="btn btn-secondary" onclick="printInvoice('A4')">
                    <i class="fas fa-file-alt"></i> A4
                </button>
                <button type="button" class="btn btn-secondary" onclick="printInvoice('80mm')">
                    <i class="fas fa-receipt"></i> 80mm
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="errorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Error</h5>
            </div>
            <div class="modal-body text-center">
                <p id="errorMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="customerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-user-plus"></i> Nuevo Cliente</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" style="padding: 0; height: 500px;">
                <iframe id="customerFrame" src="" style="width: 100%; height: 100%; border: none;"></iframe>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// === MULTI-SALE STATE (localStorage) ===
const STORAGE_KEY = 'pos_multisale_state';
const igvPercent = {{ $mainCompany->getActiveIgvPercent() }};
const allowSellWithoutStock = {{ $allowSellWithoutStock ? 'true' : 'false' }};
const productsData = @json($products->where('estado', 'ACTIVO'));
const categoriesData = @json($categories);
const customersData = @json($customers);
const seriesData = @json($series);

var appState = loadState();

function defaultState() {
    var defaultCustomerId = {{ $defaultCustomer->id ?? 'null' }};
    var defaultCustomerName = @json($defaultCustomer->nombre ?? '');
    return {
        sales: [{ id: 1, label: 'V-1', customer_id: defaultCustomerId, customer_name: defaultCustomerName, document_type: '03', payments: [{ method: 'EFECTIVO', amount: 0, reference: '' }], items: [] }],
        activeId: 1,
        nextId: 2
    };
}

function loadState() {
    try {
        var raw = localStorage.getItem(STORAGE_KEY);
        if (raw) {
            var s = JSON.parse(raw);
            if (s && s.sales && s.sales.length > 0 && s.activeId && s.nextId) return s;
        }
    } catch (e) {}
    return defaultState();
}

function saveState() {
    try { localStorage.setItem(STORAGE_KEY, JSON.stringify(appState)); } catch (e) {}
}

function activeSale() {
    for (var i = 0; i < appState.sales.length; i++) {
        if (appState.sales[i].id === appState.activeId) return appState.sales[i];
    }
    return appState.sales[0];
}

function switchTab(id) {
    syncActiveSaleToDOM();
    appState.activeId = id;
    syncDOMToActiveSale();
    renderTabs();
    saveState();
}

function addTab() {
    syncActiveSaleToDOM();
    var max = 0;
    appState.sales.forEach(function(s) {
        var m = parseInt(s.label.replace('V-', '')) || 0;
        if (m > max) max = m;
    });
    var today = new Date();
    var ds = today.getFullYear() + ('0' + (today.getMonth() + 1)).slice(-2) + ('0' + today.getDate()).slice(-2);
    var newSale = {
        id: appState.nextId,
        label: 'V-' + today.getFullYear().toString().slice(-2) + ('0' + (today.getMonth() + 1)).slice(-2) + ('0' + today.getDate()).slice(-2) + '-' + ('000' + (max + 1)).slice(-4),
        customer_id: {{ $defaultCustomer->id ?? 'null' }},
        customer_name: @json($defaultCustomer->nombre ?? ''),
        document_type: '03',
        payments: [{ method: 'EFECTIVO', amount: 0, reference: '' }],
        items: []
    };
    appState.sales.push(newSale);
    appState.activeId = newSale.id;
    appState.nextId++;
    syncDOMToActiveSale();
    renderTabs();
    saveState();
}

function removeTab(id) {
    if (appState.sales.length <= 1) return;
    var sale = appState.sales.find(function(s) { return s.id === id; });
    if (sale && sale.items.length > 0) {
        if (!confirm('¿Eliminar venta ' + sale.label + ' con ' + sale.items.reduce(function(c, i) { return c + i.quantity; }, 0) + ' items?')) return;
    }
    appState.sales = appState.sales.filter(function(s) { return s.id !== id; });
    if (appState.activeId === id) {
        appState.activeId = appState.sales[appState.sales.length - 1].id;
    }
    syncDOMToActiveSale();
    renderTabs();
    saveState();
}

function syncActiveSaleToDOM() {
    var s = activeSale();
    s.customer_id = document.getElementById('customerId').value || null;
    s.customer_name = document.getElementById('customerSearch').value;
    s.document_type = document.getElementById('documentType').value;
    s.payments = getPayments();
}

function syncDOMToActiveSale() {
    var s = activeSale();
    document.getElementById('customerId').value = s.customer_id || '';
    document.getElementById('customerSearch').value = s.customer_name || '';
    document.getElementById('documentType').value = s.document_type || '03';
    updateSerieByType();
    buildPaymentsFromArray(s.payments);
    renderSaleItems();
}

function renderTabs() {
    var container = document.getElementById('saleTabs');
    var html = '';
    appState.sales.forEach(function(s) {
        var total = 0;
        s.items.forEach(function(i) { total += i.price * i.quantity; });
        var activeClass = s.id === appState.activeId ? ' active' : '';
        var totalStr = total > 0 ? (' <span class="tab-total">S/ ' + total.toFixed(2) + '</span>') : '';
        var closeBtn = (appState.sales.length > 1) ? ('<span class="close-tab" onclick="event.stopPropagation();removeTab(' + s.id + ')">&times;</span>') : '';
        html += '<div class="sale-tab' + activeClass + '" onclick="switchTab(' + s.id + ')">' + s.label + totalStr + closeBtn + '</div>';
    });
    html += '<div class="sale-tab-add" onclick="addTab()" title="Nueva venta">+</div>';
    container.innerHTML = html;
}

function buildPaymentsFromArray(pmts) {
    var container = document.getElementById('paymentsContainer');
    var p = pmts && pmts.length > 0 ? pmts : [{ method: 'EFECTIVO', amount: 0, reference: '' }];
    var rowsHtml = '';
    p.forEach(function(pmt, idx) {
        var hideBtn = p.length === 1 ? ' style="display:none;"' : '';
        rowsHtml += '<div class="payment-row" style="display: flex; gap: 5px; margin-bottom: 4px; align-items: center;">' +
            '<select class="form-control form-control-sm payment-method" style="flex: 1.5;" onchange="syncActiveSaleToDOM(); updateAllPaymentAmounts();">' +
                '<option value="EFECTIVO"' + (pmt.method === 'EFECTIVO' ? ' selected' : '') + '>EFECTIVO</option>' +
                '<option value="TARJETA"' + (pmt.method === 'TARJETA' ? ' selected' : '') + '>TARJETA</option>' +
                '<option value="TRANSFERENCIA"' + (pmt.method === 'TRANSFERENCIA' ? ' selected' : '') + '>TRANSFERENCIA</option>' +
                '<option value="YAPE"' + (pmt.method === 'YAPE' ? ' selected' : '') + '>YAPE</option>' +
                '<option value="PLIN"' + (pmt.method === 'PLIN' ? ' selected' : '') + '>PLIN</option>' +
            '</select>' +
            '<input type="text" class="form-control form-control-sm payment-amount" placeholder="Monto" style="flex: 1;" oninput="syncActiveSaleToDOM(); updatePaymentTotals();" value="' + (pmt.amount || 0).toFixed(2) + '">' +
            '<input type="text" class="form-control form-control-sm payment-ref" placeholder="Ref" style="flex: 0.8;" value="' + (pmt.reference || '') + '" oninput="syncActiveSaleToDOM();">' +
            '<button type="button" class="btn btn-sm btn-danger remove-payment-btn" onclick="removePayment(this)" style="padding: 2px 6px;' + hideBtn + '" title="Quitar pago">&times;</button>' +
        '</div>';
    });
    container.innerHTML = rowsHtml;
    updatePaymentTotals();
}

function updateSerieByType() {
    var docType = document.getElementById('documentType').value;
    var typePrefixes = { '01': 'F', '03': 'B', 'NV': 'NV' };
    var defaultSerie = (typePrefixes[docType] || 'F') + '001';
    if (seriesData && seriesData.length > 0) {
        var matchingSerie = seriesData.find(function(s) { return s.tipo_documento === docType; });
        if (matchingSerie && matchingSerie.serie) defaultSerie = matchingSerie.serie;
    }
    document.getElementById('serieDisplay').value = defaultSerie;
}

// === SALE ITEMS (uses activeSale()) ===
function selectCustomer(id, nombre) {
    document.getElementById('customerId').value = id;
    document.getElementById('customerSearch').value = nombre;
    document.getElementById('customerDropdown').style.display = 'none';
    syncActiveSaleToDOM();
    saveState();
}

function searchCustomers(term) {
    if (term.length < 2) { document.getElementById('customerDropdown').style.display = 'none'; return; }
    const termLower = term.toLowerCase();
    const results = customersData.filter(c => { return (c.nombre && c.nombre.toLowerCase().includes(termLower)) || (c.documento_numero && c.documento_numero.includes(term)); });
    if (results.length === 0) { document.getElementById('customerDropdown').innerHTML = '<div class="customer-option"><span class="text-muted">Sin resultados</span></div>'; document.getElementById('customerDropdown').style.display = 'block'; return; }
    var html = '';
    results.slice(0, 10).forEach(function(customer) {
        html += '<div class="customer-option" onclick="selectCustomer(' + customer.id + ', \'' + customer.nombre.replace(/'/g, "\\'") + '\')"><div class="customer-option-name">' + customer.nombre + '</div><div class="customer-option-doc">' + (customer.documento_tipo || '') + ': ' + (customer.documento_numero || '') + '</div></div>';
    });
    document.getElementById('customerDropdown').innerHTML = html;
    document.getElementById('customerDropdown').style.display = 'block';
}

function showProducts(categoryId, categoryName) {
    document.getElementById('categoriesGrid').style.display = 'none';
    document.getElementById('productsSection').style.display = 'flex';
    document.getElementById('categoryTitle').textContent = categoryName;
    const products = productsData.filter(p => p.category_id === categoryId);
    if (products.length === 0) { document.getElementById('productsGrid').innerHTML = '<div class="empty-sale"><i class="fas fa-box-open"></i><p>Sin productos</p></div>'; return; }
    var html = '';
    products.forEach(function(product) {
        html += '<div class="product-card" onclick="addToSale(' + product.id + ')"><div class="product-name">' + product.descripcion + '</div><div class="product-price">S/ ' + parseFloat(product.precio).toFixed(2) + '</div><div class="product-stock">Stock: ' + product.stock + '</div></div>';
    });
    document.getElementById('productsGrid').innerHTML = html;
}

function searchPOSProducts(query) {
    query = query.trim();
    var categoriesGrid = document.getElementById('categoriesGrid');
    var productsSection = document.getElementById('productsSection');
    var categoryTitle = document.getElementById('categoryTitle');
    if (!query) { categoriesGrid.style.display = 'grid'; productsSection.style.display = 'none'; return; }
    var isNumeric = /^\d+$/.test(query);
    var q = query.toLowerCase();
    var results = productsData.filter(function(p) { return isNumeric ? ((p.codigo_barras && p.codigo_barras.includes(q)) || (p.codigo && p.codigo.toLowerCase().includes(q))) : (p.descripcion && p.descripcion.toLowerCase().includes(q)); });
    categoriesGrid.style.display = 'none';
    productsSection.style.display = 'flex';
    categoryTitle.textContent = 'Resultados: ' + results.length;
    var grid = document.getElementById('productsGrid');
    if (results.length === 0) { grid.innerHTML = '<div class="empty-sale"><i class="fas fa-box-open"></i><p>Sin resultados</p></div>'; return; }
    var html = '';
    results.forEach(function(product) { html += '<div class="product-card" onclick="addToSale(' + product.id + ')"><div class="product-name">' + product.descripcion + '</div><div class="product-price">S/ ' + parseFloat(product.precio).toFixed(2) + '</div><div class="product-stock">Stock: ' + product.stock + '</div></div>'; });
    grid.innerHTML = html;
}

function backToCategories() { document.getElementById('categoriesGrid').style.display = 'grid'; document.getElementById('productsSection').style.display = 'none'; }

// === SALE ITEMS (uses activeSale()) ===
function addToSale(productId) {
    const product = productsData.find(p => p.id === productId);
    if (!product) return;
    if (!product.is_composite && product.stock <= 0 && !allowSellWithoutStock) { showError('Sin stock'); return; }
    var sale = activeSale();
    const existingItem = sale.items.find(item => item.id === productId);
    if (existingItem) {
        if (product.is_composite || allowSellWithoutStock || existingItem.quantity < product.stock) {
            existingItem.quantity++;
            existingItem.despacho_printed = false;
        } else { showError('Stock insuficiente'); return; }
    } else {
        sale.items.push({ id: product.id, name: product.descripcion, price: parseFloat(product.precio), quantity: 1, stock: product.stock, is_composite: product.is_composite || false });
    }
    renderSaleItems();
    renderTabs();
    saveState();
}

function decreaseQty(productId) {
    var sale = activeSale();
    const item = sale.items.find(item => item.id === productId);
    if (item) { if (item.quantity > 1) { item.quantity--; } else { sale.items = sale.items.filter(i => i.id !== productId); } }
    renderSaleItems();
    renderTabs();
    saveState();
}

function increaseQty(productId) {
    var sale = activeSale();
    const item = sale.items.find(item => item.id === productId);
    if (item && (item.is_composite || allowSellWithoutStock || item.quantity < item.stock)) { item.quantity++; item.despacho_printed = false; renderSaleItems(); renderTabs(); saveState(); }
}

function removeItem(productId) {
    var sale = activeSale();
    sale.items = sale.items.filter(i => i.id !== productId);
    renderSaleItems();
    renderTabs();
    saveState();
}

function cancelSale() {
    var sale = activeSale();
    if (sale.items.length > 0 && confirm('¿Cancelar venta ' + sale.label + '?')) { sale.items = []; sale.customer_id = null; sale.customer_name = ''; sale.payments = [{ method: 'EFECTIVO', amount: 0, reference: '' }]; syncDOMToActiveSale(); renderTabs(); saveState(); }
}

function renderSaleItems() {
    var sale = activeSale();
    const container = document.getElementById('saleItems');
    const cartBadge = document.getElementById('cartCount');
    let totalItems = sale.items.reduce(function(sum, item) { return sum + item.quantity; }, 0);
    if (sale.items.length === 0) {
        container.innerHTML = '<div class="empty-sale"><i class="fas fa-shopping-basket"></i><p>Agrega productos</p></div>';
        document.getElementById('btnPay').disabled = true;
        document.getElementById('btnDespacho').disabled = true;
        cartBadge.style.display = 'none';
        calculateTotals();
        return;
    }
    cartBadge.style.display = 'inline';
    cartBadge.textContent = totalItems;
    var html = '';
    sale.items.forEach(item => {
        var despachoIcon = item.despacho_printed
            ? '<span style="color:#28a745;font-size:12px;" title="Enviado a despacho ' + (item.despacho_hora || '') + '"><i class="fas fa-check-circle"></i></span>'
            : '<span style="color:#ff8f00;font-size:12px;" title="Pendiente despacho"><i class="fas fa-clock"></i></span>';
        html += '<div class="sale-item"' + (item.despacho_printed ? ' style="opacity:0.7;border-left:3px solid #28a745;"' : '') + '>' +
            '<div class="sale-item-info"><div class="sale-item-name">' + despachoIcon + ' ' + item.name + '</div>' +
            '<div class="sale-item-price">S/ ' + item.price.toFixed(2) + ' x ' + item.quantity + '</div></div>' +
            '<div class="sale-item-actions"><button class="qty-btn qty-minus" onclick="decreaseQty(' + item.id + ')">-</button>' +
            '<span class="sale-item-qty">' + item.quantity + '</span><button class="qty-btn qty-plus" onclick="increaseQty(' + item.id + ')">+</button>' +
            '<i class="fas fa-times remove-item" onclick="removeItem(' + item.id + ')"></i></div></div>';
    });
    container.innerHTML = html;
    document.getElementById('btnPay').disabled = false;
    document.getElementById('btnDespacho').disabled = false;
    calculateTotals();
}

function getTotal() {
    var sale = activeSale();
    var total = 0;
    sale.items.forEach(item => { total += item.price * item.quantity; });
    return total;
}

function calculateTotals() {
    var total = getTotal();
    const base = total / (1 + igvPercent / 100);
    const igv = total - base;
    document.getElementById('subtotal').textContent = 'S/ ' + base.toFixed(2);
    document.getElementById('igv').textContent = 'S/ ' + igv.toFixed(2);
    document.getElementById('total').textContent = 'S/ ' + total.toFixed(2);
    updatePaymentTotals();
}

// === PAYMENTS ===
function addPayment() {
    var container = document.getElementById('paymentsContainer');
    var row = document.createElement('div');
    row.className = 'payment-row';
    row.style.cssText = 'display: flex; gap: 5px; margin-bottom: 4px; align-items: center;';
    row.innerHTML = '<select class="form-control form-control-sm payment-method" style="flex: 1.5;" onchange="syncActiveSaleToDOM(); updateAllPaymentAmounts();">' +
        '<option value="EFECTIVO">EFECTIVO</option><option value="TARJETA">TARJETA</option><option value="TRANSFERENCIA">TRANSFERENCIA</option><option value="YAPE">YAPE</option><option value="PLIN">PLIN</option></select>' +
        '<input type="text" class="form-control form-control-sm payment-amount" placeholder="Monto" style="flex: 1;" oninput="syncActiveSaleToDOM(); updatePaymentTotals();" value="0.00">' +
        '<input type="text" class="form-control form-control-sm payment-ref" placeholder="Ref" style="flex: 0.8;" oninput="syncActiveSaleToDOM();">' +
        '<button type="button" class="btn btn-sm btn-danger remove-payment-btn" onclick="removePayment(this)" style="padding: 2px 6px;" title="Quitar pago">&times;</button>';
    container.appendChild(row);
    syncActiveSaleToDOM();
    updatePaymentTotals();
    updateRemoveButtons();
}

function removePayment(btn) {
    var rows = document.querySelectorAll('#paymentsContainer .payment-row');
    if (rows.length <= 1) return;
    btn.parentElement.remove();
    syncActiveSaleToDOM();
    updateAllPaymentAmounts();
    updatePaymentTotals();
    updateRemoveButtons();
}

function updateRemoveButtons() {
    var rows = document.querySelectorAll('#paymentsContainer .payment-row');
    rows.forEach(function(row) { var btn = row.querySelector('.remove-payment-btn'); if (btn) btn.style.display = rows.length > 1 ? 'inline-block' : 'none'; });
}
function getPayments() {
    var payments = []; var rows = document.querySelectorAll('#paymentsContainer .payment-row');
    rows.forEach(function(row) { var method = row.querySelector('.payment-method').value; var amount = parseFloat(row.querySelector('.payment-amount').value) || 0; var ref = row.querySelector('.payment-ref').value.trim(); if (amount > 0) { payments.push({ method: method, amount: amount, reference: ref }); } });
    return payments;
}
function getPaidTotal() { var total = 0; document.querySelectorAll('#paymentsContainer .payment-row').forEach(function(row) { total += parseFloat(row.querySelector('.payment-amount').value) || 0; }); return total; }

function updateAllPaymentAmounts() {
    var total = getTotal();
    if (total <= 0) return;
    var rows = document.querySelectorAll('#paymentsContainer .payment-row');
    var count = rows.length;
    var amountPer = Math.floor(total / count * 100) / 100;
    var remainder = total - (amountPer * (count - 1));
    rows.forEach(function(row, i) { row.querySelector('.payment-amount').value = (i === count - 1 ? remainder : amountPer).toFixed(2); });
    updatePaymentTotals();
}

function updatePaymentTotals() {
    var total = getTotal();
    var paid = getPaidTotal();
    var pending = total - paid;
    var paidEl = document.getElementById('paidTotal');
    var pendingEl = document.getElementById('pendingBalance');
    paidEl.textContent = 'S/ ' + paid.toFixed(2);
    pendingEl.textContent = 'S/ ' + pending.toFixed(2);
    paidEl.style.color = Math.abs(pending) < 0.01 ? '#28a745' : '#dc3545';
    pendingEl.style.color = Math.abs(pending) < 0.01 ? '#28a745' : '#dc3545';
}

// === PROCESS SALE ===
function processSale() {
    var sale = activeSale();
    if (sale.items.length === 0) { showError('No hay productos'); return; }
    var total = getTotal();
    syncActiveSaleToDOM();
    var payments = getPayments();
    var paidTotal = getPaidTotal();
    if (Math.abs(paidTotal - total) > 0.01) { showError('El total pagado (S/ ' + paidTotal.toFixed(2) + ') no coincide con el total de la venta (S/ ' + total.toFixed(2) + '). Ajuste los pagos.'); return; }
    document.getElementById('customerIdInput').value = sale.customer_id || '';
    document.getElementById('documentTypeInput').value = sale.document_type;
    document.getElementById('paymentsJson').value = JSON.stringify(payments);
    document.getElementById('itemsJson').value = JSON.stringify(sale.items);
    document.getElementById('totalInput').value = total;
    document.getElementById('saleTabId').value = appState.activeId;
    localStorage.setItem('pos_pending_sale_id', appState.activeId);
    afterSaleSuccess(null);
    document.getElementById('saleForm').submit();
}

function afterSaleSuccess(invoiceId) {
    if (!invoiceId) { renderTabs(); saveState(); return; }
    appState.sales = appState.sales.filter(function(s) { return s.id !== appState.activeId; });
    if (appState.sales.length === 0) {
        var today = new Date();
        var ds = today.getFullYear().toString().slice(-2) + ('0' + (today.getMonth() + 1)).slice(-2) + ('0' + today.getDate()).slice(-2);
        appState.sales.push({ id: appState.nextId, label: 'V-' + ds + '-0001', customer_id: {{ $defaultCustomer->id ?? 'null' }}, customer_name: @json($defaultCustomer->nombre ?? ''), document_type: '03', payments: [{ method: 'EFECTIVO', amount: 0, reference: '' }], items: [] });
        appState.activeId = appState.nextId;
        appState.nextId++;
    } else {
        appState.activeId = appState.sales[appState.sales.length - 1].id;
    }
    syncDOMToActiveSale();
    renderTabs();
    saveState();
}

// === DESPACHO ===
function printDespacho() {
    var sale = activeSale();
    var itemsParaImprimir = sale.items.filter(function(i) { return !i.despacho_printed; });
    if (itemsParaImprimir.length === 0) { showError('Todos los productos ya fueron enviados a despacho'); return; }
    var btn = document.getElementById('btnDespacho');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Imprimiendo...';
    fetch('/pos/print-despacho', {
        method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ items_json: JSON.stringify(itemsParaImprimir) })
    }).then(function(res) { return res.json(); }).then(function(data) {
        if (data.success) {
            var now = new Date().toLocaleTimeString();
            itemsParaImprimir.forEach(function(i) { i.despacho_printed = true; i.despacho_hora = now; });
            saveState();
            renderSaleItems();
            btn.innerHTML = '<i class="fas fa-check"></i> ' + itemsParaImprimir.length + ' enviado' + (itemsParaImprimir.length > 1 ? 's' : '') + '!';
            btn.style.background = 'linear-gradient(135deg, #28a745, #20c997)';
            setTimeout(function() { btn.innerHTML = '<i class="fas fa-print"></i> Imprimir Despacho'; btn.style.background = 'linear-gradient(135deg, #ff8f00, #ff6d00)'; btn.disabled = false; }, 2500);
        } else {
            showError(data.message || 'Error al imprimir');
            btn.innerHTML = '<i class="fas fa-print"></i> Imprimir Despacho';
            btn.disabled = false;
        }
    }).catch(function(err) {
        showError('Error: ' + err);
        btn.innerHTML = '<i class="fas fa-print"></i> Imprimir Despacho';
        btn.disabled = false;
    });
}

// === MODALS & UTILS ===
function showError(message) { document.getElementById('errorMessage').textContent = message; $('#errorModal').modal('show'); }
function sendToSunat() {
    const invoiceId = document.getElementById('lastInvoiceId').value; if (!invoiceId) return;
    fetch('/pos/sunat/' + invoiceId, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Content-Type': 'application/json' } })
    .then(response => response.json()).then(data => { alert(data.message || (data.success ? 'Enviado' : 'Error')); }).catch(error => { alert('Error al enviar: ' + error); });
}
function printInvoice(format) { const invoiceId = document.getElementById('lastInvoiceId').value; if (!invoiceId) return; window.open('/pos/print/' + invoiceId + '/' + format, '_blank'); }

// === EVENTS ===
document.addEventListener('keydown', function(e) { if (e.key === 'Escape' && activeSale().items.length === 0) { removeTab(appState.activeId); } });
document.getElementById('customerSearch').addEventListener('input', function(e) { searchCustomers(e.target.value); });
document.getElementById('customerSearch').addEventListener('blur', function() { setTimeout(function() { document.getElementById('customerDropdown').style.display = 'none'; }, 200); });
document.getElementById('documentType').addEventListener('change', function() { syncActiveSaleToDOM(); saveState(); updateSerieByType(); });

document.addEventListener('DOMContentLoaded', function() {
    syncDOMToActiveSale();
    renderTabs();
    updateSerieByType();
});

function openCustomerModal() { document.getElementById('customerFrame').src = '/customers/create?company_id=' + ({{ $mainCompany->id ?? 1 }}); $('#customerModal').modal('show'); }
function onCustomerCreated(customer) { customersData.push(customer); $('#customerModal').modal('hide'); selectCustomer(customer.id, customer.nombre); }

</script>
@endpush