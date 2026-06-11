<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Simuler une requête POST
$request = Illuminate\Http\Request::create(
    'staff',
    'POST',
    [
        'first_name' => 'Jean',
        'last_name' => 'Dupont',
        'gender' => 'M',
        'contract_type' => 'permanent',
        'is_active' => '1',
        'positions' => ['enseignant', 'censeur'],
        'primary_position' => 'enseignant',
    ]
);

app('Illuminate\Contracts\Http\Kernel')->handle($request);

// Tester la validation
$validator = Validator::make($request->all(), [
    'first_name'    => ['required', 'string', 'max:100'],
    'last_name'     => ['required', 'string', 'max:100'],
    'gender'        => ['required', 'in:M,F'],
    'contract_type' => ['required', 'in:permanent,vacataire,stagiaire'],
    'is_active'     => ['nullable', 'boolean'],
    'positions'     => ['required', 'array', 'min:1'],
    'positions.*'   => ['string'],
    'primary_position' => ['required', 'string', 'in:enseignant,directeur,fondateur,censeur,econome,surveillant_general,secretaire'],
]);

if ($validator->passes()) {
    echo "✅ Validation RÉUSSIE\n";
} else {
    echo "❌ Validation ÉCHOUÉE\n";
    echo json_encode($validator->errors(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
}
