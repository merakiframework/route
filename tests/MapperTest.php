<?php
declare(strict_types=1);

namespace Meraki\Route;

use Meraki\Route\Mapper;
use Meraki\Route\Collection;
use Meraki\Route\Rule;
use Meraki\Route\RuleFactory;
use Meraki\TestSuite\TestCase;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

/**
 * @covers Mapper::
 */
final class MapperTest extends TestCase
{
	public function setUp(): void
	{
		$this->requestTarget = '/say-hello';
		$this->handler = $this->createMock(RequestHandler::class);
	}

	/**
	 * @test
	 */
	public function is_a_collection(): void
	{
		$mapper = new Mapper();

		$this->assertInstanceOf(Collection::class, $mapper);
	}

	/**
	 * @test
	 * @dataProvider mapperMethods
	 */
	public function mapping_a_route_creates_an_appropriate_rule(string $method): void
	{
		$mapper = new Mapper();

		$rule = call_user_func([$mapper, $method], $this->requestTarget, $this->handler);

		$this->assertInstanceOf(Rule::class, $rule);
		$this->assertEquals(strtoupper($method), $rule->getMethod());
		$this->assertEquals($this->requestTarget, (string)$rule->getPattern());
		$this->assertSame($this->handler, $rule->getHandler());
	}

	/**
	 * @test
	 * @dataProvider mapperMethods
	 */
	public function created_rule_is_added_to_collection(string $method): void
	{
		$mapper = new Mapper();

		$rule = call_user_func([$mapper, $method], $this->requestTarget, $this->handler);

		$this->assertTrue($mapper->contains($rule));
	}

	/**
	 * @test
	 * @dataProvider mapperMethods
	 */
	public function grouped_rules_are_added_with_prefix(string $method): void
	{
		$sut = $this;
		$mapper = new Mapper();

		$mapper->group('/action', function () use ($sut, $method) {
			$rule = call_user_func([$this, $method], $sut->requestTarget, $sut->handler);

			$sut->assertEquals('/action' . $sut->requestTarget, (string)$rule->getPattern());
		});
	}

	/**
	 * @test
	 */
	public function mapping_with_custom_method_creates_appropriate_rule(): void
	{
		$mapper = new Mapper();

		$rule = $mapper->map('VERB', $this->requestTarget, $this->handler);

		$this->assertInstanceOf(Rule::class, $rule);
		$this->assertEquals('VERB', $rule->getMethod());
		$this->assertEquals($this->requestTarget, (string)$rule->getPattern());
		$this->assertSame($this->handler, $rule->getHandler());
	}

	/**
	 * @test
	 */
	public function mapping_route_with_custom_method_adds_rule_to_collection(): void
	{
		$mapper = new Mapper();

		$rule = $mapper->map('VERB', $this->requestTarget, $this->handler);

		$this->assertTrue($mapper->contains($rule));
	}

	/**
	 * @test
	 */
	public function mapping_route_with_custom_method_adds_prefix(): void
	{
		$sut = $this;
		$mapper = new Mapper();

		$mapper->group('/action', function () use ($sut) {
			$rule = $this->map('VERB', $sut->requestTarget, $sut->handler);

			$sut->assertEquals('/action' . $sut->requestTarget, (string) $rule->getPattern());
		});
	}

	/**
	 * @test
	 */
	public function can_use_a_custom_rule_factory(): void
	{
		$ruleFactory = new class() implements RuleFactory {
			public $ruleParams;
			public function make(string $method, string $pattern, RequestHandler $handler): Rule {
				$this->ruleParams = [$method, $pattern, $handler];
				return Rule::create($method, $pattern, $handler);
			}
		};
		$mapper = new Mapper($ruleFactory);

		$mapper->map('GET', $this->requestTarget, $this->handler);

		$this->assertEquals(['GET', $this->requestTarget, $this->handler], $ruleFactory->ruleParams);
	}

	public function mapperMethods(): array
	{
		return [
			'head()' => ['head'],
			'get()' => ['get'],
			'put()' => ['put'],
			'post()' => ['post'],
			'delete()' => ['delete'],
			'options()' => ['options']
		];
	}
}
