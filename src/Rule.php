<?php
declare(strict_types=1);

namespace Meraki\Route;

use Meraki\Route\Pattern;
use Meraki\Route\Constraint;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use InvalidArgumentException;

/**
 * A mapping of an incoming request to its appropriate request-handler.
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
     * @var string[] [$mediaTypes description]
     */
    private $mediaTypes;

    /**
     * [__construct description]
     *
     * @param string $method  [description]
     * @param Pattern $pattern [description]
     * @param RequestHandler $handler [description]
     * @throws InvalidArgumentException [<description>]
     */
    public function __construct(string $method, Pattern $pattern, RequestHandler $handler)
    {
        if (empty($method)) {
    		throw new InvalidArgumentException('A request method was not provided.');
    	}

    	$this->method = $method;
        $this->pattern = $pattern;
        $this->handler = $handler;
        $this->name = '';
        $this->mediaTypes = [];
    }

    /**
     * [getMethod description]
     *
     * @return string [description]
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * [getPattern description]
     *
     * @return Pattern [description]
     */
    public function getPattern(): Pattern
    {
        return $this->pattern;
    }

    /**
     * [getHandler description]
     *
     * @return RequestHandler [description]
     */
    public function getHandler(): RequestHandler
    {
        return $this->handler;
    }

    /**
     * [getName description]
     *
     * @return string [description]
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * [getMediaTypes description]
     *
     * @return string[] [description]
     */
    public function getMediaTypes(): array
    {
    	return $this->mediaTypes;
    }

    /**
     * [name description]
     *
     * @param string $name [description]
     * @throws InvalidArgumentException [description]
     * @throws InvalidArgumentException [description]
     * @return self [description]
     */
    public function name(string $name): self
    {
    	if ($this->name) {
    		throw new InvalidArgumentException('Name is immutable and cannot be changed once set.');
    	}

        if (empty($name)) {
        	throw new InvalidArgumentException('Name cannot be empty.');
        }

        $this->name = $name;

        return $this;
    }

    /**
     * [accept description]
     *
     * @param string $mediaType [description]
     * @return self [description]
     */
    public function accept(string $mediaType): self
    {
    	$this->mediaTypes[] = $mediaType;

    	return $this;
    }

    /**
     * [constrain description]
     *
     * @param string $placeholder [description]
     * @param Constraint $constraint  [description]
     * @return self [description]
     */
    public function constrain(string $placeholder, Constraint $constraint): self
    {
    	$this->pattern->addConstraint($placeholder, $constraint);

    	return $this;
    }

    /**
     * [matchesMethod description]
     *
     * @param string $requestMethod [description]
     * @return boolean [description]
     */
    public function matchesMethod(string $requestMethod): bool
    {
        return strcasecmp($this->method, $requestMethod) === 0;
    }

    /**
     * [create description]
     *
     * @param string $method  [description]
     * @param string $pattern [description]
     * @param RequestHandler $handler [description]
     * @return self [description]
     */
    public static function create(string $method, string $pattern, RequestHandler $handler): self
    {
    	return new self($method, new Pattern($pattern), $handler);
    }
}
