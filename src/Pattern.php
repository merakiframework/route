<?php
declare(strict_types=1);

namespace Meraki\Route;

/**
 * Implementation of a 'request-target' but with placeholder support.
 *
 * @author Nathan Bishop <nbish11@hotmail.com> (https://nathanbishop.name)
 * @copyright 2019 Nathan Bishop
 * @license The MIT license.
 */
final class Pattern
{
	/**
     * @const string [PLACEHOLDER_REGEX description]
     */
    const PLACEHOLDER_REGEX = '`:([^/]+)`';

    /**
     * @const string [COMPILED_PLACEHOLDER_REGEX description]
     */
    const COMPILED_PLACEHOLDER_REGEX = '(?<\1>[^/]+)';

    /**
     * @var string [$pattern description]
     */
	private $pattern;

	/**
	 * @var string[] [$placeholders description]
	 */
	private $placeholders;

	/**
	 * @var string[] [$constraints description]
	 */
	private $constraints;

	/**
	 * Constructor.
	 *
	 * @param string $pattern [description]
	 */
	public function __construct(string $pattern)
	{
		$this->pattern = $this->stripQueryString($pattern);
		$this->placeholders = [];
		$this->constraints = [];
	}

	/**
	 * Retrieve any placeholders and their values.
	 *
	 * @return string[] The placeholder names and its values (only after compiling).
	 */
	public function getPlaceholders(): array
	{
		return $this->placeholders;
	}

	/**
	 * Compile the pattern and placeholder constraints into a regular expression.
	 *
	 * @todo Extract into a separate object along with the placeholders (something like `CompiledPattern`).
	 * @return string The regular expression representing this pattern.
	 */
	public function compile(): string
	{
		$pattern = preg_replace(self::PLACEHOLDER_REGEX, self::COMPILED_PLACEHOLDER_REGEX, $this->pattern);
        $pattern = sprintf('`^%s$`', $pattern);

        return $pattern;
	}

	/**
	 * Allow this object to be treated like a string.
	 *
	 * @return string The pattern BEFORE it's compiled.
	 */
	public function __toString(): string
	{
		return $this->pattern;
	}

	/**
	 * Compiles the pattern, checks if the request-target matches and extracts the placeholders.
	 *
	 * @param string $requestTarget The request-target to match against.
	 * @return boolean `true` if compiled, matched and extracted successfully, `false` otherwise.
	 */
	public function matches(string $requestTarget): bool
	{
		$pattern = $this->compile();
		$requestTarget = $this->stripQueryString($requestTarget);

		if (preg_match($pattern, $requestTarget, $matches) === 1) {
			$this->placeholders = $this->extractPlaceholders($matches);

			return true;
		}

		return false;
	}

	/**
	 * Get only the placeholders from the preg_match result.
	 *
	 * @param string[] $matches The results from preg_match().
	 * @return string[] The results from preg_match(), but without the numeric indexes or whole matches.
	 */
	private function extractPlaceholders(array $matches): array
	{
        return array_filter($matches, [$this, 'isNotNumeric'], ARRAY_FILTER_USE_KEY);
	}

	/**
	 * Check if a value is NOT numeric.
	 *
	 * @param mixed $value The value to check for.
	 * @return boolean `true` if the value is NOT a number, `false` if it is a number.
	 */
	private function isNotNumeric($value): bool
	{
		return !is_numeric($value);
	}

	/**
	 * Remove the 'query string' part from a request-target.
	 *
	 * @param string $requestTarget The request-target which may or may not have a query string.
	 * @return string The request-target without the query string.
	 */
	private function stripQueryString(string $requestTarget): string
	{
		return strpos($requestTarget, '?') !== false ? strstr($requestTarget, '?', true) : $requestTarget;
	}
}
