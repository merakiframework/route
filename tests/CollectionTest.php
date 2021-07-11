<?php
declare(strict_types=1);

namespace Meraki\Route;

use Meraki\Route\Collection;
use Meraki\Route\Pattern;
use Meraki\TestSuite\TestCase;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use LogicException;

/**
 * @covers Collection::
 */
final class CollectionTest extends TestCase
{
	private $basicRule;
    private $complexRule;

    public function setUp(): void
    {
    	$this->action = $this->createMock(RequestHandler::class);
        $this->basicRule = new Rule('get', new Pattern('/posts'), $this->action);
        $this->complexRule = new Rule('get', new Pattern('/api/:version/users/:id'), $this->action);
    }

    /**
     * @test
     */
    public function it_exists(): void
    {
        $itExists = class_exists(Collection::class);

        $this->assertTrue($itExists);
    }

    /**
     * @test
     */
    public function can_be_iterated_over(): void
    {
        $rules = new Collection();

        $this->assertInstanceOf('IteratorAggregate', $rules);
    }

    /**
     * @test
     */
    public function can_be_counted(): void
    {
        $rules = new Collection();

        $this->assertInstanceOf('Countable', $rules);
    }

	/**
     * @test
     */
    public function has_initial_count_of_zero(): void
    {
    	$rules = new Collection();

    	$this->assertCount(0, $rules);
    }

    /**
     * @test
     */
    public function adding_rule_increases_count(): void
    {
    	$rules = new Collection();

    	$rules->add($this->basicRule);

    	$this->assertCount(1, $rules);
    }

    /**
     * @test
     */
    public function removing_rule_decreases_count(): void
    {
    	$rules = new Collection();
    	$rules->add($this->basicRule);
    	$rules->add($this->complexRule);

    	$rules->remove($this->complexRule);

    	$this->assertCount(1, $rules);
    }

    /**
     * @test
     */
    public function is_empty_when_count_is_zero(): void
    {
    	$rules = new Collection();

    	$this->assertCount(0, $rules);
    	$this->assertEmpty($rules);
    	$this->assertTrue($rules->isEmpty());
    }

    /**
     * @test
     */
    public function is_not_empty_when_count_is_above_zero(): void
    {
    	$rules = new Collection();
    	$rules->add($this->basicRule);

    	$this->assertNotEmpty($rules);
    	$this->assertFalse($rules->isEmpty());
    }

    /**
     * @test
     */
    public function rule_is_in_collection_when_added(): void
    {
    	$rules = new Collection();

    	$rules->add($this->basicRule);
    	$rules->add($this->complexRule);

    	$this->assertTrue($rules->contains($this->basicRule));
    	$this->assertTrue($rules->contains($this->complexRule));
    }

    /**
     * @test
     */
    public function same_rule_cannot_be_added_twice(): void
    {
    	$expectedException = new LogicException('Route rule already exists.');
    	$rules = new Collection();
    	$rules->add($this->basicRule);

    	$callback = function () use ($rules) {
    		$rules->add($this->basicRule);
    	};

    	$this->assertThrows($expectedException, $callback);
    }

    /**
     * @test
     */
    public function rule_is_not_in_collection_when_removed(): void
    {
    	$rules = new Collection();
    	$rules->add($this->basicRule);
    	$rules->add($this->complexRule);

    	$rules->remove($this->basicRule);
    	$rules->remove($this->complexRule);

    	$this->assertFalse($rules->contains($this->basicRule));
    	$this->assertFalse($rules->contains($this->complexRule));
    }

    /**
     * @test
     */
    public function throws_exception_when_trying_to_remove_a_rule_that_was_not_added(): void
    {
    	$expectedException = new LogicException('Cannot remove a rule that is not in the collection.');
    	$rules = new Collection();

    	$callback = function () use($rules) {
    		$rules->remove($this->basicRule);
    	};

    	$this->assertThrows($expectedException, $callback);
    }

    /**
     * @test
     */
    public function can_retrieve_the_order_in_which_a_rule_was_added(): void
    {
    	$rules = new Collection();

    	$rules->add($this->basicRule);
    	$rules->add($this->complexRule);

    	$this->assertEquals(0, $rules->indexOf($this->basicRule));		// `0` indicates first-added
    	$this->assertEquals(1, $rules->indexOf($this->complexRule));	// `1` indicates second-added and so on...
    }
}
