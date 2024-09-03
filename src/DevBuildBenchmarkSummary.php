<?php

namespace emteknetnz\DevBuildBenchmark;

use SilverStripe\ORM\DataObject;

/**
 * Used to store summary data for queries during the last dev/build
 */
class DevBuildBenchmarkSummary extends DataObject
{
    private static $table_name = 'DevBuildBenchmarkSummary';

    private static $db = [
        'TotalTime' => 'Float',
        'Percentage' => 'Varchar',
        'Count' => 'Int',
        'TruncatedSQL' => 'Varchar',
    ];

    private static $summary_fields = [
        'TotalTime' => 'Time (seconds)',
        'Percentage',
        'Count',
        'TruncatedSQL',
    ];

    private static $default_sort = 'TotalTime DESC';
}
