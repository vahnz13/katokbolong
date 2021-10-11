<?php

declare(strict_types=1);

use App\Controllers\{
    PagesController,
    SitemapController
};

$app->get('/', PagesController::class . ':Home')->setName('home');
$app->get(search_route('{slug}', '{id}', '{lang}'), PagesController::class . ':Search')->setName('search');
$app->get(single_route('{id}','{slug}'), PagesController::class . ':Single')->setName('single');
$app->get(download_route('{id}'), PagesController::class . ':Download')->setName('download');
$app->get(page_route('{slug}'), PagesController::class . ':Page')->setName('page');
$app->get(playlist_route('{slug}'), PagesController::class . ':Playlist')->setName('playlist');
$app->get(genre_route('{slug}'), PagesController::class . ':Genre')->setName('genre');
$app->get(option('sitemap_permalink'), SitemapController::class . ':Index')->setName('sitemap');
$app->get(option('flush_cache_permalink'), PagesController::class . ':FlushCache')->setName('flush.cache');
$app->get('/api/comments', PagesController::class . ':ApiComments')->setName('api.comments');
$app->any('{route:.*}', PagesController::class . ':NotFound')->setName('404');