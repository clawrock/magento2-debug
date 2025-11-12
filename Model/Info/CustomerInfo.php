<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\Info;

use Magento\Customer\Api\Data\GroupInterface;
use Magento\Customer\Model\Customer;

class CustomerInfo
{
    public function __construct(
        private \Magento\Customer\Model\Session $session,
        private \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        private \Magento\Customer\Api\Data\GroupInterfaceFactory $groupInterfaceFactory
    ) {
    }

    public function isLoggedIn(): bool
    {
        return $this->session->isLoggedIn();
    }

    public function getCustomer(): Customer
    {
        return $this->session->getCustomer();
    }

    public function getGroup(): GroupInterface
    {
        try {
            $group = $this->groupRepository->getById($this->getCustomer()->getGroupId());
        } catch (\Exception $e) {
            $group = $this->groupInterfaceFactory->create();
        }

        return $group;
    }
}
