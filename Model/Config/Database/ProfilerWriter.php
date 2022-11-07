<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\Config\Database;

use Magento\Framework\Config\ConfigOptionsListConstants;
use Magento\Framework\Config\File\ConfigFilePool;
use Magento\Framework\DB\Profiler;

class ProfilerWriter
{
    private \Magento\Framework\App\DeploymentConfig\Writer $configWriter;

    public function __construct(
        \Magento\Framework\App\DeploymentConfig\Writer $configWriter
    ) {
        $this->configWriter = $configWriter;
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
