# 健康检查组件（Health Check Bundle）

[English](README.md) | [中文](README.zh-CN.md)

[![PHP Version](https://img.shields.io/packagist/php-v/tourze/health-check-bundle.svg?style=flat-square)]
(https://packagist.org/packages/tourze/health-check-bundle)
[![License](https://img.shields.io/github/license/tourze/health-check-bundle.svg?style=flat-square)]
(https://github.com/tourze/health-check-bundle/blob/master/LICENSE)
[![Build Status](https://img.shields.io/travis/tourze/health-check-bundle/master.svg?style=flat-square)]
(https://travis-ci.org/tourze/health-check-bundle)
[![Code Coverage](https://img.shields.io/codecov/c/github/tourze/health-check-bundle.svg?style=flat-square)]
(https://codecov.io/gh/tourze/health-check-bundle)

简洁高效的健康检查组件，支持多种健康检查方式，便于服务监控与自动化运维。

## 功能特性

- 支持自定义 SQL 检查，灵活配置监控项
- 内置目录权限、磁盘空间、扩展加载等常用健康检查
- 支持 Cron 表达式定时检查
- 检查结果统计与可视化输出
- 易于扩展，支持自定义检查器

## 安装说明

```bash
composer require tourze/health-check-bundle
```

## 快速开始

1. 注册 Bundle 到 Symfony 项目：
    - 自动发现或在 `config/bundles.php` 添加：

     ```php
     HealthCheckBundle\HealthCheckBundle::class => ['all' => true],
     ```

2. 配置数据库连接，并确保 `health_sql_checker` 表已创建。
3. 运行健康检查命令：

   ```bash
   php bin/console health-check:run
   ```

4. 查看终端输出的检查统计信息。

## 高级用法

### 自定义健康检查器

通过实现 `CheckInterface` 创建自定义健康检查器：

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
        // 自定义检查逻辑
        if ($condition) {
            return new Success('检查通过');
        }
        
        return new Failure('检查失败');
    }
    
    public function getLabel(): string
    {
        return '自定义健康检查';
    }
}
```

### SQL 健康检查

SQL 健康检查允许您监控数据库状态：

```php
// 示例：检查活跃用户数量
$sqlChecker = new SqlChecker();
$sqlChecker->setName('活跃用户检查');
$sqlChecker->setSql('SELECT COUNT(*) FROM users WHERE active = 1');
$sqlChecker->setOperator(SqlOperatorEnum::GT);
$sqlChecker->setCompareValue(0);
$sqlChecker->setCronExpression('0 */5 * * * *'); // 每5分钟执行一次
```

## 安全说明

### 数据保护
- SQL 查询使用参数化查询防止注入攻击
- 生产环境应限制健康检查端点的访问权限
- 敏感的健康检查结果不应公开暴露

### 最佳实践
- 定期对自定义健康检查器进行安全审计
- 监控健康检查日志中的可疑活动
- 为不同部署环境使用特定的配置

## 依赖说明

### 环境要求
- PHP 8.1 及以上
- Symfony 7.3 及以上
- Doctrine ORM、Laminas Diagnostics 等

### 核心依赖
- `doctrine/orm` ^3.0 - 实体管理和数据库操作
- `laminas/laminas-diagnostics` ^1.27 - 核心健康检查功能
- `symfony/framework-bundle` ^7.3 - Symfony 框架集成
- `dragonmantank/cron-expression` ^3.4 - Cron 表达式解析

### 内部依赖
- `tourze/bundle-dependency` - Bundle 依赖管理
- `tourze/doctrine-indexed-bundle` - 数据库索引管理
- `tourze/doctrine-timestamp-bundle` - 实体时间戳管理
- `tourze/doctrine-track-bundle` - 实体跟踪功能
- `tourze/doctrine-user-bundle` - 实体用户归属
- `tourze/enum-extra` - 增强枚举功能

## 详细文档

- 可通过实现 `Laminas\Diagnostics\Check\CheckInterface` 添加自定义检查项
- SQL 检查项可在后台或数据库表中灵活配置
- 支持多种内置检查器：
  - **目录权限检查**：验证缓存、日志和数据目录的写入权限
  - **磁盘空间检查**：监控可用磁盘空间
  - **PHP 扩展检查**：确保所需的 PHP 扩展已加载

### SQL 检查项配置说明

| 字段           | 说明         |
|----------------|--------------|
| name           | 检查名称     |
| sql            | 检查 SQL     |
| cronExpression | Cron 表达式  |
| operator       | 操作符（>, >=, <, <=, =, !=）|
| compareValue   | 对比值       |
| remark         | 备注         |
| valid          | 是否有效     |

## 贡献指南

- 欢迎提交 Issue 与 PR
- 遵循 PSR 代码规范，需通过 phpstan/phpunit 测试
- 详细贡献流程见主仓库文档

## 版权和许可

- 本项目基于 MIT 协议开源
- 版权所有 © tourze

## 更新日志

详见 [CHANGELOG](CHANGELOG.md)
