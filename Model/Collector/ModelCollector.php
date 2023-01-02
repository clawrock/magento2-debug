<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\Collector;

use ClawRock\Debug\Logger\LoggableInterface;
use ClawRock\Debug\Model\ValueObject\LoopModelAction;
use ClawRock\Debug\Model\ValueObject\ModelAction;

class ModelCollector implements CollectorInterface, LoggerCollectorInterface
{
    const NAME = 'model';

    const LOAD_CALL_THRESHOLD = 20;

    const TOTAL_TIME = 'total_time';
    const METRICS    = 'metrics';
    const LOG        = 'log';
    const LOAD_LOOPS = 'load_loops';

    private \ClawRock\Debug\Helper\Config $config;
    private \ClawRock\Debug\Model\DataCollector $dataCollector;
    private \ClawRock\Debug\Logger\DataLogger $dataLogger;

    public function __construct(
        \ClawRock\Debug\Helper\Config $config,
        \ClawRock\Debug\Model\DataCollectorFactory $dataCollectorFactory,
        \ClawRock\Debug\Logger\DataLoggerFactory $dataLoggerFactory
    ) {
        $this->config = $config;
        $this->dataCollector = $dataCollectorFactory->create();
        $this->dataLogger = $dataLoggerFactory->create();
    }

    public function collect(): CollectorInterface
    {
        $totalTime = 0;
        $metrics = [
            ModelAction::LOAD      => 0,
            ModelAction::SAVE      => 0,
            ModelAction::DELETE    => 0,
            ModelAction::LOOP_LOAD => 0,
        ];
        $traceList = [];

        /** @var \ClawRock\Debug\Model\ValueObject\ModelAction $action */
        foreach ($this->dataLogger->getLogs() as $action) {
            switch ($action->getName()) {
                case ModelAction::LOAD:
                    $metrics[$action->getName()]++;
                    // Detect load actions in loops
                    $traceHash = $action->getTraceHash();
                    $actionLoopCount = 1;
                    $actionLoopTime = $action->getTime();
                    if (isset($traceList[$traceHash])) {
                        $actionLoopCount += $traceList[$traceHash]->getCount();
                        $actionLoopTime += $traceList[$traceHash]->getTime();
                    }
                    $traceList[$traceHash] = new LoopModelAction($action, $actionLoopTime, $actionLoopCount);
                    break;
                case ModelAction::SAVE:
                case ModelAction::DELETE:
                    $metrics[$action->getName()]++;
                    break;
            }

            $totalTime += $action->getTime();
        }

        $loadLoops = array_filter($traceList, function (LoopModelAction $action) {
            return $action->getCount() > 1;
        });

        usort($loadLoops, function (LoopModelAction $action1, LoopModelAction $action2) {
            return $action2->getCount() - $action1->getCount();
        });

        $metrics[ModelAction::LOOP_LOAD] = (int) array_reduce($loadLoops, function ($i, LoopModelAction $action) {
            return $i + $action->getCount();
        });

        $this->dataCollector->setData([
            self::TOTAL_TIME => $totalTime,
            self::METRICS    => $metrics,
            self::LOG        => $this->dataLogger->getLogs(),
            self::LOAD_LOOPS => $loadLoops,
        ]);

        return $this;
    }

    public function getLog(): array
    {
        return $this->dataCollector->getData(self::LOG) ?? [];
    }

    public function getTime(): string
    {
        return (string) ($this->dataCollector->getData(self::TOTAL_TIME) ?? 0);
    }

    public function getMetrics(): array
    {
        return $this->dataCollector->getData(self::METRICS) ?? [];
    }

    public function getMetric(string $key): int
    {
        return $this->dataCollector->getData(self::METRICS)[$key] ?? 0;
    }

    public function getLoadMetric(): int
    {
        return $this->getMetric(ModelAction::LOAD);
    }

    public function getDeleteMetric(): int
    {
        return $this->getMetric(ModelAction::DELETE);
    }

    public function getSaveMetric(): int
    {
        return $this->getMetric(ModelAction::SAVE);
    }

    public function getLoopLoadMetric(): int
    {
        return $this->getMetric(ModelAction::LOOP_LOAD);
    }

    public function getTotalActionsMetric(): int
    {
        return $this->getLoadMetric() + $this->getSaveMetric() + $this->getDeleteMetric();
    }

    public function getLoadCallThreshold(): int
    {
        return self::LOAD_CALL_THRESHOLD;
    }

    public function getLoadLoops(): array
    {
        return $this->dataCollector->getData(self::LOAD_LOOPS) ?? [];
    }

    public function isThresholdExceeded(): bool
    {
        return $this->getLoadMetric() > $this->getLoadCallThreshold();
    }

    public function getOperations(): array
    {
        return [ModelAction::SAVE, ModelAction::DELETE, ModelAction::LOAD];
    }

    public function getMetricName(string $metric): string
    {
        return ucfirst(str_replace('_', ' ', $metric));
    }

    public function isEnabled(): bool
    {
        return $this->config->isModelCollectorEnabled();
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
        if ($this->getLoopLoadMetric()) {
            return self::STATUS_ERROR;
        }

        if ($this->isThresholdExceeded()) {
            return self::STATUS_WARNING;
        }

        return self::STATUS_DEFAULT;
    }

    public function log(LoggableInterface $value): LoggerCollectorInterface
    {
        $this->dataLogger->log($value);

        return $this;
    }
}
