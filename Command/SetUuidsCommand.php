<?php


namespace Dontdrinkandroot\UtilsBundle\Command;

use Doctrine\ORM\EntityManager;
use Dontdrinkandroot\Entity\UuidEntityInterface;
use Dontdrinkandroot\Repository\OrmEntityRepository;
use Dontdrinkandroot\UtilsBundle\Listener\Doctrine\UuidEntityListener;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SetUuidsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('ddr-utils:set-uuids')
            ->addArgument('entity', InputArgument::REQUIRED)
            ->addOption('strategy', null, InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var UuidEntityListener $uuidListener */
        $uuidListener = $this->getContainer()->get('ddr_utils.listener.doctrine.uuid');
        $strategy = $uuidListener->getStrategy();
        if ($input->getOption('strategy')) {
            $strategy = $input->getOption('strategy');
        }

        /** @var EntityManager $entityManager */
        $entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');

        $entityName = $input->getArgument('entity');
        /** @var OrmEntityRepository $repository */
        $repository = $this->getContainer()->get('doctrine')->getRepository($entityName);
        $entities = $repository->findAll();
        foreach ($entities as $entity) {
            if (is_a($entity, UuidEntityInterface::class)) {
                /** @var UuidEntityInterface $uuidEntity */
                $uuidEntity = $entity;
                if (null === $uuidEntity->getUuid()) {
                    $uuidEntity->setUuid($uuidListener->generateUuid($entityManager, $strategy));
                }
                $repository->flush($entity);
            }
        }
    }
}
