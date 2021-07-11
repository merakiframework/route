<?php
declare(strict_types=1);

namespace Meraki\Route;

use Meraki\Route\RuleFactory;
use Meraki\Route\RubyOnRailsRuleFactory;
use Meraki\TestSuite\TestCase;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

/**
 * @covers RubyOnRailsRuleFactory::
 */
final class RubyOnRailsRuleFactoryTest extends TestCase
{
	public function setUp(): void
	{
		$this->handler = $this->createMock(RequestHandler::class);
		$this->ruleFactory = new RubyOnRailsRuleFactory();
	}

	/**
	 * @test
	 */
	public function is_a_rule_factory(): void
	{
		$this->assertInstanceOf(RuleFactory::class, $this->ruleFactory);
	}

	/**
	 * @test
	 */
	public function creates_rule_with_method_set(): void
	{
		$expectedMethod = 'GET';
		$rule = $this->ruleFactory->make($expectedMethod, '/:username', $this->handler);

		$actualMethod = $rule->getMethod();

		$this->assertEquals($expectedMethod, $actualMethod);
	}

	/**
	 * @test
	 */
	public function creates_rule_with_pattern_set(): void
	{
		$expectedPattern = new Pattern('/:username');
		$rule = $this->ruleFactory->make('GET', '/:username', $this->handler);

		$actualPattern = $rule->getPattern();

		$this->assertEquals($expectedPattern->compile(), $actualPattern->compile());
	}

	/**
	 * @test
	 */
	public function creates_rule_with_handler_set(): void
	{
		$rule = $this->ruleFactory->make('GET', '/:username', $this->handler);

		$actualHandler = $rule->getHandler();

		$this->assertSame($this->handler, $actualHandler);
	}
}
