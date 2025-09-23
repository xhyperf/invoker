<?php

declare(strict_types=1);

namespace XHyperf\Invoker;

use BackedEnum;
use UnitEnum;

/**
 * 策略类型接口
 * @mixin BackedEnum
 */
interface StrategyType extends UnitEnum
{
    /**
     * 获取策略类型的命名空间
     */
    public function namespace(): string;

    /**
     * 获取策略类型名称
     */
    public function name(): string;
}