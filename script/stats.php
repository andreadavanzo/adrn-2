<?php

if ($argc < 3) {
    die("Usage: php summary_by_tag.php <file.csv> <column_to_calculate>\n");
}

$filename = $argv[1];
$targetCol = $argv[2];
$tagColName = $argv[3] ?? 'tag'; // The column used for grouping

if (!file_exists($filename)) {
    die("Error: File '$filename' not found.\n");
}

$handle = fopen($filename, "r");
$header = fgetcsv($handle);

// Find indices for both the data column and the tag column
$colIndex = is_numeric($targetCol) ? (int)$targetCol : array_search($targetCol, $header);
$tagIndex = array_search($tagColName, $header);

if ($colIndex === false || $tagIndex === false) {
    die("Error: Column '$targetCol' or '$tagColName' not found.\n");
}

$stats = [];

while (($data = fgetcsv($handle)) !== false) {
    if (!isset($data[$colIndex]) || !isset($data[$tagIndex]) || !is_numeric($data[$colIndex])) {
        continue;
    }

    $val = (float)$data[$colIndex];
    $tag = $data[$tagIndex];

    if (!isset($stats[$tag])) {
        $stats[$tag] = [
            'min' => $val,
            'max' => $val,
            'sum' => 0,
            'count' => 0
        ];
    }

    if ($val < $stats[$tag]['min']) $stats[$tag]['min'] = $val;
    if ($val > $stats[$tag]['max']) $stats[$tag]['max'] = $val;
    $stats[$tag]['sum'] += $val;
    $stats[$tag]['count']++;
}
fclose($handle);

echo str_pad("TAG", 15) . " | " . str_pad("COUNT", 8) . " | " . str_pad("MIN", 10) . " | " . str_pad("AVG", 10) . " | " . str_pad("MAX", 10) . "\n";
echo str_repeat("-", 65) . "\n";

foreach ($stats as $tag => $data) {
    $avg = $data['sum'] / $data['count'];
    echo str_pad($tag, 15) . " | " .
         str_pad($data['count'], 8) . " | " .
         str_pad(number_format($data['min'], 6), 10) . " | " .
         str_pad(number_format($avg, 6), 10) . " | " .
         str_pad(number_format($data['max'], 6), 10) . "\n";
}