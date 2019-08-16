<?php
declare(strict_types=1);

namespace Meraki\Route;

use Meraki\TestSuite;
use Meraki\Route\Matcher;
use Meraki\Route\Mapper;
use Meraki\Route\Constraint;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Zend\Diactoros\ServerRequestFactory;

final class MatcherTest extends TestSuite
{
	public function setUp(): void
	{
		$this->handler = $this->createMock(RequestHandler::class);;
		$this->mapper = new Mapper();

		$this->mapper->get('/', $this->handler);
		$this->mapper->get('/data', $this->handler)->accept('application/json');

		$this->genericRule = $this->mapper->get('/users', $this->handler);
		$this->specificRule = $this->mapper->get('/users', $this->handler)->accept('application/json');

		$this->showUserByIdRule = $this->mapper->get('/users/:id', $this->handler)
			->constrain('id', Constraint::digit());

		$this->showUserByUsernameRule = $this->mapper->get('/users/:username', $this->handler)
			->constrain('username', Constraint::alphaNumeric());

		$this->matcher = new Matcher($this->mapper);
		$this->requestFactory = new ServerRequestFactory();
	}

    /**
     * @test
     */
    public function it_exists(): void
    {
        $itExists = class_exists(Matcher::class);

        $this->assertTrue($itExists);
    }

    /**
     * @test
     */
    public function returns_accept_header_not_matched_result_if_accept_header_not_matched(): void
    {
    	$request = $this->requestFactory->createServerRequest('GET', '/data')->withHeader('Accept', ['text/html']);

    	$result = $this->matcher->match($request);

    	$this->assertEquals($result::ACCEPT_HEADER_NOT_MATCHED, $result->getType());
    }

    /**
     * @test
     */
    public function accept_header_not_matched_result_has_supported_media_types_set(): void
    {
    	$request = $this->requestFactory->createServerRequest('GET', '/data')->withHeader('Accept', ['text/html']);

    	$result = $this->matcher->match($request);

    	$this->assertContains('application/json', $result->getAllowedMediaTypes());
    }

    /**
     * @test
     */
    public function matches_rule_with_specific_media_type_set(): void
    {
    	$request = $this->requestFactory->createServerRequest('GET', '/users')->withHeader('Accept', ['application/json']);

    	$result = $this->matcher->match($request);

    	$this->assertSame($this->specificRule, $result->getMatchedRule());
    }

    /**
     * @test
     */
    public function matches_first_rule_if_no_specific_media_type_set(): void
    {
    	$request = $this->requestFactory->createServerRequest('GET', '/users')->withHeader('Accept', ['text/html']);

    	$result = $this->matcher->match($request);

    	$this->assertSame($this->genericRule, $result->getMatchedRule());
    }

    /**
     * @test
     */
    public function returns_first_rule_if_two_similar_endpoints_can_be_matched(): void
    {
    	$request = $this->requestFactory->createServerRequest('GET', '/users/465');

    	$result = $this->matcher->match($request);

    	$this->assertSame($this->showUserByIdRule, $result->getMatchedRule());
    }
}
