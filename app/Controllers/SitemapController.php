<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\Cache;
use Psr\Http\Message\{
    ServerRequestInterface as Request,
    ResponseInterface as Response
};
use Thepixeldeveloper\Sitemap\Urlset;
use Thepixeldeveloper\Sitemap\Url;
use Thepixeldeveloper\Sitemap\Drivers\XmlWriterDriver;

class SitemapController extends Controller
{
    public function Index(Request $request, Response $response){
        $fastCache = Cache::getAdapter();
        $cacheName = md5(site_domain());

        $urlset = new Urlset();

        $url = new Url(site_url());
        $url->setChangeFreq('daily');
        $url->setPriority('1.0');
        $urlset->add($url);

        $terms = get_terms(option('sitemap_limit'));

        if (count($terms['items']) > 0){
            foreach($terms['items'] as $item){
                $url = new Url(search_permalink($item));
                $url->setChangeFreq('daily');
                $url->setPriority('0.8');
                $urlset->add($url);
            }
        } 

        $driver = new XmlWriterDriver();
        $urlset->accept($driver);

        if (!$fastCache->has($cacheName)) {
            $xml = $driver->output();
            if ( option( 'cache_sitemap', true ) && count($terms['items']) > 0 ) {
              $fastCache->set($cacheName, $xml, option('cache_sitemap_expiration_time'));
            }
          }else{
            $xml = $fastCache->get($cacheName);
        }

        $response->getBody()->write($xml);
        return $response->withHeader('Content-Type', 'application/xml');
    }
}