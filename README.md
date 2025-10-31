# Health Check Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![PHP Version](https://img.shields.io/packagist/php-v/tourze/health-check-bundle.svg?style=flat-square)]
(https://packagist.org/packages/tourze/health-check-bundle)
[![License](https://img.shields.io/github/license/tourze/health-check-bundle.svg?style=flat-square)]
(https://github.com/tourze/health-check-bundle/blob/master/LICENSE)
[![Build Status](https://img.shields.io/travis/tourze/health-check-bundle/master.svg?style=flat-square)]
(https://travis-ci.org/tourze/health-check-bundle)
[![Code Coverage](https://img.shields.io/codecov/c/github/tourze/health-check-bundle.svg?style=flat-square)]
(https://codecov.io/gh/tourze/health-check-bundle)

A concise and efficient health check bundle for Symfony, supporting various check types for service 
monitoring and automated operations.

## Features

- Custom SQL health checks with flexible configuration
- Built-in checks for directory permissions, disk usage, PHP extensions, etc.
- Cron expression support for scheduled checks
- Summary and visualization of check results
- Easily extensible with custom checkers

## Installation

```bash
composer require tourze/health-check-bundle
```

## Quick Start

1. Register the bundle in your Symfony project:
    - Auto-discovery or add to `config/bundles.php`:

     ```php
     HealthCheckBundle\HealthCheckBundle::class => ['all' => true],
     ```

2. Configure database connection and ensure the `health_sql_checker` table is created.
3. Run health checks:

   ```bash
   php bin/console health-check:run
   ```

4. Review the summary output in the terminal.

## Advanced Usage

### Custom Health Checkers

Create custom health checkers by implementing the `CheckInterface`:

```php
<?php

use Laminas\Diagnostics\Check\CheckInterface;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Success;
use Laminas\Diagnostics\Result\Failure;

class CustomChecker implements CheckInterface
{
    public function check(): ResultInterface
    {
        // Your custom logic here
        if ($condition) {
            return new Success('Check passed');
        }
        
        return new Failure('Check failed');
    }
    
    public function getLabel(): string
    {
        return 'Custom Health Check';
    }
}
```

### SQL Health Checks

SQL health checks allow you to monitor database conditions:

```php
// Example: Check active user count
$sqlChecker = new SqlChecker();
$sqlChecker->setName('Active Users Check');
$sqlChecker->setSql('SELECT COUNT(*) FROM users WHERE active = 1');
$sqlChecker->setOperator(SqlOperatorEnum::GT);
$sqlChecker->setCompareValue(0);
$sqlChecker->setCronExpression('0 */5 * * * *'); // Every 5 minutes
```

## Security

### Data Protection
- SQL queries are parameterized to prevent injection attacks
- Access to health check endpoints should be restricted in production
- Sensitive health check results should not be exposed publicly

### Best Practices
- Regular security audits of custom health checkers
- Monitor health check logs for suspicious activities
- Use environment-specific configurations for different deployment stages

## Dependencies

### Requirements
- PHP 8.1+
- Symfony 7.3+
- Doctrine ORM, Laminas Diagnostics, and related dependencies

### Core Dependencies
- `doctrine/orm` ^3.0 - For entity management and database operations
- `laminas/laminas-diagnostics` ^1.27 - Core health check functionality
- `symfony/framework-bundle` ^7.3 - Symfony framework integration
- `dragonmantank/cron-expression` ^3.4 - Cron expression parsing

### Internal Dependencies
- `tourze/bundle-dependency` - Bundle dependency management
- `tourze/doctrine-indexed-bundle` - Database index management
- `tourze/doctrine-timestamp-bundle` - Timestamp management for entities
- `tourze/doctrine-track-bundle` - Entity tracking functionality
- `tourze/doctrine-user-bundle` - User attribution for entities
- `tourze/enum-extra` - Enhanced enum functionality

## Documentation

- Add custom checks by implementing `Laminas\Diagnostics\Check\CheckInterface`
- SQL checks can be configured in the admin panel or directly in the database
- Built-in checkers include:
  - **Directory Permission Check**: Verifies cache, logs, and data directory write permissions
  - **Disk Usage Check**: Monitors available disk space
  - **PHP Extension Check**: Ensures required PHP extensions are loaded

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
