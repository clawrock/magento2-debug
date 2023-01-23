<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\Collector;

use ClawRock\Debug\Logger\LoggableInterface;

class LayoutCollector implements CollectorInterface, LoggerCollectorInterface
{
    public const NAME = 'layout';
    public const BLOCK_PROFILER_ID_KEY = 'debug_profiler_id';
    public const BLOCK_START_RENDER_KEY = 'debug_start_render';
    public const BLOCK_STOP_RENDER_KEY = 'debug_stop_render';
    public const BLOCK_RENDER_TIME_KEY = 'debug_render_time';
    public const BLOCK_HASH_KEY = 'debug_hash';
    public const BLOCK_PARENT_PROFILER_ID_KEY = 'debug_profiler_parent_id';
    public const HANDLES = 'handles';
    public const BLOCKS = 'blocks';
    public const BLOCKS_CREATED = 'blocks_created';
    public const BLOCKS_RENDERED = 'blocks_rendered';
    public const BLOCKS_NOT_RENDERED = 'blocks_not_rendered';
    public const TOTAL_RENDER_TIME = 'total_render_time';
    public const RENDER_TIME = 'render_time';

    private \ClawRock\Debug\Helper\Config $config;
    private \ClawRock\Debug\Model\DataCollector $dataCollector;
    private \ClawRock\Debug\Logger\DataLogger $dataLogger;
    private \ClawRock\Debug\Model\Info\LayoutInfo $layoutInfo;
    private \ClawRock\Debug\Helper\Formatter $formatter;

    public function __construct(
        \ClawRock\Debug\Helper\Config $config,
        \ClawRock\Debug\Model\DataCollectorFactory $dataCollectorFactory,
        \ClawRock\Debug\Logger\DataLoggerFactory $dataLogger,
        \ClawRock\Debug\Model\Info\LayoutInfo $layoutInfo,
        \ClawRock\Debug\Helper\Formatter $formatter
    ) {
        $this->config = $config;
        $this->dataCollector = $dataCollectorFactory->create();
        $this->dataLogger = $dataLogger->create();
        $this->layoutInfo = $layoutInfo;
        $this->formatter = $formatter;
    }

    public function collect(): CollectorInterface
    {
        $renderTime = 0;

        /** @var \ClawRock\Debug\Model\ValueObject\Block $block */
        foreach ($this->dataLogger->getLogs() as $block) {
            $renderTime += $block->getRenderTime();
        }

        $this->dataCollector->setData([
            self::TOTAL_RENDER_TIME   => $renderTime,
            self::HANDLES             => $this->layoutInfo->getHandles(),
            self::BLOCKS_CREATED      => $this->layoutInfo->getCreatedBlocks(),
            self::BLOCKS_RENDERED     => $this->dataLogger->getLogs(),
            self::BLOCKS_NOT_RENDERED => $this->layoutInfo->getNotRenderedBlocks(),
        ]);

        return $this;
    }

    public function getRenderTime(): string
    {
        return $this->formatter->microtime($this->dataCollector->getData(self::TOTAL_RENDER_TIME));
    }

    public function getHandles(): array
    {
        return $this->dataCollector->getData(self::HANDLES) ?? [];
    }

    public function getCreatedBlocks(): array
    {
        return $this->dataCollector->getData(self::BLOCKS_CREATED) ?? [];
    }

    public function getRenderedBlocks(): array
    {
        return $this->dataCollector->getData(self::BLOCKS_RENDERED) ?? [];
    }

    public function getNotRenderedBlocks(): array
    {
        return $this->dataCollector->getData(self::BLOCKS_NOT_RENDERED) ?? [];
    }

    public function isEnabled(): bool
    {
        return $this->config->isLayoutCollectorEnabled();
    }

    public function getData(): array
    {
        return $this->dataCollector->getData();
    }

    public function setData(array $data): CollectorInterface
    {
        $this->dataCollector->setData($data);

        return $this;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getStatus(): string
    {
        if (!empty($this->getNotRenderedBlocks())) {
            return self::STATUS_WARNING;
        }

        return self::STATUS_DEFAULT;
    }

    public function log(LoggableInterface $value): LoggerCollectorInterface
    {
        $this->dataLogger->log($value);

        return $this;
    }
}
