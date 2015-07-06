<?php


namespace Dontdrinkandroot\UtilsBundle\Entity;

use Dontdrinkandroot\Entity\UuidEntityInterface;

class UuidEntity extends AutoIntegerIdEntity implements UuidEntityInterface
{

    /**
     * @var string
     */
    protected $uuid;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * {@inheritdoc}
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
    }
}
