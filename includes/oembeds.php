<?php

/**
 * This needs work. I don't know anything about regular expressions.
 */

add_action( 'init', 'pig_enable_sketchfab_oembed' );

function pig_enable_sketchfab_oembed(){
	// https://sketchfab.com/show/b7LzIm8JrnPw4GBDOMBNGYc39qM
	$regex = '#http(?:s)://(?:www)\.sketchfab\.com/show/\([\d]+)i';
	wp_embed_register_handler( 'sketchfab', $regex, 'pig_embed_sketchfab' );
}

function pig_embed_sketchfab( $matches, $attr, $url, $rawattr = '' ){
	/* <iframe frameborder="0" height="480" width="854" allowFullScreen webkitallowfullscreen="true" mozallowfullscreen="true" src="http://sketchfab.com/m4jig20?autostart=0&transparent=0&autospin=0&controls=1"></iframe> */
	$embed_key = pig_get_sketchfab_embed_key( $url );
	$embed = '';

	if( $embed_key ){
		$width = count( $matches ) >= 2 && $matches[1] ? $matches[1] : '100%';
		$height = count( $matches ) >= 3 && $matches[2] ? $matches[2] : '400';
		$embed = sprintf('<iframe frameborder="0" height="%3$s" width="%2$s" allowFullScreen webkitallowfullscreen="true" mozallowfullscreen="true" src="http://sketchfab.com/%1$s?autostart=0&transparent=0&autospin=0&controls=1"></iframe>',
			esc_attr( $embed_key ),
			esc_attr( $width ),
			esc_attr( $height )
		);
	}

	return apply_filters( 'embed_sketchfab', $embed, $matches, $attr, $url, $rawattr );
}

add_action( 'init', 'pig_enable_p3din_oembed' );

function pig_enable_p3din_oembed(){
	// http://p3d.in/Bg8Ru
	$regex = '#http(?:s)://(?:www)\.p3d\.in/\([\d]+)i';
	wp_embed_register_handler( 'p3din', $regex, 'pig_embed_p3din' );
}

function pig_embed_p3din( $matches, $attr, $url, $rawattr ){
	/* <iframe src="http://p3d.in/e/Bg8Ru" width="100%" height="480px" frameborder="0" seamless allowfullscreen webkitallowfullscreen></iframe> */
	$embed = sprintf('<iframe src="http://p3d.in/e/%1$s" width="100%" height="%2$s" frameborder="0" seamless allowfullscreen webkitallowfullscreen></iframe>',
		esc_attr( $matches[1] ),
		esc_attr( $matches[2] )
	);

	return apply_filters( 'embed_p3din', $embed, $matches, $attr, $url, $rawattr );
}

function pig_get_sketchfab_embed_key( $url ){
	// fetch contents of $url to get value in hidden input classname .viewer-hud-model-url
	$url = str_replace( 'https://', 'http://', $url );
	$contents = wp_remote_get( $url );

	if( is_object( $contents ) ){
		echo $contents->get_error_message( $contents->get_error_code() );
		return false;
	}

	if( is_string( $contents['body'] ) ) {
		$dom = new DomDocument();
		libxml_use_internal_errors(true);
		$dom->loadHTML( $contents['body'] );
		libxml_clear_errors();
		$finder = new DOMXPath( $dom );
		$nodes = $finder->query("//input[contains(concat(' ', normalize-space(@class), ' '), viewer-hud-model-url)]");
		foreach( $nodes as $node ){
			if( $node->getAttribute( 'value' ) ){ // we want the last element
				$value = $node->getAttribute( 'value' );
			}
		}
		if( $value )
			return str_replace( 'http://sketchfab.com/', '', $value );
	}

	return false;
}
