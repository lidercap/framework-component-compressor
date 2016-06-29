<?php

namespace Lidercap\Tests\Skeleton;

use Lidercap\Skeleton\Ola;

class OlaTest extends \PHPUnit_Framework_TestCase
{
    public function testMundo()
    {
        $ola = new Ola();
        $this->assertEquals('Olá mundo!', $ola->mundo());
    }
}
