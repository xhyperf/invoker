<?php

declare(strict_types=1);

namespace XHyperf\Invoker\Annotation;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;

/**
 * 参数映射注解，设置当前参数的数据可以从哪些字段获取其值
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
class Mapper extends AbstractAnnotation
{
    /**
     * @var array|string[] 来源 key
     */
    public readonly array $src;

    /**
     * @param string ...$src 当前参数的来源 key
     */
    public function __construct(string ...$src)
    {
        $this->src = $src;
    }
}