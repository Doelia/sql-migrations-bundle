<?php

namespace SWouters\SqlMigrationsBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class SqlMigrationsBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

}
