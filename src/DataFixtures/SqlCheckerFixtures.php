<?php

namespace HealthCheckBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use HealthCheckBundle\Entity\SqlChecker;
use HealthCheckBundle\Enum\SqlOperatorEnum;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class SqlCheckerFixtures extends Fixture implements FixtureGroupInterface
{
    public const SQL_CHECKER_USERS_COUNT_REFERENCE = 'sql-checker-users-count';
    public const SQL_CHECKER_ACTIVE_SESSIONS_REFERENCE = 'sql-checker-active-sessions';
    public const SQL_CHECKER_ERROR_LOGS_REFERENCE = 'sql-checker-error-logs';

    public function load(ObjectManager $manager): void
    {
        // 1. 用户数量检查器
        $usersCountChecker = new SqlChecker();
        $usersCountChecker->setName('用户总数检查');
        $usersCountChecker->setSql('SELECT COUNT(*) FROM users');
        $usersCountChecker->setCronExpression('0 */6 * * *');
        $usersCountChecker->setOperator(SqlOperatorEnum::GT);
        $usersCountChecker->setCompareValue(0);
        $usersCountChecker->setRemark('检查用户表是否有数据，每6小时执行一次');
        $usersCountChecker->setValid(true);

        $manager->persist($usersCountChecker);
        $this->addReference(self::SQL_CHECKER_USERS_COUNT_REFERENCE, $usersCountChecker);

        // 2. 活跃会话检查器
        $activeSessionsChecker = new SqlChecker();
        $activeSessionsChecker->setName('活跃会话检查');
        $activeSessionsChecker->setSql('SELECT COUNT(*) FROM sessions WHERE last_activity > DATE_SUB(NOW(), INTERVAL 1 HOUR)');
        $activeSessionsChecker->setCronExpression('*/15 * * * *');
        $activeSessionsChecker->setOperator(SqlOperatorEnum::LT);
        $activeSessionsChecker->setCompareValue(1000);
        $activeSessionsChecker->setRemark('检查活跃会话数量不超过1000，每15分钟执行一次');
        $activeSessionsChecker->setValid(true);

        $manager->persist($activeSessionsChecker);
        $this->addReference(self::SQL_CHECKER_ACTIVE_SESSIONS_REFERENCE, $activeSessionsChecker);

        // 3. 错误日志检查器
        $errorLogsChecker = new SqlChecker();
        $errorLogsChecker->setName('错误日志检查');
        $errorLogsChecker->setSql('SELECT COUNT(*) FROM error_logs WHERE created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)');
        $errorLogsChecker->setCronExpression('0 * * * *');
        $errorLogsChecker->setOperator(SqlOperatorEnum::LTE);
        $errorLogsChecker->setCompareValue(10);
        $errorLogsChecker->setRemark('检查每小时错误日志数量不超过10条');
        $errorLogsChecker->setValid(true);

        $manager->persist($errorLogsChecker);
        $this->addReference(self::SQL_CHECKER_ERROR_LOGS_REFERENCE, $errorLogsChecker);

        // 4. 禁用的检查器示例
        $disabledChecker = new SqlChecker();
        $disabledChecker->setName('禁用的检查器');
        $disabledChecker->setSql('SELECT 1');
        $disabledChecker->setCronExpression('0 0 * * *');
        $disabledChecker->setOperator(SqlOperatorEnum::EQ);
        $disabledChecker->setCompareValue(1);
        $disabledChecker->setRemark('这是一个已禁用的检查器示例');
        $disabledChecker->setValid(false);

        $manager->persist($disabledChecker);

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['health_check', 'sql_checker'];
    }
}
