<?php
function btn_edit($uri) {
	return anchor($uri, '<i class ="glyphicon glyphicon-edit"');
}

function add_meta_title($string) {
	$CI = &get_instance();
	$CI->data['meta_title'] = e($string).'-'.$CI->data['meta_title'];
	//return anchor($uri, '<i class="icon-edit"></i>');
}

function get_menu ($array, $child = FALSE)
{
	$str = '';
	if (count($array)) 
 	{
	if ($child==FALSE) { $str .= '<ul id="top-menu" class="sm sm-clean">'.PHP_EOL; }
	foreach ($array as $item) 
		{
			$str .= '<li><a href="'.site_url($item['slug']).'">'.e($item['title']).'</a>';
			// Do we have any children?
			if (isset($item['children']) && count($item['children'])) 
			{
				$str .= '<ul>'. PHP_EOL;
				$str .= get_menu($item['children'], TRUE);
				$str .= '</ul>';
			}
			//$str .= '</li>' . PHP_EOL;
			//$str .= $child == TRUE ? '</ul></li>' : '</li>';	
		}
	if ($child==FALSE) { $str .= '</ul>'; }
	}
return $str;
}

/*function get_menu($array, $child = FALSE, $parent_id = 0) 
{ 
	
	$str = '';
	/* $CI = &get_instance(); */ 
		
	
	/* if (count($array)) 
	{
		/* $str .= $child == FALSE ? '<ul class="nav navbar-nav">'. PHP_EOL : '<ul class = "dropdown-menu">'. PHP_EOL; */
		
		/* if ($child == FALSE) { $str .='<ul id="top-menu" class="sm sm-clean">'.PHP_EOL; } 
		/* $str .= $child == FALSE ? '<nav id="main-nav"><ul id="main-menu" class="sm sm-clean">'. PHP_EOL : '<ul>'. PHP_EOL; */
		
		/* foreach($array as $item) 
		{
			/* $active = $CI->uri->segment(1) == $item['slug'] ? TRUE : FALSE; */
			/* if (isset($item['children']) && count($item['children'])) 
			{
				
				$current_parent = $item['parent_id'];

				if ($current_parent!=$parent_id) 
					{
						$str .= '<ul>'.PHP_EOL;
						$str .= '<li><a href="'.site_url($item['slug']).'">'.e($item['title']);
						$str .= get_menu($item['children'], TRUE, $current_parent);
					    $str .= '</li>'.PHP_EOL;
						$str .= '</ul>'.PHP_EOL; 
				    	//$str .= '</li>'.PHP_EOL; 
					}
				else 
					{
						$str .= '<li><a href="'.site_url($item['slug']).'">'.e($item['title']);
						$str .= ($child == FALSE ? '</a>'. PHP_EOL : '</a></li>'. PHP_EOL);
					/* if ($child == FALSE) { $str .= '<ul>'.PHP_EOL; } */
				/*	}
					$str .= get_menu($item['children'], TRUE, $current_parent);
				}			
			else 
			{
				$str .= '<li><a href="'.site_url($item['slug']).'">'.e($item['title']).'</a></li>'.PHP_EOL;
			}
		}
	} 
	if ($child == FALSE) {$str .= '</ul>'.PHP_EOL; }
	return $str; */
	
	/* if (count($array)) 
	{
		<!-- $str .= $child == FALSE ? '<ul class="nav navbar-nav">'. PHP_EOL : '<ul class = "dropdown-menu">'. PHP_EOL; -->
		$str .= $child == FALSE ? '<ul class="menu responsive-menu">'. PHP_EOL : '<ul>'. PHP_EOL;
		
		foreach($array as $item) 
		{
			$active = $CI->uri->segment(1) == $item['slug'] ? TRUE : FALSE;
			if (isset($item['children']) && count($item['children'])) 
			{
				$str .= '<li>';
			    $str .= '<a href="'.site_url($item['slug']).'">'.e($item['title']);
				$str .= ($child == FALSE ? '<span class="caret"></span></a>'. PHP_EOL : '<span style = "display:inline;" class="glyphicon glyphicon-menu-right"></span></a>'. PHP_EOL);
				$str .= get_menu($item['children'], TRUE); 
			}
			else 
			{
				$str .= $active ? '<li class = "active">' : '<li>';
				$str .= '<a href="' .site_url($item['slug']).'">'.e($item['title']).'</a>';
			}
			$str .= '</li>'. PHP_EOL;
		}
		$str .= '</ul>' . PHP_EOL;
	} 
	return $str; 
} */

function get_footer_menu($array, $class = NULL) {

   $str = '';
    if (count($array)) {
        $str .= (isset($class) ? '<ul class = "footer-menu">'. PHP_EOL : '<ul>'. PHP_EOL);
        foreach($array as $item) {
            if(!isset($item['slug'])) { $item['slug'] = null; $item['title'] = null; }
            $str .= '<li>';
            $str .= '<a href="' .site_url($item['slug']).'">'.e($item['title']).'</a>';
            $str .= '</li>'. PHP_EOL;
        }
        $str .= '</ul>' . PHP_EOL;
    }
    return $str; 
}

function e($string) {
	return htmlentities($string);
} 

function btn_delete($uri) {
	
	return anchor($uri, '<i class ="glyphicon glyphicon-remove"></i>', array(
			'onclick' => "return confirm('You are about to delete a record. This can not be undone. Are you sure?');"));
 	}

/**
 	 * Dump helper. Functions to dump variables to the screen, in a nicley formatted manner.
 	* @author Joost van Veen
 	* @version 1.0
 	*/
 	if (!function_exists('dump')) {
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
 	
 function get_excerpt($article, $numwords = 50) {
	$string = '';
 	$url = 'article/'.intval($article->id).'/'. e($article->slug);

 	$string .= '<h3>'.anchor($url, e($article->title)).'></h3>';
 	$string .= '<p class = "pubdate">'. e($article->pubdate).'</p>';
 	$string .= '<p>'. e(limit_to_numwords(strip_tags($article->body), $numwords)).'</p>';
 	$string .= '<p>'.anchor($url, 'Read More > ...', array('title' => e($article->title))).'</p>';
 return $string;
 }
 
 function limit_to_numwords($string, $numwords) {
 		$excerpt = explode(' ', $string, $numwords + 1);
 		if (count($excerpt) >= $numwords) {
 			array_pop($excerpt);
 		}
 		$excerpt = implode(' ', $excerpt);
 	return $excerpt;
 }