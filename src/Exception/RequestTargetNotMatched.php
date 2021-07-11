<?php
declare(strict_types=1);

namespace Meraki\Route\Exception;

use Meraki\Route\Exception as RouteException;
use RuntimeException;

/**
 * Exception used when the pattern of a route could not be matched against the request-target.
 *
 * @author Nathan Bishop <nbish11@hotmail.com> (https://nathanbishop.name)
 * @copyright 2019 Nathan Bishop
 * @license The MIT license.
 */
final class RequestTargetNotMatched extends RuntimeException implements RouteException
{
    /**
     *
     */
    public const EQUIVALENT_STATUS_CODE = 404;

    /**
     * @var string [$failedRequestTarget description]
     */
    private $failedRequestTarget;

    /**
     * [__construct description]
     *
     * @param string $failedRequestTarget [description]
     */
    public function __construct(string $failedRequestTarget)
    {
        $this->failedRequestTarget = $failedRequestTarget;

        parent::__construct($this->generateMessage(), self::EQUIVALENT_STATUS_CODE);
    }

    /**
     * [getFailedRequestTarget description]
     *
     * @return string [description]
     */
    public function getFailedRequestTarget(): string
    {
        return $this->failedRequestTarget;
    }

    /**
     * [generateMessage description]
     *
     * @return string [description]
     */
    private function generateMessage(): string
    {
    	return sprintf('The request target "%s" could not be matched!', $this->failedRequestTarget);
    }
}
