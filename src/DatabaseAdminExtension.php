<?php

namespace emteknetnz\DevBuildBenchmark;

use SilverStripe\Core\Extension;

class DatabaseAdminExtension extends Extension
{
    public static $is_dev_building = false;

    public function onBeforeBuild($quiet, $populate, $testMode)
    {
        static::$is_dev_building = true;
    }

    public function onAfterBuild($quiet, $populate, $testMode)
    {
        static::$is_dev_building = false;
    }
}
