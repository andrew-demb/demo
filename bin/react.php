#!/usr/bin/env php
<?php

/**
 * PHP Service Bus (CQS implementation) Demo application
 *
 * @author  Maksim Masiukevich <desperado@minsk-info.ru>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types = 1);

include_once __DIR__ . '/../vendor/autoload.php';

use Desperado\ServiceBusDemo\Application\Bootstrap;
use Desperado\Domain\ThrowableFormatter;

try
{
    $bootstrap = Bootstrap::boot(
        __DIR__ . '/..',
        __DIR__ . '/../cache',
        __DIR__ . '/../.env'
    );

    $bootstrap
        ->getContainer()
        ->get('service_bus.http_server.entry_point')
        ->run([\getenv('APP_ENTRY_POINT_NAME')]);
}
catch(\Throwable $throwable)
{
    echo ThrowableFormatter::toString($throwable) . \PHP_EOL;
}
