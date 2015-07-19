<?php


namespace Dontdrinkandroot\UtilsBundle\Command;

use Dontdrinkandroot\Entity\UuidEntityInterface;
use Dontdrinkandroot\UtilsBundle\Listener\Doctrine\AssignUuidListener;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SetUuidsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('ddr-utils:set-uuids')
            ->addArgument('entity', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entityName = $input->getArgument('entity');
        $repository = $this->getContainer()->get('doctrine')->getRepository($entityName);
        $entities = $repository->findAll();
        foreach ($entities as $entity) {
            if (is_a($entity, 'Dontdrinkandroot\Entity\UuidEntityInterface')) {
                /** @var UuidEntityInterface $uuidEntity */
                $uuidEntity = $entity;
                if (null === $uuidEntity->getUuid()) {
                    $uuidEntity->setUuid(AssignUuidListener::generateUuid());
                }
                $repository->save($entity);
            }
        }
    }
}
