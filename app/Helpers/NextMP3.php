<?php
namespace App\Helpers;
use App\Helpers\Cache;

class NextMP3
{
  public static function getTopSong($limit='10', $genre=false) {
    $fastCache = Cache::getAdapter();
    $cacheName = md5($limit.$genre);

    $options = [
      'genre' => $genre,
      'limit' => $limit
    ];

    if (!$fastCache->has($cacheName)) {
      $data = \App\Helpers\Agc::iTunes_topsongs_api($options);

      if ( option( 'cache_itunes', true ) && isset($data['items']) ) {
        $fastCache->set($cacheName, $data, option('cache_topsong_expiration_time'));
      }
    }else{
      $data = $fastCache->get($cacheName);
    }

    return $data;
  }

  public static function getPlaylist($id='', $limit='10'){
    $fastCache = Cache::getAdapter();
    $cacheName = md5($id.$limit);

    $options = [
      'id' => $id,
      'limit' => $limit,
    ];

    if (!$fastCache->has($cacheName)) {
      $data = \App\Helpers\Agc::ytPlaylist_api( $options );

      if ( option( 'cache_playlist', true ) && isset($data['items']) ) {
        $fastCache->set($cacheName, $data, option('cache_playlist_expiration_time'));
      }
    }else{
      $data = $fastCache->get($cacheName);
    }

    return $data;
  }

  public static function getRelated($id='', $limit=10){
    $fastCache = Cache::getAdapter();
    $cacheName = md5($id.$limit);
    
    $options = [
      'id' => $id,
      'limit' => $limit,
    ];

    if (!$fastCache->has($cacheName)) {
      $data = \App\Helpers\Agc::ytRelated_api( $options );

      if ( option( 'cache_related', true ) && isset($data['items']) ) {
        $fastCache->set($cacheName, $data, option('cache_related_expiration_time'));
      }
    }else{
      $data = $fastCache->get($cacheName);
    }

    return $data;
  }

  public static function getDownload($id=''){
    $fastCache = Cache::getAdapter();
    $cacheName = md5($id);

    $options = [
      'id' => $id,
    ];

    if (!$fastCache->has($cacheName)) {
      $data = \App\Helpers\Agc::ytDownload_api( $options );

      if ( option( 'cache_download', true ) && isset($data) ) {
        $fastCache->set($cacheName, $data, option('cache_download_expiration_time'));
      }
    }else{
      $data = $fastCache->get($cacheName);
    }

    return $data;
  }

  public static function getSearch($query='', $limit=10){
    $fastCache = Cache::getAdapter();
    $cacheName = md5($query.$limit);
    
    $options = [
      'query' => $query,
      'limit' => $limit,
    ];

    if (!$fastCache->has($cacheName)) {
      $data = \App\Helpers\Agc::ytSearch_api( $options );

      if ( option( 'cache_search', true ) && isset($data['items']) ) {
        $fastCache->set($cacheName, $data, option('cache_search_expiration_time'));
      }
    }else{
      $data = $fastCache->get($cacheName);
    }

    return $data;
  }

  public static function getComment($id='', $limit=10){
    $options = [
      'id' => $id,
      'limit' => $limit,
    ];

    $data = \App\Helpers\Agc::ytComments_api( $options );
    
    return $data;
  }
}