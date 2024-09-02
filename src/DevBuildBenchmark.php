<?php

namespace emteknetnz\DevBuildBenchmark;

use SilverStripe\ORM\DataObject;

class DevBuildBenchmark extends DataObject
{
    private static $table_name = 'DevBuildBenchmark';

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
