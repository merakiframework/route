<?php
declare(strict_types=1);

namespace Meraki\Route;

use Meraki\Route\Pattern;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ServerRequestInterface as ServerRequest;
use Psr\Http\Message\ResponseInterface as Response;

/**
 *
 *
 * @author Nathan Bishop <nbish11@hotmail.com> (https://nathanbishop.name)
 * @copyright 2019 Nathan Bishop
 * @license The MIT license.
 */
final class Rule
{
	/**
	 * @var string [$method description]
	 */
    private $method;

    /**
	 * @var Pattern [$pattern description]
	 */
    private $pattern;

    /**
	 * @var RequestHandler [$handler description]
	 */
    private $handler;

    /**
	 * @var string [$name description]
	 */
    private $name;

    /**
     * [__construct description]
     *
     * @param string         $method  [description]
     * @param Pattern        $pattern [description]
     * @param RequestHandler $handler [description]
     */
    public function __construct(string $method, Pattern $pattern, RequestHandler $handler)
    {
        $this->method = $method;
        $this->pattern = $pattern;
        $this->handler = $handler;
        $this->name = '';
    }

    /**
     * [getMethod description]
     *
     * @return [type] [description]
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * [getPattern description]
     *
     * @return [type] [description]
     */
    public function getPattern(): Pattern
    {
        return $this->pattern;
    }

    /**
     * [getHandler description]
     *
     * @return [type] [description]
     */
    public function getHandler(): RequestHandler
    {
        return $this->handler;
    }

    /**
     * [getName description]
     *
     * @return [type] [description]
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * [name description]
     *
     * @param  string $name [description]
     * @return [type]       [description]
     */
    public function name(string $name): self
    {
        if (!$this->name) {
            $this->name = $name;
        }

        return $this;
    }

    /**
     * [matchesMethod description]
     *
     * @param  string $requestMethod [description]
     * @return [type]                [description]
     */
    public function matchesMethod(string $requestMethod): bool
    {
        return strcasecmp($this->method, $requestMethod) === 0;
    }

    /**
     * [create description]
     *
     * @param  string         $method  [description]
     * @param  string         $pattern [description]
     * @param  RequestHandler $handler [description]
     * @return [type]                  [description]
     */
    public static function create(string $method, string $pattern, RequestHandler $handler): self
    {
    	return new self($method, new Pattern($pattern), $handler);
    }
}
