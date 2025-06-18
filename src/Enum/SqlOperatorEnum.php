<?php

namespace HealthCheckBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum SqlOperatorEnum: string implements Itemable, Labelable, Selectable
{
    use ItemTrait;
    use SelectTrait;
    case GT = '>';
    case GTE = '>=';
    case LT = '<';
    case LTE = '<=';
    case EQ = '=';
    case NEQ = '!=';

    public function getLabel(): string
    {
        return match($this) {
            self::GT => '大于',
            self::GTE => '大于等于',
            self::LT => '小于',
            self::LTE => '小于等于',
            self::EQ => '等于',
            self::NEQ => '不等于',
        };
    }
}
