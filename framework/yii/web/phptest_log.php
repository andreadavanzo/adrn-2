<?php

function phptest_log(string $action = null): array
{
  static $data = [
    'memory_usage_start' => 0,
    'memory_usage_end' => 0,
    'memory_usage_delta' => 0,
    'memory_allocated_start' => 0,
    'memory_allocated_end' => 0,
    'memory_allocated_delta' => 0,
    'memory_peak_start' => 0,
    'memory_peak_end' => 0,
    'memory_real_peak_start' => 0,
    'memory_real_peak_end' => 0,
    'microtime_start' => 0.0,
    'microtime_end' => 0.0,
    'microtime_delta' => 0.0,
    'num_included_files' => 0,
    'num_declared_classes' => 0,
    'num_declared_interfaces' => 0,
    'num_declared_traits' => 0,
    'num_defined_functions' => 0,
    'num_defined_constants' => 0,
    'included_files' => [],
    'declared_classes' => [],
    'declared_interfaces' => [],
    'declared_traits' => [],
    'defined_functions' => [],
    'defined_constants' => []
  ];
  if ($action === null) {
    return $data;
  }
  if ($action === 'start') {
    $data['memory_usage_start'] = memory_get_usage(false);
    $data['memory_allocated_start'] = memory_get_usage(true);
    $data['memory_peak_start'] = memory_get_peak_usage(false);
    $data['memory_real_peak_start'] = memory_get_peak_usage(true);
    $data['microtime_start'] = microtime(true);
  } else if ($action === 'end') {
    $data['microtime_end'] = microtime(true);
    $data['memory_usage_end'] = memory_get_usage(false);
    $data['memory_allocated_end'] = memory_get_usage(true);
    $data['memory_peak_end'] = memory_get_peak_usage(false);
    $data['memory_real_peak_end'] = memory_get_peak_usage(true);
    $data['included_files'] = get_included_files();
    $data['memory_usage_delta'] = $data['memory_usage_end'] - $data['memory_usage_start'];
    $data['memory_allocated_delta'] = $data['memory_allocated_end'] - $data['memory_allocated_start'];
    $data['microtime_delta'] = $data['microtime_end'] - $data['microtime_start'];
    foreach (get_declared_classes() as $class) {
      if (!(new ReflectionClass($class))->isInternal()) {
        $data['declared_classes'][] = $class;
      }
    }
    foreach (get_declared_interfaces() as $interface) {
      if (!(new ReflectionClass($interface))->isInternal()) {
        $data['declared_interfaces'][] = $interface;
      }
    }
    foreach (get_declared_traits() as $trait) {
      if (!(new ReflectionClass($trait))->isInternal()) {
        $data['declared_traits'][] = $trait;
      }
    }
    $data['defined_functions'] = get_defined_functions()['user'];
    $data['defined_constants'] = get_defined_constants(true)['user'];
    $data['defined_constants'] = get_defined_constants(true)['user'];

    $data['num_included_files'] = count($data['included_files']);
    $data['num_declared_classes'] = count($data['declared_classes']);
    $data['num_declared_interfaces'] = count($data['declared_interfaces']);
    $data['num_declared_traits'] = count($data['declared_traits']);
    $data['num_defined_functions'] = count($data['defined_functions']);
    $data['num_defined_constants'] = count($data['defined_constants']);
  } else if ($action === 'print') {
    echo "<pre>\n";
    echo json_encode($data, JSON_PRETTY_PRINT);
    echo "</pre>\n";
  }
  return [];
}
