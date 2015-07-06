<?php


namespace Dontdrinkandroot\UtilsBundle\Listener\Doctrine;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Dontdrinkandroot\Entity\UuidEntityInterface;
use Symfony\Component\Security\Core\Util\SecureRandom;

class AssignUuidListener
{
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (is_a($entity, 'Dontdrinkandroot\Entity\UuidEntityInterface')) {
            /** @var UuidEntityInterface $uuidEntity */
            $uuidEntity = $entity;
            if (null === $uuidEntity->getUuid()) {
                $uuidEntity->setUuid(self::generateUuid());
            }
        }
    }

    /**
     * Not part of the public api.
     *
     * @return string
     */
    public static function generateUuid()
    {
        $secureRandom = new SecureRandom();

        return sprintf(
            '%s-%s-%04x-%04x-%s',
            bin2hex($secureRandom->nextBytes(4)),
            bin2hex($secureRandom->nextBytes(2)),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            bin2hex($secureRandom->nextBytes(6))
        );
    }
}
