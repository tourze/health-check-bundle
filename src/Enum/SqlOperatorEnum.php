<?php

namespace HealthCheckBundle\Enum;

enum SqlOperatorEnum: string
{
    case GT = '>';
    case GTE = '>=';
    case LT = '<';
    case LTE = '<=';
    case EQ = '=';
    case NEQ = '!=';
}
