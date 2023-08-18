<?php
declare(strict_types=1);

namespace App\Twig;

use Ghunti\HighchartsPHP\Highchart;
use Twig\Extension\AbstractExtension;
use Twig\{TwigFilter, TwigFunction};

class HighchartExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('hc_scripts', [$this, 'printScripts'], ['is_safe' => ['html']]),
            new TwigFilter('hc_render', [$this, 'render'], ['is_safe' => ['html']]),
            new TwigFilter('hc_options', [$this, 'renderOptions'], ['is_safe' => ['html']]),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('hc_scripts', [$this, 'printScripts'], ['is_safe' => ['html']]),
            new TwigFunction('hc_render', [$this, 'render'], ['is_safe' => ['html']]),
            new TwigFunction('hc_options', [$this, 'renderOptions'], ['is_safe' => ['html']]),
        ];
    }

    public function printScripts(Highchart $c): string
    {
        return $c->printScripts(true);
    }

    public function render(Highchart $c, string $blockId, ?string $varName = null, bool $withScriptTag = false): string
    {
        $c->chart->renderTo = $blockId;

        return $c->render($varName, withScriptTag: $withScriptTag);
    }

    public function renderOptions(Highchart $c): string
    {
        return $c->renderOptions();
    }
}