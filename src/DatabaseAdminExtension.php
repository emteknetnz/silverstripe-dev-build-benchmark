<?php

namespace emteknetnz\DevBuildBenchmark;

use SilverStripe\Core\Extension;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DB;

class DatabaseAdminExtension extends Extension
{
    public const DELIMITER = ':::';

    public static $is_dev_building = false;

    public function onBeforeBuild($quiet, $populate, $testMode)
    {
        static::$is_dev_building = true;
    }

    public function onAfterBuild($quiet, $populate, $testMode)
    {
        static::$is_dev_building = false;
        $this->createBenchmarkRecords();
    }

    public function createBenchmarkRecords()
    {
        $table = DataObject::getSchema()->tableName(DevBuildBenchmark::class);
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
            DevBuildBenchmark::create([
                'When' => $when,
                'Time' => $time,
                'SQL' => $sql,
            ])->write();
        }
        unlink($filename);
    }
}
