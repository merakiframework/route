<?php
declare(strict_types=1);

namespace Meraki\Route;

use IteratorAggregate;
use Countable;
use Meraki\Route\Rule;
use LogicException;
use ArrayIterator;

/**
 * A store for route rules.
 *
 * @author Nathan Bishop <nbish11@hotmail.com> (https://nathanbishop.name)
 * @copyright 2019 Nathan Bishop
 * @license The MIT license.
 */
class Collection implements IteratorAggregate, Countable
{
	/**
     * @var Rule[] [$rules description]
     */
    private $rules = [];

    /**
     * Add a route to the collection.
     *
     * @param Rule $rule The route object to add.
     * @throws LogicException When trying to add a route that already exists.
     */
    public function add(Rule $rule): void
    {
        if ($this->contains($rule)) {
            throw new LogicException('Route rule already exists.');
        }

        $this->rules[] = $rule;
    }

    /**
     * Remove a route from the collection.
     *
     * @param Rule $rule The route object to remove.
     * @throws LogicException When trying to remove a route that has not beenadded previously.
     */
    public function remove(Rule $rule): void
    {
        $index = $this->indexOf($rule);

        if ($index === -1) {
        	throw new LogicException('Cannot remove a rule that is not in the collection.');
        }

        unset($this->rules[$index]);
    }

    /**
     * Check if a route is in the collection.
     *
     * @param Rule $rule The route to check for.
     * @return boolean `true` if the rule exists, otherwise `false`.
     */
    public function contains(Rule $rule): bool
    {
        return $this->indexOf($rule) !== -1;
    }

    /**
     * Get the order in which a route was added.
     *
     * @param Rule $rule The route object to check for.
     * @return integer The index (0 or a positive integer if it exists, -1 if it doesn't) of the route.
     */
    public function indexOf(Rule $rule): int
    {
        foreach ($this->rules as $key => $value) {
            if ($value === $rule) {
                return $key;
            }
        }

        return -1;
    }

    /**
     * Merge another route collection into this one.
     *
     * @param self $rules The other collection of routes to add.
     */
    public function merge(self $rules): void
    {
        foreach ($rules as $rule) {
            $this->add($rule);
        }
    }

    /**
     * Quickly determine if there are any routes contained in the collection.
     *
     * @return boolean `true`, if the collection has not routes, `false` otherwise.
     */
    public function isEmpty(): bool
    {
    	return empty($this->rules);
    }

    /**
     * Count the number of routes added.
     *
     * @return integer The number of routes added.
     */
    public function count(): int
    {
        return count($this->rules);
    }

    /**
     * Get an iterator that can be used with the `foreach` and other like constructs.
     *
     * @return ArrayIterator An iterator for the currently contained routes.
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->rules);
    }
}
