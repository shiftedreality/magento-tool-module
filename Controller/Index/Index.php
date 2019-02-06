<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\RemoteManage\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Console\Cli;

class Index extends Action
{
    public function execute()
    {
        $app = new Cli();
        $app->run();
    }
}
