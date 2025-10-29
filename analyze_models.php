#!/usr/bin/env php
<?php

// Script to analyze all models and check for missing casts

$modelsPath = __DIR__ . '/app/Models';
$files = glob($modelsPath . '/*.php');

$report = "# Model Casting Analysis Report\n\n";
$report .= "Generated: " . date('Y-m-d H:i:s') . "\n\n";

$issues = [];
$total = 0;
$withIssues = 0;

foreach ($files as $file) {
    $total++;
    $modelName = basename($file, '.php');
    $content = file_get_contents($file);
    
    // Extract fillable fields
    preg_match('/protected \$fillable\s*=\s*\[(.*?)\];/s', $content, $fillableMatch);
    $fillable = [];
    if (!empty($fillableMatch[1])) {
        preg_match_all("/'([^']+)'/", $fillableMatch[1], $fields);
        $fillable = $fields[1];
    }
    
    // Extract casts
    preg_match('/protected \$casts\s*=\s*\[(.*?)\];/s', $content, $castsMatch);
    $casts = [];
    if (!empty($castsMatch[1])) {
        preg_match_all("/'([^']+)'\s*=>\s*'([^']+)'/", $castsMatch[1], $castFields);
        $casts = array_combine($castFields[1], $castFields[2]);
    }
    
    // Find ID fields that should be cast
    $idFields = array_filter($fillable, function($field) {
        return preg_match('/_id$/', $field) || $field === 'id';
    });
    
    // Check for missing casts
    $missingCasts = [];
    foreach ($idFields as $field) {
        if (!isset($casts[$field])) {
            $missingCasts[] = $field;
        }
    }
    
    if (!empty($missingCasts)) {
        $withIssues++;
        $issues[$modelName] = [
            'missing' => $missingCasts,
            'fillable' => $fillable,
            'casts' => $casts,
        ];
    }
}

$report .= "## Summary\n\n";
$report .= "- Total models: $total\n";
$report .= "- Models with issues: $withIssues\n";
$report .= "- Models OK: " . ($total - $withIssues) . "\n\n";

$report .= "## Models with Missing ID Casts\n\n";

foreach ($issues as $modelName => $data) {
    $report .= "### $modelName\n\n";
    $report .= "**Missing casts for:**\n";
    foreach ($data['missing'] as $field) {
        $report .= "- `$field`\n";
    }
    
    $report .= "\n**Suggested fix:**\n```php\n";
    $report .= "protected \$casts = [\n";
    
    // Add missing casts
    foreach ($data['missing'] as $field) {
        $report .= "    '$field' => 'integer',  // ADD THIS\n";
    }
    
    // Show existing casts
    foreach ($data['casts'] as $field => $type) {
        $report .= "    '$field' => '$type',\n";
    }
    
    $report .= "];\n```\n\n";
}

echo $report;

// Save to file
file_put_contents(__DIR__ . '/MODEL_CASTING_REPORT.md', $report);
echo "\n\nReport saved to MODEL_CASTING_REPORT.md\n";
