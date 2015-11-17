<?php


namespace Dontdrinkandroot\UtilsBundle\Listener\Doctrine;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Dontdrinkandroot\Entity\UpdatedEntityInterface;

class UpdatedEntityListener
{

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (is_a($entity, UpdatedEntityInterface::class)) {
            /** @var UpdatedEntityInterface $updatedEntity */
            $updatedEntity = $entity;
            $updatedEntity->setUpdated(new \DateTime());
        }
    }
}
