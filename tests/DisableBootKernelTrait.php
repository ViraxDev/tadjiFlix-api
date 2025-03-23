<?php

namespace App\Tests;

trait DisableBootKernelTrait
{
    /**
     * Prevent to recreate the database each time a new client is created
     * @see https://github.com/api-platform/core/issues/6991
     */
    protected function setUp(): void
    {
        parent::setUp();
        static::$alwaysBootKernel = false;
    }
}
