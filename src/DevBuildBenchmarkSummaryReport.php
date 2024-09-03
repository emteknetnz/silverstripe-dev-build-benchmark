<?php

namespace emteknetnz\DevBuildBenchmark;

use SilverStripe\Reports\Report;
use SilverStripe\Security\Security;
use SilverStripe\Security\Permission;
use SilverStripe\ORM\ArrayList;

/**
 * Report showing summary of queries during the last dev/build
 */
class DevBuildBenchmarkSummaryReport extends Report
{
    protected $title = 'dev/build summary';

    protected $description = 'Summary of queries from the last dev/build';

    public function sourceRecords()
    {
        return DevBuildBenchmarkSummary::get();
    }

    public function canView($member = null)
    {
        if (!$member) {
            $member = Security::getCurrentUser();
        }
        return Permission::checkMember($member, 'ADMIN');
    }
}
