<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\NextMP3;
use App\Helpers\Cache;
use Psr\Http\Message\{
    ServerRequestInterface as Request,
    ResponseInterface as Response
};
use Stichoza\GoogleTranslate\GoogleTranslate;

class PagesController extends Controller
{
  public function Home(Request $request, Response $response) {

    if (isset($_GET['q'])){
      $slug = search_permalink($_GET['q']);
      redirect_to($slug);
    }

    $meta = [
      'title' => strtr( option( 'home_title', '%site_tagline%' ), [
        '%site_tagline%' => option( 'site_tagline' ),
        '%site_name%' => option( 'site_name' ),
        '%subdomain%' => get_sub()
      ] ),
      'description' => strtr( option( 'home_meta_description' ), [
        '%site_name%' => option( 'site_name' ),
        '%site_tagline%' => option( 'site_tagline' ),
        '%domain%' => site_domain()
      ] ),
      'robots' => option( 'home_meta_robots' )
    ];

    $data = [
      'title' => $meta['title'],
      'description' => $meta['description'],
      'robots' => $meta['robots'],
    ];
  
    return $this->view->render($response, 'home.twig', $data);
  }
  
  public function Search(Request $request, Response $response, array $args) {
    // Check DMCA
    dmca_block();

    // Translate Description
    $fastCache = Cache::getAdapter();
    $cacheName = md5('search'.$args['lang']);

    if (!$fastCache->has($cacheName)) {
      try {
        // Description
        $opt_search_description = spintax(option( 'search_meta_description' ));
        $wrap_description = robot_text($opt_search_description);
        $tranlate_description = GoogleTranslate::trans($wrap_description, $args['lang'], 'en');
        $search_meta_description = normal_text($tranlate_description);

        $translate = [
          'search_title' => spintax(option( 'search_title' )),
          'search_meta_description' => $search_meta_description,
        ];

        $fastCache->set($cacheName, $translate, 999999999999);

      } catch (\Throwable $th) {
        $translate = [
          'search_title' => spintax(option( 'search_title' )),
          'search_meta_description' => spintax(option( 'search_meta_description' )),
        ];
      }
    }else{
      $translate = $fastCache->get($cacheName);
    }

    $search_query = ucwords(str_replace(option('permalink_slug_separator'), ' ', $args['slug']));
    $search = NextMP3::getSearch($search_query, option('youtube_search_limit'));

    if (isset($search['items'])) {  
      $meta = [
        'title' => strtr($translate['search_title'], [
          '%query%' => $search_query,
          '%title%' => $search['items'][0]['title'],
          '%size%' => $search['items'][0]['size'],
          '%duration%' => $search['items'][0]['duration'],
          '%slug%' => $args['slug'],
          '%site_name%' => option( 'site_name' ),
          '%subdomain%' => get_sub()
        ] ),
        'description' => strtr($translate['search_meta_description'], [
          '%query%' => $search_query,
          '%title%' => $search['items'][0]['title'],
          '%channelTitle%' => $search['items'][0]['channelTitle'],
          '%duration%' => $search['items'][0]['duration'],
          '%ptsTime%' => $search['items'][0]['ptsTime'],
          '%size%' => $search['items'][0]['size'],
          '%viewCount%' => $search['items'][0]['viewCount'],
          '%likeCount%' => $search['items'][0]['likeCount'],
          '%dislikeCount%' => $search['items'][0]['dislikeCount'],
          '%publishedAt%' => $search['items'][0]['publishedAt'],
          '%createdAt%' => $search['createdAt'],
          '%slug%' => $args['slug'],
          '%site_name%' => option( 'site_name' ),
          '%domain%' => site_domain()
        ] ),
        'robots' => option( 'search_meta_robots' )
      ];
      
      $data = [
        'title' => $meta['title'],
        'description' => $meta['description'],
        'robots' => $meta['robots'],
        'query' => $search_query,
        'search' => $search,
      ];
      
      return $this->view->render($response, 'search.twig', $data);
    }else {
      return redirect_to('/');
    }
  }
  public function Single(Request $request, Response $response, array $args) {
    // Check DMCA
    dmca_block();

    $id = $args['id'];
    $single = NextMP3::getDownload($id);

    if (isset($single['id'])) {  
      $meta = [
        'title' => strtr( option( 'single_title', '%site_tagline%' ), [
          '%title%' => $single['title'],
          '%size%' => $single['size'],
          '%duration%' => $single['duration'],
          '%site_name%' => option( 'site_name' ),
          '%subdomain%' => get_sub()
        ] ),
        'description' => strtr( option( 'single_meta_description' ), [
          '%title%' => $single['title'],
          '%channelTitle%' => $single['channelTitle'],
          '%duration%' => $single['duration'],
          '%ptsTime%' => $single['ptsTime'],
          '%size%' => $single['size'],
          '%viewCount%' => $single['viewCount'],
          '%likeCount%' => $single['likeCount'],
          '%dislikeCount%' => $single['dislikeCount'],
          '%publishedAt%' => $single['publishedAt'],
          '%createdAt%' => $single['createdAt'],
          '%site_name%' => option( 'site_name' ),
          '%domain%' => site_domain()
        ] ),
        'robots' => option( 'single_meta_robots' )
      ];
      
      $data = [
        'title' => $meta['title'],
        'description' => $meta['description'],
        'robots' => $meta['robots'],
        'single' => $single,
      ];
      
      return $this->view->render($response, 'single.twig', $data);
    }else {
      return redirect_to('/');
    }
  }

  public function Download(Request $request, Response $response, array $args) {
    // Check DMCA
    dmca_block();
    
    $id = $args['id'];
    $download = NextMP3::getDownload($id);

    if (isset($download['id'])) {  
      $meta = [
        'title' => strtr( option( 'download_title', '%site_tagline%' ), [
          '%title%' => $download['title'],
          '%size%' => $download['size'],
          '%duration%' => $download['duration'],
          '%site_name%' => option( 'site_name' ),
          '%subdomain%' => get_sub()
        ] ),
        'description' => strtr( option( 'download_meta_description' ), [
          '%title%' => $download['title'],
          '%channelTitle%' => $download['channelTitle'],
          '%duration%' => $download['duration'],
          '%ptsTime%' => $download['duration'],
          '%size%' => $download['size'],
          '%viewCount%' => $download['viewCount'],
          '%likeCount%' => $download['likeCount'],
          '%dislikeCount%' => $download['dislikeCount'],
          '%publishedAt%' => $download['publishedAt'],
          '%createdAt%' => $download['createdAt'],
          '%site_name%' => option( 'site_name' ),
          '%domain%' => site_domain()
        ] ),
        'robots' => option( 'download_meta_robots' )
      ];
      
      $data = [
        'title' => $meta['title'],
        'description' => $meta['description'],
        'robots' => $meta['robots'],
        'download' => $download,
      ];
      
      return $this->view->render($response, 'download.twig', $data);
    }else {
      return redirect_to('/');
    }
  }

  public function Page(Request $request, Response $response, array $args) {
    $slug = $args['slug'];

    if (isset($slug)) {  
      $title = ucwords(str_replace(option('permalink_slug_separator'), ' ', $slug));

      $meta = [
        'title' => strtr( option( 'page_title', '%site_tagline%' ), [
            '%title%' => $title,
            '%site_name%' => option( 'site_name' ),
            '%subdomain%' => get_sub()
        ] ),
        'description' => strtr( option( 'page_meta_description' ), [
            '%title%' => $title,
            '%site_name%' => option( 'site_name' ),
            '%domain%' => site_domain()
        ] ),
        'robots' => option( 'page_meta_robots' )
      ];

      $data = [
          'title' => $meta['title'],
          'description' => $meta['description'],
          'robots' => $meta['robots'],
      ];
      
      return $this->view->render($response, 'pages/'.$slug.'.twig', $data);
    }else {
      return redirect_to('/');
    }
  }

  public function Playlist(Request $request, Response $response, array $args) {
    $slug = $args['slug'];
    $playlistData = \App\Settings\Playlist::get();

    if (isset($playlistData[$slug])) { 
      $playlist = NextMP3::getPlaylist($playlistData[$slug]['id'], option('youtube_playlist_limit') );
      
      if (isset($playlist['items'])) {  
        $meta = [
          'title' => strtr( option( 'playlist_title', '%site_tagline%' ), [
              '%title%' => $playlistData[$slug]['title'],
              '%site_name%' => option( 'site_name' ),
              '%subdomain%' => get_sub()
          ] ),
          'description' => strtr( option( 'playlist_meta_description' ), [
              '%title%' => $playlistData[$slug]['title'],
              '%createdAt%' => $playlist['createdAt'],
              '%site_name%' => option( 'site_name' ),
              '%domain%' => site_domain()
          ] ),
          'robots' => option( 'playlist_meta_robots' )
        ];

        $data = [
            'title' => $meta['title'],
            'description' => $meta['description'],
            'robots' => $meta['robots'],
            'playlist' => $playlist,
            'name' => $playlistData[$slug]['title']
        ];
        
        return $this->view->render($response, 'playlist.twig', $data);
      }else{
        return redirect_to('/');
      }
    }else {
      return redirect_to('/');
    }
  }

  public function Genre(Request $request, Response $response, array $args) {
    $slug = $args['slug'];
    $genreData = \App\Settings\Genre::get();
    
    if (isset($genreData[$slug])) { 
      $genre = NextMP3::getTopSong(option('itunes_genre_limit'), $genreData[$slug]['id'] );
      
      if (isset($genre['items'])) {  
        $meta = [
          'title' => strtr( option( 'genre_title', '%site_tagline%' ), [
              '%title%' => $genreData[$slug]['title'],
              '%site_name%' => option( 'site_name' ),
              '%subdomain%' => get_sub()
          ] ),
          'description' => strtr( option( 'genre_meta_description' ), [
              '%title%' => $genreData[$slug]['title'],
              '%createdAt%' => $genre['createdAt'],
              '%site_name%' => option( 'site_name' ),
              '%domain%' => site_domain()
          ] ),
          'robots' => option( 'genre_meta_robots' )
        ];
        
        $data = [
            'title' => $meta['title'],
            'description' => $meta['description'],
            'robots' => $meta['robots'],
            'genre' => $genre,
            'name' => $genreData[$slug]['title']
        ];

        return $this->view->render($response, 'genre.twig', $data);
      }else{
        return redirect_to('/');
      }
    }else {
      return redirect_to('/');
    }
  }

  public function NotFound(Request $request, Response $response) {
    $meta = [
      'title' => strtr( option( '404_title', '%site_tagline%' ), [
        '%site_tagline%' => option( 'site_tagline' ),
        '%site_name%' => option( 'site_name' ),
        '%subdomain%' => get_sub()
      ] ),
      'description' => strtr( option( '404_meta_description' ), [
        '%site_name%' => option( 'site_name' ),
        '%site_tagline%' => option( 'site_tagline' ),
        '%domain%' => site_domain()
      ] ),
      'robots' => option( '404_meta_robots' )
    ];
  
    $data = [
      'title' => $meta['title'],
      'description' => $meta['description'],
      'robots' => $meta['robots'],
    ];
  
    return $this->view->render($response, '404.twig', $data);
  }

  public function FlushCache(Request $request, Response $response) {
    rrmdir(__DIR__ . '/../../data/cache');
    $response->getBody()->write("Success Delete All Cache..");
    return $response;
  }
  
  public function ApiComments(Request $request, Response $response) {
    $args = $request->getQueryParams();
    $data = NextMP3::getComment($args['id'], isset($args['limit']) ? $args['limit'] : '10');
    $response->getBody()->write(json_encode($data));
    return $response->withHeader('Content-Type', 'application/json');
  }
}