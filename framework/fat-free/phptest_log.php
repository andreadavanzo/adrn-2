<?php

function phptest_log(string $action = null): array
{
  static $data = [
    'phptest_usage_start' => 0,
    'phptest_allocated_start' => 0,
    'phptest_peak_start' => 0,
    'phptest_real_peak_start' => 0,
    'phptest_microtime_start' => 0.0,
    'phptest_usage_end' => 0,
    'phptest_allocated_end' => 0,
    'phptest_peak_end' => 0,
    'phptest_real_peak_end' => 0,
    'phptest_microtime_end' => 0.0
  ];
  if ($action === null) {
    return $data;
  }
  if ($action === 'start') {
    $data['phptest_usage_start'] = memory_get_usage(false);
    $data['phptest_allocated_start'] = memory_get_usage(true);
    $data['phptest_peak_start'] = memory_get_peak_usage(false);
    $data['phptest_real_peak_start'] = memory_get_peak_usage(true);
    $data['phptest_microtime_start'] = microtime(true);
  } else if ($action === 'end') {
    $data['phptest_usage_end'] = memory_get_usage(false);
    $data['phptest_allocated_end'] = memory_get_usage(true);
    $data['phptest_peak_end'] = memory_get_peak_usage(false);
    $data['phptest_real_peak_end'] = memory_get_peak_usage(true);
    $data['phptest_microtime_end'] = microtime(true);
  } else if ($action === 'print') {
    echo "<pre>\n";
    echo sprintf("phptest_usage_start: %d\n", $data['phptest_usage_start']);
    echo sprintf("phptest_allocated_start: %d\n", $data['phptest_allocated_start']);
    echo sprintf("phptest_peak_start: %d\n", $data['phptest_peak_start']);
    echo sprintf("phptest_real_peak_start: %d\n", $data['phptest_real_peak_start']);

    echo sprintf("phptest_usage_end: %d\n", $data['phptest_usage_end']);
    echo sprintf("phptest_allocated_end: %d\n", $data['phptest_allocated_end']);
    echo sprintf("phptest_peak_end: %d\n", $data['phptest_peak_end']);
    echo sprintf("phptest_real_peak_end: %d\n", $data['phptest_real_peak_end']);

    echo sprintf("phptest_usage: %d\n", $data['phptest_usage_end'] - $data['phptest_usage_start']);
    echo sprintf("phptest_allocated: %d\n", $data['phptest_allocated_end'] - $data['phptest_allocated_start']);
    echo sprintf("phptest_microtime: %f\n", $data['phptest_microtime_end'] - $data['phptest_microtime_start']);

    print_r(get_included_files());
//    print_r(opcache_get_status(true));
  }
  return [];
}
