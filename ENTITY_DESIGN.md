# SQLChecker Entity Design

This module contains a core database entity: **SqlChecker**, which is used to define and manage custom SQL health check items.

## Table: health_sql_checker

| Field          | Type               | Description     |
|----------------|--------------------|-----------------|
| id             | int, PK, auto-inc  | Primary key     |
| name           | string(50)         | Check name      |
| sql            | text               | SQL statement   |
| cronExpression | string(50)         | Cron expression |
| operator       | enum               | Operator        |
| compareValue   | int                | Compare value   |
| remark         | text, nullable     | Remark          |
| valid          | bool, default:0    | Is enabled      |
| createdBy      | string, nullable   | Created by      |
| updatedBy      | string, nullable   | Updated by      |
| createTime     | datetime, nullable | Created time    |
| updateTime     | datetime, nullable | Updated time    |

## Field Design Explanation

- **name**: The name of this SQL check, for management and display.
- **sql**: The SQL statement to execute, should return a single value.
- **cronExpression**: Cron-style expression for scheduling the check.
- **operator**: Comparison operator, supports `>`, `>=`, `<`, `<=`, `=`, `!=` for comparing with compareValue.
- **compareValue**: The value to compare the SQL result against.
- **valid**: Whether this check is enabled.
- **createdBy/updatedBy/createTime/updateTime**: For audit and traceability.

## Relationship

- Each SqlChecker is configured independently and not directly related to other tables.

## Example

```sql
SELECT COUNT(*) FROM users WHERE status = 'active';
```

Can be used to monitor if the number of active users meets expectations.
