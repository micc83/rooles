<?php

namespace Rooles;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class UnauthorizedHttpException
 * @package Rooles
 */
class UnauthorizedHttpException extends HttpException
{

    /**
     * Constructor.
     *
     * @param string $message The internal exception message
     */
    public function __construct($message = null)
    {
        parent::__construct(401, $message, null, [], 0);
    }

}