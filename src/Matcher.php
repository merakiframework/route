<?php
declare(strict_types=1);

namespace Meraki\Route;

use Meraki\Route\Collection;
use Meraki\Route\Rule;
use Meraki\Route\Exception\RequestTargetNotMatched;
use Meraki\Route\Exception\MethodNotMatched;
use Psr\Http\Message\ServerRequestInterface as ServerRequest;
use Negotiation\Negotiator;

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
    public function __construct(Collection $rules, Negotiator $negotiator = null)
    {
        $this->rules = $rules;
        $this->negotiator = $negotiator ?: new Negotiator();
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
        $rulesMatchingMethod = [];
        $allowedMediaTypes = [];

        // check for rules that matches the request-target
        foreach ($this->rules as $rule) {
            if ($rule->getPattern()->matches($request->getRequestTarget())) {
                $matchedRules[] = $rule;
            }
        }

        // else, 404
        if (empty($matchedRules)) {
            return MatchResult::requestTargetNotMatched($request);
        }

        $requestMethod = $this->guessCorrectMethod($request);

        // make sure rule matches method used and build 'allowed methods' in case of 405
        foreach ($matchedRules as $matchedRule) {
            $allowedMethods[] = $matchedRule->getMethod();

            if (strcasecmp($matchedRule->getMethod(), $requestMethod) === 0) {
            	$rulesMatchingMethod[] = $matchedRule;
            }
        }

        // request-target and method do not match, 405
        if (empty($rulesMatchingMethod)) {
        	return MatchResult::methodNotMatched($request, $this->removeDuplicateMethods($allowedMethods));
        }

    	$acceptHeader = $request->getHeaderLine('Accept');
    	$genericMatch = null;

    	// content negotiation: check for rules explicitly matching content-type
    	foreach ($rulesMatchingMethod as $rule) {
    		$supportedMediaTypes = $rule->getMediaTypes();

    		// a rule with no defined media-types is the equivalent of accepting anything (*/*)
    		// save the first match in case no specific one can be found.
    		if (empty($supportedMediaTypes)) {
    			if (!$genericMatch) {
    				$genericMatch = $rule;
    			}

    			continue;
    		}

    		$allowedMediaTypes = array_merge($allowedMediaTypes, $supportedMediaTypes);

    		// a more specific match was found: return it.
    		if ($mediaType = $this->negotiator->getBest($acceptHeader, $supportedMediaTypes)) {
    			return MatchResult::matched($request, $rule);
    		}
    	}

    	if ($genericMatch) {
    		return MatchResult::matched($request, $genericMatch);
    	}

        return MatchResult::acceptHeaderNotMatched($request, $allowedMediaTypes);
    }

    private function guessCorrectMethod(ServerRequest $request): string
    {
    	$method = $request->getMethod();

		if (strcasecmp($method, 'POST') === 0 && array_key_exists('_METHOD', $request->getParsedBody())) {
			$method = $request->getParsedBody()['_METHOD'];
		} else if ($request->hasHeader('X-HTTP-Method-Override')) {
			$method = $request->getHeaderLine('X-HTTP-Method-Override');
		}

		return $method;
    }

    private function removeDuplicateMethods(array $methods): array
    {
    	// array_unique() function does not re-index array so using the
    	// array_keys()/array_flip() trick to make array unique instead.
    	return array_keys(array_flip(array_map('strtoupper', $methods)));
    }
}
