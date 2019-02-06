<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * These commands must not be called via CLI.
 */
if (PHP_SAPI !== 'cli') {
    \Magento\Framework\Console\CommandLocator::register(\Magento\RemoteManage\Console\CommandList::class);
}
