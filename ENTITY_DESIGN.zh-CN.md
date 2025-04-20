# 健康检查 SQLChecker 实体设计说明

本模块包含一个核心数据库实体：**SqlChecker**，用于自定义和管理 SQL 健康检查项。

## health_sql_checker 表结构

| 字段名         | 类型             | 说明           |
|----------------|------------------|----------------|
| id             | int, PK, 自增    | 主键           |
| name           | string(50)       | 检查名称       |
| sql            | text             | 检查 SQL       |
| cronExpression | string(50)       | Cron 表达式    |
| operator       | enum             | 操作符         |
| compareValue   | int              | 对比值         |
| remark         | text, nullable   | 备注           |
| valid          | bool, default:0  | 是否有效       |
| createdBy      | string, nullable | 创建人         |
| updatedBy      | string, nullable | 更新人         |
| createTime     | datetime, nullable | 创建时间     |
| updateTime     | datetime, nullable | 更新时间     |

## 字段设计说明

- **name**：用于标识本条 SQL 检查的名称，便于管理和展示。
- **sql**：实际执行的 SQL 语句，要求返回单行单列的数值。
- **cronExpression**：健康检查的定时表达式，遵循 Cron 语法。
- **operator**：对比操作符，支持 `>`, `>=`, `<`, `<=`, `=`, `!=`，用于和 compareValue 对比。
- **compareValue**：与 SQL 返回值进行比较的目标值。
- **valid**：是否启用该检查项。
- **createdBy/updatedBy/createTime/updateTime**：用于审计追踪。

## 关联说明

- 每个 SqlChecker 独立配置，不与其他表直接关联。

## 示例

```sql
SELECT COUNT(*) FROM users WHERE status = 'active';
```

可用于监控活跃用户数是否达到预期。
