<?php
declare(strict_types=1);

namespace Meraki\Route;

use Meraki\Route\Collector;
use Meraki\TestSuite\TestCase;

/**
 * @covers Collector::
 */
final class CollectorTest extends TestCase
{
	/**
	 * @test
	 */
	public function is_an_interface(): void
	{
		$this->assertTrue(interface_exists(Collector::Class));
	}
}
