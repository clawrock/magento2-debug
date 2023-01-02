<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\View;

use ClawRock\Debug\Api\Data\ProfileInterface;
use ClawRock\Debug\Helper\Formatter;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 */
class Profiler implements ArgumentInterface
{
    private \ClawRock\Debug\Model\View\Renderer\TraceRendererFactory $traceRendererFactory;
    private \ClawRock\Debug\Model\View\Renderer\LayoutGraphRendererFactory $layoutGraphRendererFactory;
    private \ClawRock\Debug\Model\View\Renderer\ParametersRendererFactory $parametersRendererFactory;
    private \ClawRock\Debug\Model\View\Renderer\QueryParametersRendererFactory $queryParametersRendererFactory;
    private \ClawRock\Debug\Model\View\Renderer\QueryRendererFactory $queryRendererFactory;
    private \ClawRock\Debug\Model\View\Renderer\QueryListRendererFactory $queryListRendererFactory;
    private \ClawRock\Debug\Model\View\Renderer\TableRendererFactory $tableRendererFactory;
    private \ClawRock\Debug\Model\View\Renderer\VarRenderer $varRenderer;
    private \ClawRock\Debug\Model\Storage\ProfileMemoryStorage $profileMemoryStorage;
    private \ClawRock\Debug\Helper\Formatter $formatter;

    public function __construct(
        \ClawRock\Debug\Model\View\Renderer\TraceRendererFactory $traceRendererFactory,
        \ClawRock\Debug\Model\View\Renderer\LayoutGraphRendererFactory $layoutGraphRendererFactory,
        \ClawRock\Debug\Model\View\Renderer\ParametersRendererFactory $parametersRendererFactory,
        \ClawRock\Debug\Model\View\Renderer\QueryParametersRendererFactory $queryParametersRendererFactory,
        \ClawRock\Debug\Model\View\Renderer\QueryRendererFactory $queryRendererFactory,
        \ClawRock\Debug\Model\View\Renderer\QueryListRendererFactory $queryListRendererFactory,
        \ClawRock\Debug\Model\View\Renderer\TableRendererFactory $tableRendererFactory,
        \ClawRock\Debug\Model\View\Renderer\VarRenderer $varRenderer,
        \ClawRock\Debug\Model\Storage\ProfileMemoryStorage $profileMemoryStorage,
        \ClawRock\Debug\Helper\Formatter $formatter
    ) {
        $this->traceRendererFactory = $traceRendererFactory;
        $this->layoutGraphRendererFactory = $layoutGraphRendererFactory;
        $this->parametersRendererFactory = $parametersRendererFactory;
        $this->queryParametersRendererFactory = $queryParametersRendererFactory;
        $this->queryRendererFactory = $queryRendererFactory;
        $this->queryListRendererFactory = $queryListRendererFactory;
        $this->tableRendererFactory = $tableRendererFactory;
        $this->varRenderer = $varRenderer;
        $this->profileMemoryStorage = $profileMemoryStorage;
        $this->formatter = $formatter;
    }

    public function renderLayoutGraph(array $blocks, string $totalTime): string
    {
        return $this->layoutGraphRendererFactory->create([
            'blocks' => $blocks,
            'totalRenderTime' => $totalTime,
        ])->render();
    }

    public function renderTrace(array $trace): string
    {
        return $this->traceRendererFactory->create(['trace' => $trace])->render();
    }

    public function renderParameters(\Laminas\Stdlib\ParametersInterface $parameters): string
    {
        return $this->parametersRendererFactory->create(['parameters' => $parameters])->render();
    }

    public function renderQueryParameters(string $query, array $parameters): string
    {
        return $this->queryParametersRendererFactory->create([
            'query' => $query,
            'parameters' => $parameters,
        ])->render();
    }

    public function renderQuery(string $query): string
    {
        return $this->queryRendererFactory->create(['query' => $query])->render();
    }

    public function renderQueryList(array $queries): string
    {
        return $this->queryListRendererFactory->create(['queries' => $queries])->render();
    }

    public function renderTable(array $items, array $labels = []): string
    {
        return $this->tableRendererFactory->create(['items' => $items, 'labels' => $labels])->render();
    }

    public function dump(string $variable): string
    {
        return $this->varRenderer->render($variable);
    }

    public function getProfile(): ProfileInterface
    {
        return $this->profileMemoryStorage->read();
    }

    public function getFormatter(): Formatter
    {
        return $this->formatter;
    }
}
