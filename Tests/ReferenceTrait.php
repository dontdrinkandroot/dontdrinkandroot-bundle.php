<?php


namespace Dontdrinkandroot\UtilsBundle\Tests;

trait ReferenceTrait
{

    /**
     * @param string $name
     *
     * @return mixed
     */
    abstract protected function getReference($name);
}
