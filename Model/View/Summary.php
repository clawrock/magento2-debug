<?php

namespace ClawRock\Debug\Model\View;

use ClawRock\Debug\Api\Data\ProfileInterface;
use ClawRock\Debug\Model\ValueObject\Redirect;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Summary implements ArgumentInterface
{
    /**
     * @var \ClawRock\Debug\Model\Storage\ProfileMemoryStorage
     */
    private $profileMemoryStorage;

    /**
     * @var \ClawRock\Debug\Helper\Url
     */
    private $url;

    /**
     * @var \ClawRock\Debug\Model\View\Renderer\RedirectRendererFactory
     */
    private $redirectRendererFactory;

    public function __construct(
        \ClawRock\Debug\Model\Storage\ProfileMemoryStorage $profileMemoryStorage,
        \ClawRock\Debug\Helper\Url $url,
        \ClawRock\Debug\Model\View\Renderer\RedirectRendererFactory $redirectRendererFactory
    ) {
        $this->profileMemoryStorage = $profileMemoryStorage;
        $this->url = $url;
        $this->redirectRendererFactory = $redirectRendererFactory;
    }

    public function getProfile(): ProfileInterface
    {
        return $this->profileMemoryStorage->read();
    }

    public function getProfilerUrl($token): string
    {
        return $this->url->getProfilerUrl($token);
    }

    public function renderRedirect(Redirect $redirect): string
    {
        return $this->redirectRendererFactory->create(['redirect' => $redirect])->render();
    }
}
