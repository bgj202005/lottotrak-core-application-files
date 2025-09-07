<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function btn_edit($uri) 
{
	return anchor($uri, '<i class="fa fa-pencil fa-2x" aria-hidden="true"></i>', array('title' => 'Edit the lottery profile'));
}

function btn_import($uri) 
{
	return anchor($uri, '<i class="fa fa-arrow-circle-o-up fa-2x" aria-hidden="true"></i>', array('title' => 'Imports draws into database from cvs file'));
}

function btn_view($uri) 
{
	return anchor($uri, '<i class="fa fa-eye fa-2x" aria-hidden="true"></i>', array('title' => 'View lottery draws'));
}

function btn_prizes($uri) 
{
	return anchor($uri, '<i class="fa fa-usd fa-2x" aria-hidden="true"></i>', array('title' => 'Lottery Prize Breakdown'));
}

function add_meta_title($string) 
{
	$CI = &get_instance();
	$CI->data['meta_title'] = e($string).' | '.$CI->config->config['brand_name'];
}

function add_meta_description($string) 
{
	$CI = &get_instance();
	$CI->data['meta_description'] = e($string);
}

function add_meta_canonical($bool) 
{
	$CI = &get_instance();
	$CI->data['meta_canonical'] = $bool;
}

/**
	 * Add a Meta No Index tag to pages such as the Maintenance page and 404 Error page
	 * @param       none
	 * @return      none
 */
function add_meta_noindex() 
{
	$CI = &get_instance();
	$CI->data['meta_noindex'] = TRUE;
}
function get_menu($array, $m = FALSE, $child = FALSE)
{
    $str = '';
    if ($m) {
        // If maintenance mode is enabled, show a simple message
        return '<nav class="navbar navbar-expand-lg navbar-dark bg-dark"><span class="navbar-text text-white">Maintenance Mode</span></nav>';
    }

    if (count($array)) {
        if ($child == FALSE) {
            $str .= '<nav class="navbar navbar-expand-lg navbar-dark bg-dark">' . PHP_EOL;
            $str .= '<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#top-menu" aria-controls="top-menu" aria-expanded="false" aria-label="Toggle navigation" style="margin-left: auto;">' . PHP_EOL;
            $str .= '<span class="navbar-toggler-icon"></span>' . PHP_EOL;
            $str .= '</button>' . PHP_EOL;
            $str .= '<div class="collapse navbar-collapse" id="top-menu">' . PHP_EOL;
            $str .= '<ul class="navbar-nav ml-auto">' . PHP_EOL; // Align menu items to the right
        }

        foreach ($array as $item) {
            $hasChildren = isset($item['children']) && count($item['children']);
            $str .= '<li class="nav-item' . ($hasChildren ? ' dropdown' : '') . '">' . PHP_EOL;

            if ($hasChildren) {
                $str .= '<a href="' . site_url($item['slug']) . '" class="nav-link dropdown-toggle" id="dropdown' . $item['slug'] . '" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' . e(($item['slug'] == 'home' ? "Home" : $item['title'])) . '</a>' . PHP_EOL;
                $str .= '<div class="dropdown-menu" aria-labelledby="dropdown' . $item['slug'] . '">' . PHP_EOL;
                foreach ($item['children'] as $child) {
                    $str .= '<a href="' . site_url($child['slug']) . '" class="dropdown-item">' . e($child['title']) . '</a>' . PHP_EOL;
                }
                $str .= '</div>' . PHP_EOL;
            } else {
                $str .= '<a href="' . site_url($item['slug']) . '" class="nav-link">' . e(($item['slug'] == 'home' ? "Home" : $item['title'])) . '</a>' . PHP_EOL;
            }

            $str .= '</li>' . PHP_EOL;
        }

        if ($child == FALSE) {
            $str .= '</ul>' . PHP_EOL;
            $str .= '</div>' . PHP_EOL;
            $str .= '</nav>' . PHP_EOL;
        }
    }
    return $str;
}

function get_footer_menu($array, $m = FALSE, $class = NULL) 
{

   $str = '';
    if (count($array)) {
        $str .= (isset($class) ? '<ul class = "footer-menu">'. PHP_EOL : '<ul>'. PHP_EOL);
        foreach($array as $item) {
            if(!isset($item['slug'])) { $item['slug'] = null; $item['title'] = null; }
            $str .= '<li>';
            $str .= (!$m ? '<a href="' .site_url($item['slug']).'">'.e($item['title']).'</a>' 
			: '<a href="' .site_url($item['slug']).'" class = "disabled">'.e($item['title']).'</a>');
            $str .= '</li>'. PHP_EOL;
        }
        $str .= '</ul>' . PHP_EOL;
    }
    return $str; 
}

function e($string) 
{
	return htmlentities($string);
} 

function btn_lottery_delete($uri, $name) 
{
	return anchor($uri, '<i class="fa fa-times-circle fa-2x" aria-hidden="true"></i>', array(
			'onclick' => "return confirm('You are about to make a permanent deletion of the $name\'s Profile, Draw Database and Prize Profile. This can not be UNDONE. Are you sure?');"));
 }
	 
function btn_delete($uri) 
{
return anchor($uri, '<i class="fa fa-times-circle fa-2x" aria-hidden="true"></i>', array(
		'onclick' => "return confirm('You are about to make a permanent deletion. This can not be UNDONE. Are you sure?');"));
}


/**
 	 * Dump helper. Functions to dump variables to the screen, in a nicley formatted manner.
 	* @author Joost van Veen
 	* @version 1.0
 	*/
	 if (!function_exists('dump')) 
	 {
 		function dump ($var, $label = 'Dump', $echo = TRUE)
 		{
 			// Store dump in variable
 			ob_start();
 			var_dump($var);
 			$output = ob_get_clean();
 	
 			// Add formatting
 			$output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
 			$output = '<pre style="background: #FFFEEF; color: #000; border: 1px dotted #000; padding: 10px; margin: 10px 0; text-align: left;">' . $label . ' => ' . $output . '</pre>';
 	
 			// Output
 			if ($echo == TRUE) {
 				echo $output;
 			}
 			else {
 				return $output;
 			}
 		}
 	}
 	if (!function_exists('dump_exit')) {
 		function dump_exit($var, $label = 'Dump', $echo = TRUE) {
 			dump ($var, $label, $echo);
 			exit;
 		}
 	}
/**
	 * Simple helper to debug to the console
	 * 
	 * @param  Array, String $data
	 * @return String
 */
	if (!function_exists('debug_to_console')) {
		function debug_to_console( $data ) {
			if ( is_array( $data ) )
				$output = "<script>console.log( 'Debug Objects: " . implode( ',', $data) . "' );</script>";
			else
				$output = "<script>console.log( 'Debug Objects: " . $data . "' );</script>";

			echo $output;
		}
	} 

 	function article_link($article) {
 		return 'article/'. intval($article->id).'/'. e($article->slug);
 	}
 	
 	function article_links($articles) {
 		$string = '<ul>';
 		foreach($articles as $article) {
 			$url = article_link($article);
 			$string .= '<li>';
 			$string .= '<h3>'.anchor($url, e($article->title)).'</h3>';
 			$string .= '<p class "pubdate">'.e($article->pubdate).'</p>';
 			$string .= '</li>';
 		}
 		$string .= '</ul>';
 	return $string;
 	}
 	
 function get_excerpt($article, $first = FALSE, $numwords = 50) 
 {
	$string = '';
 	$url = 'article/'.intval($article->id).'/'. e($article->slug);
	$string .= (!$first  ? '<h3>'.anchor($url, e($article->title)).'</h3>' : '<h2>'.anchor($url, e($article->title)).'</h2>');
 	$string .= (!$first  ? '<p class = "pubdate">'. e($article->pubdate).'</p>' : '');
 	$string .= '<p>'. e(limit_to_numwords(strip_tags($article->body), $numwords)).'</p>';
 	$string .= '<div align = "left" style = "width:1em;"><p>'.anchor($url, 'Read More > ...', array('title' => e($article->title), 'class' => 'readmore')).'</p></div>';
 return $string;
 }
 
 function limit_to_numwords($string, $numwords) 
 {
 		$excerpt = explode(' ', $string, $numwords + 1);
 		if (count($excerpt) >= $numwords) {
 			array_pop($excerpt);
 		}
 		$excerpt = implode(' ', $excerpt);
 	return $excerpt;
 }

/**
 * Safe getimagesize function that handles SSL certificate issues
 * 
 * @param      string 		$url - The image URL or path
 * @return     array|false  	Image information array or false on failure
 */
function safe_getimagesize($url)
{
	// If it's a local file path, use regular getimagesize
	if (!filter_var($url, FILTER_VALIDATE_URL)) {
		$result = @getimagesize($url);
		// If local file fails, try to get actual file dimensions
		if ($result === false && file_exists($url)) {
			// Try alternative approach for local files
			$result = @getimagesize($url);
		}
		// Return result or fallback with typical lottery logo dimensions
		return $result !== false ? $result : [300, 150, IMAGETYPE_PNG, 'width="300" height="150"'];
	}
	
	// For URLs, especially HTTPS localhost URLs, create a context with SSL options
	$context = stream_context_create([
		"ssl" => [
			"verify_peer" => false,
			"verify_peer_name" => false,
			"allow_self_signed" => true
		],
		"http" => [
			"timeout" => 10
		]
	]);
	
	// Use @ to suppress warnings and handle errors gracefully
	$result = @getimagesize($url, $context);
	
	// If getimagesize fails, return reasonable default size to prevent errors
	if ($result === false) {
		// Log the error for debugging (if CI logging is available)
		if (function_exists('log_message')) {
			log_message('error', 'safe_getimagesize failed for URL: ' . $url);
		}
		// Return typical lottery logo dimensions (300x150 aspect ratio 2:1)
		return [300, 150, IMAGETYPE_PNG, 'width="300" height="150"'];
	}
	
	return $result;
}

/**
 * Get responsive image attributes with proper aspect ratio
 * 
 * @param      array 		$image_info - Result from getimagesize
 * @param      int 		$max_width - Maximum width for display
 * @return     array  		Attributes for img tag
 */
function get_responsive_image_attrs($image_info, $max_width = 150)
{
	if (!$image_info || !isset($image_info[0]) || !isset($image_info[1])) {
		return ['width' => $max_width, 'height' => $max_width/2];
	}
	
	$original_width = $image_info[0];
	$original_height = $image_info[1];
	
	// Calculate aspect ratio
	$aspect_ratio = $original_height / $original_width;
	
	// Calculate display dimensions
	$display_width = min($original_width, $max_width);
	$display_height = $display_width * $aspect_ratio;
	
	return [
		'width' => round($display_width),
		'height' => round($display_height),
		'style' => 'max-width: 100%; height: auto;'
	];
}

/** For PHP <= 7.3.0 :
* array_key_last helper
* 
* @param      array 		any associative array
* @return     array  		return the last key or NULl, if not an array
*/
 if (! function_exists("array_key_last")) 
{
	function array_key_last($array) 
	{
			if (!is_array($array) || empty($array)) 
			{
				return NULL;
			}
		return array_keys($array)[count($array)-1];
	}
}