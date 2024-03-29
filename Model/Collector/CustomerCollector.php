<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\Collector;

class CustomerCollector implements CollectorInterface
{
    public const NAME = 'customer';
    public const LOGGED_IN = 'logged_in';
    public const CUSTOMER_ID = 'customer_id';
    public const CUSTOMER_EMAIL = 'customer_email';
    public const CUSTOMER_NAME = 'customer_name';
    public const CUSTOMER_GROUP_ID = 'customer_group_id';
    public const CUSTOMER_GROUP_CODE = 'customer_group_code';
    public const CUSTOMER_TAX_CLASS_ID = 'customer_tax_class_id';
    public const CUSTOMER_TAX_CLASS_NAME = 'customer_tax_class_name';

    private \ClawRock\Debug\Helper\Config $config;
    private \ClawRock\Debug\Model\DataCollector $dataCollector;
    private \ClawRock\Debug\Model\Info\CustomerInfo $customerInfo;

    public function __construct(
        \ClawRock\Debug\Helper\Config $config,
        \ClawRock\Debug\Model\DataCollectorFactory $dataCollectorFactory,
        \ClawRock\Debug\Model\Info\CustomerInfo $customerInfo
    ) {
        $this->config = $config;
        $this->dataCollector = $dataCollectorFactory->create();
        $this->customerInfo = $customerInfo;
    }

    public function collect(): CollectorInterface
    {
        $this->dataCollector->setData([
            self::LOGGED_IN               => $this->customerInfo->isLoggedIn(),
            self::CUSTOMER_ID             => $this->customerInfo->getCustomer()->getId(),
            self::CUSTOMER_EMAIL          => $this->customerInfo->getCustomer()->getEmail(),
            self::CUSTOMER_NAME           => $this->customerInfo->getCustomer()->getName(),
            self::CUSTOMER_GROUP_ID       => $this->customerInfo->getCustomer()->getGroupId(),
            self::CUSTOMER_GROUP_CODE     => $this->customerInfo->getGroup()->getCode(),
            self::CUSTOMER_TAX_CLASS_ID   => $this->customerInfo->getCustomer()->getTaxClassId(),
            self::CUSTOMER_TAX_CLASS_NAME => $this->customerInfo->getGroup()->getTaxClassName(),
        ]);

        return $this;
    }

    public function isLoggedIn(): bool
    {
        return (bool) $this->dataCollector->getData(self::LOGGED_IN);
    }

    public function getCustomerId(): string
    {
        return (string) $this->dataCollector->getData(self::CUSTOMER_ID);
    }

    public function getCustomerEmail(): string
    {
        return $this->dataCollector->getData(self::CUSTOMER_EMAIL) ?? '';
    }

    public function getCustomerName(): string
    {
        return $this->dataCollector->getData(self::CUSTOMER_NAME) ?? '';
    }

    public function getCustomerGroupId(): string
    {
        return (string) $this->dataCollector->getData(self::CUSTOMER_GROUP_ID);
    }

    public function getCustomerGroupCode(): string
    {
        return (string) $this->dataCollector->getData(self::CUSTOMER_GROUP_CODE);
    }

    public function getCustomerTaxClassId(): string
    {
        return (string) $this->dataCollector->getData(self::CUSTOMER_TAX_CLASS_ID);
    }

    public function getCustomerTaxClassName(): string
    {
        return (string) $this->dataCollector->getData(self::CUSTOMER_TAX_CLASS_NAME);
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isEnabled(): bool
    {
        return $this->config->isCustomerCollectorEnabled() && $this->config->isFrontend();
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
        return self::STATUS_DEFAULT;
    }
}
