<?php
namespace App\Settings;

class Sites
{
  public static function get(){
    return [
      'site_name' => 'Download Popular MP3 Songs',
      'site_tagline' => 'Awesome collection of trending and viral songs in the World right now.',

      /**
      * Pengaturan Theme
      */
      'theme_name' => 'luvmp3',

      /**
      * Pengaturan SEO
      * - Home Title Tag : %site_name%, %site_tagline%, %subdomain%
      * - Search Title Tag : %query%, %title%, %size%, %duration%, %slug%, %subdomain%
      * - Single & Download Title Tag : %title%, %size%, %duration%, %subdomain%
      * - Page, Genre & Playlist Title Tag : %name%, %subdomain%
      *
      * - Home Description Tag : %site_name%, %site_tagline%, %domain%
      * - Search Description Tag : %query%, %title%, %channelTitle%, %duration%, %ptsTime%, %size%, %viewCount%, %likeCount%, %dislikeCount%, %publishedAt%, %createdAt%, %slug%, %site_name%, %domain%.
      * - Single & Download Description Tag : %title%, %channelTitle%, %duration%, %ptsTime%, %size%, %viewCount%, %likeCount%, %dislikeCount%, %publishedAt%, %createdAt%, %site_name%, %domain%.
      * - Genre & Playlist Description Tag :  %title%, %site_name%, %createdAt%, %domain%
      * - Page Description Tag :  %title%, %site_name%, %domain%
      *
      */
    
      'home_title' => '%subdomain% - %site_tagline%',
      'home_meta_description' => '%site_name% The most complete collections mp3 download with lyrics and full album also with POP, K-Pop, Religious, Classic, and almost any songs from all over the world.',
      'home_meta_robots' => 'index,follow',
    
      'search_title' => '{Download|Get} %query%, %title%, %size%, %duration%, %slug% - %subdomain%',
      'search_meta_description' => '{Download|Get} Mp3 New %query%, %title%, %channelTitle%, %duration%, %ptsTime%, %size%, %viewCount%, %likeCount%, %dislikeCount%, %publishedAt%, %createdAt%, %slug%, %site_name%, %domain%',
      'search_meta_robots' => 'index,follow',
    
      'single_title' => '%title%, %size%, %duration% - %site_name%',
      'single_meta_description' => '%title%, %channelTitle%, %duration%, %ptsTime%, %size%, %viewCount%, %likeCount%, %dislikeCount%, %publishedAt%, %createdAt%, %site_name%, %domain%',
      'single_meta_robots' => 'noindex,follow',
    
      'download_title' => '%title%, %size%, %duration% - %site_name%',
      'download_meta_description' => '%title%, %channelTitle%, %duration%, %ptsTime%, %size%, %viewCount%, %likeCount%, %dislikeCount%, %publishedAt%, %createdAt%, %site_name%, %domain%.',
      'download_meta_robots' => 'noindex,follow',
    
      'page_title' => '%title% - %site_name%',
      'page_meta_description' => '%title%, %site_name%, %domain%',
      'page_meta_robots' => 'noindex,follow',
    
      'playlist_title' => 'Playlist %title% - %site_name%',
      'playlist_meta_description' => '%title%, %site_name%, %createdAt%, %domain%',
      'playlist_meta_robots' => 'noindex,follow',

      'genre_title' => 'Genre %title% - %site_name%',
      'genre_meta_description' => '%title%, %site_name%, %createdAt%, %domain%',
      'genre_meta_robots' => 'noindex,follow',

      '404_title' => 'Page Not Found - %site_name%',
      '404_meta_description' => '%site_name% %site_tagline% %domain%',
      '404_meta_robots' => 'noindex,follow',
    
      /**
       * Pengaturan Pemalink AGC
       * - Search Tag : %slug%
       * - Download Tag : %slug%, %id%
       * - Page, Genre, Playlist Tag : %name%
       */

      'permalink_slug_separator' => '+',
  
      'search_permalink' => '/song/%lang%/%id%/%slug%/s',
      'single_permalink' => '/single/%id%/%slug%',
      'download_permalink' => '/download/%id%',
    
      'page_permalink' => '/page/%slug%',
      'playlist_permalink' => '/playlist/%slug%',
      'genre_permalink' => '/genre/%slug%',
    
      'sitemap_permalink' => '/sitemap.xml',
      'flush_cache_permalink' => '/flush-cache',

      // Pengaturan Youtube
      'youtube_api_keys' => 'AIzaSyA-dlBUjVQeuc4a6ZN4RkNUYDFddrVLxrA',
      'youtube_country_code' => 'US',
      'youtube_search_limit' => 25,
      'youtube_related_limit' => 10,
      'youtube_playlist_limit' => 20,

      // Pengaturan Itunes
      'itunes_genre_limit' => 50,
    
      // Ubah menjadi 'false' jika tidak ingin mengaktifkan cache
      'cache_search' => true,
      'cache_download' => true,
      'cache_related' => true,
      'cache_playlist' => true,
      'cache_itunes' => true,
      'cache_sitemap' => true,
    
      // Isi dalam detik. 604800 berarti cache akan disimpan selama 1 minggu
      'cache_search_expiration_time' => 604800,
      'cache_download_expiration_time' => 604800,
      'cache_related_expiration_time' => 604800,
      'cache_playlist_expiration_time' => 604800,
      'cache_topsong_expiration_time' => 604800,
      'cache_sitemap_expiration_time' => 604800,
    
      // Sitemap
      'sitemap_limit' => 250000,

      // Experiment
      'shuffle_results' => true,
      'search_results_exc' => [0,1],
      'translate_lang' => ['en','id','hi', 'ko', 'ms', 'pt', 'ru', 'ms', 'ar', 'nl', 'fr', 'de', 'ja', 'it', 'la', 'es', 'tr', 'th', 'vi', 'tl'], // https://cloud.google.com/translate/docs/languages
      'limit_domain' => '4'
    ];
  }
}