<?php

namespace emteknetnz\DevBuildBenchmark;

use SilverStripe\ORM\DataObject;

/**
 * Used to store benchmark data for every query during dev/builds if configured to do so
 */
class DevBuildBenchmarkQuery extends DataObject
{
    private static $table_name = 'DevBuildBenchmarkQuery';

    private static $db = [
        'When' => 'Datetime',
        'Time' => 'Float',
        'SQL' => 'Text',
    ];

    private static $summary_fields = [
        'ID',
        'When',
        'Time',
        'SQL',
    ];

    private static $default_sort = 'Time DESC';
}
