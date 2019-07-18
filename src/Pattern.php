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
    const PLACEHOLDER_SEARCH_REGEX = '~:([^/]+)~';

    /**
     * @const string [COMPILED_PLACEHOLDER_REGEX description]
     */
    const PLACEHOLDER_REPLACE_REGEX = '(?<\1>[^/]+)';

    /**
     * @var string [$pattern description]
     */
	private $pattern;

	/**
	 * @var string[] [$placeholders description]
	 */
	private $placeholders;

	/**
	 * Constructor.
	 *
	 * @param string $pattern [description]
	 */
	public function __construct(string $pattern)
	{
		$this->pattern = $this->stripQueryString($pattern);
		$this->placeholders = [];
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
	 * Allow this object to be treated like a string.
	 *
	 * @return string The pattern BEFORE it's compiled.
	 */
	public function __toString(): string
	{
		return $this->pattern;
	}

	/**
	 * Compile the request-target pattern into a regular expression.
	 *
	 * @todo Extract into a separate object along with the placeholders (something like `CompiledPattern`).
	 * @return string A regular expression representing the pattern.
	 */
	public function compile(): string
	{
		$regex = preg_replace_callback(self::PLACEHOLDER_SEARCH_REGEX, [$this, 'extractPlaceholders'], $this->pattern);
    	$regex = sprintf('~^%s$~', $regex);

        return $regex;
	}

	/**
	 * Compiles the pattern, checks if the request-target matches and extracts the placeholders.
	 *
	 * @param string $requestTarget The request-target to match against.
	 * @return boolean `true` if compiled, matched and extracted successfully, `false` otherwise.
	 */
	public function matches(string $requestTarget): bool
	{
		$compiledPattern = $this->compile();
		$requestTarget = $this->stripQueryString($requestTarget);

		if (preg_match($compiledPattern, $requestTarget, $matches) === 1) {
			$this->setPlaceholderValues($matches);

			return true;
		}

		return false;
	}

	/**
	 * Extract placeholder names and give it an appropriate regex for matching.
	 *
	 * @param array $matches The matches returned from compile().
	 * @return string The regex to use in place of the placeholder name.
	 */
	private function extractPlaceholders(array $matches): string
	{
		$this->placeholders[$matches[1]] = null;

		return sprintf('(?<%s>[^/]+)', $matches[1]);
	}

	/**
	 * Set the placeholder values from the match results.
	 *
	 * @param string[] $matches The results from preg_match().
	 */
	private function setPlaceholderValues(array $matches): void
	{
		foreach ($this->placeholders as $key => $value) {
			if (isset($matches[$key])) {
				$this->placeholders[$key] = $matches[$key];
			}
		}
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
