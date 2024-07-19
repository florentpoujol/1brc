<?php

/*
 * Usage:
 * calculateAverage.php [file suffix] [number of results to output] [debug]
 * 
 * The debug argument must be exactly "debug" as a string to ouput debug info
 */

$suffix = $argv[1] ?? '';
$fileName = 'measurements.txt' . $suffix;

$isDebug = ($argv[3] ?? '') === 'debug';

function debug(string $output): void
{
    global $isDebug;
    if (!$isDebug) {
        return;
    }

    $date = date('H:i:s');
    echo "[$date] $output" . PHP_EOL;
}

$fileHandle = fopen($fileName, 'r');
if ($fileHandle === false) {
    throw new Exception("Could not read file '$fileName'");
}

debug("Starting to process file '$fileName'");

// -----------------------------------------------
// first read the whole file to extract all values

/**
 * @var $valuesPerStation array<string, array{min: float, max: float, count: int, mean: float}>
 */
$valuesPerStation = [
    /*
    'station name' => [
        'min' => 0.0,
        'max' => 0.0,
        'count' => 0,
        'sum' => 0.0,
    ]
    */
];

$startTimeInMs = microtime(true) * 1_000;
$timeInMs = $startTimeInMs;

$lineCount = 0;

while (($line = fgetcsv($fileHandle, null, ';')) !== false) {
    $name = $line[0];
    $value = (float) $line[1];

    $valuesPerStation[$name] ??= [
        'min' => 0.0,
        'max' => 0.0,
        'count' => 0,
        'sum' => 0.0,
    ];
    $valuesPerStation[$name]['min'] = min($valuesPerStation[$name]['min'], $value);
    $valuesPerStation[$name]['max'] = max($valuesPerStation[$name]['max'], $value);
    $valuesPerStation[$name]['count']++;
    $valuesPerStation[$name]['sum'] += $value;

    $lineCount++;
    if ($isDebug && $lineCount % 100_000 === 0) {
        $time2InMs = microtime(true) * 1_000;
        $milliseconds = ($time2InMs - $timeInMs) / 1_000;

        $memory = memory_get_usage(true) / 1_000_000;
        $memoryPeak = memory_get_peak_usage(true) / 1_000_000;

        debug("$lineCount lines processed in a total of $milliseconds ms (memory=$memory M (peak $memoryPeak M))");
    }
}

debug("");

$time2InMs = microtime(true) * 1_000;
$seconds = ($time2InMs - $timeInMs) / 1_000;
debug("File read in $seconds s.");

$memory = memory_get_usage(true) / 1_000_000;
$memoryPeak = memory_get_peak_usage(true) / 1_000_000;
debug("Consumed $memory M of memory (peak $memoryPeak M).");

debug("====================================");

// -----------------------------------------------
// then parse the values to extract the data we need

/**
 * @var $results array<string, array{min: float, max: float, mean: float}>
 */
$results = [
    /*
    'station name' => [
        'min' => 0.0,
        'max' => 0.0,
        'mean' => 0.0,
    ]
     */
];

$timeInMs = microtime(true) * 1_000;

foreach ($valuesPerStation as $name => $values) {
    $results[$name] = [
        'min' => $values['min'],
        'mean' => $values['sum'] / $values['count'],
        'max' => $values['max'],
    ];
}

ksort($results);

$time2InMs = microtime(true) * 1_000;
$totalTimeInMs = $time2InMs - $startTimeInMs;

$milliseconds = $time2InMs - $timeInMs;
debug("Data processed in $milliseconds ms.");

$memory = memory_get_usage(true) / 1_000_000 ;
$memoryPeak = memory_get_peak_usage(true) / 1_000_000;
debug("Consumed $memory M of memory (peak $memoryPeak M).");

// -----------------------------------------------
// now output result if needed

$resultCountToOutput = (int) ($argv[2] ?? PHP_INT_MAX);

if ($resultCountToOutput === 0) {
    goto end;
}

if ($resultCountToOutput < 0) {
    $resultCountToOutput = PHP_INT_MAX;
}

debug('==================================');
debug("Echoing $resultCountToOutput results");
echo PHP_EOL;

$i = 0;
foreach ($results as $name => $result) {
    echo $name . ';' .
        number_format($result['min'], 1) . ';' .
        number_format($result['mean'], 1) . ';' .
        number_format($result['max'], 1) . PHP_EOL;

    $i++;
    if ($i >= $resultCountToOutput) {
        break;
    }
}

end:

$lineCountPerMs = (int) ($lineCount / $totalTimeInMs);
echo PHP_EOL . "Total time to process: $totalTimeInMs ms ($lineCountPerMs lines/ms)" . PHP_EOL;

$peakMemoryUsageInKb = memory_get_peak_usage() / 1_000;
$memoryUsageInKb = memory_get_peak_usage() / 1_000;
echo "Peak memory: $peakMemoryUsageInKb Kb ($memoryUsageInKb Kb)" . PHP_EOL;
