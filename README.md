# Health Check Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/health-check-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/health-check-bundle)
[![Build Status](https://img.shields.io/travis/tourze/health-check-bundle/master.svg?style=flat-square)](https://travis-ci.org/tourze/health-check-bundle)
[![Quality Score](https://img.shields.io/scrutinizer/g/tourze/health-check-bundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/tourze/health-check-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/health-check-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/health-check-bundle)

A concise and efficient health check bundle for Symfony, supporting various check types for service monitoring and automated operations.

## Features

- Custom SQL health checks with flexible configuration
- Built-in checks for directory permissions, disk usage, PHP extensions, etc.
- Cron expression support for scheduled checks
- Summary and visualization of check results
- Easily extensible with custom checkers

## Installation

- Requires PHP 8.1+
- Requires Symfony 6.4+
- Requires Doctrine ORM, Laminas Diagnostics, and related dependencies

```bash
composer require tourze/health-check-bundle
```

## Quick Start

1. Register the bundle in your Symfony project:
   - Auto-discovery or add to `config/bundles.php`:

     ```php
     HealthCheckBundle\\HealthCheckBundle::class => ['all' => true],
     ```

2. Configure database connection and ensure the `health_sql_checker` table is created.
3. Run health checks:

   ```bash
   php bin/console health-check:run
   ```

4. Review the summary output in the terminal.

## Documentation

- Add custom checks by implementing `Laminas\\Diagnostics\\Check\\CheckInterface`
- SQL checks can be configured in the admin panel or directly in the database
- Built-in checkers include:
  - Directory permission check
  - Disk usage check
  - PHP extension dependency check

### SQL Checker Configuration

| Field          | Description      |
|----------------|-----------------|
| name           | Check name      |
| sql            | SQL to execute  |
| cronExpression | Cron expression |
| operator       | Operator (>, >=, <, <=, =, !=) |
| compareValue   | Value to compare|
| remark         | Remark          |
| valid          | Is enabled      |

## Contributing

- Issues and PRs are welcome
- Follows PSR coding standards; must pass phpstan/phpunit tests
- See main repository for detailed contribution guide

## License

- MIT License
- © tourze

## Changelog

See [CHANGELOG](CHANGELOG.md)
