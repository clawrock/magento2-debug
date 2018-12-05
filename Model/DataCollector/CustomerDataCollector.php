<?php

namespace ClawRock\Debug\Model\DataCollector;

use Magento\Framework\App\Area;

class CustomerDataCollector extends AbstractDataCollector
{
    const NAME = 'customer';

    const LOGGED_IN               = 'logged_in';
    const CUSTOMER_ID             = 'customer_id';
    const CUSTOMER_EMAIL          = 'customer_email';
    const CUSTOMER_NAME           = 'customer_name';
    const CUSTOMER_GROUP_ID       = 'customer_group_id';
    const CUSTOMER_GROUP_CODE     = 'customer_group_code';
    const CUSTOMER_TAX_CLASS_ID   = 'customer_tax_class_id';
    const CUSTOMER_TAX_CLASS_NAME = 'customer_tax_class_name';

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Customer\Api\GroupRepositoryInterface
     */
    protected $groupRepository;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $appState;

    public function __construct(
        \ClawRock\Debug\Helper\Profiler $helper,
        \Magento\Customer\Model\Session $session,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Framework\App\State $appState
    ) {
        parent::__construct($helper);

        $this->session = $session;
        $this->groupRepository = $groupRepository;
        $this->appState = $appState;
    }

    /**
     * @param \Magento\Framework\HTTP\PhpEnvironment\Request  $request
     * @param \Magento\Framework\HTTP\PhpEnvironment\Response $response
     * @return $this;
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function collect(
        \Magento\Framework\HTTP\PhpEnvironment\Request $request,
        \Magento\Framework\HTTP\PhpEnvironment\Response $response
    ) {
        $customer = $this->session->getCustomer();

        try {
            $group = $this->groupRepository->getById($customer->getGroupId());
        } catch (\Exception $e) {
            $group = new \Magento\Framework\DataObject();
        }

        $this->data = [
            self::LOGGED_IN               => $this->session->isLoggedIn(),
            self::CUSTOMER_ID             => $customer->getId(),
            self::CUSTOMER_EMAIL          => $customer->getEmail(),
            self::CUSTOMER_NAME           => $customer->getName(),
            self::CUSTOMER_GROUP_ID       => $customer->getGroupId(),
            self::CUSTOMER_GROUP_CODE     => $group->getCode(),
            self::CUSTOMER_TAX_CLASS_ID   => $customer->getTaxClassId(),
            self::CUSTOMER_TAX_CLASS_NAME => $group->getTaxClassName(),
        ];

        return $this;
    }

    public function isLoggedIn()
    {
        return $this->data[self::LOGGED_IN] ?? false;
    }

    public function getCustomerId()
    {
        return $this->data[self::CUSTOMER_ID] ?? null;
    }

    public function getCustomerEmail()
    {
        return $this->data[self::CUSTOMER_EMAIL] ?? null;
    }

    public function getCustomerName()
    {
        return $this->data[self::CUSTOMER_NAME] ?? null;
    }

    public function getCustomerGroupId()
    {
        return $this->data[self::CUSTOMER_GROUP_ID] ?? null;
    }

    public function getCustomerGroupCode()
    {
        return $this->data[self::CUSTOMER_GROUP_CODE] ?? null;
    }

    public function getCustomerTaxClassId()
    {
        return $this->data[self::CUSTOMER_TAX_CLASS_ID] ?? null;
    }

    public function getCustomerTaxClassName()
    {
        return $this->data[self::CUSTOMER_TAX_CLASS_NAME] ?? null;
    }

    public function isEnabled()
    {
        try {
            return $this->helper->isCustomerDataCollectorEnabled()
                && $this->appState->getAreaCode() === Area::AREA_FRONTEND;
        } catch (\Exception $e) {
            return false;
        }
    }
}
