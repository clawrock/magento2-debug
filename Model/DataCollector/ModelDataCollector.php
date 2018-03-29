<?php

namespace ClawRock\Debug\Model\DataCollector;

use Magento\Framework\Model\AbstractModel;

class ModelDataCollector extends AbstractDataCollector
{
    const NAME = 'model';

    const LOAD_CALL_THRESHOLD = 20;

    const ACTION_LOAD   = 'load';
    const ACTION_SAVE   = 'save';
    const ACTION_DELETE = 'delete';
    const LOOP_LOAD     = 'loop_load';

    const TOTAL_TIME = 'total_time';
    const METRICS    = 'metrics';
    const LOG        = 'log';

    const MODEL      = 'model';
    const ACTION     = 'action';
    const TRACE      = 'trace';
    const TRACE_HASH = 'trace_hash';
    const TIME       = 'time';
    const COUNT      = 'count';

    /**
     * @var array
     */
    protected $log = [];

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param \Magento\Framework\App\Request\Http  $request
     * @param \Magento\Framework\App\Response\Http $response
     * @return $this
     */
    public function collect(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\App\Response\Http $response
    ) {
        $metrics = [
            self::ACTION_LOAD   => 0,
            self::ACTION_SAVE   => 0,
            self::ACTION_DELETE => 0,
            self::LOOP_LOAD     => 0,
        ];

        $time = 0;
        $traceList = [];

        foreach ($this->log as &$log) {
            switch ($log[self::ACTION]) {
                case self::ACTION_LOAD:
                    $metrics[$log[self::ACTION]]++;
                    // Detect load actions in loops
                    $traceHash = $log[self::TRACE_HASH];
                    if (!isset($traceList[$traceHash])) {
                        $traceList[$traceHash] = 0;
                    }
                    $traceList[$traceHash]++;
                    break;
                case self::ACTION_SAVE:
                case self::ACTION_DELETE:
                    $metrics[$log[self::ACTION]]++;
                    break;
            }

            $time += $log[self::TIME];
        }

        $traceList = array_filter($traceList, function ($count) {
            return $count > 1;
        });

        $metrics[self::LOOP_LOAD] = array_sum($traceList);

        $this->data = [
            self::TOTAL_TIME => $time,
            self::METRICS    => $metrics,
            self::LOG        => $this->log,
        ];

        return $this;
    }

    public function isEnabled()
    {
        return true;
    }

    public function logLoad(AbstractModel $object, $time)
    {
        return $this->logAction(self::ACTION_LOAD, $object, $time);
    }

    protected function logAction($action, AbstractModel $object, $time)
    {
        $trace = $this->parseBacktrace($this->getBacktrace(DEBUG_BACKTRACE_IGNORE_ARGS));
        $this->log[] = [
            self::ACTION     => $action,
            self::MODEL      => get_class($object),
            self::TIME       => $time,
            self::TRACE      => $trace,
            self::TRACE_HASH => md5(serialize($trace)),
        ];
    }

    public function logSave(AbstractModel $object, $time)
    {
        return $this->logAction(self::ACTION_SAVE, $object, $time);
    }

    public function getLoadLoopCalls()
    {
        $traceList = [];

        foreach ($this->getLog() as $item) {
            if ($item[self::ACTION] !== self::ACTION_LOAD) {
                continue;
            }
            $traceHash = $item[self::TRACE_HASH];
            if (!isset($traceList[$traceHash])) {
                $item[self::COUNT] = 0;
                $item[self::TIME] = 0;
                $traceList[$traceHash] = $item;
            }
            $traceList[$traceHash][self::COUNT]++;
            $traceList[$traceHash][self::TIME] += $item[self::TIME];
        }

        $traceList = array_filter($traceList, function ($trace) {
            return $trace[self::COUNT] > 1;
        });

        usort($traceList, function ($trace1, $trace2) {
            return $trace2[self::COUNT] - $trace1[self::COUNT];
        });

        return array_values($traceList);
    }

    public function logDelete(AbstractModel $object, $time)
    {
        return $this->logAction(self::ACTION_DELETE, $object, $time);
    }

    public function getLog()
    {
        return $this->data[self::LOG] ?? [];
    }

    public function getTime()
    {
        return sprintf('%0.2f', $this->data[self::TOTAL_TIME] ?? 0 * 1000);
    }

    public function getMetric($key = null)
    {
        if ($key === null) {
            return $this->data[self::METRICS] ?? [];
        }

        return $this->data[self::METRICS][$key] ?? null;
    }

    public function getLoadCallThreshold()
    {
        return self::LOAD_CALL_THRESHOLD;
    }

    protected function parseBacktrace(array $backtrace)
    {
        $item = reset($backtrace);
        while ($item && !$this->isBacktraceItemValid($item)) {
            array_shift($backtrace);
            $item = reset($backtrace);
        }

        $backtrace = array_map(function ($item) {
            unset($item['object'], $item['args'], $item['type']);

            return $item;
        }, $backtrace);

        return $backtrace;
    }

    protected function isBacktraceItemValid($data)
    {
        if (!is_array($data)) {
            return false;
        }

        if (!isset($data['class'], $data['function'])) {
            return false;
        }

        if (!in_array($data['function'], [self::ACTION_LOAD, self::ACTION_SAVE, self::ACTION_DELETE])) {
            return false;
        }

        return true;
    }
}
