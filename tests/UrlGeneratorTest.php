<?php
declare(strict_types=1);

namespace Meraki\Route;

use Meraki\Route\Collection;
use Meraki\Route\Rule;
use Meraki\Route\Pattern;
use Meraki\Route\UrlGenerator;
use Meraki\TestSuite\TestCase;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use RuntimeException;
use InvalidArgumentException;

/**
 * @covers UrlGenerator::
 */
final class UrlGeneratorTest extends TestCase
{
	private $basicRoute;
	private $complexRoute;
	private $routeCollection;

	public function setUp(): void
	{
		$this->routeCollection = new Collection();
		$this->handler = $this->createMock(RequestHandler::class);
		$this->homepageRoute = new Rule('get', new Pattern('/'), $this->handler);
		$this->basicRoute = new Rule('get', new Pattern('/posts'), $this->handler);
		$this->complexRoute = new Rule('get', new Pattern('/api/:version/users/:id'), $this->handler);

		// basic route
		$this->homepageRoute->name('homepage.route');
		$this->routeCollection->add($this->homepageRoute);

		// basic route
		$this->basicRoute->name('basic.route');
		$this->routeCollection->add($this->basicRoute);

		// route with placeholders
		$this->complexRoute->name('complex.route');
		$this->routeCollection->add($this->complexRoute);
	}

	/**
	 * @test
	 */
	public function it_exists(): void
	{
		$exists = class_exists(UrlGenerator::class);

		$this->assertTrue($exists);
	}

	/**
	 * @test
	 * @dataProvider invalidRelativeUriReferences
	 */
	public function throws_exception_for_invalid_base_url(string $invalidRelativeUriReferences): void
	{
		$expectedException = new InvalidArgumentException(sprintf('Invalid relative-uri reference "%s".', $invalidRelativeUriReferences));
		$urlGenerator = new UrlGenerator($this->routeCollection);

		$callback = function () use ($urlGenerator, $invalidRelativeUriReferences) {
			$urlGenerator->setBaseUrl($invalidRelativeUriReferences);
		};

		$this->assertThrows($expectedException, $callback);
	}

	/**
	 * @test
	 */
	public function throws_exception_if_route_cannot_be_found_for_name(): void
	{
		$expectedException = new RuntimeException('A route could not be found for "non.existent.route".');
		$urlGenerator = new UrlGenerator($this->routeCollection);

		$callback = function () use ($urlGenerator) {
			$urlGenerator->generate('non.existent.route');
		};

		$this->assertThrows($expectedException, $callback);
	}

	/**
	 * @test
	 */
	public function generates_request_target_for_a_route_without_placeholders(): void
	{
		$urlGenerator = new UrlGenerator($this->routeCollection);

		$actual = $urlGenerator->generate('basic.route');

		$this->assertEquals('/posts', $actual);
	}

	/**
	 * @test
	 */
	public function interpolates_placeholders_correctly_for_routes_with_placeholders(): void
	{
		$params = ['version' => 'v3', 'id' => 65535];
		$urlGenerator = new UrlGenerator($this->routeCollection);

		$actual = $urlGenerator->generate('complex.route', $params);

		$this->assertEquals('/api/v3/users/65535', $actual);
	}

	/**
	 * @test
	 */
	public function resolves_request_target_against_a_base_url(): void
	{
		$params = ['version' => 'v3', 'id' => 65535];
		$urlGenerator = new UrlGenerator($this->routeCollection);
		$urlGenerator->setBaseUrl('//domain.com');

		$basicRoute = $urlGenerator->generate('basic.route');
		$complexRoute = $urlGenerator->generate('complex.route', $params);

		$this->assertEquals('//domain.com/posts', $basicRoute);
		$this->assertEquals('//domain.com/api/v3/users/65535', $complexRoute);
	}

	/**
	 * @test
	 */
	public function resolves_request_target_against_a_base_url_removes_trailing_slash(): void
	{
		$urlGenerator = new UrlGenerator($this->routeCollection);
		$urlGenerator->setBaseUrl('//domain.com');

		$this->assertEquals('//domain.com', $urlGenerator->generate('homepage.route'));
	}

	public function invalidRelativeUriReferences(): array
	{
		return [
			'has a scheme' => ['http://domain.com'],
			'has an empty scheme' => ['://domain.com'],
			'does not start with double slash' => ['domain.com']
		];
	}
}
