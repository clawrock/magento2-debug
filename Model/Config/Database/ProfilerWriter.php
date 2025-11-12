<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\Config\Database;

use Magento\Framework\Config\ConfigOptionsListConstants;
use Magento\Framework\Config\File\ConfigFilePool;
use Magento\Framework\DB\Profiler;

class ProfilerWriter
{
    public function __construct(
        private \Magento\Framework\App\DeploymentConfig\Writer $configWriter
    ) {
    }

    public function save(bool $flag): void
    {
        $configGroup = [
            ConfigOptionsListConstants::CONFIG_PATH_DB => [
                'connection' => [
                    'default' => [
                        'profiler' => [
                            'class' => Profiler::class,
                            'enabled' => $flag,
                        ],
                    ],
                ],
            ],
        ];

        $this->configWriter->saveConfig([ConfigFilePool::APP_ENV => $configGroup]);
    }
}
