<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

try {
    require __DIR__ . '/../app/bootstrap.php';
} catch (\Exception $e) {
    echo 'Autoload error: ' . $e->getMessage();
    exit(1);
}
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'on');

$handler = new \Magento\Framework\App\ErrorHandler();
set_error_handler([$handler, 'handler']);

$_SERVER['argv'] = [];

try {
    $application = new Magento\Framework\Console\Cli('Magento Remote CLI');
    $application->setDefaultCommand(\Magento\RemoteManage\Console\Command\Remote::NAME);
    $application->setCatchExceptions(false);
    $application->run();
} catch (\Exception $e) {
    while ($e) {
        echo $e->getMessage();
        echo $e->getTraceAsString();
        echo "\n\n";
        $e = $e->getPrevious();
    }
    exit(Magento\Framework\Console\Cli::RETURN_FAILURE);
}
