<?php
declare(strict_types=1);

namespace Meraki\Route;

use Meraki\TestSuite\TestCase;
use Meraki\Route\Exception as RouteException;

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
