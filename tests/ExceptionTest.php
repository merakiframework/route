<?php
declare(strict_types=1);

namespace Meraki\Route;

use Meraki\TestSuite;
use Meraki\Route\Exception as RouteException;

final class ExceptionTest extends TestSuite
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
