<?php
declare(strict_types=1);

namespace Meraki\Route;

use Meraki\Route\Pattern;
use Meraki\Route\Rule;
use Meraki\Route\Constraint;
use Meraki\TestSuite\TestCase;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use InvalidArgumentException;

/**
 * @covers RuleTest::
 */
final class RuleTest extends TestCase
{
	private $pattern;
	private $handler;

	public function setUp(): void
    {
    	$this->pattern = new Pattern('*');
    	$this->handler = $this->createMock(RequestHandler::class);
    }

    /**
     * @test
     */
    public function it_exists(): void
    {
        $itExists = class_exists(Rule::class);

        $this->assertTrue($itExists);
    }

    /**
     * @test
     */
    public function new_rule_has_no_media_types_defined(): void
    {
    	$rule = new Rule('HEAD', $this->pattern, $this->handler);

    	$supportedMediaTypes = $rule->getMediaTypes();

    	$this->assertEmpty($supportedMediaTypes);
    }

    /**
     * @test
     */
    public function can_add_a_media_type_explicitly_supported_by_rule(): void
    {
    	$expectedMediaType = 'application/json';
    	$rule = new Rule('HEAD', $this->pattern, $this->handler);
    	$rule->accept($expectedMediaType);

    	$supportedMediaTypes = $rule->getMediaTypes();

    	$this->assertContains($expectedMediaType, $supportedMediaTypes);
    }

    /**
     * @test
     */
    public function throws_exception_if_method_is_empty(): void
    {
    	$expectedException = new InvalidArgumentException('A request method was not provided.');

    	$callback = function () {
    		$rule = new Rule('', $this->pattern, $this->handler);
    	};

    	$this->assertThrows($expectedException, $callback);
    }

    /**
     * @test
     */
    public function method_is_set(): void
    {
    	$expectedMethod = 'gEt';
    	$rule = new Rule($expectedMethod, $this->pattern, $this->handler);

    	$actual = $rule->getMethod();

    	$this->assertEquals($expectedMethod, $actual);
    }

    /**
     * @test
     */
    public function handler_is_set(): void
    {
    	$rule = new Rule('get', $this->pattern, $this->handler);

    	$actual = $rule->getHandler();

    	$this->assertEquals($this->handler, $actual);
    }

    /**
     * @test
     */
    public function pattern_is_set(): void
    {
    	$rule = new Rule('get', $this->pattern, $this->handler);

    	$actual = $rule->getPattern();

    	$this->assertSame($this->pattern, $actual);
    }

    /**
     * @test
     */
    public function name_cannot_be_empty(): void
    {
    	$expectedException = new InvalidArgumentException('Name cannot be empty.');
    	$rule = new Rule('get', $this->pattern, $this->handler);

    	$callback = function () use ($rule) {
    		$rule->name('');
    	};

    	$this->assertThrows($expectedException, $callback);
    }

    /**
     * @test
     */
    public function name_is_immutable(): void
    {
    	$expectedException = new InvalidArgumentException('Name is immutable and cannot be changed once set.');
    	$rule = new Rule('get', $this->pattern, $this->handler);
    	$rule->name('test.route');

    	$callback = function () use ($rule) {
    		$rule->name('renamed.test.route');
    	};

    	$this->assertThrows($expectedException, $callback);
    }

    /**
     * @test
     */
    public function name_is_set(): void
    {
    	$expectedName = 'test.route';
    	$rule = new Rule('get', $this->pattern, $this->handler);

    	$rule->name($expectedName);

    	$this->assertEquals($expectedName, $rule->getName());
    }

    /**
     * @test
     */
    public function a_new_route_does_not_have_a_description(): void
    {
    	$rule = new Rule('get', $this->pattern, $this->handler);

    	$this->assertEquals('', $rule->getDescription());
    }

    /**
     * @test
     */
    public function a_description_can_be_set(): void
    {
    	$expectedDescription = 'A test route!';
    	$rule = new Rule('get', $this->pattern, $this->handler);

    	$rule->describe($expectedDescription);

    	$this->assertEquals($expectedDescription, $rule->getDescription());
    }

    /**
     * @test
     */
    public function can_add_constraints_to_placeholders(): void
    {
    	$constraint = Constraint::alpha();
    	$pattern = new Pattern('/say-hello/:name');
    	$rule = new Rule('get', $pattern, $this->handler);

    	$rule->constrain('name', $constraint);

    	$this->assertTrue($pattern->hasConstraint('name'));
    	$this->assertSame($constraint, $pattern->getConstraint('name'));
    }
}
