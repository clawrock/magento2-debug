<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\View\Renderer;

class QueryParametersRenderer implements RendererInterface
{
    private string $query;
    private array $parameters;
    private \Magento\Framework\App\ResourceConnection $resource;

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
            // @phpstan-ignore-next-line
            if (!array_key_exists($i, $parameters) && (false === $key || !array_key_exists($key, $parameters))) {
                return $matches[0];
            }
            $value  = array_key_exists($i, $parameters) ? $parameters[$i] : $parameters[$key];
            $result = $this->resource->getConnection()->quote($value);
            $i++;

            return $result;
        }, $this->query);

        return (string) $result;
    }
}
