<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\RemoteManage\Console\Command;

use Magento\Framework\Validation\ValidationException;
use Magento\RemoteManage\Auth\Decoder;
use Magento\RemoteManage\Console\Command\Response\ListResponse;
use Magento\RemoteManage\Console\Command\Response\RunResponse;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @inheritdoc
 */
class Remote extends Command
{
    const NAME = 'remote';
    const TYPE_RUN = 'run';

    /**
     * @var object
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
            ->setDescription('Default remote fallback');
    }

    /**
     * @inheritdoc
     *
     * @throws \Exception
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->payload->type === self::TYPE_RUN) {
            $this->getApplication()->find(RunResponse::NAME)->run($input, $output);

            return;
        }

        $this->getApplication()->find(ListResponse::NAME)->run($input, $output);
    }
}
