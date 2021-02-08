<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\RemoteManage\Console\Command;

use Magento\Framework\Console\Cli;
use Magento\RemoteManage\Console\Command\Response\ListResponse;
use Magento\RemoteManage\Console\Command\Response\RunResponse;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\App\RequestInterface;

/**
 * @inheritdoc
 */
class Remote extends Command
{
    public const NAME = 'remote';
    public const TYPE_RUN = 'run';

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param RequestInterface $request
     */
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
            ->setDescription('Default remote fallback');
    }

    /**
     * @inheritdoc
     *
     * @throws \Exception
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->request->getParam('type') === self::TYPE_RUN) {
            $this->getApplication()->find(RunResponse::NAME)->run($input, $output);

            return Cli::RETURN_SUCCESS;
        }

        $this->getApplication()->find(ListResponse::NAME)->run($input, $output);

        return Cli::RETURN_SUCCESS;
    }
}
