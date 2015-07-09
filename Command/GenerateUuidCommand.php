<?php


namespace Dontdrinkandroot\UtilsBundle\Command;

use Dontdrinkandroot\UtilsBundle\Listener\Doctrine\AssignUuidListener;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateUuidCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('ddr-utils:generate-uuid');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(AssignUuidListener::generateUuid());
    }
}
