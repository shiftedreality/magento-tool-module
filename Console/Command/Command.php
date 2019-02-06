<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\RemoteManage\Console\Command;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Console\Cli;
use Magento\Framework\Validation\ValidationException;
use Magento\RemoteManage\Auth\Validator;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;

/**
 * Class Command.
 */
class Command extends \Symfony\Component\Console\Command\Command
{
    const NAME = 'remote';

    const PARAM_TYPE = 'type';
    const PARAM_NAME = 'name';
    const PARAM_ARGUMENTS = 'arguments';
    const PARAM_OPTIONS = 'options';

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var Validator
     */
    private $validator;

    /**
     * @param RequestInterface $request
     * @param Validator $validator
     */
    public function __construct(RequestInterface $request, Validator $validator)
    {
        $this->request = $request;
        $this->validator = $validator;

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
     * @return int|null|void
     * @throws ValidationException
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->validator->validate();

        switch ($this->request->getParam(self::PARAM_TYPE, 'list')) {
            case 'list':
                $this->listCommands();
                break;
            case 'run':
                $this->runCommand($input);
        }
    }

    /**
     * List commands.
     */
    private function listCommands()
    {
        $commands = [];

        /** @var Command $command */
        foreach ($this->getApplication()->getApplicationCommands() as $command) {
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

        echo json_encode($commands);
        exit(Cli::RETURN_SUCCESS);
    }

    /**
     * Run specific command.
     *
     * @param InputInterface $input
     * @throws ValidationException
     */
    private function runCommand(InputInterface $input)
    {
        if (!$this->request->getParam(self::PARAM_NAME)) {
            throw new ValidationException(__(
                'Command name is empty'
            ));
        }

        $name = $this->request->getParam(self::PARAM_NAME);
        $arguments = $this->request->getParam(self::PARAM_ARGUMENTS, []);
        $options = $this->request->getParam(self::PARAM_OPTIONS, []);

        $cmdInput = new ArrayInput($arguments);
        $cmdOutput = new StreamOutput(fopen('php://output', 'wb'));

        array_walk($options, function ($value, $key) use ($input) {
            $input->setOption($key, $value);
        });

        $this->getApplication()->setAutoExit(false);
        $this->getApplication()->find($name)->run($cmdInput, $cmdOutput);
    }
}
