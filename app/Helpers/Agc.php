<?php
namespace App\Helpers;
use Carbon\Carbon;

class Agc
{
  public static function ytSearch_api( $options = [] ) {
    $data = [];
    $url = 'https://www.googleapis.com/youtube/v3/search?part=snippet&q=' . urlencode( $options['query'] ) . '&type=video&regionCode=US&maxResults=' . $options['limit'] . '&key=' . youtube_api_key(option('youtube_api_keys'));
    $response = fetch( $url );

    if ( $response->getStatusCode() == 200 ) {
      $json = json_decode( $response->getBody(), true );

      if ( !empty( $json['items'] ) ) {
        foreach ( $json['items'] as $item )
          $video_ids[] = $item['id']['videoId'];

        unset( $item );

        $url_detail = 'https://www.googleapis.com/youtube/v3/videos?part=snippet,contentDetails,statistics&id=' . implode(',', $video_ids ) . '&key=' . youtube_api_key(option('youtube_api_keys'));
        $response_detail = fetch( $url_detail );

        if ( $response_detail->getStatusCode() == 200 ) {
          $json_detail = json_decode( $response_detail->getBody(), true );
            
          foreach ( $json_detail['items'] as $item ) {
            $snippet = $item['snippet'];
            $content_details = $item['contentDetails'];
            $statistics = $item['statistics'];

            $item_data['id'] = $item['id'];
            $item_data['title'] = preg_replace('/["?]/', '', $snippet['title']);
            $item_data['description'] = remove_http($snippet['description']);
            $item_data['thumbnails'] = $snippet['thumbnails'];
            $item_data['channelId'] = $item['snippet']['channelId'];
            $item_data['channelTitle'] = $snippet['channelTitle'];

            $duration = convert_youtube_time( $content_details['duration'] );
            $exp_duration = explode( ':', $duration );

            if ( count( $exp_duration ) == 2 ) {
                $parsed = date_parse( '00:' . $duration );
                $seconds = ( $parsed['minute'] * 60 ) + $parsed['second'];
            } else {
                $parsed = date_parse( $duration );
                $seconds = ( $parsed['hour'] * 60 * 60 ) + ( $parsed['minute'] * 60 ) + $parsed['second'];
            }

            $item_data['duration'] = $duration;
            $item_data['ptsTime'] = $content_details['duration'];
            $item_data['size'] = format_bytes( ( $seconds * ( 192 / 8 ) * 1000 ) );
            $item_data['second'] = $seconds;
            $item_data['viewCount'] = isset($statistics['viewCount']) ? number_format($statistics['viewCount']) : 0;
            $item_data['likeCount'] = isset($statistics['likeCount']) ? number_format($statistics['likeCount']) : 0;
            $item_data['dislikeCount'] = isset($statistics['dislikeCount']) ? number_format($statistics['dislikeCount']) : 0;
            $item_data['publishedAt'] = Carbon::parse($snippet['publishedAt']);
            $item_data['tags'] = isset($snippet['tags']) ? $snippet['tags'] : [];

            $data[] = $item_data;
          }

          $data_final = array(
            'items'=> $data,
            'createdAt' => Carbon::now()
          );
        }
      }
    }

    if (isset($data_final['items'])){
      
      if(option('shuffle_results') == true){
        $data_final['items'] = shuffle_exclude($data_final['items'], option('search_results_exc'));
      }

      return $data_final;
    } else {
      return $data;
    }
  }

  public static function ytRelated_api( $options = [] ) {
    $data = [];
    $url = 'https://www.googleapis.com/youtube/v3/search?part=snippet&type=video&regionCode=US&maxResults='. $options['limit'] .'&relatedToVideoId=' . $options['id'] . '&key=' . youtube_api_key( option('youtube_api_keys'));
    $response = fetch( $url );
    
    if ( $response->getStatusCode() == 200 ) {
      $json = json_decode( $response->getBody(), true );

      if ( !empty( $json['items'] ) ) {
        foreach ( $json['items'] as $item )
          $video_ids[] = $item['id']['videoId'];

        unset( $item );

        $url_detail = 'https://www.googleapis.com/youtube/v3/videos?part=snippet,contentDetails,statistics&id=' . implode(',', $video_ids ) . '&key=' . youtube_api_key(option('youtube_api_keys'));
        $response_detail = fetch( $url_detail );

        if ( $response_detail->getStatusCode() == 200 ) {
          $json_detail = json_decode( $response_detail->getBody(), true );
            
          foreach ( $json_detail['items'] as $item ) {
            $snippet = $item['snippet'];
            $content_details = $item['contentDetails'];
            $statistics = $item['statistics'];

            $item_data['id'] = $item['id'];
            $item_data['title'] = preg_replace('/["?]/', '', $snippet['title']);
            $item_data['description'] = remove_http($snippet['description']);
            $item_data['thumbnails'] = $snippet['thumbnails'];
            $item_data['channelId'] = $item['snippet']['channelId'];
            $item_data['channelTitle'] = $snippet['channelTitle'];

            $duration = convert_youtube_time( $content_details['duration'] );
            $exp_duration = explode( ':', $duration );

            if ( count( $exp_duration ) == 2 ) {
                $parsed = date_parse( '00:' . $duration );
                $seconds = ( $parsed['minute'] * 60 ) + $parsed['second'];
            } else {
                $parsed = date_parse( $duration );
                $seconds = ( $parsed['hour'] * 60 * 60 ) + ( $parsed['minute'] * 60 ) + $parsed['second'];
            }

            $item_data['duration'] = $duration;
            $item_data['ptsTime'] = $content_details['duration'];
            $item_data['size'] = format_bytes( ( $seconds * ( 192 / 8 ) * 1000 ) );
            $item_data['second'] = $seconds;
            $item_data['viewCount'] = isset($statistics['viewCount']) ? number_format($statistics['viewCount']) : 0;
            $item_data['likeCount'] = isset($statistics['likeCount']) ? number_format($statistics['likeCount']) : 0;
            $item_data['dislikeCount'] = isset($statistics['dislikeCount']) ? number_format($statistics['dislikeCount']) : 0;
            $item_data['publishedAt'] = Carbon::parse($snippet['publishedAt']);
            $item_data['tags'] = isset($snippet['tags']) ? $snippet['tags'] : [];

            $data[] = $item_data;
          }

          $data_final = array(
            'items'=> $data,
            'createdAt' => Carbon::now()
          );
        }
      }
    }
    
    if (isset($data_final['items'])){
        return $data_final;
    } else {
        return $data;
    }
  }

  public static function ytDownload_api( $options = [] ) {
    $data = [];
    $url = 'https://www.googleapis.com/youtube/v3/videos?part=snippet,contentDetails,statistics&id=' . $options['id'] . '&key=' . youtube_api_key(option('youtube_api_keys'));
    $response = fetch( $url );

    if ( $response->getStatusCode() == 200 ) {
      $json = json_decode( $response->getBody(), true );

      if ( isset( $json['items'][0] ) ) {
        $snippet = $json['items'][0]['snippet'];
        $content_details = $json['items'][0]['contentDetails'];
        $statistics = $json['items'][0]['statistics'];
        $item = $json['items'][0];

        $data['id'] = $item['id'];
        $data['title'] = preg_replace('/["?]/', '', $snippet['title']);
        $data['description'] = remove_http($snippet['description']);
        $data['thumbnails'] = $snippet['thumbnails'];
        $data['channelId'] = $item['snippet']['channelId'];
        $data['channelTitle'] = $snippet['channelTitle'];

        $duration = convert_youtube_time( $content_details['duration'] );
        $exp_duration = explode( ':', $duration );

        if ( count( $exp_duration ) == 2 ) {
            $parsed = date_parse( '00:' . $duration );
            $seconds = ( $parsed['minute'] * 60 ) + $parsed['second'];
        } else {
            $parsed = date_parse( $duration );
            $seconds = ( $parsed['hour'] * 60 * 60 ) + ( $parsed['minute'] * 60 ) + $parsed['second'];
        }

        $data['duration'] = $duration;
        $data['ptsTime'] = $content_details['duration'];
        $data['size'] = format_bytes( ( $seconds * ( 192 / 8 ) * 1000 ) );
        $data['second'] = $seconds;
        $data['viewCount'] = isset($statistics['viewCount']) ? number_format($statistics['viewCount']) : 0;
        $data['likeCount'] = isset($statistics['likeCount']) ? number_format($statistics['likeCount']) : 0;
        $data['dislikeCount'] = isset($statistics['dislikeCount']) ? number_format($statistics['dislikeCount']) : 0;
        $data['publishedAt'] = Carbon::parse($snippet['publishedAt']);
        $data['tags'] = isset($snippet['tags']) ? $snippet['tags'] : [];
        $data['createdAt'] = Carbon::now();
      }
    }

    return $data;
  }

  public static function ytPlaylist_api( $options = [] ) {
    $data = [];
    $url = 'https://www.googleapis.com/youtube/v3/playlistItems?part=snippet,id&maxResults=' . $options['limit'] . '&playlistId=' . $options['id'] . '&key=' . youtube_api_key(option('youtube_api_keys'));
    $response = fetch( $url );

    if ( $response->getStatusCode() == 200 ) {
      $json = json_decode( $response->getBody(), true );
      
      if ( !empty( $json['items'] ) ) {
        foreach ( $json['items'] as $item )
          $video_ids[] = $item['snippet']['resourceId']['videoId'];

        unset( $item );

        $url_detail = 'https://www.googleapis.com/youtube/v3/videos?part=snippet,contentDetails,statistics&id=' . implode(',', $video_ids ) . '&key=' . youtube_api_key(option('youtube_api_keys'));
        $response_detail = fetch( $url_detail );

        if ( $response_detail->getStatusCode() == 200 ) {
          $json_detail = json_decode( $response_detail->getBody(), true );
            
          foreach ( $json_detail['items'] as $item ) {
            $snippet = $item['snippet'];
            $content_details = $item['contentDetails'];
            $statistics = $item['statistics'];

            $item_data['id'] = $item['id'];
            $item_data['title'] = preg_replace('/["?]/', '', $snippet['title']);
            $item_data['description'] = remove_http($snippet['description']);
            $item_data['thumbnails'] = $snippet['thumbnails'];
            $item_data['channelId'] = $item['snippet']['channelId'];
            $item_data['channelTitle'] = $snippet['channelTitle'];

            $duration = convert_youtube_time( $content_details['duration'] );
            $exp_duration = explode( ':', $duration );

            if ( count( $exp_duration ) == 2 ) {
                $parsed = date_parse( '00:' . $duration );
                $seconds = ( $parsed['minute'] * 60 ) + $parsed['second'];
            } else {
                $parsed = date_parse( $duration );
                $seconds = ( $parsed['hour'] * 60 * 60 ) + ( $parsed['minute'] * 60 ) + $parsed['second'];
            }

            $item_data['duration'] = $duration;
            $item_data['ptsTime'] = $content_details['duration'];
            $item_data['size'] = format_bytes( ( $seconds * ( 192 / 8 ) * 1000 ) );
            $item_data['second'] = $seconds;
            $item_data['viewCount'] = isset($statistics['viewCount']) ? number_format($statistics['viewCount']) : 0;
            $item_data['likeCount'] = isset($statistics['likeCount']) ? number_format($statistics['likeCount']) : 0;
            $item_data['dislikeCount'] = isset($statistics['dislikeCount']) ? number_format($statistics['dislikeCount']) : 0;
            $item_data['publishedAt'] = Carbon::parse($snippet['publishedAt']);
            $item_data['tags'] = isset($snippet['tags']) ? $snippet['tags'] : [];

            $data[] = $item_data;
          }

          $data_final = array(
            'items'=> $data,
            'createdAt' => Carbon::now()
          );
        }
      }
    }

    if (isset($data_final['items'])){
        return $data_final;
    } else {
        return $data;
    }
  }

  public static function ytComments_api( $options = [] ) {
    $data = [];
    $api_comments = 'https://www.googleapis.com/youtube/v3/commentThreads?part=snippet&maxResults=' . $options['limit'] . '&videoId=' . $options['id'] . '&key=' . youtube_api_key(option('youtube_api_keys'));
    $response = fetch( $api_comments );

    if ( $response->getStatusCode() == 200 ) {
      $json = json_decode( $response->getBody(), true );
        
      foreach ( $json['items'] as $item ) {
        $snippet = $item['snippet']['topLevelComment']['snippet'];

        $item_data['videoId'] = $snippet['videoId'];
        $item_data['authorDisplayName'] = $snippet['authorDisplayName'];
        $item_data['authorProfileImageUrl'] = $snippet['authorProfileImageUrl'];
        $item_data['textDisplay'] = $snippet['textDisplay'];
        $item_data['likeCount'] = $snippet['likeCount'];
        $item_data['publishedAt'] = Carbon::parse($snippet['publishedAt']);

        $data[] = $item_data;
      }

      $data_final = array(
        'items'=> $data,
        'createdAt' => Carbon::now()
      );
    }

    if (isset($data_final['items'])){
        return $data_final;
    } else {
        return $data;
    }
  }

  public static function iTunes_musicgenre_api(){
    $data = [];
    $url = 'https://itunes.apple.com/WebObjects/MZStoreServices.woa/ws/genres?id=34';
    $response = fetch( $url );

    if ( $response->getStatusCode() == 200 ) {
      $json = json_decode( $response->getBody(), true );

      if ( !empty( $json['34']['subgenres'] ) ) {
        foreach ( $json['34']['subgenres'] as $subgenre ) {
          $data[ $subgenre['id'] ] = $subgenre['name'];

          if ( isset( $subgenre['subgenres'] ) && is_array( $subgenre['subgenres'] ) ) {
            foreach ( $subgenre['subgenres'] as $subsubgenre ) {
              $data[ $subsubgenre['id'] ] = $subsubgenre['name'];
            }
          }
        }

        asort($data);

        $data_final = array(
          'items'=> $data,
          'createdAt' => Carbon::now()
        );
      }
    }

    if (isset($data_final['items'])){
        return $data_final;
    } else {
        return $data;
    }
  }

  public static function iTunes_topsongs_api( $options = [] ) {
    $data = [];

    if (!$options['genre']) {
      $url = 'http://ax.itunes.apple.com/WebObjects/MZStoreServices.woa/ws/RSS/topsongs/limit='. $options['limit'] .'/json';
    } else {
      $url = 'http://itunes.apple.com/us/rss/topsongs/limit='. $options['limit'] .'/genre='. $options['genre'] .'/json';
    }
    
    $response = fetch( $url );

    if ( $response->getStatusCode() == 200 ) {
      $json = json_decode( $response->getBody(), true );

      if ( !empty( $json['feed']['entry'] ) ) {
        foreach ( $json['feed']['entry'] as $result ) {
          $data[] = [
            'id' => $result['id']['attributes']['im:id'],
            'title' => $result['im:artist']['label'] . ' - ' . $result['im:name']['label'],
            'name' => $result['im:name']['label'],
            'artist' => $result['im:artist']['label'],
            'image' => $result['im:image'][ count( $result['im:image'] ) - 1 ]['label'],
            'genre' => $result['category']['attributes']['label'],
            'album' => ( isset( $result['im:collection']['im:name']['label'] ) ? $result['im:collection']['im:name']['label'] : '-' ),
            'dateRelease' => isset($result['im:releaseDate']['attributes']['label']) ? Carbon::parse($result['im:releaseDate']['attributes']['label']) : Carbon::now(),
          ];
        }

        $data_final = array(
          'items'=> $data,
          'createdAt' => Carbon::now()
        );
      }
    }

    if (isset($data_final['items'])){
        return $data_final;
    } else {
        return $data;
    }
  }
}
