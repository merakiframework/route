<?php
declare(strict_types=1);

namespace Meraki\Route;

use Psr\Http\Message\ServerRequestInterface as ServerRequest;
use Meraki\Route\Rule;

/**
 *
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
	const FOUND = 200;

	/**
	 * @const integer [NOT_FOUND description]
	 */
	const NOT_FOUND = 404;

	/**
	 * @const integer [METHOD_NOT_ALLOWED description]
	 */
	const METHOD_NOT_ALLOWED = 405;

	/**
	 * @var integer [$type description]
	 */
	private $type;

	/**
	 * @var ServerRequest [$request description]
	 */
	private $request;

	/**
	 * @var Rule[] [$rules description]
	 */
	private $rules;

	/**
	 * @var string[] [$allowedMethods description]
	 */
	private $allowedMethods;

	private function __construct()
	{
		// intentionally private constructor
	}

	/**
	 * [getType description]
	 *
	 * @return [type] [description]
	 */
	public function getType(): int
	{
		return $this->type;
	}

	/**
	 * [getRequest description]
	 *
	 * @return [type] [description]
	 */
	public function getRequest(): ServerRequest
	{
		return $this->request;
	}

	/**
	 * [getRules description]
	 *
	 * @return [type] [description]
	 */
	public function getRules(): array
	{
		return $this->rules;
	}

	/**
	 * [getAllowedMethods description]
	 *
	 * @return [type] [description]
	 */
	public function getAllowedMethods(): array
	{
		return $this->allowedMethods;
	}

	/**
	 * [isSuccessful description]
	 *
	 * @return boolean [description]
	 */
	public function isSuccessful(): bool
	{
		return $this->type === self::FOUND;
	}

	/**
	 * [isFailure description]
	 *
	 * @return boolean [description]
	 */
	public function isFailure(): bool
	{
		return $this->type === self::NOT_FOUND
			|| $this->type === self::METHOD_NOT_ALLOWED;
	}

	/**
	 * [success description]
	 *
	 * @param  ServerRequest $request [description]
	 * @param  [type]        $rules   [description]
	 * @return [type]                 [description]
	 */
	public static function success(ServerRequest $request, Rule ...$rules): self
	{
		$result = new self();
		$result->type = self::FOUND;
		$result->request = $request;
		$result->rules = $rules;
		$result->allowedMethods = [];

		return $result;
	}

	/**
	 * [notFound description]
	 *
	 * @param  ServerRequest $request [description]
	 * @return [type]                 [description]
	 */
	public static function notFound(ServerRequest $request): self
	{
		$result = new self();
		$result->type = self::NOT_FOUND;
		$result->request = $request;
		$result->rules = [];
		$result->allowedMethods = [];

		return $result;
	}

	/**
	 * [methodNotAllowed description]
	 *
	 * @param  ServerRequest $request        [description]
	 * @param  [type]        $allowedMethods [description]
	 * @return [type]                        [description]
	 */
	public static function methodNotAllowed(ServerRequest $request, string ...$allowedMethods): self
	{
		$result = new self();
		$result->type = self::METHOD_NOT_ALLOWED;
		$result->request = $request;
		$result->rules = [];
		$result->allowedMethods = $allowedMethods;

		return $result;
	}
}
