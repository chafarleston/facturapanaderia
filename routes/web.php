<?php

use App\Http\Controllers\BackupController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\SerieController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\StockOutputController;
use App\Http\Controllers\CashRegisterController;
use App\Http\Controllers\CustomerApiController;
use App\Http\Controllers\DecolectaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SunatPadronController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\UbigeoController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\PosController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth'])->name('dashboard');
Route::post('/theme', [ThemeController::class, 'change'])->name('theme.change')->middleware('auth');

// Rutas públicas
Route::get('/ubigeo/departamentos', [UbigeoController::class, 'getDepartamentos']);
Route::get('/ubigeo/provincias', [UbigeoController::class, 'getProvincias']);
Route::get('/ubigeo/distritos', [UbigeoController::class, 'getDistritos']);
Route::get('/ubigeo/by-codigo', [UbigeoController::class, 'getByUbigeo']);
Route::get('/decolecta/search', [DecolectaController::class, 'search'])->name('decolecta.search');
Route::get('/auxiliary-items/list', [\App\Http\Controllers\AuxiliaryItemController::class, 'list'])->name('auxiliary-items.list');

Route::get('/test-json', function() {
    return response()->json(['test' => 'ok', 'time' => now()]);
});

Route::post('/test-post', function() {
    return response()->json(['success' => true, 'message' => 'POST works!']);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin-only resources
    Route::middleware(['admin'])->group(function () {
        Route::get('/backup', [BackupController::class, 'index'])->name('backup.index');
        Route::post('/backup/run', [BackupController::class, 'run'])->name('backup.run');
        Route::resource('companies', CompanyController::class);
        Route::post('/companies/{company}/certificate', [CompanyController::class, 'updateCertificate'])->name('companies.certificate');
        Route::post('/companies/{company}/set-main', [CompanyController::class, 'setMain'])->name('companies.setMain');
        Route::resource('customers', CustomerController::class)->parameters(['customers' => 'customer']);
        Route::get('/products/export', [ProductController::class, 'export'])->name('products.export');
        Route::get('/products/import', [ProductController::class, 'importForm'])->name('products.import.form');
        Route::post('/products/import', [ProductController::class, 'importStore'])->name('products.import.store');
        Route::post('/products/import-preview', [ProductController::class, 'previewImport'])->name('products.import.preview');
        Route::get('/products/import/template', [ProductController::class, 'downloadTemplate'])->name('products.import.template');
        Route::get('/products/inventory-report', [ProductController::class, 'inventoryReport'])->name('products.inventory.report');
        Route::get('/products/inventory-report/excel', [ProductController::class, 'inventoryReportExcel'])->name('products.inventory.report.excel');
        Route::get('/products/inventory-report/pdf', [ProductController::class, 'inventoryReportPdf'])->name('products.inventory.report.pdf');
        Route::post('/products/{product}/duplicate', [ProductController::class, 'duplicate'])->name('products.duplicate');
        Route::resource('products', ProductController::class);
        Route::get('/products/composite/create', [ProductController::class, 'createComposite'])->name('products.composite.create');
        Route::post('/products/composite/store', [ProductController::class, 'storeComposite'])->name('products.composite.store');
        Route::get('/products/{product}/composite/edit', [ProductController::class, 'editComposite'])->name('products.composite.edit');
        Route::put('/products/{product}/composite/update', [ProductController::class, 'updateComposite'])->name('products.composite.update');
        Route::resource('categories', CategoryController::class);
        Route::resource('suppliers', SupplierController::class);
        Route::resource('purchases', PurchaseController::class);
        Route::get('/purchases/{purchase}/print/a4', [\App\Http\Controllers\PurchaseController::class, 'printA4'])->name('purchases.print.a4');
        Route::get('/purchases/{purchase}/print/ticket', [\App\Http\Controllers\PurchaseController::class, 'printTicket'])->name('purchases.print.ticket');
        Route::resource('stock-outputs', StockOutputController::class);
        Route::get('/stock-outputs/{stock_output}/print/a4', [\App\Http\Controllers\StockOutputController::class, 'printA4'])->name('stock-outputs.print.a4');
        Route::get('/stock-outputs/{stock_output}/print/ticket', [\App\Http\Controllers\StockOutputController::class, 'printTicket'])->name('stock-outputs.print.ticket');
        Route::resource('cashregisters', CashRegisterController::class);
        Route::get('/cashregisters/{cashregister}/pdf', [CashRegisterController::class, 'pdf'])->name('cashregisters.pdf');
        Route::get('/cashregisters/{cashregister}/ticket', [CashRegisterController::class, 'ticketPdf'])->name('cashregisters.ticket');
        Route::post('/cashregisters/{cashregister}/print-caja', [CashRegisterController::class, 'printCaja'])->name('cashregisters.printCaja');
        Route::post('/cashregister/open', [CashRegisterController::class, 'open'])->name('cashregisters.open');
        Route::post('/cashregister/close', [CashRegisterController::class, 'close'])->name('cashregisters.close');
        Route::resource('series', SerieController::class)->parameters(['series' => 'serie']);
        Route::resource('users', \App\Http\Controllers\UserController::class);
        Route::resource('roles', \App\Http\Controllers\RoleController::class);
        Route::resource('permissions', \App\Http\Controllers\PermissionController::class);
        Route::resource('auxiliary-items', \App\Http\Controllers\AuxiliaryItemController::class);
        Route::get('/sunat-padron', [SunatPadronController::class, 'index'])->name('sunat-padron.index');
        Route::post('/sunat-padron/download', [SunatPadronController::class, 'download'])->name('sunat-padron.download');
        Route::post('/companies/download-padron', [SunatPadronController::class, 'downloadPadron'])->name('sunat.padron.download');
    });
    
    Route::get('/invoices/{invoice}/send', [InvoiceController::class, 'sendToSunat'])->name('invoices.send');
    Route::get('/invoices/nv', [InvoiceController::class, 'nvIndex'])->name('invoices.nv');
    Route::get('/invoices/{invoice}/print/nv/a4', [InvoiceController::class, 'printNvA4'])->name('invoices.print_nv_a4');
    Route::get('/invoices/{invoice}/print/nv/ticket', [InvoiceController::class, 'printNvTicket'])->name('invoices.print_nv_ticket');
    Route::get('/sunat-products/search', [\App\Http\Controllers\SunatProductSearchController::class, 'search'])->name('sunat-products.search');
    Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'generatePdf'])->name('invoices.pdf');
    Route::get('/invoices/{invoice}/ticket', [InvoiceController::class, 'generateTicketPdf'])->name('invoices.ticket');
    Route::get('/invoices/{invoice}/xml', [InvoiceController::class, 'downloadXml'])->name('invoices.downloadXml');
    Route::get('/invoices/{invoice}/cdr', [InvoiceController::class, 'downloadCdr'])->name('invoices.downloadCdr');
    Route::get('/invoices/{invoice}/credit-note', [InvoiceController::class, 'creditNoteForm'])->name('invoices.creditNoteForm');
    Route::post('/invoices/{invoice}/credit-note', [InvoiceController::class, 'sendCreditNote'])->name('invoices.sendCreditNote');
    Route::get('/invoices/{invoice}/debit-note', [InvoiceController::class, 'debitNoteForm'])->name('invoices.debitNoteForm');
    Route::post('/invoices/{invoice}/debit-note', [InvoiceController::class, 'sendDebitNote'])->name('invoices.sendDebitNote');
    Route::resource('invoices', InvoiceController::class);
    Route::get('/invoices/{invoice}/generate-despatch', [\App\Http\Controllers\DocumentController::class, 'createFromInvoice'])->name('invoices.generateDespatch');
    Route::get('/sunat-summaries', [\App\Http\Controllers\SummaryController::class, 'index'])->name('sunat-summaries.index');
    Route::post('/sunat-summaries/check-all', [\App\Http\Controllers\SummaryController::class, 'checkAllPending'])->name('sunat-summaries.checkAll');
    Route::post('/sunat-summaries/{summary}/check', [\App\Http\Controllers\SummaryController::class, 'checkStatus'])->name('sunat-summaries.check');
    Route::post('/sunat-summaries/send-daily', [\App\Http\Controllers\SummaryController::class, 'sendDaily'])->name('sunat-summaries.sendDaily');
    Route::post('/sunat-summaries/retry-pending', [\App\Http\Controllers\SummaryController::class, 'retryPending'])->name('sunat-summaries.retryPending');
    Route::get('/documents/{tipo}', [\App\Http\Controllers\DocumentController::class, 'index'])->name('documents.index');
    Route::get('/documents/{tipo}/create', [\App\Http\Controllers\DocumentController::class, 'create'])->name('documents.create');
    Route::post('/documents/{tipo}', [\App\Http\Controllers\DocumentController::class, 'store'])->name('documents.store');
    Route::get('/documents/{tipo}/{document}', [\App\Http\Controllers\DocumentController::class, 'show'])->name('documents.show');
    Route::post('/documents/{tipo}/{document}/send', [\App\Http\Controllers\DocumentController::class, 'send'])->name('documents.send');
    Route::get('/api/invoice-data/{id}', [\App\Http\Controllers\DocumentController::class, 'getInvoiceData'])->name('api.invoice.data');
    
Route::get('/customers/search', [CustomerApiController::class, 'search'])->name('customers.search');
    Route::post('/customers/quick-store', [CustomerApiController::class, 'quickStore'])->name('customers.quickStore');
    
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos', [PosController::class, 'store'])->name('pos.store');
    Route::get('/pos/success/{id}', [PosController::class, 'success'])->name('pos.success');
    Route::post('/pos/sunat/{id}', [PosController::class, 'sendToSunat'])->name('pos.sunat');
    Route::get('/pos/print/{id}/{format}', [PosController::class, 'printInvoice'])->name('pos.print');
    Route::post('/pos/open-drawer', [PosController::class, 'openDrawer'])->name('pos.openDrawer');

    // Bakery: Recetas (Recipes)
    Route::resource('recipes', \App\Http\Controllers\RecipeController::class);

    // Bakery: Órdenes de Producción (Production Orders)
    Route::resource('production-orders', \App\Http\Controllers\ProductionOrderController::class);
    Route::post('/production-orders/{productionOrder}/start', [\App\Http\Controllers\ProductionOrderController::class, 'start'])->name('production-orders.start');
    Route::post('/production-orders/{productionOrder}/complete', [\App\Http\Controllers\ProductionOrderController::class, 'complete'])->name('production-orders.complete');
    Route::post('/production-orders/{productionOrder}/cancel', [\App\Http\Controllers\ProductionOrderController::class, 'cancel'])->name('production-orders.cancel');

    // Bakery: Mermas (Waste)
    Route::resource('waste', \App\Http\Controllers\WasteRecordController::class);

    // Bakery: Pedidos Programados (Scheduled Orders)
    Route::resource('scheduled-orders', \App\Http\Controllers\ScheduledOrderController::class);
    Route::post('/scheduled-orders/{scheduledOrder}/confirm', [\App\Http\Controllers\ScheduledOrderController::class, 'confirm'])->name('scheduled-orders.confirm');
    Route::post('/scheduled-orders/{scheduledOrder}/start-production', [\App\Http\Controllers\ScheduledOrderController::class, 'startProduction'])->name('scheduled-orders.startProduction');
    Route::post('/scheduled-orders/{scheduledOrder}/mark-ready', [\App\Http\Controllers\ScheduledOrderController::class, 'markReady'])->name('scheduled-orders.markReady');
    Route::post('/scheduled-orders/{scheduledOrder}/deliver', [\App\Http\Controllers\ScheduledOrderController::class, 'deliver'])->name('scheduled-orders.deliver');
    Route::post('/scheduled-orders/{scheduledOrder}/cancel', [\App\Http\Controllers\ScheduledOrderController::class, 'cancel'])->name('scheduled-orders.cancel');
    Route::get('/scheduled-orders/{scheduledOrder}/print-comanda', [\App\Http\Controllers\ScheduledOrderController::class, 'printComanda'])->name('scheduled-orders.printComanda');

    // Bakery: Reparto (Delivery)
    Route::resource('deliveries', \App\Http\Controllers\DeliveryController::class);
    Route::post('/deliveries/{delivery}/assign', [\App\Http\Controllers\DeliveryController::class, 'assign'])->name('deliveries.assign');
    Route::post('/deliveries/{delivery}/start', [\App\Http\Controllers\DeliveryController::class, 'startRoute'])->name('deliveries.start');
    Route::post('/deliveries/{delivery}/complete', [\App\Http\Controllers\DeliveryController::class, 'complete'])->name('deliveries.complete');
    Route::post('/deliveries/{delivery}/cancel', [\App\Http\Controllers\DeliveryController::class, 'cancel'])->name('deliveries.cancel');
    Route::resource('delivery-zones', \App\Http\Controllers\DeliveryZoneController::class);
    Route::resource('delivery-persons', \App\Http\Controllers\DeliveryPersonController::class);

    // Printer routes
    Route::get('/printers/detect', [\App\Http\Controllers\Admin\PrinterController::class, 'detect'])->name('printers.detect');
    Route::post('/printers/detect', [\App\Http\Controllers\Admin\PrinterController::class, 'detect'])->name('printers.detect.post');
    Route::get('/printers', [\App\Http\Controllers\Admin\PrinterController::class, 'index'])->name('printers.index');
    Route::get('/printers/queue', [\App\Http\Controllers\Admin\PrinterController::class, 'queue'])->name('printers.queue');
    Route::post('/printers/queue/{printJob}/retry', [\App\Http\Controllers\Admin\PrinterController::class, 'retry'])->name('printers.queue.retry');
    Route::delete('/printers/queue/{printJob}', [\App\Http\Controllers\Admin\PrinterController::class, 'destroy'])->name('printers.queue.destroy');
    Route::put('/printers/{printer}', [\App\Http\Controllers\Admin\PrinterController::class, 'update'])->name('printers.update');
});

require __DIR__.'/auth.php';

Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
Route::get('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout.get');
