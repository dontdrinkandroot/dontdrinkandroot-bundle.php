<?php


namespace Dontdrinkandroot\UtilsBundle\Test;

trait ReferenceTrait
{

    /**
     * @param string $name
     *
     * @return mixed
     */
    abstract protected function getReference($name);
}
