<?php
declare(strict_types=1);

namespace Meraki\Route\Exception;

use Meraki\Route\Exception as RouteException;
use RuntimeException;

/**
 * Exception used when the 'accept' header could not be matched.
 *
 * @author Nathan Bishop <nbish11@hotmail.com> (https://nathanbishop.name)
 * @copyright 2019 Nathan Bishop
 * @license The MIT license.
 */
final class AcceptHeaderNotMatched extends RuntimeException implements RouteException
{
	/**
	 *
	 */
	public const EQUIVALENT_STATUS_CODE = 406;

	/**
	 * @var string [$acceptHeader description]
	 */
	private $acceptHeader;

	/**
	 * @var string[] [$allowedMediaTypes description]
	 */
	private $allowedMediaTypes;

	/**
	 * [__construct description]
	 *
	 * @param string $acceptHeader [description]
	 * @param string[] $allowedMediaTypes [description]
	 */
	public function __construct(string $acceptHeader, array $allowedMediaTypes = [])
	{
		$this->acceptHeader = $acceptHeader;
		$this->allowedMediaTypes = $allowedMediaTypes;

		parent::__construct($this->generateMessage(), self::EQUIVALENT_STATUS_CODE);
	}

	/**
	 * [getAcceptHeader description]
	 *
	 * @return string [description]
	 */
	public function getAcceptHeader(): string
	{
		return $this->acceptHeader;
	}

	/**
	 * [getAllowedMediaTypes description]
	 *
	 * @return string[] [description]
	 */
	public function getAllowedMediaTypes(): array
	{
		return $this->allowedMediaTypes;
	}

	/**
	 * [generateMessage description]
	 *
	 * @return string [description]
	 */
	private function generateMessage(): string
	{
		$message = 'A representation could not be generated for the requested media-types.';

		if (!empty($this->allowedMediaTypes)) {
			$message .= ' Try one of the following: ' . implode(', ', $this->allowedMediaTypes);
		}

		return $message;
	}
}
