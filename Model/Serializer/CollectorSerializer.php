<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\Serializer;

use ClawRock\Debug\Exception\CollectorNotFoundException;

class CollectorSerializer
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;
    private \ClawRock\Debug\Logger\Logger $logger;
    private \ClawRock\Debug\Helper\Config $config;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \ClawRock\Debug\Logger\Logger $logger,
        \ClawRock\Debug\Helper\Config $config
    ) {
        $this->objectManager = $objectManager;
        $this->logger = $logger;
        $this->config = $config;
    }

    /**
     * @param \ClawRock\Debug\Model\Collector\CollectorInterface[] $collectors
     * @return array
     */
    public function serialize(array $collectors): array
    {
        foreach ($collectors as &$collector) {
            $collector = $collector->getData();
        }

        return $collectors;
    }

    public function unserialize(array $data): array
    {
        $collectors = [];
        foreach ($data as $name => $collector) {
            try {
                $collectorClass = $this->config->getCollectorClass($name);
                $collectors[$name] = $this->objectManager->create($collectorClass)->setData($collector);
            } catch (CollectorNotFoundException $e) {
                $this->logger->error(sprintf('ClawRock_Debug: collector "%s" not found', $name), ['exception' => $e]);
                continue;
            }
        }

        return $collectors;
    }
}
