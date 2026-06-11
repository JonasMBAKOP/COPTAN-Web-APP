<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Validator;

// Simuler une requête POST
$request = Illuminate\Http\Request::create(
    'students',
    'POST',
    [
        'first_name' => 'Jean',
        'last_name' => 'Dupont',
        'gender' => 'M',
        'date_of_birth' => '2010-05-15',
        'place_of_birth' => 'Yaoundé',
        'nationality' => 'Camerounaise',
        'academic_year_id' => 1,
        'class_group_id' => 1,
        'enrollment_date' => date('Y-m-d'),
    ]
);

// Tester la validation
$validator = Validator::make($request->all(), [
    'first_name'            => ['required', 'string', 'max:100'],
    'last_name'             => ['required', 'string', 'max:100'],
    'gender'                => ['required', 'in:M,F'],
    'date_of_birth'         => ['required', 'date', 'before:today'],
    'place_of_birth'        => ['nullable', 'string', 'max:150'],
    'nationality'           => ['nullable', 'string', 'max:100'],
    'academic_year_id'      => ['required', 'exists:academic_years,id'],
    'class_group_id'        => ['required', 'exists:class_groups,id'],
    'enrollment_date'       => ['required', 'date'],
]);

if ($validator->passes()) {
    echo "✅ Validation Student RÉUSSIE\n";
} else {
    echo "❌ Validation Student ÉCHOUÉE\n";
    echo json_encode($validator->errors(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
}
