<?php
declare(strict_types=1);

namespace Meraki\Route;

use Meraki\Route\MatchResult;
use Meraki\Route\Matcher;
use Meraki\Route\Exception\MethodNotMatched as MethodNotMatchedException;
use Meraki\Route\Exception\RequestTargetNotMatched as RequestTargetNotMatchedException;
use Meraki\Route\Exception\AcceptHeaderNotMatched as AcceptHeaderNotMatchedException;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Message\ServerRequestInterface as ServerRequest;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Invokes the matched request-handler or modifies the response in case of failure.
 *
 * @author Nathan Bishop <nbish11@hotmail.com> (https://nathanbishop.name)
 * @copyright 2019 Nathan Bishop
 * @license The MIT license.
 */
final class Dispatcher implements Middleware
{
	/**
	 * @var Matcher [$matcher description]
	 */
	private $matcher;

	/**
	 * [__construct description]
	 *
	 * @param Matcher $matcher [description]
	 */
	public function __construct(Matcher $matcher)
	{
		$this->matcher = $matcher;
	}

    /**
     * [process description]
     *
     * @param ServerRequest  $request [description]
     * @param RequestHandler $handler [description]
     * @return Response [description]
     */
    public function process(ServerRequest $request, RequestHandler $handler): Response
    {
    	$result = $this->matcher->match($request);

    	if ($result->isSuccessful()) {
    		$rule = $result->getMatchedRule();

			foreach ($rule->getPattern()->getPlaceholders() as $key => $value) {
				$request = $request->withAttribute($key, $value);
			}

			return $rule->getHandler()->handle($request);
    	}

    	if ($result->getType() === $result::METHOD_NOT_MATCHED) {
    		return $handler->handle($request)
    			->withStatus(405, 'Method Not Allowed')
    			->withHeader('Allow', implode(',', $result->getAllowedMethods()));
    	}

    	if ($result->getType() === $result::ACCEPT_HEADER_NOT_MATCHED) {
    		return $handler->handle($request)
    			->withStatus(406, 'Not Acceptable');
    	}

    	return $handler->handle($request)->withStatus(404, 'Not Found');
    }

    /**
     * [dispatch description]
     *
     * @param MatchResult $result [description]
     * @throws RequestTargetNotMatchedException [<description>]
     * @throws MethodNotMatchedException [<description>]
     * @return Response [description]
     */
    public static function dispatch(MatchResult $result): Response
    {
    	$request = $result->getRequest();

    	if ($result->getType() === $result::REQUEST_TARGET_NOT_MATCHED) {
    		throw new RequestTargetNotMatchedException($request->getRequestTarget());
    	}

    	if ($result->getType() === $result::METHOD_NOT_MATCHED) {
    		throw new MethodNotMatchedException($request->getMethod(), $result->getAllowedMethods());
    	}

    	if ($result->getType() === $result::ACCEPT_HEADER_NOT_MATCHED) {
    		throw new AcceptHeaderNotMatchedException($request->getHeaderLine('Accept'), $result->getAllowedMediaTypes());
    	}

    	$rule = $result->getMatchedRule();

		foreach ($rule->getPattern()->getPlaceholders() as $key => $value) {
			$request = $request->withAttribute($key, $value);
		}

		return $rule->getHandler()->handle($request);
    }
}
