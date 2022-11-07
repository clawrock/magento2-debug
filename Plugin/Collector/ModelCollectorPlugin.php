<?php
declare(strict_types=1);

namespace ClawRock\Debug\Plugin\Collector;

use ClawRock\Debug\Model\ValueObject\ModelAction;
use Magento\Framework\Model\ResourceModel\AbstractResource;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class ModelCollectorPlugin
{
    private \ClawRock\Debug\Model\Collector\ModelCollector $modelCollector;
    private \ClawRock\Debug\Helper\Formatter $formatter;
    private \ClawRock\Debug\Helper\Debug $debug;

    public function __construct(
        \ClawRock\Debug\Model\Collector\ModelCollector $modelCollector,
        \ClawRock\Debug\Helper\Formatter $formatter,
        \ClawRock\Debug\Helper\Debug $debug
    ) {
        $this->modelCollector = $modelCollector;
        $this->formatter = $formatter;
        $this->debug = $debug;
    }

    /**
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $subject
     * @param callable $proceed
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param mixed $value
     * @param mixed $field
     * @return mixed
     */
    public function aroundLoad(AbstractResource $subject, callable $proceed, $object, $value, $field = null)
    {
        $time = microtime(true);
        $result = $proceed($object, $value, $field);
        $trace = $this->debug->getBacktrace([
            ModelAction::LOAD,
            ModelAction::SAVE,
            ModelAction::DELETE,
        ], DEBUG_BACKTRACE_IGNORE_ARGS);

        $this->modelCollector->log(new ModelAction(
            ModelAction::LOAD,
            get_class($object),
            (float) $this->formatter->microtime(microtime(true) - $time),
            $trace
        ));

        return $result;
    }

    /**
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $subject
     * @param callable $proceed
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return mixed
     */
    public function aroundSave(AbstractResource $subject, callable $proceed, $object)
    {
        $time = microtime(true);
        $result = $proceed($object);
        $trace = $this->debug->getBacktrace([
            ModelAction::LOAD,
            ModelAction::SAVE,
            ModelAction::DELETE,
        ], DEBUG_BACKTRACE_IGNORE_ARGS);

        $this->modelCollector->log(new ModelAction(
            ModelAction::SAVE,
            get_class($object),
            (float) $this->formatter->microtime(microtime(true) - $time),
            $trace
        ));

        return $result;
    }

    /**
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $subject
     * @param callable $proceed
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return mixed
     */
    public function aroundDelete(AbstractResource $subject, callable $proceed, $object)
    {
        $time = microtime(true);
        $result = $proceed($object);
        $trace = $this->debug->getBacktrace([
            ModelAction::LOAD,
            ModelAction::SAVE,
            ModelAction::DELETE,
        ], DEBUG_BACKTRACE_IGNORE_ARGS);

        $this->modelCollector->log(new ModelAction(
            ModelAction::DELETE,
            get_class($object),
            (float) $this->formatter->microtime(microtime(true) - $time),
            $trace
        ));

        return $result;
    }
}
