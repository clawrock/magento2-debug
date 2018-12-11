<?php

namespace ClawRock\Debug\Helper;

class Debug
{
    public function isDebugClass($class)
    {
        return strpos($class, 'ClawRock\Debug') === 0;
    }

    public function getBacktrace(array $functions, int $options = DEBUG_BACKTRACE_PROVIDE_OBJECT): array
    {
        if (!function_exists('debug_backtrace')) {
            return [];
        }

        $backtrace = debug_backtrace($options);

        $item = reset($backtrace);
        while ($item && !$this->isBacktraceItemValid($item, $functions)) {
            array_shift($backtrace);
            $item = reset($backtrace);
        }

        $backtrace = array_map(function ($item) {
            unset($item['object'], $item['args'], $item['type']);

            return $item;
        }, $backtrace);

        return $backtrace;
    }

    private function isBacktraceItemValid(array $data, array $functions): bool
    {
        if (!isset($data['class'], $data['function'])) {
            return false;
        }

        if (empty($functions)) {
            return true;
        }

        if (!in_array($data['function'], $functions)) {
            return false;
        }

        return true;
    }
}
