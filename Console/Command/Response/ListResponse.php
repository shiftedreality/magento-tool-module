<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\RemoteManage\Console\Command\Response;

use Magento\Framework\App\Response\HttpFactory;
use Magento\Framework\Console\Cli;
use Magento\Framework\Validation\ValidationException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

/**
 * Class Command.
 */
class ListResponse extends Command
{
    public const NAME = 'remote:list';

    /**
     * @var HttpFactory
     */
    private $responseFactory;

    /**
     * @param HttpFactory $responseFactory
     */
    public function __construct(HttpFactory $responseFactory)
    {
        $this->responseFactory = $responseFactory;

        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName(self::NAME)
            ->setDescription('Output list of available commands');

        parent::configure();
    }

    /**
     * @inheritdoc
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws ValidationException
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $commands = [];

        $application = $this->getApplication();
        $reflectionClass = new \ReflectionClass($application);
        $reflectionMethod = $reflectionClass->getMethod('getApplicationCommands');
        $reflectionMethod->setAccessible(true);

        $reflectionCommands = $reflectionMethod->invokeArgs($application, []);

        /** @var ListResponse $command */
        foreach ($reflectionCommands as $command) {
            $options = [];
            $arguments = [];

            foreach ($command->getDefinition()->getArguments() as $argument) {
                $arguments[$argument->getName()] = [
                    'description' => $argument->getDescription(),
                    'is_required' => $argument->isRequired(),
                    'is_array' => $argument->isArray(),
                    'default' => $argument->getDefault()
                ];
            }

            foreach ($command->getDefinition()->getOptions() as $option) {
                $options[$option->getName()] = [
                    'description' => $option->getDescription(),
                    'shortcut' => $option->getShortcut(),
                    'is_array' => $option->isArray(),
                    'default' => $option->getDefault()
                ];
            }

            $commands[$command->getName()] = [
                'description' => $command->getDescription(),
                'help' => $command->getHelp(),
                'usages' => $command->getUsages(),
                'definition' => [
                    'options' => $options,
                    'arguments' => $arguments
                ]
            ];
        }

        $this->responseFactory->create()
            ->setHeader('Content-Type', 'application/json', true)
            ->setBody(json_encode($commands))
            ->sendResponse();

        $this->getApplication()->setAutoExit(true);

        return Cli::RETURN_SUCCESS;
    }
}
