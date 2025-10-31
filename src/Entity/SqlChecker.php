<?php

namespace HealthCheckBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use HealthCheckBundle\Enum\SqlOperatorEnum;
use HealthCheckBundle\Repository\SqlCheckerRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

#[ORM\Entity(repositoryClass: SqlCheckerRepository::class)]
#[ORM\Table(name: 'health_sql_checker', options: ['comment' => 'SQL健康检查'])]
class SqlChecker implements \Stringable
{
    use TimestampableAware;
    use BlameableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private int $id = 0;

    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    #[ORM\Column(type: Types::STRING, length: 50, options: ['comment' => '检查名称'])]
    private string $name = '';

    #[Assert\NotBlank]
    #[Assert\Length(max: 65535)]
    #[ORM\Column(type: Types::TEXT, options: ['comment' => '检查SQL'])]
    private string $sql = '';

    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    #[ORM\Column(type: Types::STRING, length: 50, options: ['comment' => 'Cron表达式'])]
    private string $cronExpression = '0 * * * * *';

    #[Assert\Choice(choices: ['>', '>=', '<', '<=', '=', '!='])]
    #[ORM\Column(type: Types::STRING, enumType: SqlOperatorEnum::class, options: ['comment' => '操作符'])]
    private SqlOperatorEnum $operator = SqlOperatorEnum::EQ;

    #[Assert\GreaterThanOrEqual(value: 0)]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '对比值', 'default' => 0])]
    private int $compareValue = 0;

    #[Assert\Length(max: 1000)]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '备注'])]
    private ?string $remark = null;

    #[Assert\Type(type: 'bool')]
    #[IndexColumn(name: 'valid')]
    #[TrackColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    private ?bool $valid = false;

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getSql(): string
    {
        return $this->sql;
    }

    public function setSql(string $sql): void
    {
        $this->sql = $sql;
    }

    public function getCronExpression(): string
    {
        return $this->cronExpression;
    }

    public function setCronExpression(string $cronExpression): void
    {
        $this->cronExpression = $cronExpression;
    }

    public function getOperator(): SqlOperatorEnum
    {
        return $this->operator;
    }

    public function setOperator(SqlOperatorEnum $operator): void
    {
        $this->operator = $operator;
    }

    public function getCompareValue(): int
    {
        return $this->compareValue;
    }

    public function setCompareValue(int $compareValue): void
    {
        $this->compareValue = $compareValue;
    }

    public function getRemark(): ?string
    {
        return $this->remark;
    }

    public function setRemark(?string $remark): void
    {
        $this->remark = $remark;
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): void
    {
        $this->valid = $valid;
    }

    public function __toString(): string
    {
        return sprintf('SqlChecker[%d]: %s', $this->id, $this->name);
    }
}
