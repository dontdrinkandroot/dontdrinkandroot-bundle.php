<?php


namespace Dontdrinkandroot\UtilsBundle\Entity;

use Dontdrinkandroot\Entity\EntityInterface;

class AutoIntegerIdEntity implements EntityInterface
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
