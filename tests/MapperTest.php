<?php
declare(strict_types=1);

namespace Meraki\Route;

use Meraki\TestSuite;
use Meraki\Route\Mapper;
use Meraki\Route\Collection;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Meraki\Route\Rule;

final class MapperTest extends TestSuite
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
		$this->assertEquals($this->requestTarget, $rule->getRequestTarget());
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

			$sut->assertEquals('/action' . $sut->requestTarget, $rule->getRequestTarget());
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
		$this->assertEquals($this->requestTarget, $rule->getRequestTarget());
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

			$sut->assertEquals('/action' . $sut->requestTarget, $rule->getRequestTarget());
		});
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