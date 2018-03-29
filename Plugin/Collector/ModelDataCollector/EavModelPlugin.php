<?php

namespace ClawRock\Debug\Plugin\Collector\ModelDataCollector;

use Magento\Eav\Model\Entity\AbstractEntity;

class EavModelPlugin
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

    public function aroundLoad(AbstractEntity $subject, callable $proceed, $object, $entityId, $attributes = [])
    {
        $time = microtime(true);
        $result = $proceed($object, $entityId, $attributes);
        $this->dataCollector->logLoad($object, microtime(true) - $time);

        return $result;
    }

    public function aroundSave(AbstractEntity $subject, callable $proceed, $object)
    {
        $time = microtime(true);
        $result = $proceed($object);
        $this->dataCollector->logSave($object, microtime(true) - $time);

        return $result;
    }

    public function aroundDelete(AbstractEntity $subject, callable $proceed, $object)
    {
        $time = microtime(true);
        $result = $proceed($object);
        $this->dataCollector->logDelete($object, microtime(true) - $time);

        return $result;
    }
}
