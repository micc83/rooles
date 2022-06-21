<?php

namespace Rooles;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class ForbiddenHttpException
 * @package Rooles
 */
class ForbiddenHttpException extends HttpException
{
    /**
     * Constructor.
     *
     * @param string|null $message The internal exception message
     */
    public function __construct(string $message = null)
    {
        parent::__construct(403, (string) $message, null, [], 0);
    }

}
