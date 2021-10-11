<?php
use Carbon\Carbon;

function option($key='') {
  $option = \App\Settings\Sites::get();

  if(empty($key)) {
    return $option;
  }

  return isset($option[$key]) ? $option[$key] : null;
};

function photon_resize($url='', $width='', $height='', $server='0') {
  $path = preg_replace('/(^\w+:|^)\/\//', '', $url);
  
  return 'https://i'. $server .'.wp.com/'. $path .'?resize='. $width .','. $height;
}

function fetch($url='', $options = []) {
  $userAgent = new \App\Helpers\RandomUserAgent();
  $defaults = [
    'decode_content' => 'gzip, deflate',
    'headers' => [
        'User-Agent' => $userAgent->getAgent(),
        'Referer' => 'https://www.youtube.com',
    ],
    'timeout' => option('agc_connection_timeout'),
    'http_errors' => false,
  ];
  $options  = array_merge( $defaults, $options );
  $client = new \GuzzleHttp\Client();

  return $client->request('GET', $url, $options);
}

function toDayAgo($date, $locate='en_US') {
  return Carbon::parse($date)->locale($locate)->diffForHumans();
}

function toIsoFormat($date, $locate='en_US') {
  if ($locate == 'id_ID'){
    return Carbon::parse($date)->locale($locate)->isoFormat('DD MMMM YYYY');
  }else{
    return Carbon::parse($date)->locale($locate)->isoFormat('MMMM DD YYYY');
  }
}

function clean_array( $data ) {
  return array_values( array_filter( array_map( 'trim', $data ), 'strlen' ) );
}

function extract_domain($domain)
{
    if(preg_match("/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i", $domain, $matches))
    {
        return $matches['domain'];
    } else {
        return $domain;
    }
}

function extract_subdomains($domain)
{
    $subdomains = $domain;
    $domain = extract_domain($subdomains);
    $subdomains = idn_to_utf8(rtrim(strstr($subdomains, $domain, true), '.'));

    return $subdomains;
}

function site_url() {
  if (
    isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ||
    ! empty( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ||
    ! empty( $_SERVER['HTTP_X_FORWARDED_SSL'] ) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on'
  ) {
      $https = true;
    } else {
    $https = false;
  }

  if ( ! $https && isset( $_SERVER['HTTP_CF_VISITOR'] ) ) {
    $is_cloudflare = json_decode( $_SERVER['HTTP_CF_VISITOR'] );

    if ( isset( $is_cloudflare->scheme ) && $is_cloudflare->scheme === 'https' )
      $https = true;
  }

  $protocol  = $https ? 'https' : 'http';
  $host      = $_SERVER['HTTP_HOST'];
  $url       = $protocol . '://' . $host;

  return $url;
}

function canonical_url() {
  $base = str_replace( "\\", '/', dirname( __FILE__, 4 ) );
  $base_path = strtr( $base, array( rtrim( $_SERVER['DOCUMENT_ROOT'], '/' ) => '' ) );

  $path         = ( $base_path === '' ) ? '/' : $base_path;
  $parse_uri    = parse_url( $_SERVER['REQUEST_URI'] );
  $clean_path   = str_replace( $base_path, '', $parse_uri['path'] );

  if ( $path === '/' ) {
    $uri = ( $parse_uri['path'] != '/' ) ? '/' . ltrim( $parse_uri['path'], '/' ) : '';
  } else {
    $uri = ( $clean_path !== '/' ) ? '/' . str_replace( $base_path . '/', '', $parse_uri['path'] ) : '';
  }

  if (
    isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ||
    ! empty( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ||
    ! empty( $_SERVER['HTTP_X_FORWARDED_SSL'] ) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on'
  ) {
      $https = true;
    } else {
    $https = false;
  }

  if ( ! $https && isset( $_SERVER['HTTP_CF_VISITOR'] ) ) {
    $is_cloudflare = json_decode( $_SERVER['HTTP_CF_VISITOR'] );

    if ( isset( $is_cloudflare->scheme ) && $is_cloudflare->scheme === 'https' )
      $https = true;
  }

  $host      = '://' . $_SERVER['HTTP_HOST'];
  $protocol  = $https ? 'https' : 'http';
  $url       = $protocol . $host . $uri;

  return $url;
}

function themes_url($file='') {
  $path = '/themes/'. option('theme_name'). '/assets/' . $file;
  return site_url() . $path;  
}

function redirect_to( $url = '', $options = [] ) {
  $defaults = [
    'permanent' => false,
    'method'    => '',
    'timeout'   => 5
  ];
  $option = array_merge( $defaults, $options );

  if ( $option['method'] === 'refresh' ) {
    header( 'Refresh: ' . $option['timeout'] . '; url=' . $url );
  } else {
    if ( $option['permanent'] )
      header( 'HTTP/1.1 301 Moved Permanently' );
      header( 'Location: ' . $url );
    die();
  }
}

function site_domain() {
  return strtr( $_SERVER['HTTP_HOST'], array( 'www.' => '' ) );
}

function make_slug( $str, $delimiter = '-', $options = [] ) {
  $str = strtr( $str, [
    '&amp;' => '&',
    '&quot;' => '"',
    '&#039;' => "'",
    '&#39;' => "'",
    "n't" => 'nt'
  ] );
  $str = urldecode( html_entity_decode( $str ) );
  $str = mb_convert_encoding( ( string ) $str, 'UTF-8', mb_list_encodings() );
  $defaults = [
		'delimiter'     => $delimiter,
		'limit'         => null,
		'lowercase'     => true,
		'replacements'  => [],
		'transliterate' => false,
	];
  $options = array_merge( $defaults, $options );
  $chars_map = [
		// Latin
		'ÃƒÂ€' => 'A', 'ÃƒÂ' => 'A', 'ÃƒÂ‚' => 'A', 'ÃƒÂƒ' => 'A', 'ÃƒÂ„' => 'A', 'ÃƒÂ…' => 'A', 'ÃƒÂ†' => 'AE', 'ÃƒÂ‡' => 'C',
		'ÃƒÂˆ' => 'E', 'ÃƒÂ‰' => 'E', 'ÃƒÂŠ' => 'E', 'ÃƒÂ‹' => 'E', 'ÃƒÂŒ' => 'I', 'ÃƒÂ' => 'I', 'ÃƒÂŽ' => 'I', 'ÃƒÂ' => 'I',
		'ÃƒÂ' => 'D', 'ÃƒÂ‘' => 'N', 'ÃƒÂ’' => 'O', 'ÃƒÂ“' => 'O', 'ÃƒÂ”' => 'O', 'ÃƒÂ•' => 'O', 'ÃƒÂ–' => 'O', 'Ã…Â' => 'O',
		'ÃƒÂ˜' => 'O', 'ÃƒÂ™' => 'U', 'ÃƒÂš' => 'U', 'ÃƒÂ›' => 'U', 'ÃƒÂœ' => 'U', 'Ã…Â°' => 'U', 'ÃƒÂ' => 'Y', 'ÃƒÂž' => 'TH',
		'ÃƒÂŸ' => 'ss',
		'Ãƒ ' => 'a', 'ÃƒÂ¡' => 'a', 'ÃƒÂ¢' => 'a', 'ÃƒÂ£' => 'a', 'ÃƒÂ¤' => 'a', 'ÃƒÂ¥' => 'a', 'ÃƒÂ¦' => 'ae', 'ÃƒÂ§' => 'c',
		'ÃƒÂ¨' => 'e', 'ÃƒÂ©' => 'e', 'ÃƒÂª' => 'e', 'ÃƒÂ«' => 'e', 'ÃƒÂ¬' => 'i', 'ÃƒÂ­' => 'i', 'ÃƒÂ®' => 'i', 'ÃƒÂ¯' => 'i',
		'ÃƒÂ°' => 'd', 'ÃƒÂ±' => 'n', 'ÃƒÂ²' => 'o', 'ÃƒÂ³' => 'o', 'ÃƒÂ´' => 'o', 'ÃƒÂµ' => 'o', 'ÃƒÂ¶' => 'o', 'Ã…Â‘' => 'o',
		'ÃƒÂ¸' => 'o', 'ÃƒÂ¹' => 'u', 'ÃƒÂº' => 'u', 'ÃƒÂ»' => 'u', 'ÃƒÂ¼' => 'u', 'Ã…Â±' => 'u', 'ÃƒÂ½' => 'y', 'ÃƒÂ¾' => 'th',
		'ÃƒÂ¿' => 'y',

		// Latin symbols
		'Ã‚Â©' => '(c)',

		// Greek
		'ÃŽÂ‘' => 'A', 'ÃŽÂ’' => 'B', 'ÃŽÂ“' => 'G', 'ÃŽÂ”' => 'D', 'ÃŽÂ•' => 'E', 'ÃŽÂ–' => 'Z', 'ÃŽÂ—' => 'H', 'ÃŽÂ˜' => '8',
		'ÃŽÂ™' => 'I', 'ÃŽÂš' => 'K', 'ÃŽÂ›' => 'L', 'ÃŽÂœ' => 'M', 'ÃŽÂ' => 'N', 'ÃŽÂž' => '3', 'ÃŽÂŸ' => 'O', 'ÃŽ ' => 'P',
		'ÃŽÂ¡' => 'R', 'ÃŽÂ£' => 'S', 'ÃŽÂ¤' => 'T', 'ÃŽÂ¥' => 'Y', 'ÃŽÂ¦' => 'F', 'ÃŽÂ§' => 'X', 'ÃŽÂ¨' => 'PS', 'ÃŽÂ©' => 'W',
		'ÃŽÂ†' => 'A', 'ÃŽÂˆ' => 'E', 'ÃŽÂŠ' => 'I', 'ÃŽÂŒ' => 'O', 'ÃŽÂŽ' => 'Y', 'ÃŽÂ‰' => 'H', 'ÃŽÂ' => 'W', 'ÃŽÂª' => 'I',
		'ÃŽÂ«' => 'Y',
		'ÃŽÂ±' => 'a', 'ÃŽÂ²' => 'b', 'ÃŽÂ³' => 'g', 'ÃŽÂ´' => 'd', 'ÃŽÂµ' => 'e', 'ÃŽÂ¶' => 'z', 'ÃŽÂ·' => 'h', 'ÃŽÂ¸' => '8',
		'ÃŽÂ¹' => 'i', 'ÃŽÂº' => 'k', 'ÃŽÂ»' => 'l', 'ÃŽÂ¼' => 'm', 'ÃŽÂ½' => 'n', 'ÃŽÂ¾' => '3', 'ÃŽÂ¿' => 'o', 'ÃÂ€' => 'p',
		'ÃÂ' => 'r', 'ÃÂƒ' => 's', 'ÃÂ„' => 't', 'ÃÂ…' => 'y', 'ÃÂ†' => 'f', 'ÃÂ‡' => 'x', 'ÃÂˆ' => 'ps', 'ÃÂ‰' => 'w',
		'ÃŽÂ¬' => 'a', 'ÃŽÂ­' => 'e', 'ÃŽÂ¯' => 'i', 'ÃÂŒ' => 'o', 'ÃÂ' => 'y', 'ÃŽÂ®' => 'h', 'ÃÂŽ' => 'w', 'ÃÂ‚' => 's',
		'ÃÂŠ' => 'i', 'ÃŽÂ°' => 'y', 'ÃÂ‹' => 'y', 'ÃŽÂ' => 'i',

		// Turkish
		'Ã…Âž' => 'S', 'Ã„Â°' => 'I', 'ÃƒÂ‡' => 'C', 'ÃƒÂœ' => 'U', 'ÃƒÂ–' => 'O', 'Ã„Âž' => 'G',
		'Ã…ÂŸ' => 's', 'Ã„Â±' => 'i', 'ÃƒÂ§' => 'c', 'ÃƒÂ¼' => 'u', 'ÃƒÂ¶' => 'o', 'Ã„ÂŸ' => 'g',

		// Russian
		'ÃÂ' => 'A', 'ÃÂ‘' => 'B', 'ÃÂ’' => 'V', 'ÃÂ“' => 'G', 'ÃÂ”' => 'D', 'ÃÂ•' => 'E', 'ÃÂ' => 'Yo', 'ÃÂ–' => 'Zh',
		'ÃÂ—' => 'Z', 'ÃÂ˜' => 'I', 'ÃÂ™' => 'J', 'ÃÂš' => 'K', 'ÃÂ›' => 'L', 'ÃÂœ' => 'M', 'ÃÂ' => 'N', 'ÃÂž' => 'O',
		'ÃÂŸ' => 'P', 'Ã ' => 'R', 'ÃÂ¡' => 'S', 'ÃÂ¢' => 'T', 'ÃÂ£' => 'U', 'ÃÂ¤' => 'F', 'ÃÂ¥' => 'H', 'ÃÂ¦' => 'C',
		'ÃÂ§' => 'Ch', 'ÃÂ¨' => 'Sh', 'ÃÂ©' => 'Sh', 'ÃÂª' => '', 'ÃÂ«' => 'Y', 'ÃÂ¬' => '', 'ÃÂ­' => 'E', 'ÃÂ®' => 'Yu',
		'ÃÂ¯' => 'Ya',
		'ÃÂ°' => 'a', 'ÃÂ±' => 'b', 'ÃÂ²' => 'v', 'ÃÂ³' => 'g', 'ÃÂ´' => 'd', 'ÃÂµ' => 'e', 'Ã‘Â‘' => 'yo', 'ÃÂ¶' => 'zh',
		'ÃÂ·' => 'z', 'ÃÂ¸' => 'i', 'ÃÂ¹' => 'j', 'ÃÂº' => 'k', 'ÃÂ»' => 'l', 'ÃÂ¼' => 'm', 'ÃÂ½' => 'n', 'ÃÂ¾' => 'o',
		'ÃÂ¿' => 'p', 'Ã‘Â€' => 'r', 'Ã‘Â' => 's', 'Ã‘Â‚' => 't', 'Ã‘Âƒ' => 'u', 'Ã‘Â„' => 'f', 'Ã‘Â…' => 'h', 'Ã‘Â†' => 'c',
		'Ã‘Â‡' => 'ch', 'Ã‘Âˆ' => 'sh', 'Ã‘Â‰' => 'sh', 'Ã‘ÂŠ' => '', 'Ã‘Â‹' => 'y', 'Ã‘ÂŒ' => '', 'Ã‘Â' => 'e', 'Ã‘ÂŽ' => 'yu',
		'Ã‘Â' => 'ya',

		// Ukrainian
		'ÃÂ„' => 'Ye', 'ÃÂ†' => 'I', 'ÃÂ‡' => 'Yi', 'Ã’Â' => 'G',
		'Ã‘Â”' => 'ye', 'Ã‘Â–' => 'i', 'Ã‘Â—' => 'yi', 'Ã’Â‘' => 'g',

		// Czech
		'Ã„ÂŒ' => 'C', 'Ã„ÂŽ' => 'D', 'Ã„Âš' => 'E', 'Ã…Â‡' => 'N', 'Ã…Â˜' => 'R', 'Ã… ' => 'S', 'Ã…Â¤' => 'T', 'Ã…Â®' => 'U',
		'Ã…Â½' => 'Z',
		'Ã„Â' => 'c', 'Ã„Â' => 'd', 'Ã„Â›' => 'e', 'Ã…Âˆ' => 'n', 'Ã…Â™' => 'r', 'Ã…Â¡' => 's', 'Ã…Â¥' => 't', 'Ã…Â¯' => 'u',
		'Ã…Â¾' => 'z',

		// Polish
		'Ã„Â„' => 'A', 'Ã„Â†' => 'C', 'Ã„Â˜' => 'e', 'Ã…Â' => 'L', 'Ã…Âƒ' => 'N', 'ÃƒÂ“' => 'o', 'Ã…Âš' => 'S', 'Ã…Â¹' => 'Z',
		'Ã…Â»' => 'Z',
		'Ã„Â…' => 'a', 'Ã„Â‡' => 'c', 'Ã„Â™' => 'e', 'Ã…Â‚' => 'l', 'Ã…Â„' => 'n', 'ÃƒÂ³' => 'o', 'Ã…Â›' => 's', 'Ã…Âº' => 'z',
		'Ã…Â¼' => 'z',

		// Latvian
		'Ã„Â€' => 'A', 'Ã„ÂŒ' => 'C', 'Ã„Â’' => 'E', 'Ã„Â¢' => 'G', 'Ã„Âª' => 'i', 'Ã„Â¶' => 'k', 'Ã„Â»' => 'L', 'Ã…Â…' => 'N',
		'Ã… ' => 'S', 'Ã…Âª' => 'u', 'Ã…Â½' => 'Z',
		'Ã„Â' => 'a', 'Ã„Â' => 'c', 'Ã„Â“' => 'e', 'Ã„Â£' => 'g', 'Ã„Â«' => 'i', 'Ã„Â·' => 'k', 'Ã„Â¼' => 'l', 'Ã…Â†' => 'n',
		'Ã…Â¡' => 's', 'Ã…Â«' => 'u', 'Ã…Â¾' => 'z'
	];
  $str = preg_replace( array_keys( $options['replacements'] ), $options['replacements'], $str );
  $str = ( $options['transliterate'] ) ? str_replace( array_keys( $chars_map ), $chars_map, $str ) : $str;
	$str = preg_replace( '/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $str );
	$str = preg_replace( '/(' . preg_quote( $options['delimiter'], '/') . '){2,}/', '$1', $str );
	$str = substr( $str, 0, ( $options['limit'] ? $options['limit'] : strlen( $str ) ) );
	$str = trim( $str, $options['delimiter'] );
  $str = $options['lowercase'] ? strtolower( $str ) : $str;

	return $str;
}

function convert_youtube_time( $time ) {
  $start = new DateTime( '@0' );
  $start->add( new DateInterval( $time ) );
  
  return $start->format( 'i:s' );
}

function format_bytes( $bytes, $precision = 2 ) {
  $units = [ 'B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB' ];
  $bytes = max( $bytes, 0);
  $pow = floor( ( $bytes ? log( $bytes ) : 0 ) / log( 1024 ) );
  $pow = min( $pow, count( $units ) - 1 );
  $bytes /= pow( 1024, $pow );

  return round( $bytes, $precision ) . ' ' . $units[$pow];
}

function youtube_api_key($keys) {
  $api_key = false;

  if ( $api_keys = $keys ) {
      $api_key_parts = clean_array( explode( PHP_EOL, $api_keys ) );
      $api_key = $api_key_parts[array_rand( $api_key_parts, 1 )];
  }

  return $api_key;
}

function spintax( $text ) {
  return preg_replace_callback(
    '/\{(((?>[^\{\{\}\}]+)|(?R))*)\}/x',
    'do_spintax',
    $text
  );
};

function do_spintax( $text ) {
  $text = spintax( $text[1] );
  $parts = explode( '|', $text );

  return $parts[array_rand( $parts )];
};

function get_terms( $limit = 20 ) {
  $terms_files = glob( dirname(__DIR__, 3) . '/data/keywords/*.txt' );
  $terms = [];

  if ( $terms_files ) {
    $terms_file = $terms_files[array_rand( $terms_files, 1 )];
    $terms = clean_array( file( $terms_file ) );
    $terms = array_map( 'ucwords', $terms );

    shuffle( $terms );

    $terms = array_slice( $terms, 0, $limit );
  }

  return array('items' => $terms);
}

function str_limit($value, $limit = 100, $end = '...') {
    $limit = $limit - mb_strlen($end); // Take into account $end string into the limit
    $valuelen = mb_strlen($value);
    return $limit < $valuelen ? mb_substr($value, 0, mb_strrpos($value, ' ', $limit - $valuelen)) . $end : $value;
}

function autop( $pee, $br = true ) {
	$pre_tags = [];

	if ( trim( $pee ) === '' )
		return '';

	$pee = $pee . "\n";

	if ( strpos( $pee, '<pre' ) !== false ) {
		$pee_parts = explode( '</pre>', $pee );
		$last_pee = array_pop( $pee_parts );
		$pee = '';
		$i = 0;

		foreach ( $pee_parts as $pee_part ) {
			$start = strpos( $pee_part, '<pre' );

			if ( $start === false ) {
				$pee.= $pee_part;
				continue;
			}

			$name = "<pre pre-tag-$i></pre>";
			$pre_tags[$name] = substr( $pee_part, $start ) . '</pre>';

			$pee.= substr( $pee_part, 0, $start ) . $name;
			$i++;
		}

		$pee.= $last_pee;
	}

	$pee = preg_replace( '|<br\s*/?>\s*<br\s*/?>|', "\n\n", $pee );
	$all_blocks = '(?:table|script|thead|tfoot|caption|col|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|form|map|area|blockquote|address|math|style|p|h[1-6]|hr|fieldset|legend|section|article|aside|hgroup|header|footer|nav|figure|figcaption|details|menu|summary)';
	$pee = preg_replace( '!(<' . $all_blocks . '[\s/>])!', "\n\n$1", $pee );
	$pee = preg_replace( '!(</' . $all_blocks . '>)!', "$1\n\n", $pee );
	$pee = str_replace( [ "\r\n", "\r" ], "\n", $pee );
	$pee = replace_in_html_tags( $pee, [ "\n" => " <!-- wpnl --> " ] );

	if ( strpos( $pee, '<option' ) !== false ) {
		$pee = preg_replace( '|\s*<option|', '<option', $pee );
		$pee = preg_replace( '|</option>\s*|', '</option>', $pee );
	}

	if ( strpos( $pee, '</object>' ) !== false ) {
		$pee = preg_replace( '|(<object[^>]*>)\s*|', '$1', $pee );
		$pee = preg_replace( '|\s*</object>|', '</object>', $pee );
		$pee = preg_replace( '%\s*(</?(?:param|embed)[^>]*>)\s*%', '$1', $pee );
	}

	if ( strpos( $pee, '<source' ) !== false || strpos( $pee, '<track' ) !== false ) {
		$pee = preg_replace( '%([<\[](?:audio|video)[^>\]]*[>\]])\s*%', '$1', $pee );
		$pee = preg_replace( '%\s*([<\[]/(?:audio|video)[>\]])%', '$1', $pee );
		$pee = preg_replace( '%\s*(<(?:source|track)[^>]*>)\s*%', '$1', $pee );
	}

	if ( strpos( $pee, '<figcaption' ) !== false ) {
		$pee = preg_replace( '|\s*(<figcaption[^>]*>)|', '$1', $pee );
		$pee = preg_replace( '|</figcaption>\s*|', '</figcaption>', $pee );
	}

	$pee = preg_replace( "/\n\n+/", "\n\n", $pee );
	$pees = preg_split( '/\n\s*\n/', $pee, -1, PREG_SPLIT_NO_EMPTY );
	$pee = '';

	foreach ( $pees as $tinkle )
		$pee.= '<p>' . trim( $tinkle, "\n" ) . "</p>\n";

	$pee = preg_replace( '|<p>\s*</p>|', '', $pee );
	$pee = preg_replace( '!<p>([^<]+)</(div|address|form)>!', "<p>$1</p></$2>", $pee );
	$pee = preg_replace( '!<p>\s*(</?' . $all_blocks . '[^>]*>)\s*</p>!', "$1", $pee );
	$pee = preg_replace( "|<p>(<li.+?)</p>|", "$1", $pee );
	$pee = preg_replace( '|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $pee );
	$pee = str_replace( '</blockquote></p>', '</p></blockquote>', $pee );
	$pee = preg_replace( '!<p>\s*(</?' . $all_blocks . '[^>]*>)!', "$1", $pee );
	$pee = preg_replace( '!(</?' . $all_blocks . '[^>]*>)\s*</p>!', "$1", $pee );

	if ( $br ) {
		$pee = preg_replace_callback( '/<(script|style).*?<\/\\1>/s', '_autop_newline_preservation_helper', $pee );
		$pee = str_replace( [ '<br>', '<br/>' ], '<br />', $pee );
		$pee = preg_replace( '|(?<!<br />)\s*\n|', "<br />\n", $pee );
		$pee = str_replace( '<WPPreserveNewline />', "\n", $pee );
	}

	$pee = preg_replace( '!(</?' . $all_blocks . '[^>]*>)\s*<br />!', "$1", $pee );
	$pee = preg_replace( '!<br />(\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)[^>]*>)!', '$1', $pee );
	$pee = preg_replace( "|\n</p>$|", '</p>', $pee );

	if ( ! empty( $pre_tags ) ) {
		$pee = str_replace( array_keys( $pre_tags ), array_values( $pre_tags ), $pee );
  } if ( false !== strpos( $pee, '<!-- wpnl -->' ) ) {
		$pee = str_replace( [ ' <!-- wpnl --> ', '<!-- wpnl -->' ], "\n", $pee );
  }

	return $pee;
}

function replace_in_html_tags( $haystack, $replace_pairs ) {
	$textarr = html_split( $haystack );
	$changed = false;

	if ( 1 === count( $replace_pairs ) ) {
		foreach ( $replace_pairs as $needle => $replace );

		for ( $i = 1, $c = count( $textarr ); $i < $c; $i += 2 ) {
			if ( false !== strpos( $textarr[$i], $needle ) ) {
				$textarr[$i] = str_replace( $needle, $replace, $textarr[$i] );
				$changed = true;
			}
		}
	} else {
		$needles = array_keys( $replace_pairs );
		for ( $i = 1, $c = count( $textarr ); $i < $c; $i += 2 ) {
			foreach ( $needles as $needle ) {
				if ( false !== strpos( $textarr[$i], $needle ) ) {
					$textarr[$i] = strtr( $textarr[$i], $replace_pairs );
					$changed = true;

					break;
				}
			}
		}
	}

	if ( $changed )
		$haystack = implode( $textarr );

	return $haystack;
}

function html_split( $input ) {
	return preg_split( html_split_regex(), $input, -1, PREG_SPLIT_DELIM_CAPTURE );
}

function html_split_regex() {
	static $regex;

	if ( ! isset( $regex ) ) {
		$comments = '!(?:-(?!->)[^\-]*+)*+(?:-->)?';
		$cdata = '!\[CDATA\[[^\]]*+(?:](?!]>)[^\]]*+)*+(?:]]>)?';
		$escaped = '(?=!--|!\[CDATA\[)(?(?=!-)' . $comments . '|' . $cdata . ')';
		$regex = '/(<(?' . $escaped . '|[^>]*>?))/';
	}

	return $regex;
}

function _autop_newline_preservation_helper( $matches ) {
	return str_replace( "\n", "<WPPreserveNewline />", $matches[0] );
}

function shuffle_include( $a, $inc ) 
{
  // $a is array to shuffle
  // $inc is array of indices to be included only in the shuffle
  // all other elements/indices will remain unaltered

  // fisher-yates-knuth shuffle variation O(n)
  $N = count($inc);
  while ( $N-- )
  { 
    $perm = mt_rand( 0, $N ); 
    $swap = $a[ $inc[$N] ]; 
    $a[ $inc[$N] ] = $a[ $inc[$perm] ]; 
    $a[ $inc[$perm] ] = $swap; 
  }
  // in-place
  return $a;
}

function shuffle_exclude( $a, $exc ) 
{
  // $a is array to shuffle
  // $exc is array of indices to be excluded from the shuffle
  // all other elements/indices will be shuffled
  // assumed excluded indices are given in ascending order
  $inc = array();
  $i=0; $j=0; $l = count($a); $le = count($exc);
  while ($i < $l)
  {
    if ($j >= $le || $i<$exc[$j]) $inc[] = $i;
    else $j++;
    $i++;
  }
  // rest is same as shuffle_include function above

  // fisher-yates-knuth shuffle variation O(n)
  $N = count($inc);
  while ( $N-- )
  { 
    $perm = mt_rand( 0, $N ); 
    $swap = $a[ $inc[$N] ]; 
    $a[ $inc[$N] ] = $a[ $inc[$perm] ]; 
    $a[ $inc[$perm] ] = $swap; 
  }
  // in-place
  
  return $a;
}

function remove_http($str) {
  $str = preg_replace('/\b((https?):\/\/|www\.)/i', ' ', $str);
  return $str;
}

function dmca_block() {
  $dmca_file = dirname(__DIR__, 3) . '/data/dmca.txt';
  
	if ( file_exists( $dmca_file ) ) {
	  $urls = array_map( 'trim', file( $dmca_file ) );
	} else {
	  $urls = [];
  }
  
  $block_permalink = str_replace(site_url(), '', canonical_url());
  
	if ( in_array( $block_permalink , $urls ) ) {
    redirect_to('/');
	}
}

function yt_clean($str) {
  $str = preg_replace("/\(([^()]*+|(?R))*\)/","", $str);
  return $str;
}

function rrmdir($dir) { 
  if (is_dir($dir)) { 
    $objects = scandir($dir);
    foreach ($objects as $object) { 
      if ($object != "." && $object != "..") { 
        if (is_dir($dir. DIRECTORY_SEPARATOR .$object) && !is_link($dir."/".$object))
          rrmdir($dir. DIRECTORY_SEPARATOR .$object);
        else
          unlink($dir. DIRECTORY_SEPARATOR .$object); 
      } 
    }
    rmdir($dir); 
  } 
}

function random_pick($arr) {
  return $arr[array_rand($arr)];
}

function robot_text($str){
  $search = ['%query%', '%title%', '%channelTitle%', '%duration%', '%ptsTime%', '%size%', '%viewCount%', '%likeCount%', '%dislikeCount%', '%publishedAt%', '%createdAt%', '%slug%', '%site_name%', '%domain%'];
  $replace = ['_a_', '_b_', '_c_', '_d_', '_e_', '_f_', '_g_', '_h_', '_i_', '_j_', '_k_', '_l_', '_m_', '_n_'];
  return str_replace($search, $replace, $str);
}

function normal_text($str){
  $search = ['_a_', '_b_', '_c_', '_d_', '_e_', '_f_', '_g_', '_h_', '_i_', '_j_', '_k_', '_l_', '_m_', '_n_'];
  $replace = ['%query%', '%title%', '%channelTitle%', '%duration%', '%ptsTime%', '%size%', '%viewCount%', '%likeCount%', '%dislikeCount%', '%publishedAt%', '%createdAt%', '%slug%', '%site_name%', '%domain%'];
  return str_replace($search, $replace, $str);
}

function get_sub(){
  $sub = extract_subdomains(site_domain());
  $kw = ucwords(preg_replace('/\s|\-|\&|\.|\+/', ' ', $sub));
  return !empty($kw) ? $kw : option( 'site_name' );
}