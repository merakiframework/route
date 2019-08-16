<?php
declare(strict_types=1);

namespace Meraki\Route;

/**
 * A constraint is bound to a placeholder, limiting what it can/cannot match.
 *
 * @author Nathan Bishop <nbish11@hotmail.com> (https://nathanbishop.name)
 * @copyright 2019 Nathan Bishop
 * @license The MIT license.
 */
final class Constraint
{
	/**
	 * @var string [$regex description]
	 */
	private $regex;

	/**
	 * [__construct description]
	 *
	 * @param string $regex [description]
	 */
	protected function __construct(string $regex)
	{
		$this->regex = $regex;
	}

	/**
	 * [getRegex description]
	 *
	 * @return string [description]
	 */
	public function getRegex(): string
	{
		return $this->regex;
	}

	/**
	 * [__toString description]
	 *
	 * @see self::getRegex()
	 * @return string [description]
	 */
	public function __toString(): string
	{
		return $this->getRegex();
	}

	/**
	 * Create a constraint allowing only whole numbers to be used
	 * in place of the placeholder.
	 *
	 * @return self [description]
	 */
	public static function digit(): self
	{
		return new self('\d+');
	}

	/**
	 * Create a constraint allowing any character to be used, up to
	 * the next forward-slash, in place of the placeholder.
	 *
	 * @return self [description]
	 */
	public static function any(): self
	{
		return new self('[^/]+');
	}

	/**
	 * Create a constraint allowing any lowercase or uppercase letter
	 * to be used in place of the placeholder.
	 *
	 * @return self [description]
	 */
	public static function alpha(): self
	{
		return new self('[a-zA-Z]+');
	}

	/**
	 * Create a constraint allowing any hexadecimal value to be used
	 * in place of the placeholder.
	 *
	 * @return self [description]
	 */
	public static function hex(): self
	{
		return new self('[a-fA-F0-9]+');
	}

	/**
	 * Create a constraint using a custom regex for the placeholder to match.
	 *
	 * @param string $regex The regex to use for the placeholder. Delimiters are escaped automatically.
	 * @return self [description]
	 */
	public static function custom(string $regex): self
	{
		return new self(str_replace('~', '\~', $regex));
	}
}
