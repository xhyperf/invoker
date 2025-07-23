<?php

declare(strict_types=1);

namespace XHyperf\Invoker\Annotation;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;
use XHyperf\Invoker\StrategyCollector;
use XHyperf\Invoker\StrategyType;

/**
 * 策略注解
 * @Annotation
 * @Target({"METHOD"})
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Strategy extends AbstractAnnotation
{
    public function __construct(public StrategyType $type, public string $scope = '')
    {
    }

    public function collectMethod(string $className, ?string $target): void
    {
        StrategyCollector::register([$className, $target], $this->type, $this->scope);
    }
}