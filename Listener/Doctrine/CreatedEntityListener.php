<?php


namespace Dontdrinkandroot\UtilsBundle\Listener\Doctrine;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Dontdrinkandroot\Entity\CreatedEntityInterface;

class CreatedEntityListener
{

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (is_a($entity, CreatedEntityInterface::class)) {
            /** @var CreatedEntityInterface $createdEntity */
            $createdEntity = $entity;
            if (null === $createdEntity->getCreated()) {
                $createdEntity->setCreated(new \DateTime());
            }
        }
    }
}
