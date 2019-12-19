<?php

namespace Kuai6\EventBus\Module\Tool\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class EventBus
 * @package Kuai6\EventBus\Module\Tool\Command
 */
class EventBusCommand extends Command
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $input->getOption('manager');

        $this->getHelper('eventBusManager')->getManager($input->getOption('manager'))->init();
    }
}
