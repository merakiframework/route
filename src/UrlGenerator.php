<?php
declare(strict_types=1);

namespace Meraki\Route;

use Meraki\Route\Collection;
use Meraki\Route\Pattern;
use InvalidArgumentException;
use RuntimeException;

/**
 * Generates a URL/request-target from a route's pattern.
 *
 * @author Nathan Bishop <nbish11@hotmail.com> (https://nathanbishop.name)
 * @copyright 2019 Nathan Bishop
 * @license The MIT license.
 */
final class UrlGenerator
{
	/**
	 * @const string [RELATIVE_URI_REFERENCE_REGEX description]
	 */
	private const RELATIVE_URI_REFERENCE_REGEX = '~^//([^/?#]+)([^?#]*)(\?([^#]*))?~';

	/**
	 * @var Collection The route collection.
	 */
	private $routes;

	/**
	 * @var string [$base description]
	 */
	private $base;

	/**
	 * Constructs a new route path generator.
	 *
	 * @param Collection $routes The collection of "named" routes.
	 */
	public function __construct(Collection $routes)
	{
		$this->routes = $routes;
		$this->base = '';
	}

	/**
	 * Set a URL for the path to be appended to.
	 *
	 * @param string $relativeUri A relative URI reference (a URI without the scheme component).
	 * @throws InvalidArgumentException If a relative URI reference was not provided.
	 */
	public function setBaseUrl(string $relativeUri): void
	{
		if (preg_match(self::RELATIVE_URI_REFERENCE_REGEX, $relativeUri) !== 1) {
			throw new InvalidArgumentException(sprintf('Invalid relative-uri reference "%s".', $relativeUri));
		}

		$this->base = $relativeUri;
	}

	/**
	 * Retrieve the URL that the path is being resolved against.
	 *
	 * @return string Either the URI that was set or an empty string if none was set.
	 */
	public function getBaseUrl(): string
	{
		return $this->base;
	}

	/**
	 * Generates a URL for the given route name.
	 *
	 * @param string $name The route name.
	 * @param mixed[] $parameters Replacements for named placeholders.
	 * @throws RuntimeException If a route could not be found for the given name.
	 * @return string The full protocol-less URL.
	 */
	public function generate(string $name, array $parameters = [])
	{
		foreach ($this->routes as $route) {
			if ($route->getName() === $name) {
				$requestTarget = $this->interpolate($route->getPattern(), $parameters);

				return $this->resolve($this->base, $requestTarget);
			}
		}

		throw new RuntimeException(sprintf('A route could not be found for "%s".', $name));
	}

	/**
	 * Resolve the request-target against a base URL.
	 *
	 * @param string $base A relative URI reference.
	 * @param string $requestTarget The interpolated request-target.
	 * @return string A protocol-relative (begins with two slashes) URL.
	 */
	protected function resolve(string $base, string $requestTarget): string
	{
		if ($base) {
			return rtrim($base . $requestTarget, '/');
		}

		return $requestTarget;
	}

	/**
	 * Replace placeholders in a template with their contextual values.
	 *
	 * @param Pattern $template The template/pattern to use.
	 * @param mixed[] $parameters Replacements for the named placeholders.
	 * @return string The pattern with the placeholders filled in.
	 */
	protected function interpolate(Pattern $template, array $parameters)
	{
		$replacements = [];

		foreach ($parameters as $key => $value) {
			$replacements[":{$key}"] = $value;
		}

		return strtr((string)$template, $replacements);
	}
}
