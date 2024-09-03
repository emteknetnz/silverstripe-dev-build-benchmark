<?php

namespace emteknetnz\DevBuildBenchmark;

use Exception;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Extension;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DB;

class DatabaseAdminExtension extends Extension
{
    use Configurable;

    /**
     * Whether to store query data during dev/build in a `DevBuildBenchmark` record
     * so that it can be analysed later on
     * 
     * Be careful enabling this on production data as it will store all queries during dev/build
     */
    private static bool $store_query_data = false;

    public const DELIMITER = ':::';

    public static $is_dev_building = false;

    public function onBeforeBuild($quiet, $populate, $testMode)
    {
        static::$is_dev_building = true;
    }

    public function onAfterBuild($quiet, $populate, $testMode)
    {
        static::$is_dev_building = false;
        $filename = (new DevBuildBenchmarkMySQLDatabase)->getTempFilename();
        if (file_exists($filename)) {
            $data = $this->getTempFileData();
            $this->createBenchmarkSummaryRecords($data);
            $this->createBenchmarkQueryRecords($data);
            unlink($filename);
        }
    }

    /**
     * Creates summary results of the benchmark data from the temp file that was created
     * during the last dev/build. It will not show the values from insert or update queries
     * on the off chance that they contain sensitive data
     */
    private function createBenchmarkSummaryRecords(array $data): void
    {
        $table = DataObject::getSchema()->tableName(DevBuildBenchmarkSummary::class);
        DB::query("TRUNCATE TABLE \"$table\"");
        $res = [];
        $totalCount = 0;
        $counts = [];
        foreach ($data as $arr) {
            [$when, $time, $sql] = $arr;
            // truncate the SQL before any quotes
            $quotes = ['"', "'", '`', '&quot;', '&apos;', '&#039;'];
            foreach ($quotes as $quote) {
                $pos = strpos($sql, $quote);
                if ($pos !== false) {
                    $sql = substr($sql, 0, $pos);
                    break;
                }
            }
            $res[$sql] ??= 0;
            $res[$sql] += $time;
            $counts[$sql] ??= 0;
            $counts[$sql]++;
            $totalCount++;
        }
        // limit to 14 records and sum everything else into an "Other queries" category
        arsort($res);
        $res2 = array_slice($res, 0, 14, true) + ['Other queries' => 0];
        $res2['Other queries'] = array_sum(array_slice($res, 14));
        uksort($counts, function ($a, $b) use ($res2) {
            return array_key_exists($a, $res2) ? -1 : 0;
        });
        $counts2 = array_slice($counts, 0, 14, true) + ['Other queries' => 0];
        $counts2['Other queries'] = array_sum(array_slice($counts, 14));
        asort($res2);
        $total = array_sum($res2);
        foreach ($res2 as $sql => $time) {
            $perc = number_format(($time / $total) * 100, 1) . '%';
            $count = $counts2[$sql];
            DevBuildBenchmarkSummary::create([
                'TotalTime' => number_format($time, 4),
                'Percentage' => $perc,
                'Count' => $count,
                'TruncatedSQL' => $sql,
            ])->write();
        }
    }

    /**
     * Create `DevBuildBenchmark` records from results in the temp file that was created
     * during the last dev/build in DevBuildBenchmarkMySQLDatabase
     */
    private function createBenchmarkQueryRecords(array $data): void
    {
        // always remove any previous data first in case the data was query data was previously
        // being stored, and not it's not
        $table = DataObject::getSchema()->tableName(DevBuildBenchmarkQuery::class);
        DB::query("TRUNCATE TABLE \"$table\"");
        if (!$this->config()->get('store_query_data')) {
            return;
        }
        foreach ($data as $arr) {
            [$when, $time, $sql] = $arr;
            DevBuildBenchmarkQuery::create([
                'When' => $when,
                'Time' => $time,
                'SQL' => $sql,
            ])->write();
        }
    }

    /**
     * Get the data from the temp file that was created during the last dev/build
     */
    private function getTempFileData(): array
    {
        $data = [];
        $filename = (new DevBuildBenchmarkMySQLDatabase)->getTempFilename();
        $contents = file_get_contents($filename);
        $lines = explode(PHP_EOL, $contents);
        foreach ($lines as $line) {
            if (empty($line)) {
                continue;
            }
            [$when, $time, $sql] = explode(static::DELIMITER, $line, 3);
            $data[] = [$when, $time, $sql];
        }
        return $data;
    }
}
