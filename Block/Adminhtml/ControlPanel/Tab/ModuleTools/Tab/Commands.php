<?php

declare(strict_types=1);

namespace M2E\Core\Block\Adminhtml\ControlPanel\Tab\ModuleTools\Tab;

class Commands extends \M2E\Core\Block\Adminhtml\Magento\AbstractBlock
{
    protected $_template = 'M2E_Core::control_panel/tab/module_tools/commands_tab.phtml';

    private string $controllerClass;
    private string $route;
    private array $commands = [];
    private \M2E\Core\Model\ControlPanel\ModuleTools\ControllerParser $controllerParser;

    public function __construct(
        string $controllerClass,
        string $route,
        \M2E\Core\Model\ControlPanel\ModuleTools\ControllerParser $controllerParser,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->controllerClass = $controllerClass;
        $this->route = $route;
        $this->controllerParser = $controllerParser;
    }

    public function getCommands(): array
    {
        if (empty($this->commands)) {
            $this->commands = $this->controllerParser->parseGeneralCommandsData(
                $this->controllerClass,
                $this->route,
            );
        }

        return $this->commands;
    }

    public function getCommandLauncherHtml(array $commandRow): string
    {
        $href = $commandRow['url'];
        $target = '';
        $commandRow['new_window'] && $target = 'target="_blank"';
        $onClick = '';

        $commandRow['confirm'] && $onClick = "return confirm('{$commandRow['confirm']}');";
        if (!empty($commandRow['prompt']['text']) && !empty($commandRow['prompt']['var'])) {
            $onClick = <<<JS
var result = prompt('{$commandRow['prompt']['text']}');
if (result) window.location.href = $(this).getAttribute('href') + '?{$commandRow['prompt']['var']}=' + result;
return false;
JS;
        }

        $title = $commandRow['title'];

        return <<<HTML
<a href="{$href}" {$target} onclick="{$onClick}" title="{$commandRow['description']}">{$title}</a>
HTML;
    }
}
