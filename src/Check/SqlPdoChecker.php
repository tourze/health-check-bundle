<?php

namespace HealthCheckBundle\Check;

use Cron\CronExpression;
use Doctrine\DBAL\Connection;
use HealthCheckBundle\Entity\SqlChecker;
use HealthCheckBundle\Enum\SqlOperatorEnum;
use Laminas\Diagnostics\Check\CheckInterface;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Skip;
use Laminas\Diagnostics\Result\Success;

class SqlPdoChecker implements CheckInterface
{
    public function __construct(
        private readonly SqlChecker $sqlChecker,
        private readonly Connection $connection,
    ) {
    }

    public function check(): ResultInterface
    {
        $cron = new CronExpression($this->sqlChecker->getCronExpression());
        if (!$cron->isDue()) {
            return new Skip('不在检查时间范围内');
        }

        try {
            $stmt = $this->connection->executeQuery($this->sqlChecker->getSql());
            $result = $stmt->fetchOne();

            if ($result === false) {
                return new Failure(sprintf(
                    '[%s] SQL执行失败: 未返回数据',
                    $this->sqlChecker->getName()
                ));
            }

            $value = (int)$result;
            $compareValue = $this->sqlChecker->getCompareValue();
            $operator = $this->sqlChecker->getOperator();

            $isValid = match ($operator) {
                SqlOperatorEnum::GT => $value > $compareValue,
                SqlOperatorEnum::GTE => $value >= $compareValue,
                SqlOperatorEnum::LT => $value < $compareValue,
                SqlOperatorEnum::LTE => $value <= $compareValue,
                SqlOperatorEnum::EQ => $value === $compareValue,
                SqlOperatorEnum::NEQ => $value !== $compareValue,
            };

            if ($isValid) {
                return new Success(sprintf(
                    '[%s] SQL检查通过: %d %s %d',
                    $this->sqlChecker->getName(),
                    $value,
                    $operator->value,
                    $compareValue
                ));
            }

            return new Failure(sprintf(
                '[%s] SQL检查未通过: %d %s %d',
                $this->sqlChecker->getName(),
                $value,
                $operator->value,
                $compareValue
            ));
        } catch (\Throwable $e) {
            return new Failure(sprintf(
                '[%s] SQL执行失败: %s',
                $this->sqlChecker->getName(),
                $e->getMessage()
            ));
        }
    }

    public function getLabel(): string
    {
        return sprintf('SQL检查: %s', $this->sqlChecker->getName());
    }
}
