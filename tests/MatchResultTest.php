<?php
declare(strict_types=1);

namespace Meraki\Route;

use Meraki\TestSuite\TestCase;
use Psr\Http\Message\ServerRequestInterface as ServerRequest;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Meraki\Route\MatchResult;
use Meraki\Route\Rule;

/**
 * @covers MatchResult::
 */
final class MatchResultTest extends TestCase
{
	public function setUp(): void
	{
		$this->request = $this->createMock(ServerRequest::class);
		$this->method ='GET';
		$this->requestTarget = '/';
		$this->handler = $this->createMock(RequestHandler::class);
		$this->rule = Rule::create($this->method, $this->requestTarget, $this->handler);
		$this->allowedMethods = ['GET', 'POST'];
		$this->supportedMediaTypes = ['application/json', 'text/html'];
	}

    /**
     * @test
     */
    public function it_exists(): void
    {
        $itExists = class_exists(MatchResult::class);

        $this->assertTrue($itExists);
    }

    /**
     * @test
     */
    public function a_matched_result_has_the_correct_type(): void
    {
    	$result = MatchResult::matched($this->request, $this->rule);

    	$actual = $result->getType();

    	$this->assertEquals(MatchResult::MATCHED, $actual);
    }

    /**
     * @test
     */
    public function a_matched_result_provides_the_request_object(): void
    {
    	$result = MatchResult::matched($this->request, $this->rule);

    	$actual = $result->getRequest();

    	$this->assertSame($this->request, $actual);
    }

    /**
     * @test
     */
    public function a_matched_result_has_the_matched_rule_set(): void
    {
    	$result = MatchResult::matched($this->request, $this->rule);

    	$actual = $result->getMatchedRule();

    	$this->assertSame($this->rule, $actual);
    }

    /**
     * @test
     */
    public function a_matched_result_does_not_have_any_allowed_methods(): void
    {
    	$result = MatchResult::matched($this->request, $this->rule);

    	$actual = $result->getAllowedMethods();

    	$this->assertEmpty($actual);
    }

    /**
     * @test
     */
    public function a_matched_result_is_considered_successful(): void
    {
    	$result = MatchResult::matched($this->request, $this->rule);

    	$actual = $result->isSuccessful();

    	$this->assertTrue($actual);
    }

    /**
     * @test
     */
    public function a_matched_result_is_not_considered_a_failure(): void
    {
    	$result = MatchResult::matched($this->request, $this->rule);

    	$actual = $result->isFailure();

    	$this->assertFalse($actual);
    }

	/**
     * @test
     */
    public function a_request_target_not_matched_result_has_the_correct_type(): void
    {
    	$result = MatchResult::requestTargetNotMatched($this->request);

    	$actual = $result->getType();

    	$this->assertEquals(MatchResult::REQUEST_TARGET_NOT_MATCHED, $actual);
    }

    /**
     * @test
     */
    public function a_request_target_not_matched_result_provides_the_request_object(): void
    {
    	$result = MatchResult::requestTargetNotMatched($this->request);

    	$actual = $result->getRequest();

    	$this->assertSame($this->request, $actual);
    }

    /**
     * @test
     */
    public function a_request_target_not_matched_result_does_not_have_a_matched_rule_set(): void
    {
    	$result = MatchResult::requestTargetNotMatched($this->request);

    	$actual = $result->getMatchedRule();

    	$this->assertNull($actual);
    }

    /**
     * @test
     */
    public function a_request_target_not_matched_result_does_not_have_any_allowed_methods(): void
    {
    	$result = MatchResult::requestTargetNotMatched($this->request);

    	$actual = $result->getAllowedMethods();

    	$this->assertEmpty($actual);
    }

    /**
     * @test
     */
    public function a_request_target_not_matched_result_is_not_considered_successful(): void
    {
    	$result = MatchResult::requestTargetNotMatched($this->request);

    	$actual = $result->isSuccessful();

    	$this->assertFalse($actual);
    }

    /**
     * @test
     */
    public function a_request_target_not_matched_result_is_considered_a_failure(): void
    {
    	$result = MatchResult::requestTargetNotMatched($this->request);

    	$actual = $result->isFailure();

    	$this->assertTrue($actual);
    }

	/**
     * @test
     */
    public function a_method_not_matched_result_has_the_correct_type(): void
    {
    	$result = MatchResult::methodNotMatched($this->request, $this->allowedMethods);

    	$actual = $result->getType();

    	$this->assertEquals(MatchResult::METHOD_NOT_MATCHED, $actual);
    }

    /**
     * @test
     */
    public function a_method_not_matched_result_provides_the_request_object(): void
    {
    	$result = MatchResult::methodNotMatched($this->request, $this->allowedMethods);

    	$actual = $result->getRequest();

    	$this->assertSame($this->request, $actual);
    }

    /**
     * @test
     */
    public function a_method_not_matched_result_does_not_have_a_matched_rule_set(): void
    {
    	$result = MatchResult::methodNotMatched($this->request, $this->allowedMethods);

    	$actual = $result->getMatchedRule();

    	$this->assertNull($actual);
    }

    /**
     * @test
     */
    public function a_method_not_matched_result_provides_the_methods_that_are_allowed(): void
    {
    	$result = MatchResult::methodNotMatched($this->request, $this->allowedMethods);

    	$actual = $result->getAllowedMethods();

    	$this->assertEquals($this->allowedMethods, $actual);
    }

    /**
     * @test
     */
    public function a_method_not_matched_result_is_not_considered_successful(): void
    {
    	$result = MatchResult::methodNotMatched($this->request, $this->allowedMethods);

    	$actual = $result->isSuccessful();

    	$this->assertFalse($actual);
    }

    /**
     * @test
     */
    public function a_method_not_matched_result_is_considered_a_failure(): void
    {
    	$result = MatchResult::methodNotMatched($this->request, $this->allowedMethods);

    	$actual = $result->isFailure();

    	$this->assertTrue($actual);
    }

	/**
     * @test
     */
    public function an_accept_header_not_matched_result_has_the_correct_type(): void
    {
    	$result = MatchResult::acceptHeaderNotMatched($this->request, $this->supportedMediaTypes);

    	$actual = $result->getType();

    	$this->assertEquals(MatchResult::ACCEPT_HEADER_NOT_MATCHED, $actual);
    }

    /**
     * @test
     */
    public function an_accept_header_not_matched_result_provides_the_request_object(): void
    {
    	$result = MatchResult::acceptHeaderNotMatched($this->request, $this->supportedMediaTypes);

    	$actual = $result->getRequest();

    	$this->assertSame($this->request, $actual);
    }

    /**
     * @test
     */
    public function an_accept_header_not_matched_result_does_not_have_a_matched_rule_set(): void
    {
    	$result = MatchResult::acceptHeaderNotMatched($this->request, $this->supportedMediaTypes);

    	$actual = $result->getMatchedRule();

    	$this->assertNull($actual);
    }

    /**
     * @test
     */
    public function an_accept_header_not_matched_result_provides_the_media_types_that_are_supported(): void
    {
    	$result = MatchResult::acceptHeaderNotMatched($this->request, $this->supportedMediaTypes);

    	$actual = $result->getAllowedMediaTypes();

    	$this->assertEquals($this->supportedMediaTypes, $actual);
    }

    /**
     * @test
     */
    public function an_accept_header_not_matched_result_is_not_considered_successful(): void
    {
    	$result = MatchResult::acceptHeaderNotMatched($this->request, $this->supportedMediaTypes);

    	$actual = $result->isSuccessful();

    	$this->assertFalse($actual);
    }

    /**
     * @test
     */
    public function an_accept_header_not_matched_result_is_considered_a_failure(): void
    {
    	$result = MatchResult::acceptHeaderNotMatched($this->request, $this->supportedMediaTypes);

    	$actual = $result->isFailure();

    	$this->assertTrue($actual);
    }
}
