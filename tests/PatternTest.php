<?php
declare(strict_types=1);

namespace Meraki\Route;

use Meraki\TestSuite;
use Meraki\Route\Pattern;

final class PatternTest extends TestSuite
{
    /**
     * @test
     */
    public function it_exists(): void
    {
        $itExists = class_exists(Pattern::class);

        $this->assertTrue($itExists);
    }

    /**
     * @test
     */
    public function query_string_is_removed_from_pattern(): void
    {
    	$pattern = new Pattern('/users?per_page=30');

    	$actual = (string) $pattern;

    	$this->assertEquals('/users', $actual);
    }

    /**
     * @test
     */
    public function no_placeholders_are_set_until_compiled(): void
    {
    	$pattern = new Pattern('/api/:version/users/:id');

    	$placeholders = $pattern->getPlaceholders();

    	$this->assertEmpty($placeholders);
    }

    /**
     * @test
     */
    public function pattern_without_placeholders_does_nothing_when_compiled(): void
    {
    	$pattern = new Pattern('/posts/456/comments/123');

    	$regex = $pattern->compile();

    	$this->assertEmpty($pattern->getPlaceholders());
    }

    /**
     * @test
     */
    public function pattern_with_placeholders_are_parsed_and_populated_when_compiled(): void
    {
    	$expectedPlaceholders = ['version' => null, 'id' => null];
    	$pattern = new Pattern('/api/:version/users/:id');

    	$regex = $pattern->compile();

    	$this->assertEquals($expectedPlaceholders, $pattern->getPlaceholders());
    }

    /**
     * @test
     */
    public function regex_should_match_pattern_when_compiled_without_placeholders(): void
    {
    	$requestTarget = '/posts/456/comments/123';
    	$pattern = new Pattern($requestTarget);

    	$regex = $pattern->compile();
    	$matched = preg_match($regex, $requestTarget) === 1;

    	$this->assertTrue($matched);
    }

    /**
     * @test
     */
    public function regex_should_match_pattern_when_compiled_with_placeholders(): void
    {
    	$requestTarget = '/api/3/users/123';
    	$pattern = new Pattern('/api/:version/users/:id');

    	$regex = $pattern->compile();
    	$matched = preg_match($regex, $requestTarget) === 1;

    	$this->assertTrue($matched);
    }

    /**
     * @test
     */
    public function returns_true_if_pattern_matches_input(): void
    {
    	$pattern = new Pattern('/say-hello/:person');

    	$matched = $pattern->matches('/say-hello/nathan');

    	$this->assertTrue($matched);
    }

    /**
     * @test
     */
    public function returns_false_if_pattern_does_not_match_input(): void
    {
    	$pattern = new Pattern('/say-hello/:person');

    	$matched = $pattern->matches('/say-hello/nathan/bishop');

    	$this->assertFalse($matched);
    }

    /**
     * @test
     */
    public function attempting_a_match_will_compile_the_pattern_before_matching(): void
    {
    	$pattern = new Pattern('/api/:version/users/:id');

    	$result = $pattern->matches('/api/3/users/123');

    	// assume the `compile()` method was called if the following assertions pass
    	$this->assertTrue($result);
    	$this->assertNotEmpty($pattern->getPlaceholders());
    }

    /**
     * @test
     */
    public function if_a_match_succeeds_placeholders_will_be_populated_with_the_appropriate_values(): void
    {
    	$expectedPlaceholders = ['version' => '3', 'id' => '123'];
    	$pattern = new Pattern('/api/:version/users/:id');

    	$result = $pattern->matches('/api/3/users/123');

    	$this->assertEquals($expectedPlaceholders, $pattern->getPlaceholders());
    }
}
