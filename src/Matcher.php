<?php
declare(strict_types=1);

namespace Meraki\Route;

use Meraki\Route\Collector;
use Psr\Http\Message\ServerRequestInterface as ServerRequest;
use Meraki\Route\Rule;
use Meraki\Route\Exception\RequestTargetNotMatched;
use Meraki\Route\Exception\MethodNotMatched;

/**
 * Cycles through a route group yielding any route rules that match the request.
 */
final class Matcher
{
    /**
     * @var Collection [$rules description]
     */
    private $rules;

    /**
     * [__construct description]
     *
     * @param Collection $rules [description]
     */
    public function __construct(Collection $rules)
    {
        $this->rules = $rules;
    }

    /**
     * [match description]
     *
     * @param ServerRequest $request [description]
     * @return MatchResult [description]
     */
    public function match(ServerRequest $request): MatchResult
    {
        $matchedRules = [];
        $allowedMethods = [];

        // check for rules that matches the request-target
        foreach ($this->rules as $rule) {
            if ($rule->getPattern()->matches($request->getRequestTarget())) {
                $matchedRules[] = $rule;
            }
        }

        // else, 404
        if (empty($matchedRules)) {
            return MatchResult::notFound($request);
        }

        // make sure rule matches method used and build 'allowed methods' in case of 405
        foreach ($matchedRules as $matchedRule) {
            $allowedMethods[] = $matchedRule->getMethod();

            if ($matchedRule->matchesMethod($request->getMethod())) {
            	return MatchResult::found($request, $matchedRule);
            }
        }

        return MatchResult::methodNotAllowed($request, $allowedMethods);
    }
}