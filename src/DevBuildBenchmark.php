<?php

namespace zzz\emteknetnz\DevBuildBenchmark;

use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DB;

class DevBuildBenchmark extends DataObject
{
    public const DELIMITER = ':::';

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

    public function requireDefaultRecords()
    {
        $table = DataObject::getSchema()->tableName(static::class);
        DB::query("TRUNCATE TABLE \"$table\"");
        $filename = (new DevBuildBenchmarkMySQLDatabase)->getTempFilename();
        if (!file_exists($filename)) {
            return;
        }
        $contents = file_get_contents($filename);
        $lines = explode(PHP_EOL, $contents);
        foreach ($lines as $line) {
            if (empty($line)) {
                continue;
            }
            [$when, $time, $sql] = explode(static::DELIMITER, $line, 3);
            static::create([
                'When' => $when,
                'Time' => $time,
                'SQL' => $sql,
            ])->write();
        }
        unlink($filename);
    }
}
