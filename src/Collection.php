<?php
declare(strict_types=1);

namespace Meraki\Route;

use IteratorAggregate;
use Countable;
use Meraki\Route\Rule;
use LogicException;
use ArrayIterator;

/**
 * Base class intended to be used with 'collections' of route rules.
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
     * Add a route rule to the collection.
     *
     * @param Rule $rule Add the rule if it does not already exist.
     */
    public function add(Rule $rule): void
    {
    	if ($this->contains($rule)) {
    		throw new LogicException('Rule already exists in collection.');
    	}

    	$this->rules[] = $rule;
    }

    /**
     * Remove a route rule from the collection.
     *
     * @param Rule $rule Remove the rule if it exists.
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
     * Check if a specific rule was collected.
     *
     * @param Rule $rule The rule to check for.
     * @return boolean `true` if the rule exists, otherwise `false`.
     */
    public function contains(Rule $rule): bool
    {
        return $this->indexOf($rule) !== -1;
    }

    /**
     * Get the order in which a rule was added
     *
     * @param Rule $rule The rule to check for.
     * @return integer The index of the rule.
     */
    public function indexOf(Rule $rule): int
    {
        foreach ($this as $key => $value) {
            if ($value === $rule) {
                return $key;
            }
        }

        return -1;
    }

    /**
     * Quickly determine if there are any rules contained in the collection.
     *
     * @return boolean `true`, if there are no rules in the collection, `false` otherwise.
     */
    public function isEmpty(): bool
    {
    	return count($this->rules) === 0;
    }

    /**
     * Count how many rules have been collected.
     *
     * @return integer The amount of rules contained within.
     */
    public function count(): int
    {
        return count($this->rules);
    }

    /**
     * Get an iterator that can be used with the `foreach` and other like constructs.
     *
     * @return ArrayIterator An iterator for the currently contained rules.
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->rules);
    }
}
