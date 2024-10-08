# Silverstripe dev/build benchmark

Will benchmark the time taken to run queries during the last `dev/build` on a Silverstripe application, including deployments, and show them in a report format in the CMS

![screenshot](screenshot.png)

## Requirements

This module works with Silverstripe 4 and 5.

You must be using MySQL or MariaDB to use this module. The default `MySQLDatabase` class will be replaced with the `DevBuildBenchmarkMySQLDatabase` class in this module via injector. If you have already replaced the `MySQLDatabase` class with another class then this module will not work.

## Installation

```bash
composer require emteknetnz/silverstripe-dev-build-benchmark
```

The next time `dev/build` is run, the benchmark data will be recorded and reports will be ready to view in the CMS.

## Reports

There are two reports available. You must have `ADMIN` permissions to view the reports.

### dev/build summary

The `dev/build summary` report (`DevBuildBenchmarkSummary`) shows the total time in seconds taken for queries, which are grouped together. This is always enabled.

### dev/build queries

The `dev/build queries` report (`DevBuildBenchmarkQuery`) will show the time taken in seconds for each `dev/build` run. This is useful for identifying slow queries that may be affecting the performance of your application. Not enabled by default and must be configured to enable.

> [!WARNING]
> The `dev/build queries` report will show **ALL** database queries run during `dev/build`.
>
> It is up to the developer installing this module to be mindful of any sensitive information that may be displayed in the report, for instance any queries run during `requireDefaultRecords()`.
>
> Be sure you are aware of what will be logged before deploying this module to an environment with sensitive data e.g. production.

To enable the `dev/build queries` report, add the following to your `config.yml`:

```yaml
emteknetnz\DevBuildBenchmark\DatabaseAdminExtension:
  store_query_data: true
```
