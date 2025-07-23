<?php

declare(strict_types=1);

namespace XHyperf\Invoker;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
            ],
            'listeners'    => [
            ],
            'annotations'  => [
                'scan' => [
                    'collectors' => [
                        StrategyCollector::class,
                    ],
                ],
            ],
            'aspects'      => [
            ],
            'publish'      => [
            ],
        ];
    }
}
