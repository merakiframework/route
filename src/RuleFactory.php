<?php
declare(strict_types=1);

namespace Meraki\Route;

use Meraki\Route\Rule;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

interface RuleFactory
{
	public function make(string $method, string $pattern, RequestHandler $handler): Rule;
}
