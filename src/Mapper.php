<?php
declare(strict_types=1);

namespace Meraki\Route;

use Meraki\Route\Collection;
use Meraki\Route\Rule;
use Meraki\Route\RuleFactory;
use Meraki\Route\RubyOnRailsRuleFactory;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Closure;

/**
 * An in-memory store for route rules.
 *
 * @author Nathan Bishop <nbish11@hotmail.com> (https://nathanbishop.name)
 * @copyright 2019 Nathan Bishop
 * @license The MIT license.
 */
final class Mapper
{
    /**
     * @var string The request-target prefix.
     */
    private $prefix = '';

    /**
     * @var Collection|null The collection of route-rule objects.
     */
    private $rules;

    /**
     * @var RuleFactory|null Responsible for creating route-rule instances.
     */
    private $ruleFactory;

    /**
     * Constructor.
     *
     * @param Collection|null $ruleFactory The collection to hold the route-rules.
     * @param RuleFactory|null $ruleFactory The factory responsible for making route-rules.
     */
    public function __construct(Collection $rules = null, RuleFactory $ruleFactory = null)
    {
    	$this->rules = $rules ?: new Collection();
    	$this->ruleFactory = $ruleFactory ?: new RubyOnRailsRuleFactory();
    }

    /**
     * Adds a route that responds to the 'HEAD' request method to the collection.
     *
     * @param string $requestTarget The path/pattern the route should respond to.
     * @param callable $handler The method/function to call when a route is matched.
     * @return Rule The actual route object being added to the collection.
     */
    public function head(string $requestTarget, RequestHandler $handler): Rule
    {
        return $this->map('HEAD', $requestTarget, $handler);
    }

    /**
     * Adds a route that responds to the 'GET' request method to the collection.
     *
     * @param string $requestTarget The path/pattern the route should respond to.
     * @param callable $handler The method/function to call when a route is matched.
     * @return Rule The actual route object being added to the collection.
     */
    public function get(string $requestTarget, RequestHandler $handler): Rule
    {
        return $this->map('GET', $requestTarget, $handler);
    }

    /**
     * Adds a route that responds to the 'POST' request method to the collection.
     *
     * @param string $requestTarget The path/pattern the route should respond to.
     * @param callable $handler The method/function to call when a route is matched.
     * @return Rule The actual route object being added to the collection.
     */
    public function post(string $requestTarget, RequestHandler $handler): Rule
    {
        return $this->map('POST', $requestTarget, $handler);
    }

    /**
     * Adds a route that responds to the 'PUT' request method to the collection.
     *
     * @param string $requestTarget The path/pattern the route should respond to.
     * @param callable $handler The method/function to call when a route is matched.
     * @return Rule The actual route object being added to the collection.
     */
    public function put(string $requestTarget, RequestHandler $handler): Rule
    {
        return $this->map('PUT', $requestTarget, $handler);
    }

    /**
     * Adds a route that responds to the 'PATCH' request method to the collection.
     *
     * @param string $requestTarget The path/pattern the route should respond to.
     * @param callable $handler The method/function to call when a route is matched.
     * @return Rule The actual route object being added to the collection.
     */
    public function patch(string $requestTarget, RequestHandler $handler): Rule
    {
        return $this->map('PATCH', $requestTarget, $handler);
    }

    /**
     * Adds a route that responds to the 'DELETE' request method to the collection.
     *
     * @param string $requestTarget The path/pattern the route should respond to.
     * @param callable $handler The method/function to call when a route is matched.
     * @return Rule The actual route object being added to the collection.
     */
    public function delete(string $requestTarget, RequestHandler $handler): Rule
    {
        return $this->map('DELETE', $requestTarget, $handler);
    }

    /**
     * Adds a route that responds to the 'OPTIONS' request method to the collection.
     *
     * @param string $requestTarget The path/pattern the route should respond to.
     * @param callable $handler The method/function to call when a route is matched.
     * @return Rule The actual route object being added to the collection.
     */
    public function options(string $requestTarget, RequestHandler $handler): Rule
    {
        return $this->map('OPTIONS', $requestTarget, $handler);
    }

    /**
     * Add routes with a prefix attached to the request-target.
     *
     * @param string $prefix The request-target to be prefixed to every added route.
     * @param Closure $grouper Routes added from within this closure will be prefixed with the supplied request-target.
     */
    public function group(string $prefix, Closure $grouper): void
    {
        $previousPrefix = $this->prefix;
        $this->prefix = $previousPrefix . $prefix;

        $grouper->call($this);

        $this->prefix = $previousPrefix;
    }

    /**
     * Create a new route rule and add it to the collection.
     *
     * @param string $method The request method.
     * @param string $requestTarget The request target.
     * @param RequestHandler $handler The request handler.
     * @return Rule The rule that was created to match the request.
     */
    public function map(string $method, string $requestTarget, RequestHandler $handler): Rule
    {
        $rule = $this->ruleFactory->make($method, $this->prefix . $requestTarget, $handler);

        $this->rules->add($rule);

        return $rule;
    }

    /**
     * Get the collection containing the route-rules.
     *
     * @return Collection The route-rules that have been mapped.
     */
    public function getRules(): Collection
    {
    	return $this->rules;
    }
}
