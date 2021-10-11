<?php

declare(strict_types=1);

use Slim\Views\Twig;

$container->set('view', function() {
    $view = Twig::create( __DIR__ . '/../themes/'. option('theme_name'), [
        'cache' => __DIR__ . '/../data/cache/views'
    ]);
    $view->addExtension(new \App\Helpers\Twig());
    return $view;
});