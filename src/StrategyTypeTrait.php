<?php

declare(strict_types=1);

namespace XHyperf\Invoker;

use BackedEnum;
use XHyperf\Invoker\Exception\StrategyException;
use UnitEnum;

/**
 * 策略类型 trait
 */
trait StrategyTypeTrait
{
    /**
     * 获取策略类型的命名空间
     * @return string
     */
    public function namespace(): string
    {
        return static::class;
    }

    /**
     * 获取策略类型名称
     * @return string
     * @throws
     */
    public function name(): string
    {
        return match (true) {
            $this instanceof BackedEnum => $this->value,
            $this instanceof UnitEnum => $this->name,
            default => throw new StrategyException('This trait must in enum'),
        };
    }
}