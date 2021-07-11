<?php
declare(strict_types=1);

namespace Meraki\Route\Exception;

use Meraki\TestSuite\TestCase;
use Meraki\Route\Exception\RequestTargetNotMatched;
use Meraki\Route\Exception as RouteException;
use RuntimeException;

/**
 * @covers RequestTargetNotMatched::
 */
final class RequestTargetNotMatchedTest extends TestCase
{
    /**
     * @test
     */
    public function it_implements_the_route_components_base_exception(): void
    {
        $requestTargetNotMatched = new RequestTargetNotMatched('*');

        $this->assertInstanceOf(RouteException::class, $requestTargetNotMatched);
    }

    /**
     * @test
     */
    public function it_is_a_runtime_exception(): void
    {
        $requestTargetNotMatched = new RequestTargetNotMatched('*');

        $this->assertInstanceOf(RuntimeException::class, $requestTargetNotMatched);
    }

    /**
     * @test
     */
    public function sets_correct_message(): void
    {
        $expectedMessage = 'The request target "*" could not be matched!';
        $requestTargetNotMatched = new RequestTargetNotMatched('*');

        $actualMessage = $requestTargetNotMatched->getMessage();

        $this->assertEquals($expectedMessage, $actualMessage);
    }

    /**
     * @test
     */
    public function can_get_failed_request_target(): void
    {
        $expectedRequestTarget = '*';
        $requestTargetNotMatched = new RequestTargetNotMatched($expectedRequestTarget);

        $actualRequestTarget = $requestTargetNotMatched->getFailedRequestTarget();

        $this->assertEquals($expectedRequestTarget, $actualRequestTarget);
    }

    /**
     * @test
     */
    public function code_is_set_to_the_equivalent_http_status_code(): void
    {
        $expectedCode = 404;
        $requestTargetNotMatched = new RequestTargetNotMatched('*');

        $actualCode = $requestTargetNotMatched->getCode();

        $this->assertEquals($expectedCode, $actualCode);
    }
}
