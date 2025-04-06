<?php
/*
 * Plugin Name: bs-simple-formatting-fixes
 * Description: A WordPress plugin for implementing simple formatting fixes
 * Author: Ben Simon
 * Author URI: https://blogbyben.com/
 */

add_filter('the_content', function($content) {

  $render_img = function($url) {
    $style = 'max-width: 400px; max-height: 400px';
    return "<a href='$url' target='_blank'><img src='$url' style='$style'/></a>";
  };

  $render_a = function($url) {
    return "<a href='$url'>$url</a>";
  };

  $render_youtube = function($url) {
    $parsed_url = parse_url($url);
    parse_str($parsed_url['query'], $query_params);
    $code = $query_params['v'] ?? false;
    if($code) {
      $player = "<a href='$url' target='_blank'><img style='border: 1px dotted #2d3642; border-radius: .25em' width='512' src='https://img.youtube.com/vi/$code/maxresdefault.jpg'/></a>";
      $caption = "<div><a target='_blank' href='$url'>(Click to watch video)</a></div>";
      return "<div style='margin: .5em 0'>$player\n$caption</div>";
    }

    return $url;
  };

  $callback = function($matches) use($render_img, $render_a, $render_youtube) {
    $prefix = $matches[1];
    $url = $matches[2];
    $suffix = $matches[3];

    if(!in_array($prefix, ['"', '\''])) { // "
      if(preg_match('/jpeg|jpg|png/', strtolower($url))) {
        $html = $render_img($url);
      } else if(preg_match('|youtube.com/watch[?]v=|', $url)) {
        $html = $render_youtube($url);
      } else {
        $html = $render_a($url);
      }
    } else {
      $html = $url;
    }

    return "{$prefix}{$html}{$suffix}";
  };



  return preg_replace_callback('/(.)?(http.*?)([\'"< ]|$)/m', $callback, $content);
});

remove_filter( 'the_content', array( $GLOBALS['wp_embed'], 'autoembed' ), 8 );
