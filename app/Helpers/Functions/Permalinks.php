<?php

function search_permalink($keyword='') {
	$slug = make_slug($keyword, option('permalink_slug_separator'));
	return site_url() . strtr( option( 'search_permalink', '%slug' ), [ '%slug%' => $slug, '%id%' => substr(preg_replace("/[^A-Za-z0-9 ]/", '', $_SERVER['HTTP_HOST']), 0, option('limit_domain')), '%lang%' => random_pick(option('translate_lang')) ] );
};

function single_permalink($id='', $title=''){
  $slug = make_slug($title, option('permalink_slug_separator'));
	return site_url() . strtr( option( 'single_permalink', '%id%-%slug' ), [ '%slug%' => $slug, '%id%' => $id ] );
};

function download_permalink($id=''){
	return site_url() . strtr( option( 'download_permalink', '%id%' ), [ '%id%' => $id ] );
};

function page_permalink($slug) {
	return site_url() . strtr( option( 'page_permalink', '%slug' ), [ '%slug%' => $slug ] );
}

function playlist_permalink($slug) {
	return site_url() . strtr( option( 'playlist_permalink', '%slug' ), [ '%slug%' => $slug ] );
}

function genre_permalink($slug) {
	return site_url() . strtr( option( 'genre_permalink', '%slug' ), [ '%slug%' => $slug ] );
}

function search_route($slug, $id, $lang) {
	return strtr( option( 'search_permalink', '%slug' ), [ '%slug%' => $slug, '%id%' => $id, '%lang%' => $lang ] );
}

function single_route($id, $slug) {
	return strtr( option( 'single_permalink', '%id%-%slug' ), [ '%slug%' => $slug, '%id%' => $id ] );
}

function download_route($id) {
	return strtr( option( 'download_permalink', '%id%' ), [ '%id%' => $id ] );
}

function page_route($slug) {
	return strtr( option( 'page_permalink', '%slug%' ), [ '%slug%' => $slug ] );
}

function playlist_route($slug) {
	return strtr( option( 'playlist_permalink', '%slug%' ), [ '%slug%' => $slug ] );
}

function genre_route($slug) {
	return strtr( option( 'genre_permalink', '%slug%' ), [ '%slug%' => $slug ] );
}
