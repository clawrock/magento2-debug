<?php

namespace ClawRock\Debug\Block\Profiler\Renderer;

use Magento\Framework\App\ResourceConnection;

class QueryRenderer extends DefaultRenderer
{
    /**
     * @var string[]
     */
    protected $formattedQueries = [];

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\ResourceConnection $resource,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->resource = $resource;
    }

    public function replaceQueryParameters($query, array $parameters)
    {
        $i = !array_key_exists(0, $parameters) && array_key_exists(1, $parameters) ? 1 : 0;

        $result = preg_replace_callback('/\?|((?<!:):[a-z0-9_]+)/i', function ($matches) use ($parameters, &$i) {
            $key = $matches[0];
            if (!array_key_exists($i, $parameters) && (false === $key || !array_key_exists($key, $parameters))) {
                return $matches[0];
            }
            $value  = array_key_exists($i, $parameters) ? $parameters[$i] : $parameters[$key];
            $result = $this->resource->getConnection(ResourceConnection::DEFAULT_CONNECTION)->quote($value);
            $i++;
            return $result;
        }, $query);

        return $result;
    }

    public function formatQuery($sql, $highlightOnly = false)
    {
        $cacheKey = md5($sql . ($highlightOnly ? '1' : 0));
        if (isset($this->formattedQueries[$cacheKey])) {
            return $this->formattedQueries[$cacheKey];
        }

        \SqlFormatter::$cli = false;
        \SqlFormatter::$pre_attributes = 'class="highlight highlight-sql"';
        \SqlFormatter::$quote_attributes = 'class="string"';
        \SqlFormatter::$backtick_quote_attributes = 'class="string"';
        \SqlFormatter::$reserved_attributes = 'class="keyword"';
        \SqlFormatter::$boundary_attributes = 'class="symbol"';
        \SqlFormatter::$number_attributes = 'class="number"';
        \SqlFormatter::$word_attributes = 'class="word"';
        \SqlFormatter::$error_attributes = 'class="error"';
        \SqlFormatter::$comment_attributes = 'class="comment"';
        \SqlFormatter::$variable_attributes = 'class="variable"';

        if ($highlightOnly) {
            $html = \SqlFormatter::highlight($sql);
            $html = preg_replace('/<pre class=".*">([^"]*+)<\/pre>/Us', '\1', $html);
        } else {
            $html = \SqlFormatter::format($sql);
            $html = preg_replace('/<pre class="(.*)">([^"]*+)<\/pre>/Us', '<div class="\1"><pre>\2</pre></div>', $html);
        }

        return $this->formattedQueries[$cacheKey] = $html;
    }

    protected function _construct()
    {
        $this->setData('template', 'ClawRock_Debug::profiler/renderer/query.phtml');
        parent::_construct();
    }
}
