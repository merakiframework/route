<?php
declare(strict_types=1);

namespace Meraki\Route;

interface RuleFactory
{
	public function make(string $method, string $pattern, callable $handler): Rule;
}
