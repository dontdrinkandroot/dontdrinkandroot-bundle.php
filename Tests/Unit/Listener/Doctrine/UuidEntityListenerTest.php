<?php

namespace Dontdrinkandroot\UtilsBundle\Tests\Unit\Listener\Doctrine;

use Dontdrinkandroot\Entity\UuidEntityInterface;
use Dontdrinkandroot\UtilsBundle\Listener\Doctrine\UuidEntityListener;

class UuidEntityListenerTest extends \PHPUnit_Framework_TestCase
{

    public function testGenerateRandomUuid()
    {
        $uuidEntityListener = new UuidEntityListener();
        for ($i = 0; $i < 10; $i++) {
            $this->assertRegExp(
                '/' . UuidEntityInterface::VALID_UUID_PATTERN . '/',
                $uuidEntityListener->generateRandomUuid()
            );
        }
    }
}
