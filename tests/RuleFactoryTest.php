<?php
declare(strict_types=1);

namespace Meraki\Route;

use Meraki\Route\RuleFactory;
use Meraki\TestSuite;

final class RuleFactoryTest extends TestSuite
{
	/**
	 * @test
	 */
	public function can_be_implemented(): void
	{
		$isAnInterface = interface_exists(RuleFactory::class);

		$this->assertTrue($isAnInterface);
	}
}
