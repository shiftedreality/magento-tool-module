<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\RemoteManage\Console\Command\Response;

use Magento\Framework\Console\Cli;
use Magento\Framework\Validation\ValidationException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;
use Magento\Framework\App\RequestInterface;

/**
 * Class Command.
 */
class RunResponse extends Command
{
    public const NAME = 'remote:run';

    /**
     * @var RequestInterface
     */
    private $request;

    public function __construct(RequestInterface $request)
    {
        $this->request = $request;

        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName(self::NAME)
            ->setDescription('Run specific command');

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
        if (!$this->request->getParam('name')) {
            throw new ValidationException(__(
                'Command name is empty'
            ));
        }

        $name = (string)$this->request->getParam('name');
        $arguments = array_filter(
            (array)$this->request->getParam('arguments')
        );
        $options = array_filter(
            (array)$this->request->getParam('options')
        );

        $cmdInput = new ArrayInput($arguments);
        $cmdOutput = new StreamOutput(fopen('php://output', 'wb'));

        array_walk($options, static function ($value, $key) use ($input) {
            $input->setOption($key, $value);
        });

        $this->getApplication()
            ->find($name)
            ->run($cmdInput, $cmdOutput);

        return Cli::RETURN_SUCCESS;
    }
}
