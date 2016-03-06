<?php

namespace Dontdrinkandroot\UtilsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Dumper\Dumper;
use Symfony\Component\DependencyInjection\Dumper\GraphvizDumper;

class DumpContainerGraphvizCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('ddr:utils:dump-container-graphviz');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $kernel = new DumperKernel('dev', true);
        $r = new \ReflectionObject($this->getApplication()->getKernel());
        $kernel->setRootDir(dirname($r->getFileName()));
        $kernel->initializeForDumping();
        $output->writeln(
            $kernel->dumper->dump(
                [
                    'graph' => [
                        'rankdir' => 'RL',
                    ]
                ]
            )
        );
    }

}

class DumperKernel extends \AppKernel
{
    /** @var Dumper */
    public $dumper;

    /**
     * {@inheritdoc}
     */
    protected function initializeContainer()
    {
        $container = $this->buildContainer();
        $this->dumper = new GraphvizDumper($container);
        $container->compile();

        return $container;
    }

    public function initializeForDumping()
    {
        $this->initializeBundles();
        $this->initializeContainer();
    }

    public function setRootDir($rootDir)
    {
        $this->rootDir = $rootDir;
    }

}
