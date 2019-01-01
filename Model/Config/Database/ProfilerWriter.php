<?php

namespace ClawRock\Debug\Model\Config\Database;

use Magento\Framework\Config\ConfigOptionsListConstants;
use Magento\Framework\Config\File\ConfigFilePool;
use Magento\Framework\DB\Profiler;

class ProfilerWriter
{
    /**
     * @var \Magento\Framework\App\DeploymentConfig\Writer
     */
    private $configWriter;

    public function __construct(
        \Magento\Framework\App\DeploymentConfig\Writer $configWriter
    ) {
        $this->configWriter = $configWriter;
    }

    /**
     * @param bool $flag
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function save(bool $flag)
    {
        $configGroup = [
            ConfigOptionsListConstants::CONFIG_PATH_DB => [
                'connection' => [
                    'default' => [
                        'profiler' => [
                            'class' => Profiler::class,
                            'enabled' => $flag
                        ]
                    ]
                ]
            ]
        ];

        $this->configWriter->saveConfig([ConfigFilePool::APP_ENV => $configGroup]);
    }
}
