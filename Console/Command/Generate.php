<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\RemoteManage\Console\Command;

use Magento\Framework\App\DeploymentConfig\Writer;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Math\Random;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @inheritdoc
 */
class Generate extends Command
{
    /**
     * @var Writer
     */
    private $writer;

    /**
     * @var Random
     */
    private $random;

    /**
     * @param Writer $writer
     * @param Random $random
     */
    public function __construct(Writer $writer, Random $random)
    {
        $this->writer = $writer;
        $this->random = $random;

        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('remote:generate')
            ->setDescription('Generate new tokens');
    }

    /**
     * Generate new key
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws FileSystemException
     * @throws LocalizedException
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $key = $this->random->getRandomString(32);

        $this->writer->saveConfig([
            'app_env' => [
                'remote' => [
                    'key' => $this->random->getRandomString(32)
                ]
            ]
        ], true);

        $output->writeln($key);
    }
}
