<?php

use Rooles\ForbiddenHttpException;

class ForbiddenHttpExceptionTest extends BaseCase
{
    public function test_message_can_be_null()
    {
        $exception = new ForbiddenHttpException();

        $this->assertSame( '', $exception->getMessage());
    }
}
