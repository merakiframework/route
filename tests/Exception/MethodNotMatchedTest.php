<?php
declare(strict_types=1);

namespace Meraki\Route\Exception;

use Meraki\TestSuite;
use Meraki\Route\Exception\MethodNotMatched;
use Meraki\Route\Exception as RouteException;
use RuntimeException;
use LogicException;

final class MethodNotMatchedTest extends TestSuite
{
    /**
     * @test
     */
    public function it_implements_the_route_components_base_exception(): void
    {
        $methodNotMatched = new MethodNotMatched('get');

        $this->assertInstanceOf(RouteException::class, $methodNotMatched);
    }

    /**
     * @test
     */
    public function it_is_a_runtime_exception(): void
    {
        $methodNotMatched = new MethodNotMatched('get');

        $this->assertInstanceOf(RuntimeException::class, $methodNotMatched);
    }

    /**
     * @test
     */
    public function throws_exception_if_request_method_also_exists_in_allowed_methods(): void
    {
        $expectedException = new LogicException('The method that failed the match should not be in the allowed methods.');

        $callback = function () {
            $methodNotMatched = new MethodNotMatched('head', ['head']);
        };

        $this->assertThrows($expectedException, $callback);
    }

    /**
     * @test
     */
    public function failed_method_is_normalised_to_uppercase(): void
    {
        $methodNotMatched = new MethodNotMatched('head');

        $methodThatFailed = $methodNotMatched->getFailedMethod();

        $this->assertEquals('HEAD', $methodThatFailed);
    }

    /**
     * @test
     */
    public function sets_correct_message_when_no_allowed_methods_were_provided(): void
    {
        $expectedMessage = 'The "GET" method could not be matched.';
        $methodNotMatched = new MethodNotMatched('get');

        $actualMessage = $methodNotMatched->getMessage();

        $this->assertEquals($expectedMessage, $actualMessage);
    }

    /**
     * @test
     */
    public function sets_correct_message_when_allowed_methods_were_provided(): void
    {
        $expectedMessage = 'The "GET" method could not be matched. Try one of the following: PUT, POST';
        $methodNotMatched = new MethodNotMatched('get', ['put', 'post']);

        $actualMessage = $methodNotMatched->getMessage();

        $this->assertEquals($expectedMessage, $actualMessage);
    }

    /**
     * @test
     */
    public function code_is_set_to_the_equivalent_http_status_code(): void
    {
        $expectedCode = 405;
        $methodNotMatched = new MethodNotMatched('get');

        $actualCode = $methodNotMatched->getCode();

        $this->assertEquals($expectedCode, $actualCode);
    }

    /**
     * @test
     */
    public function allowed_methods_are_normalised_to_uppercase(): void
    {
        $methodNotMatched = new MethodNotMatched('get', ['put']);

        $actualAllowedMethods = $methodNotMatched->getAllowedMethods();

        $this->assertEquals(['PUT'], $actualAllowedMethods);
    }

    /**
     * @test
     */
    public function method_that_failed_match_can_be_retrieved(): void
    {
        $expectedFailedMethod = 'HEAD';
        $methodNotMatched = new MethodNotMatched($expectedFailedMethod, []);

        $actualFailedMethod = $methodNotMatched->getFailedMethod();

        $this->assertEquals($expectedFailedMethod, $actualFailedMethod);
    }

    /**
     * @test
     */
    public function can_get_allowed_methods(): void
    {
        $expectedAllowedMethods = ['PUT'];
        $methodNotMatched = new MethodNotMatched('get', $expectedAllowedMethods);

        $actualAllowedMethods = $methodNotMatched->getAllowedMethods();

        $this->assertEquals($expectedAllowedMethods, $actualAllowedMethods);
    }
}
