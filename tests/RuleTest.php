<?php
declare(strict_types=1);

namespace Meraki\Route;

use Meraki\TestSuite;
use Meraki\Route\Rule;

final class RuleTest extends TestSuite
{
    /**
     * @test
     */
    public function it_exists(): void
    {
        $itExists = interface_exists(Rule::class);

        $this->assertTrue($itExists);
    }

    /**
     * @test
     */
    public function method_is_normalised_to_uppercase(): void
    {
    	$rule = new Rule('get', '/', $this->action);

    	$method = $rule->getMethod();

    	$this->assertEquals('GET', $method);
    }

    /**
     * @test
     * @dataProvider invalidForms
     */
    public function throws_exception_if_not_an_allowed_form(string $invalidForm): void
    {
    	$expectedException = new InvalidArgumentException('Request target must me in asterix or absolute form.');

    	$callback = function () use ($invalidForm) {
    		$rule = new Rule('GET', $invalidForm, $this->action);
    	};

    	$this->assertThrows($expectedException, $callback);
    }

    /**
     * @test
     * @dataProvider validForms
     */
    public function throws_exception_if_not_an_allowed_form(string $validForm): void
    {
    	$expectedException = new InvalidArgumentException('Request target must me in asterix or absolute form.');

    	$callback = function () use ($validForm) {
    		$rule = new Rule('GET', $validForm, $this->action);
    	};

    	$this->assertThrows($expectedException, $callback);
    }

    /**
     * @test
     */
    public function can_name_rule(): void
    {
    	$expectedName = 'my.test.route';
    	$rule = new Rule('get', '/', $this->action);

    	$rule->name($expectedName);

    	$this->assertEquals($expectedName, $rule->getName());
    }

    public function rule_name_cannot_be_empty(): void
    {
    	$expectedException = new InvalidArgumentException('Rule name cannot be empty!');
    }

    public function invalidForms(): array
    {
    	return [
    		'' => []
    	];
    }

    public function validForms(): array
    {
    	return [
    		'asterix form' => ['*']
    	];
    }
}
