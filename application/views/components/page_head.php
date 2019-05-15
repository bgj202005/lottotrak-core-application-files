<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
<title><?php echo $meta_title; ?></title>

<!-- Bootstrap -->
<link href="<?php echo site_url('css/bootstrap.min.css');?>" rel="stylesheet">
<link href="<?php echo site_url('css/styles.css');?>" rel="stylesheet">
<link href="<?php echo site_url('css/sm-clean-menu.css');?>" rel="stylesheet">
<link href="<?php echo site_url('css/animate.css');?>" rel="stylesheet">
	

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="<?php echo site_url('js/bootstrap.min.js');?>"></script>
<link href='http://fonts.googleapis.com/css?family=Lato:400,700,900,400italic,700italic,900italic' rel='stylesheet' type='text/css'>
<!-- <script src="<?php // echo site_url('js/jquery-1.7.min.js');?>" type="text/javascript"></script> -->
<script src="<?php echo site_url('js/FF-cash.js');?>" type="text/javascript"></script>
<!-- <script src="<?php site_url('js/superfish.js');?>" type="text/javascript"></script> -->
<!-- <script src="<?php site_url('js/script.js');?>" type="text/javascript"></script> -->
<script src="<?php echo site_url('js/jquery.hoverIntent.js');?>" type="text/javascript"></script>
<!-- jQuery -->
<script type="text/javascript" src="<?php echo site_url('js/jquery/jquery.js') ?>"></script>
<script src="<?php echo site_url('js/jquery.smartmenus.min.js');?>" type="text/javascript"></script>
<script src="<?php echo site_url('js/jquery.responsivemenu.js');?>" type="text/javascript"></script>
   	<script>
		$(document).ready(function() { 
			$('.play-button').hover(
				function(){$(this).find('span').stop().animate({opacity: 0}, 350)},
				function(){$(this).find('span').stop().animate({opacity: 1}, 350)}
			)
			$('.hover').hover(
				function(){$(this).find('span').css({'left':$(this).width()/2, 'height':$(this).height()}).stop().animate({width: $(this).width(), left: 0}, 200);},
				function(){$(this).find('span').stop().animate({width: 0, left: $(this).width()/2}, 200)}
			)
		}); 
   </script>
    <!--[if lt IE 8]>
        <div style=' clear: both; text-align:center; position: relative;'>
            <a href="http://windows.microsoft.com/en-US/internet-explorer/products/ie/home?ocid=ie6_countdown_bannercode">
             <img src="http://storage.ie6countdown.com/assets/100/images/banners/warning_bar_0000_us.jpg" border="0" height="42" width="820" alt="You are using an outdated browser. For a faster, safer browsing experience, upgrade for free today." />
            </a>
        </div>
    <![endif]-->
    <!--[if lt IE 9]>
   		<script type="text/javascript" src="js/html5.js"></script>
        <link rel="stylesheet" href="css/ie.css" type="text/css" media="screen">
	<![endif]-->
</head>