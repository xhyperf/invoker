<?php

declare(strict_types=1);

namespace XHyperf\Invoker;

use Hyperf\Collection\Arr;
use Hyperf\Di\MetadataCollector;
use XHyperf\Invoker\Annotation\Mapper;
use ReflectionAttribute;
use ReflectionException;

/**
 * 策略收集器
 */
class StrategyCollector extends MetadataCollector
{
    protected static array $container = [];

    /**
     * 注解收集器添加策略处理器
     * @param array        $handler callable 调用数组
     * @param StrategyType $type    策略类型枚举
     * @param string       $scope   作用域
     * @return void
     */
    public static function register(array $handler, StrategyType $type, string $scope = ''): void
    {
        try {
            $parameters = Reflection::reflectMethod(...$handler)->getParameters();
        } catch (ReflectionException) {
            $parameters = [];
        }

        $mapper = [];
        foreach ($parameters as $parameter) {
            $name = $parameter->getName();
            foreach ($parameter->getAttributes(Mapper::class, ReflectionAttribute::IS_INSTANCEOF) as $attr) {
                foreach ($attr->newInstance()->src as $src) {
                    $mapper[$src] = $name;
                }
            }
            // 默认的参数名放在最后，优先级最低
            $mapper[$name] = $name;
        }

        [$namespace, $type] = self::getType($type, $scope);

        static::$container[$namespace][$type] = [$handler, $mapper];
    }

    /**
     * 收集器中是否包含策略处理器
     * @param StrategyType|null $rule  策略类型枚举
     * @param string            $scope 作用域
     * @return bool
     */
    public static function hasHandler(?StrategyType $rule, string $scope = ''): bool
    {
        if (! $rule) {
            return false;
        }

        [$namespace, $rule] = self::getType($rule, $scope);

        return isset(static::$container[$namespace][$rule]);
    }

    /**
     * 获取策略处理器
     * @param StrategyType|null $type  策略类型枚举
     * @param string            $scope 作用域
     * @return array|null
     */
    public static function getHandler(?StrategyType $type, string $scope = ''): array|null
    {
        if (! $type) {
            return null;
        }

        [$namespace, $type] = self::getType($type, $scope);

        return Arr::get(static::$container, $namespace . '.' . $type);
    }

    /**
     * 获取策略类型
     * @param StrategyType $type  策略类型枚举
     * @param string       $scope 作用域
     * @return array
     */
    public static function getType(StrategyType $type, string $scope = ''): array
    {
        return [$scope ?: $type->namespace(), $type->name()];
    }

    /**
     * 重写清除收集器的方法，因为数据结构不一样，基类的清除方法会造成数据丢失
     * @param string|null $key 类名
     * @return void
     */
    public static function clear(?string $key = null): void
    {
        if ($key) {
            foreach (static::$container as $namespace => $types) {
                foreach ($types as $type => $handler) {
                    if ($handler[0][0] == $key) {
                        unset(static::$container[$namespace][$type]);
                    }
                }
            }
        } else {
            static::$container = [];
        }
    }
}