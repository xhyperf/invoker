<?php

declare(strict_types=1);

namespace XHyperf\Invoker;

use Hyperf\Context\ApplicationContext;

use function Hyperf\Collection\data_get;
use function Hyperf\Collection\value;

class Invoker
{
    /**
     * 策略调用
     * @param StrategyType|null $type    策略类型枚举
     * @param array             $args    参数
     * @param mixed|null        $default 默认值，策略执行方法不存在时返回
     * @param string            $scope   作用域，用于区分同一策略类型枚举在不同场景下的调用
     * @param bool              $strict  是否严格模式，为true时，空参数都会使用默认值
     * @return mixed
     */
    public static function strategy(?StrategyType $type, array $args = [], mixed $default = null, string $scope = '', bool $strict = false): mixed
    {
        if (! $handler = StrategyCollector::getHandler($type, $scope)) {
            return value($default, $args);
        }

        return static::reflect($handler[0], $args, $handler[1], $strict);
    }

    /**
     * 自动适配参数的反射调用
     * @param array $callable callable 调用数组
     * @param array $args     参数
     * @param array $mapper   参数映射，格式 源参数 key => 转换后的目标参数 key
     * @param bool  $strict   是否严格模式，为true时，空参数都会使用默认值
     * @return mixed
     * @throws
     */
    public static function reflect(array $callable, array $args = [], array $mapper = [], bool $strict = false): mixed
    {
        [$class, $method] = $callable;

        if (is_string($class)) {
            $class = ApplicationContext::getContainer()->get($class);
        }

        $className = get_class($class);

        // 返回获取的默认参数值
        $reflect    = Reflection::reflectParameters($className, $method);
        $parameters = [];

        $missingValue = random_bytes(10);

        // 转换参数映射
        $argsMapper = [];
        foreach ($mapper as $src => $target) {
            $val = data_get($args, $src, $missingValue);
            if ($val !== $missingValue && ! isset($argsMapper[$target])) {
                $argsMapper[$target] = $val;
            }
        }

        $argsMapper = $argsMapper ?: $args; // 没有映射时使用原参数

        foreach ($reflect as $name => $default) {
            // 参数值获取顺序：注解key -> 参数key -> 默认值或null
            $val = data_get($argsMapper, $name, $default);

            $parameters[] = $strict ? ($val ?: $default) : $val;
        }

        return $class->{$method}(...$parameters);
    }
}