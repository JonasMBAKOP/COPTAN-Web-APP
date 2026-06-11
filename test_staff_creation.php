<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$staffCount = App\Models\Staff::count();
echo "Staff avant: $staffCount\n";

try {
    $staff = App\Models\Staff::create([
        'first_name' => 'TestFirst',
        'last_name' => 'TestLast',
        'gender' => 'M',
        'contract_type' => 'permanent',
        'is_active' => true,
    ]);
    echo "✅ Staff créé avec succès: ID=" . $staff->id . "\n";
    
    $staff->positions()->create([
        'position' => 'enseignant',
        'is_primary' => true,
    ]);
    echo "✅ Position créée\n";
    
    $staffAfter = App\Models\Staff::count();
    echo "Staff après: $staffAfter\n";
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
