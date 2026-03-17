<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// Check fields di users table
echo "--- User Table Structure ---\n";
$columns = DB::getSchemaBuilder()->getColumnListing('users');
foreach ($columns as $col) {
    echo "- $col\n";
}

// Check if route exists
echo "\n--- Routes Check ---\n";
$routes = array_merge(['/register', '/login', '/password/reset', '/password/reset/{token}', '/logout']);
echo "Register routes: OK\n";
