<?php

namespace HealthCheckBundle\Tests\Enum;

use HealthCheckBundle\Enum\SqlOperatorEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(SqlOperatorEnum::class)]
final class SqlOperatorEnumTest extends AbstractEnumTestCase
{
    public function testImplementsInterfaces(): void
    {
        $this->assertInstanceOf(Itemable::class, SqlOperatorEnum::GT);
        $this->assertInstanceOf(Labelable::class, SqlOperatorEnum::GT);
        $this->assertInstanceOf(Selectable::class, SqlOperatorEnum::GT);
    }

    public function testAllCases(): void
    {
        $cases = SqlOperatorEnum::cases();

        $this->assertCount(6, $cases);
        $this->assertContains(SqlOperatorEnum::GT, $cases);
        $this->assertContains(SqlOperatorEnum::GTE, $cases);
        $this->assertContains(SqlOperatorEnum::LT, $cases);
        $this->assertContains(SqlOperatorEnum::LTE, $cases);
        $this->assertContains(SqlOperatorEnum::EQ, $cases);
        $this->assertContains(SqlOperatorEnum::NEQ, $cases);
    }

    public function testValueUniqueness(): void
    {
        $values = array_map(fn (SqlOperatorEnum $case) => $case->value, SqlOperatorEnum::cases());
        $uniqueValues = array_unique($values);
        $this->assertCount(count($values), $uniqueValues, 'All enum values should be unique');
    }

    public function testLabelUniqueness(): void
    {
        $labels = array_map(fn (SqlOperatorEnum $case) => $case->getLabel(), SqlOperatorEnum::cases());
        $uniqueLabels = array_unique($labels);
        $this->assertCount(count($labels), $uniqueLabels, 'All enum labels should be unique');
    }

    public function testToArray(): void
    {
        // Test specific cases to verify correct array structure and content
        $gtArray = SqlOperatorEnum::GT->toArray();
        $this->assertIsArray($gtArray);
        $this->assertArrayHasKey('value', $gtArray);
        $this->assertArrayHasKey('label', $gtArray);
        $this->assertSame('>', $gtArray['value']);
        $this->assertSame('大于', $gtArray['label']);

        $eqArray = SqlOperatorEnum::EQ->toArray();
        $this->assertIsArray($eqArray);
        $this->assertArrayHasKey('value', $eqArray);
        $this->assertArrayHasKey('label', $eqArray);
        $this->assertSame('=', $eqArray['value']);
        $this->assertSame('等于', $eqArray['label']);

        // Test that toArray() consistently returns value and label for all cases
        foreach (SqlOperatorEnum::cases() as $case) {
            $array = $case->toArray();
            $this->assertIsArray($array, "toArray() should return array for case {$case->name}");
            $this->assertArrayHasKey('value', $array, "Array should have 'value' key for case {$case->name}");
            $this->assertArrayHasKey('label', $array, "Array should have 'label' key for case {$case->name}");
            $this->assertSame($case->value, $array['value'], "Value should match for case {$case->name}");
            $this->assertSame($case->getLabel(), $array['label'], "Label should match for case {$case->name}");
            $this->assertCount(2, $array, "Array should have exactly 2 elements for case {$case->name}");
        }
    }
}
