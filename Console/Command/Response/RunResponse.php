<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\RemoteManage\Console\Command\Response;

use Magento\Framework\Validation\ValidationException;
use Magento\RemoteManage\Auth\Decoder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;

/**
 * Class Command.
 */
class RunResponse extends Command
{
    const NAME = 'remote:run';

    /**
     * @var Decoder
     */
    private $payload;

    /**
     * @param Decoder $decoder
     * @throws ValidationException
     */
    public function __construct(Decoder $decoder)
    {
        $this->payload = $decoder->decode();

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
     * @return int|null|void
     * @throws ValidationException
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->payload->name) {
            throw new ValidationException(__(
                'Command name is empty'
            ));
        }

        $name = (string)$this->payload->name;
        $arguments = array_filter(
            (array)$this->payload->arguments
        );
        $options = array_filter(
            (array)$this->payload->options
        );

        $cmdInput = new ArrayInput($arguments);
        $cmdOutput = new StreamOutput(fopen('php://output', 'wb'));

        array_walk($options, function ($value, $key) use ($input) {
            $input->setOption($key, $value);
        });

        $this->getApplication()->find($name)->run($cmdInput, $cmdOutput);
    }
}
