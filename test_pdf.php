<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$greenter = new App\Services\GreenterService();
$invoice = App\Models\Invoice::find(12);
$pdf = $greenter->generatePdf($invoice);
file_put_contents(__DIR__ . '/test_final7.pdf', $pdf);
echo "PDF generado: test_final7.pdf (" . strlen($pdf) . " bytes)\n";