<?php
declare(strict_types=1);

namespace Meraki\Route;

use Meraki\TestSuite;
use Meraki\Route\Matcher;

final class MatcherTest extends TestSuite
{
    /**
     * @test
     */
    public function it_exists(): void
    {
        $itExists = class_exists(Matcher::class);

        $this->assertTrue($itExists);
    }
}
