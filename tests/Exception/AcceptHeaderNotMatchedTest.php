<?php
declare(strict_types=1);

namespace Meraki\Route\Exception;

use Meraki\Route\Exception\AcceptHeaderNotMatched;
use Meraki\Route\Exception as RouteException;
use Meraki\TestSuite\TestCase;
use RuntimeException;

/**
 * @covers AcceptHeaderNotMatched::
 */
final class AcceptHeaderNotMatchedTest extends TestCase
{
	/**
	 * @test
	 */
	public function it_implements_the_route_components_base_exception(): void
	{
		$acceptHeaderNotMatched = new AcceptHeaderNotMatched('text/xml');

		$this->assertInstanceOf(RouteException::class, $acceptHeaderNotMatched);
	}

	/**
	 * @test
	 */
	public function it_is_a_runtime_exception(): void
	{
		$acceptHeaderNotMatched = new AcceptHeaderNotMatched('text/xml');

		$this->assertInstanceOf(RuntimeException::class, $acceptHeaderNotMatched);
	}

	/**
	 * @test
	 */
	public function accept_header_that_failed_match_can_be_retrieved(): void
	{
		$expectedAcceptHeader = 'text/xml';
		$acceptHeaderNotMatched = new AcceptHeaderNotMatched($expectedAcceptHeader);

		$actualAcceptHeader = $acceptHeaderNotMatched->getAcceptHeader();

		$this->assertEquals($expectedAcceptHeader, $actualAcceptHeader);
	}

	/**
	 * @test
	 */
	public function can_get_allowed_media_types(): void
	{
		$expectedAllowedMediaTypes = ['application/json', 'text/html'];
		$acceptHeaderNotMatched = new AcceptHeaderNotMatched('text/xml', $expectedAllowedMediaTypes);

		$actualAllowedMediaTypes = $acceptHeaderNotMatched->getAllowedMediaTypes();

		$this->assertEquals($expectedAllowedMediaTypes, $actualAllowedMediaTypes);
	}

	/**
	 * @test
	 */
	public function sets_correct_message_when_no_allowed_media_types_were_provided(): void
	{
		$expectedMessage = 'A representation could not be generated for the requested media-types.';
		$acceptHeaderNotMatched = new AcceptHeaderNotMatched('text/xml');

		$actualMessage = $acceptHeaderNotMatched->getMessage();

		$this->assertEquals($expectedMessage, $actualMessage);
	}

	/**
	 * @test
	 */
	public function sets_correct_message_when_allowed_media_types_are_provided(): void
	{
		$expectedMessage = 'A representation could not be generated for the requested media-types. Try one of the following: text/html, application/json';
		$acceptHeaderNotMatched = new AcceptHeaderNotMatched('text/xml', ['text/html', 'application/json']);

		$actualMessage = $acceptHeaderNotMatched->getMessage();

		$this->assertEquals($expectedMessage, $actualMessage);
	}

	/**
	 * @test
	 */
	public function code_is_set_to_the_equivalent_http_status_code(): void
	{
		$expectedCode = 406;
		$acceptHeaderNotMatched = new AcceptHeaderNotMatched('text/xml');

		$actualCode = $acceptHeaderNotMatched->getCode();

		$this->assertEquals($expectedCode, $actualCode);
	}
}
