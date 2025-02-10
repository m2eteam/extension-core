<?php

declare(strict_types=1);

namespace M2E\Core\Block\Adminhtml\Magento\Button;

class DropDown extends \Magento\Backend\Block\Widget\Button\SplitButton
{
    protected $_template = 'M2E_Core::control_panel/tab/database.phtml';

    /**
     * @param array $option
     * @param string $title
     * @param string $classes
     * @param string $disabled
     *
     * @return array
     */
    protected function _prepareOptionAttributes($option, $title, $classes, $disabled)
    {
        $attributes = [
            'id' => isset($option['id']) ? $this->getId() . '-' . $option['id'] : '',
            'title' => $title,
            'class' => join(' ', $classes),
            'onclick' => isset($option['onclick']) ? $option['onclick'] : '',
            'style' => isset($option['style']) ? $option['style'] : '',
            'disabled' => $disabled,
        ];

        if (isset($option['data_attribute'])) {
            $this->_getDataAttributes($option['data_attribute'], $attributes);
        }

        return $attributes;
    }
}
