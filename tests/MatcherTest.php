<?php
declare(strict_types=1);

namespace Meraki\Route;

use Meraki\Route\Matcher;
use Meraki\Route\Mapper;
use Meraki\Route\Constraint;
use Meraki\TestSuite\TestCase;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Zend\Diactoros\ServerRequestFactory;

/**
 * @covers Matcher::
 */
final class MatcherTest extends TestCase
{
	public function setUp(): void
	{
		$this->handler = $this->createMock(RequestHandler::class);
		$this->mapper = new Mapper();

		$this->mapper->get('/', $this->handler);
		$this->mapper->get('/data', $this->handler)->accept('application/json');

		$this->genericRule = $this->mapper->get('/users', $this->handler);
		$this->specificRule = $this->mapper->get('/users', $this->handler)->accept('application/json');

		$this->showUserByIdRule = $this->mapper->get('/users/:id', $this->handler)
			->constrain('id', Constraint::digit());

		$this->showUserByUsernameRule = $this->mapper->get('/users/:username', $this->handler)
			->constrain('username', Constraint::alphaNumeric());

		$this->deleteUserRule = $this->mapper->delete('/users/:id', $this->handler)
			->constrain('id', Constraint::digit());

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

    /**
     * @test
     */
    public function will_match_a_form_method_override_if_post_request(): void
    {
    	$request = $this->requestFactory->createServerRequest('POST', '/users/465')
    		->withParsedBody(['_METHOD' => 'delete']);

    	$result = $this->matcher->match($request);

    	$this->assertSame($this->deleteUserRule, $result->getMatchedRule());
    }

    /**
     * @test
     */
    public function will_not_match_a_form_method_override_if_get_request(): void
    {
    	$request = $this->requestFactory->createServerRequest('GET', '/users/465')
    		->withParsedBody(['_METHOD' => 'delete']);

    	$result = $this->matcher->match($request);

    	$this->assertSame($this->showUserByIdRule, $result->getMatchedRule());
    }

    /**
     * @test
     */
    public function will_match_a_http_header_method_override(): void
    {
    	$request = $this->requestFactory->createServerRequest('GET', '/users/465')
    		->withHeader('x-http-method-override', 'delete');

    	$result = $this->matcher->match($request);

    	$this->assertSame($this->deleteUserRule, $result->getMatchedRule());
    }

    /**
     * @test
     */
    public function rule_is_matched_if_request_method_and_rule_method_are_equivalent(): void
    {
    	$request = $this->requestFactory->createServerRequest('get', '/users/465');

    	$result = $this->matcher->match($request);

    	$this->assertSame($this->showUserByIdRule, $result->getMatchedRule());
    }

    /**
     * @test
     */
    public function rule_is_not_matched_if_request_method_and_rule_method_differ(): void
    {
    	$request = $this->requestFactory->createServerRequest('patch', '/users/465');

    	$result = $this->matcher->match($request);

    	$this->assertEquals($result::METHOD_NOT_MATCHED, $result->getType());
    }

    /**
     * @test
     */
    public function allowed_methods_provided_to_match_result_are_unique(): void
    {
    	$request = $this->requestFactory->createServerRequest('PATCH', '/users/465');

    	$result = $this->matcher->match($request);

    	$this->assertEquals(['GET', 'DELETE'], $result->getAllowedMethods());
    }
}
