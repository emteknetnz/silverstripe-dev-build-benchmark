<?php

namespace emteknetnz\DevBuildBenchmark;

use SilverStripe\ORM\Connect\MySQLDatabase;
use SilverStripe\Core\TempFolder;
use SilverStripe\Core\Path;

class DevBuildBenchmarkMySQLDatabase extends MySQLDatabase
{
    public function getTempFilename()
    {
        return Path::join(TempFolder::getTempFolder(BASE_PATH), 'dev-build-benchmark.log');
    }

    protected function benchmarkQuery($sql, $callback, $parameters = [])
    {
        if (!DatabaseAdminExtension::$is_dev_building) {
            return parent::benchmarkQuery($sql, $callback, $parameters);
        }
        $start = microtime(true);
        $result = $callback($sql);
        $time = microtime(true) - $start;
        $cleanSql = preg_replace('#[\r\n\t]#', ' ', $sql);
        $cleanSql = preg_replace('# {2,}#', ' ', $cleanSql);
        $delimter = DatabaseAdminExtension::DELIMITER;
        $line = implode($delimter, [
            date('Y-m-d H:i:s'),
            number_format($time, 5),
            $cleanSql,
        ]) . PHP_EOL;
        $tempfile = $this->getTempFilename();
        file_put_contents($tempfile, $line, FILE_APPEND);
        return $result;
    }
}
