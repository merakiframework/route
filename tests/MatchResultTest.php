<?php
declare(strict_types=1);

namespace Meraki\Route;

use Meraki\TestSuite;
use Meraki\Route\MatchResult;

final class MatchResultTest extends TestSuite
{
    /**
     * @test
     */
    public function it_exists(): void
    {
        $itExists = class_exists(MatchResult::class);

        $this->assertTrue($itExists);
    }
}
