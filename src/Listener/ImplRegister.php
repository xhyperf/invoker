<?php

namespace XHyperf\Invoker\Listener;

use Hyperf\Contract\ContainerInterface;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\BootApplication;
use XHyperf\Invoker\Annotation\Impl;
use XHyperf\Invoker\Reflection;

/**
 * 注册 Impl 注解
 */
class ImplRegister implements ListenerInterface
{
    public function __construct(protected ContainerInterface $container)
    {
    }

    public function listen(): array
    {
        return [
            BootApplication::class,
        ];
    }

    public function process(object $event): void
    {
        if (! $event instanceof BootApplication) {
            return;
        }

        $classes = AnnotationCollector::getClassesByAnnotation(Impl::class);
        foreach (array_keys($classes) as $class) {
            foreach (Reflection::reflectClass($class)->getInterfaces() as $interface) {
                $name = $interface->getName();
                if (! $this->container->has($name)) {
                    $this->container->define($name, $class);
                }
            }
        }
    }
}