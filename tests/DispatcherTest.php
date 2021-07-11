<?php
declare(strict_types=1);

namespace Meraki\Route;

use Meraki\Route\Dispatcher;
use Meraki\Route\Mapper;
use Meraki\Route\Matcher;
use Meraki\TestSuite\TestCase;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ServerRequestInterface as ServerRequest;
use Psr\Http\Message\ResponseInterface as Response;
use Zend\Diactoros\Response\EmptyResponse;
use Zend\Diactoros\ServerRequestFactory;

/**
 * @covers Dispatcher::
 */
final class DispatcherTest extends TestCase
{
	private $handler;

	public function setUp(): void
	{
		$this->handler = new class() implements RequestHandler {
			public $requestPassedToHandle;
			public function handle(ServerRequest $request): Response {
				$this->requestPassedToHandle = $request;
				return new EmptyResponse();
			}
		};

		$this->mapper = new Mapper();
		$this->mapper->get('/', $this->handler);
		$this->mapper->get('/say-hello/:person', $this->handler);
		$this->mapper->get('/xml-file', $this->handler)->accept('text/xml');

		$this->matcher = new Matcher($this->mapper);

		$this->serverRequest = (new ServerRequestFactory())->createServerRequest('GET', '/');
	}

    /**
     * @test
     */
    public function it_exists(): void
    {
        $itExists = class_exists(Dispatcher::class);

        $this->assertTrue($itExists);
    }

    /**
     * @test
     */
    public function dispatching_as_middleware_invokes_handler_if_match_result_is_successful(): void
    {
    	$dispatcher = new Dispatcher($this->matcher);

    	$response = $dispatcher->process($this->serverRequest, $this->createMock(RequestHandler::class));

    	$this->assertInstanceOf(Response::class, $response);
    }

    /**
     * @test
     */
    public function dispatching_as_middleware_sets_placeholders_as_request_attributes_if_match_result_is_successful(): void
    {
    	$dispatcher = new Dispatcher($this->matcher);
    	$serverRequest = $this->serverRequest->withRequestTarget('/say-hello/Nathan');

    	$response = $dispatcher->process($serverRequest, $this->createMock(RequestHandler::class));

    	$this->assertEquals('Nathan', $this->handler->requestPassedToHandle->getAttribute('person'));
    }

    /**
     * @test
     */
    public function dispatching_as_middleware_modifies_response_with_405_status_if_method_wasnt_matched(): void
    {
    	$dispatcher = new Dispatcher($this->matcher);
    	$serverRequest = $this->serverRequest->withMethod('OPTIONS');

    	$response = $dispatcher->process($serverRequest, $this->handler);

    	$this->assertEquals(405, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function dispatching_as_middleware_returns_405_response_with_allow_header_set(): void
    {
    	$dispatcher = new Dispatcher($this->matcher);
    	$serverRequest = $this->serverRequest->withMethod('OPTIONS');

    	$response = $dispatcher->process($serverRequest, $this->handler);

    	$this->assertEquals('GET', $response->getHeaderLine('allow'));
    }

    /**
     * @test
     */
    public function dispatching_as_middleware_modifies_response_with_404_status_if_request_target_wasnt_matched(): void
    {
    	$dispatcher = new Dispatcher($this->matcher);
    	$serverRequest = $this->serverRequest->withRequestTarget('/say-goodbye');

    	$response = $dispatcher->process($serverRequest, $this->handler);

    	$this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function dispatching_as_middleware_modifies_response_with_406_status_if_accept_header_wasnt_matched(): void
    {
    	$dispatcher = new Dispatcher($this->matcher);
    	$serverRequest = $this->serverRequest->withRequestTarget('/xml-file')->withHeader('Accept', ['text/html']);

    	$response = $dispatcher->process($serverRequest, $this->handler);

    	$this->assertEquals(406, $response->getStatusCode());
    }
}
