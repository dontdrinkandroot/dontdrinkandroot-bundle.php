<?php


namespace Dontdrinkandroot\UtilsBundle\Entity;

use Dontdrinkandroot\Entity\AbstractEntity;
use Dontdrinkandroot\Entity\EntityInterface;

class AutoIntegerIdEntity extends AbstractEntity implements EntityInterface
{

    /**
     * @var int
     */
    protected $id;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}
