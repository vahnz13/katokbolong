<?php
namespace App\Helpers;

class Twig extends \Twig\Extension\AbstractExtension
{
    public function getFunctions()
    {
        return [
            new \Twig\TwigFunction('ucwords', 'ucwords'),
            new \Twig\TwigFunction('strtoupper', 'strtoupper'),
            new \Twig\TwigFunction('count', 'count'),
            new \Twig\TwigFunction('toDayAgo', 'toDayAgo'),
            new \Twig\TwigFunction('toIsoFormat', 'toIsoFormat'),
            new \Twig\TwigFunction('photonResize', 'photon_resize'),
            new \Twig\TwigFunction('option', 'option'),
            new \Twig\TwigFunction('spintax', 'spintax'),
            new \Twig\TwigFunction('getTerms', 'get_terms'),
            new \Twig\TwigFunction('strLimit', 'str_limit'),
            new \Twig\TwigFunction('ytClean', 'yt_clean'),
            new \Twig\TwigFunction('autop', 'autop'),
            new \Twig\TwigFunction('getTopSong', '\App\Helpers\NextMP3::getTopSong'),
            new \Twig\TwigFunction('getPlaylist', '\App\Helpers\NextMP3::getPlaylist'),
            new \Twig\TwigFunction('getRelated', '\App\Helpers\NextMP3::getRelated'),
            new \Twig\TwigFunction('getComment', '\App\Helpers\NextMP3::getComment'),
            new \Twig\TwigFunction('themes', 'themes_url'),
            new \Twig\TwigFunction('site_url', 'site_url'),
            new \Twig\TwigFunction('subdomain', 'get_sub'),
            new \Twig\TwigFunction('canonical_url', 'canonical_url'),
            new \Twig\TwigFunction('site_domain', 'site_domain'),
            new \Twig\TwigFunction('search_permalink', 'search_permalink'),
            new \Twig\TwigFunction('single_permalink', 'single_permalink'),
            new \Twig\TwigFunction('download_permalink', 'download_permalink'),
            new \Twig\TwigFunction('page_permalink', 'page_permalink'),
            new \Twig\TwigFunction('playlist_permalink', 'playlist_permalink'),
            new \Twig\TwigFunction('genre_permalink', 'genre_permalink'),
            new \Twig\TwigFunction('get_sub', 'get_sub'),
        ];
    }
}