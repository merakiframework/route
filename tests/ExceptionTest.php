<?php
declare(strict_types=1);

namespace Meraki\Route;

use Meraki\Route\Exception as RouteException;
use Meraki\TestSuite\TestCase;

/**
 * @covers Exception::
 */
final class ExceptionTest extends TestCase
{
    /**
     * @test
     */
    public function it_exists(): void
    {
        $itExists = interface_exists(RouteException::class);

        $this->assertTrue($itExists);
    }
}
