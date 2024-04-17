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
    $code = preg_replace('/^.*v=/', '', $url);
    return "<iframe width='560' height='315' src='https://www.youtube.com/embed/$code' title='YouTube video player' frameborder='0' allow='accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share' referrerpolicy='strict-origin-when-cross-origin' allowfullscreen></iframe>";
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



  return preg_replace_callback('/(.)?(http.*?)([\'"< ]|$)/', $callback, $content);
});
