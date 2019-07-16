<?php
declare(strict_types=1);

namespace Meraki\Route;

use Meraki\TestSuite;
use Meraki\Route\Pattern;

final class PatternTest extends TestSuite
{
    /**
     * @test
     */
    public function it_exists(): void
    {
        $itExists = interface_exists(Pattern::class);

        $this->assertTrue($itExists);
    }
}
