<?php

namespace emteknetnz\DevBuildBenchmark;

use SilverStripe\Reports\Report;
use SilverStripe\Security\Security;
use SilverStripe\Security\Permission;

class DevBuildBenchmarkReport extends Report
{
    protected $title = 'dev/build benchmark';

    protected $description = 'Benchmark from the last dev/build';

    public function sourceRecords()
    {
        return ZZZDevBuildBenchmark::get();
    }

    public function canView($member = null)
    {
        if (!$member) {
            $member = Security::getCurrentUser();
        }
        return Permission::checkMember($member, 'ADMIN');
    }
}
