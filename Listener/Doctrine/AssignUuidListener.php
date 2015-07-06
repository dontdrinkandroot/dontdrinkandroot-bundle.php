<?php


namespace Dontdrinkandroot\UtilsBundle\Listener\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Dontdrinkandroot\Entity\UuidEntityInterface;

class AssignUuidListener
{
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();

        if (is_a($entity, 'Dontdrinkandroot\Entity\UuidEntityInterface')) {
            /** @var UuidEntityInterface $uuidEntity */
            $uuidEntity = $entity;
            if (null === $uuidEntity->getUuid()) {
                $uuidEntity->setUuid($this->generateUuid($entityManager));
            }
        }
    }

    /**
     * @param EntityManager $entityManager
     *
     * @return string
     */
    private function generateUuid(EntityManager $entityManager)
    {
        $connection = $entityManager->getConnection();
        $platform = $connection->getDatabasePlatform();
        if (is_a($platform, 'Doctrine\DBAL\Platforms\MySqlPlatform')) {
            $statement = $connection->executeQuery('SELECT UUID()');
            $uuid = $statement->fetchColumn(0);

            return $uuid;
        }

        return null;
    }
}
