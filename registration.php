<?php

use Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::MODULE,
    \M2E\Core\Helper\Module::IDENTIFIER,
    __DIR__
);
