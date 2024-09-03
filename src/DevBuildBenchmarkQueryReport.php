<?php

namespace emteknetnz\DevBuildBenchmark;

use SilverStripe\Reports\Report;
use SilverStripe\Security\Security;
use SilverStripe\Security\Permission;

/**
 * Report showing benchmark data for every query during the last dev/build, if configured to do so
 */
class DevBuildBenchmarkQueryReport extends Report
{
    protected $title = 'dev/build queries';

    protected $description = 'Queries from the last dev/build';

    public function sourceRecords()
    {
        return DevBuildBenchmarkQuery::get();
    }

    public function canView($member = null)
    {
        if (!$member) {
            $member = Security::getCurrentUser();
        }
        return Permission::checkMember($member, 'ADMIN');
    }
}
