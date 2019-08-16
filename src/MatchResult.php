<?php
declare(strict_types=1);

namespace Meraki\Route;

use Psr\Http\Message\ServerRequestInterface as ServerRequest;
use Meraki\Route\Rule;

/**
 * Represents the result of a route attempting a match against the incoming request.
 *
 * @author Nathan Bishop <nbish11@hotmail.com> (https://nathanbishop.name)
 * @copyright 2019 Nathan Bishop
 * @license The MIT license.
 */
final class MatchResult
{
	/**
	 * @const integer [FOUND description]
	 */
	const MATCHED = 200;

	/**
	 * @const integer [NOT_FOUND description]
	 */
	const REQUEST_TARGET_NOT_MATCHED = 404;

	/**
	 * @const integer [METHOD_NOT_ALLOWED description]
	 */
	const METHOD_NOT_MATCHED = 405;

	/**
	 * @const integer [ACCEPT_HEADER_NOT_MATCHED description]
	 */
	const ACCEPT_HEADER_NOT_MATCHED = 406;

	/**
	 * @var integer [$type description]
	 */
	private $type;

	/**
	 * @var ServerRequest [$request description]
	 */
	private $request;

	/**
	 * @var Rule [$matchedRule description]
	 */
	private $matchedRule;

	/**
	 * @var string[] [$allowedMethods description]
	 */
	private $allowedMethods;

	/**
	 * @var string[][] [$allowedMediaTypes description]
	 */
	private $allowedMediaTypes;

	/**
	 * [__construct description]
	 *
	 * @param integer $type [description]
	 * @param ServerRequest $request [description]
	 */
	private function __construct(int $type, ServerRequest $request)
	{
		$this->type = $type;
		$this->request = $request;
		$this->allowedMethods = [];
		$this->allowedMediaTypes = [];
	}

	/**
	 * [getType description]
	 *
	 * @return integer [description]
	 */
	public function getType(): int
	{
		return $this->type;
	}

	/**
	 * [getRequest description]
	 *
	 * @return ServerRequest [description]
	 */
	public function getRequest(): ServerRequest
	{
		return $this->request;
	}

	/**
	 * [getMatchedRule description]
	 *
	 * @return Rule|null [description]
	 */
	public function getMatchedRule(): ?Rule
	{
		return $this->matchedRule;
	}

	/**
	 * [getAllowedMethods description]
	 *
	 * @return string[] [description]
	 */
	public function getAllowedMethods(): array
	{
		return $this->allowedMethods;
	}

	/**
	 * [getAllowedMediaTypes description]
	 *
	 * @return string[][] [description]
	 */
	public function getAllowedMediaTypes(): array
	{
		return $this->allowedMediaTypes;
	}

	/**
	 * [isSuccessful description]
	 *
	 * @return boolean [description]
	 */
	public function isSuccessful(): bool
	{
		return $this->type === self::MATCHED;
	}

	/**
	 * [isFailure description]
	 *
	 * @return boolean [description]
	 */
	public function isFailure(): bool
	{
		return $this->type === self::REQUEST_TARGET_NOT_MATCHED
			|| $this->type === self::METHOD_NOT_MATCHED
			|| $this->type === self::ACCEPT_HEADER_NOT_MATCHED;
	}

	/**
	 * [success description]
	 *
	 * @param ServerRequest $request [description]
	 * @param Rule $rule [description]
	 * @return self [description]
	 */
	public static function matched(ServerRequest $request, Rule $rule): self
	{
		$result = new self(self::MATCHED, $request);
		$result->matchedRule = $rule;

		return $result;
	}

	/**
	 * [notFound description]
	 *
	 * @param ServerRequest $request [description]
	 * @return self [description]
	 */
	public static function requestTargetNotMatched(ServerRequest $request): self
	{
		return new self(self::REQUEST_TARGET_NOT_MATCHED, $request);
	}

	/**
	 * [methodNotAllowed description]
	 *
	 * @param ServerRequest $request [description]
	 * @param string[] $allowedMethods [description]
	 * @return self [description]
	 */
	public static function methodNotMatched(ServerRequest $request, array $allowedMethods): self
	{
		$result = new self(self::METHOD_NOT_MATCHED, $request);
		$result->allowedMethods = $allowedMethods;

		return $result;
	}

	/**
	 * [acceptHeaderNotMatched description]
	 *
	 * @param ServerRequest $request [description]
	 * @param string[][] $allowedMediaTypes [description]
	 * @return self [description]
	 */
	public static function acceptHeaderNotMatched(ServerRequest $request, array $allowedMediaTypes): self
	{
		$result = new self(self::ACCEPT_HEADER_NOT_MATCHED, $request);
		$result->allowedMediaTypes = $allowedMediaTypes;

		return $result;
	}
}
