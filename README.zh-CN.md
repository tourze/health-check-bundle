# 健康检查组件（Health Check Bundle）

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/health-check-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/health-check-bundle)
[![Build Status](https://img.shields.io/travis/tourze/health-check-bundle/master.svg?style=flat-square)](https://travis-ci.org/tourze/health-check-bundle)
[![Quality Score](https://img.shields.io/scrutinizer/g/tourze/health-check-bundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/tourze/health-check-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/health-check-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/health-check-bundle)

简洁高效的健康检查组件，支持多种健康检查方式，便于服务监控与自动化运维。

## 功能特性

- 支持自定义 SQL 检查，灵活配置监控项
- 内置目录权限、磁盘空间、扩展加载等常用健康检查
- 支持 Cron 表达式定时检查
- 检查结果统计与可视化输出
- 易于扩展，支持自定义检查器

## 安装说明

- 依赖 PHP 8.1 及以上
- 依赖 Symfony 6.4 及以上
- 依赖 Doctrine ORM、Laminas Diagnostics 等

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

## 详细文档

- 可通过实现 `Laminas\Diagnostics\Check\CheckInterface` 添加自定义检查项
- SQL 检查项可在后台或数据库表中灵活配置
- 支持多种内置检查器：
  - 目录权限检查
  - 磁盘空间检查
  - PHP 扩展依赖检查

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
