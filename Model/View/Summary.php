<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\View;

use ClawRock\Debug\Api\Data\ProfileInterface;
use ClawRock\Debug\Model\ValueObject\Redirect;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Summary implements ArgumentInterface
{
    private \ClawRock\Debug\Model\Storage\ProfileMemoryStorage $profileMemoryStorage;
    private \ClawRock\Debug\Helper\Url $url;
    private \ClawRock\Debug\Model\View\Renderer\RedirectRendererFactory $redirectRendererFactory;

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

    public function getProfilerUrl(string $token): string
    {
        return $this->url->getProfilerUrl($token);
    }

    public function renderRedirect(Redirect $redirect): string
    {
        return $this->redirectRendererFactory->create(['redirect' => $redirect])->render();
    }
}
