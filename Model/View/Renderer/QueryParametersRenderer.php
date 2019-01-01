<?php

namespace ClawRock\Debug\Model\View\Renderer;

use Magento\Framework\App\ResourceConnection;

class QueryParametersRenderer implements RendererInterface
{
    /**
     * @var string
     */
    private $query;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resource;

    public function __construct(
        string $query,
        array $parameters,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->query = $query;
        $this->parameters = $parameters;
        $this->resource = $resource;
    }

    public function render(): string
    {
        $parameters = $this->parameters;
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
        }, $this->query);

        return $result;
    }
}
