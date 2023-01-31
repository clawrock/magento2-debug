<?php
declare(strict_types=1);

namespace ClawRock\Debug\Plugin;

use Magento\Framework\View\LayoutInterface;

class PreventMessageBlockInitByToolbarPlugin
{
    /**
     * @param \Magento\Framework\View\LayoutInterface $subject
     * @param callable $proceed
     * @param array|string $messageGroups
     * @return void
     */
    public function aroundInitMessages(LayoutInterface $subject, callable $proceed, $messageGroups = [])
    {
        $handles = $subject->getUpdate()->getHandles();
        if ($handles === ['clawrock_debug']) {
            // Block is initialized by layout collector, messages can't be initialized
            // because there will be no messages during block rendering
            return;
        }

        $proceed($messageGroups);
    }
}
