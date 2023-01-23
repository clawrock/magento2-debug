<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\View;

use ClawRock\Debug\Api\Data\ProfileInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Toolbar implements ArgumentInterface
{
    private const COLLECTOR_PLACEHOLDER = 'debug.toolbar.collectors.%s';

    private ?\ClawRock\Debug\Api\Data\ProfileInterface $profile = null;
    /** @var \ClawRock\Debug\Model\Collector\CollectorInterface[]|null */
    private ?array $collectors = null;
    private \Magento\Framework\View\LayoutInterface $layout;
    private \ClawRock\Debug\Model\Storage\ProfileMemoryStorage $profileMemoryStorage;
    private \ClawRock\Debug\Helper\Url $url;

    public function __construct(
        \Magento\Framework\View\LayoutInterface $layout,
        \ClawRock\Debug\Model\Storage\ProfileMemoryStorage $profileMemoryStorage,
        \ClawRock\Debug\Helper\Url $url
    ) {
        $this->layout = $layout;
        $this->profileMemoryStorage = $profileMemoryStorage;
        $this->url = $url;
    }

    public function getToken(): string
    {
        return $this->getProfile()->getToken();
    }

    public function getCollectors(): array
    {
        if ($this->collectors === null) {
            $this->collectors = $this->getProfile()->getCollectors();
        }

        return $this->collectors;
    }

    public function getCollectorBlocks(): array
    {
        $blocks = [];

        foreach ($this->getCollectors() as $name => $collector) {
            /** @var \ClawRock\Debug\Model\Collector\CollectorInterface $collector */
            if (!$block = $this->layout->getBlock(sprintf(self::COLLECTOR_PLACEHOLDER, $name))) {
                continue;
            }
            /** @var \Magento\Framework\View\Element\Template $block */
            $block->setData('collector', $collector);
            $block->setData('profiler_url', $this->url->getProfilerUrl($this->getToken(), $collector->getName()));
            $blocks[$collector->getName()] = $block;
        }

        return $blocks;
    }

    public function getToolbarUrl(): string
    {
        return $this->url->getToolbarUrl($this->getToken());
    }

    private function getProfile(): ProfileInterface
    {
        if ($this->profile === null) {
            $this->profile = $this->profileMemoryStorage->read();
        }

        return $this->profile;
    }
}
