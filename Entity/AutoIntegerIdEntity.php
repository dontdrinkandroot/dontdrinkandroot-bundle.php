<?php


namespace Dontdrinkandroot\UtilsBundle\Entity;

use Dontdrinkandroot\Entity\AbstractEntity;
use Dontdrinkandroot\Entity\EntityInterface;

/**
 * Entity with an automatically assigned integer id.
 */
class AutoIntegerIdEntity extends AbstractEntity implements EntityInterface
{

    /**
     * @var int
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}
