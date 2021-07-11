<?php
declare(strict_types=1);

namespace Meraki\Route;

use Meraki\Route\RuleFactory;
use Meraki\TestSuite\TestCase;

/**
 * @covers RuleFactory::
 */
final class RuleFactoryTest extends TestCase
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
