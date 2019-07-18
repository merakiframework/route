<?php
declare(strict_types=1);

namespace Meraki\Route\Exception;

use RuntimeException;
use Meraki\Route\Exception as RouteException;
use LogicException;

/**
 * Exception used when the method of a route could not be matched to the request.
 *
 * @author Nathan Bishop <nbish11@hotmail.com> (https://nathanbishop.name)
 * @copyright 2019 Nathan Bishop
 * @license The MIT license.
 */
final class MethodNotMatched extends RuntimeException implements RouteException
{
    /**
     *
     */
    public const EQUIVALENT_STATUS_CODE = 405;

    /**
     * @var string [$failedMethod description]
     */
    private $failedMethod;

    /**
     * @var string[] [$allowedMethods description]
     */
    private $allowedMethods;

    /**
     * [__construct description]
     *
     * @param string $failedMethod [description]
     * @param string[] $allowedMethods [description]
     */
    public function __construct(string $failedMethod, array $allowedMethods = [])
    {
        $this->failedMethod = strtoupper($failedMethod);
        $this->allowedMethods = array_map('strtoupper', $allowedMethods);

        if (in_array($this->failedMethod, $this->allowedMethods)) {
            throw new LogicException('The method that failed the match should not be in the allowed methods.');
        }

        parent::__construct($this->generateMessage(), self::EQUIVALENT_STATUS_CODE);
    }

    /**
     * [getFailedMethod description]
     *
     * @return string [description]
     */
    public function getFailedMethod(): string
    {
        return $this->failedMethod;
    }

    /**
     * [getAllowedMethods description]
     *
     * @return string[] [description]
     */
    public function getAllowedMethods(): array
    {
        return $this->allowedMethods;
    }

    /**
     * [generateMessage description]
     *
     * @return strings [description]
     */
    private function generateMessage(): string
    {
        $message = sprintf('The "%s" method could not be matched.', $this->failedMethod);

        if (!empty($this->allowedMethods)) {
            $message .= sprintf(' Try one of the following: %s', implode(', ', $this->allowedMethods));
        }

        return $message;
    }
}
