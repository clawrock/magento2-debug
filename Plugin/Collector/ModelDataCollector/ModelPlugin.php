<?php

namespace ClawRock\Debug\Plugin\Collector\ModelDataCollector;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ModelPlugin
{
    /**
     * @var \ClawRock\Debug\Model\DataCollector\ModelDataCollector
     */
    protected $dataCollector;

    public function __construct(
        \ClawRock\Debug\Model\DataCollector\ModelDataCollector $dataCollector
    ) {
        $this->dataCollector = $dataCollector;
    }

    public function aroundLoad(AbstractDb $subject, callable $proceed, $object, $value, $field = null)
    {
        $time = microtime(true);
        $result = $proceed($object, $value, $field);
        $this->dataCollector->logLoad($object, microtime(true) - $time);

        return $result;
    }

    public function aroundSave(AbstractDb $subject, callable $proceed, $object)
    {
        $time = microtime(true);
        $result = $proceed($object);
        $this->dataCollector->logSave($object, microtime(true) - $time);

        return $result;
    }

    public function aroundDelete(AbstractDb $subject, callable $proceed, $object)
    {
        $time = microtime(true);
        $result = $proceed($object);
        $this->dataCollector->logDelete($object, microtime(true) - $time);

        return $result;
    }
}
