<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\RemoteManage\Console;

use Magento\Framework\Console\CommandListInterface;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\ObjectManagerInterface;
use Magento\RemoteManage\Console;

/**
 * Class CommandList
 */
class CommandList implements CommandListInterface
{
    /**
     * Object Manager
     *
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @inheritdoc
     *
     * @throws ValidatorException
     */
    public function getCommands(): array
    {
        $commands = [];
        $list = [
            Console\Command\Remote::class,
            Console\Command\Response\ListResponse::class,
            Console\Command\Response\RunResponse::class
        ];

        foreach ($list as $class) {
            if (class_exists($class)) {
                $commands[] = $this->objectManager->get($class);
            } else {
                throw new ValidatorException('Class ' . $class . ' does not exist');
            }
        }

        return $commands;
    }
}
