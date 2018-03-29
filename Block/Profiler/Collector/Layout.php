<?php

namespace ClawRock\Debug\Block\Profiler\Collector;

use ClawRock\Debug\Block\Profiler\Collector;

class Layout extends Collector
{
    /**
     * @var array
     */
    private static $colors = [
        'block'    => '#dfd',
        'macro'    => '#ddf',
        'template' => '#ffd',
        'big'      => '#d44',
    ];

    /**
     * @var array
     */
    protected $tree;

    public function renderTree()
    {
        $html = '';
        foreach ($this->getCallTree() as $node) {
            $html .= $this->renderNode($node);
        }

        return '<pre>' . $html . '</pre>';
    }

    protected function renderNode($node, $prefix = '', $sibling = false)
    {
        if (!$node['parent_id']) {
            $start = $node['name'];
        } else {
            if (isset($node['template'])) {
                $start = $this->_formatTemplate($node, $prefix);
            } else {
                $start = $this->_formatBlock($node, $prefix);
            }
            $prefix .= $sibling ? '│ ' : '  ';
        }
        $percent = $node['render_time_percent'] *= 100;

        if (false && $node['render_time'] * 1000 < 1) {
            $str = $start . "\n";
        } else {
            $str = sprintf("%s %s\n", $start, $this->_formatTime($node, $percent));
        }

        if ($node['children']) {
            $nCount = count($node['children']);
            $index  = 0;
            foreach ($node['children'] as $childNode) {
                $index++;
                $str .= $this->renderNode($childNode, $prefix, $index !== $nCount);
            }
        }
        return $str;
    }


    public function getCallTree()
    {
        if ($this->tree === null) {
            $this->tree = $this->createCallTree();
        }

        return $this->tree;
    }

    protected function createCallTree()
    {
        $tree = [];
        $collector       = $this->getCollector();
        $totalRenderTime = $collector->getRenderTime();
        $nodeList        = $collector->getBlocksData();

        foreach ($nodeList as $id => &$node) {
            $node['render_time_percent'] = $node['render_time'] / $totalRenderTime;
        }

        foreach ($nodeList as $id => &$node) {
            if ($node['parent_id'] === false) {
                $this->resolveChildren($node, $nodeList);
                $tree[$id] = $node;
            }
        }

        return $tree;
    }

    protected function resolveChildren(&$node, array $nodeList)
    {
        $children = [];
        if (isset($node['children'])) {
            foreach ($node['children'] as $childId) {
                $child = $nodeList[$childId];

                $this->resolveChildren($child, $nodeList);
                $children[$childId] = $child;
            }
        }
        $node['children'] = $children;
    }


    protected function _formatTemplate($blockData, $prefix)
    {
        return sprintf(
            '%s└ <span style="background-color: %s">%s <small>%s::%s</small></span>',
            $prefix,
            self::$colors['template'],
            $blockData['name'],
            $blockData['class'],
            $blockData['template']
        );
    }

    protected function _formatBlock($blockData, $prefix)
    {
        return sprintf(
            '%s└ <span style="background-color: %s">%s <small>%s</small></span>',
            $prefix, self::$colors['block'],
            $blockData['name'],
            $blockData['class']
        );
    }

    protected function _formatTime($node, $percent)
    {
        return sprintf(
            '<span style="color: %s">%.2fms/%.0f%% (excl. %.2fms)</span>',
            $percent > 20 ? self::$colors['big'] : 'auto', $node['render_time'],
            $percent,
            $node['render_time']
        );
    }
}
