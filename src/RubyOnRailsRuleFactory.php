<?php
declare(strict_types=1);

namespace Meraki\Route;

use Meraki\Route\RuleFactory;
use Meraki\Route\Rule;
use Meraki\Route\Pattern;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

final class RubyOnRailsRuleFactory implements RuleFactory
{
	public function make(string $method, string $pattern, RequestHandler $handler): Rule
	{
		return new Rule($method, new Pattern($pattern), $handler);
	}
}
