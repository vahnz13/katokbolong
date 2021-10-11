<?php

declare(strict_types=1);

use DI\Container;
use Slim\Factory\AppFactory;

$container = new Container();

AppFactory::setContainer($container);

$app = AppFactory::create();

require __DIR__ . '/container.php';

require __DIR__ . '/middleware.php';

require __DIR__ . '/../routes/web.php';