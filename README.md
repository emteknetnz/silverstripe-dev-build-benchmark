# Silverstripe dev/build benchmark

Will benchmark the time taken to run every individual query during the last `dev/build` on a Silverstripe application. This includes deployments.

There is a `dev/build benchmark` report (`DevBuildBenchmarkReport`) available in the CMS which will show the time taken for each `dev/build` run. The report is sorted by time taken, with the slowest queries at the top.

This report is only available to users with the `ADMIN` permission.

> [!CAUTION]
> The generated report will show **ALL** database queries run during `dev/build`.
>
> It is up to the developer installing this module to be mindful any sensitive information that may be displayed in the report, for instance any queries run during `requireDefaultRecords()`.
>
> Be sure you are aware of what will be logged before deploying this module to an environment with sensitive data e.g. production.

## Installation

```bash
composer require emteknetnz/silverstripe-dev-build-benchmark
```

Simply install this module. The next time `dev/build` is run, the benchmark data will be recorded in the `DevBuildBenchmark` database table.

This modules with with Silverstripe 4 and 5.
