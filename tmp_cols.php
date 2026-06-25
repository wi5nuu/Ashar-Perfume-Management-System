<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

$cols = DB::select('SHOW COLUMNS FROM wholesale_customers');
echo implode("\n", array_map(fn($c) => $c->Field . ' [' . $c->Type . '] ' . ($c->Null === 'NO' ? 'NOT NULL' : 'NULL') . ' default:' . ($c->Default ?? 'none'), $cols));
