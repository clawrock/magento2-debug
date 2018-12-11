<?php

namespace ClawRock\Debug\Model\Info;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\GroupInterface;
use Magento\Customer\Model\Customer;

class CustomerInfo
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $session;

    /**
     * @var \Magento\Customer\Api\GroupRepositoryInterface
     */
    private $groupRepository;

    /**
     * @var \Magento\Customer\Api\Data\GroupInterfaceFactory
     */
    private $groupInterfaceFactory;

    public function __construct(
        \Magento\Customer\Model\Session\Proxy $session,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Customer\Api\Data\GroupInterfaceFactory $groupInterfaceFactory
    ) {
        $this->session = $session;
        $this->groupRepository = $groupRepository;
        $this->groupInterfaceFactory = $groupInterfaceFactory;
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
