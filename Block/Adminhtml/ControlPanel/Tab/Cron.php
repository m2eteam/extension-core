<?php

declare(strict_types=1);

namespace M2E\Core\Block\Adminhtml\ControlPanel\Tab;

class Cron extends AbstractTab
{
    public const TAB_ID = 'cron';

    protected $_template = 'M2E_Core::control_panel/tab/cron.phtml';

    private array $tasks;

    private \M2E\Core\Model\ControlPanel\CronTaskCollection $cronTaskCollection;
    private \M2E\Core\Model\ControlPanel\CurrentExtensionResolver $currentExtensionResolver;

    public function __construct(
        \M2E\Core\Model\ControlPanel\CurrentExtensionResolver $currentExtensionResolver,
        \M2E\Core\Model\ControlPanel\CronTaskCollection $cronTaskCollection,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
        $this->cronTaskCollection = $cronTaskCollection;
        $this->currentExtensionResolver = $currentExtensionResolver;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function getBlockId(): string
    {
        return 'controlPanelCron';
    }

    public static function getTabId(): string
    {
        return self::TAB_ID;
    }

    public static function getLabel(): string
    {
        return 'Cron';
    }

    public function getCronRunUrl(): string
    {
        return $this->getUrl('*/controlPanel_cron/run', ['_query' => ['task_code' => 'task-plx']]);
    }

    public function getTasks(): array
    {
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        if (!isset($this->tasks)) {
            $tasks = [];
            $extensionTasks = $this->cronTaskCollection->getTasksForExtension($this->currentExtensionResolver->get()->getModuleName());
            foreach ($extensionTasks as $task) {
                $group = $task->group;
                $nick = $task->nick;
                $tasks[ucfirst($group)][$task->code] = $this->generateTaskTitle($group, $nick);
            }

            foreach ($tasks as &$tasksByGroup) {
                asort($tasksByGroup);
            }

            unset($tasksByGroup);

            $this->tasks = $tasks;
        }

        return $this->tasks;
    }

    private function generateTaskTitle(string $group, string $nick): string
    {
        $titleParts = explode('/', $nick);

        if (reset($titleParts) === $group) {
            array_shift($titleParts);
        }

        return preg_replace_callback(
            '/_([a-z])/i',
            static fn($matches) => ucfirst($matches[1]),
            implode(' > ', array_map('ucfirst', $titleParts))
        );
    }
}
