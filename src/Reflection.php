<?php

declare(strict_types=1);

namespace XHyperf\Invoker;

use Hyperf\Context\ApplicationContext;
use Hyperf\Di\ReflectionManager;
use InvalidArgumentException;

use function Hyperf\Collection\data_get;
use function Hyperf\Collection\value;

class Reflection extends ReflectionManager
{
    /**
     * 反射获取方法的参数及默认值
     * @param string $className 类名
     * @param string $method    方法名
     * @return array
     * @throws
     */
    public static function reflectParameters(string $className, string $method): array
    {
        $key = $className . '::' . $method;
        if (isset(static::$container['parameters'][$key])) {
            return static::$container['parameters'][$key];
        }

        if (! class_exists($className)) {
            throw new InvalidArgumentException("Class {$className} not exist");
        }

        return static::$container['parameters'][$key] = value(function () use ($className, $method) {
            $parameters = static::reflectMethod($className, $method)->getParameters();

            $result = [];
            foreach ($parameters as $param) {
                $result[$param->getName()] = $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null;
            }

            return $result;
        });
    }
}
