<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
<title><?php echo $meta_title; ?></title>
<?php if (isset($meta_description)&&(!is_null($meta_description))) echo '<meta name="description" content="'.$meta_description.' " />';
if(isset($meta_noindex)&&$meta_noindex) echo '<meta name="robots" content="noindex, nofollow">'; 
if (isset($meta_canonical)&&$meta_canonical) echo '<link rel="canonical" href="'.($this->uri->segment(1)=='home' ? site_url() : current_url()).'" />'; ?>
<script data-ad-client="ca-pub-5976356078715284" async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- BootstrapCND v4.3 -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<!-- End of BootstrapCND 4.3 -->	
<link href="<?php echo site_url('css/styles.css');?>" rel="stylesheet">
<link href="<?php echo site_url('css/sm-clean-menu.css');?>" rel="stylesheet">
<!-- BootstrapCND v4.3 JS -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<!-- End of BootstrapCND 4.3 -->
<!-- Font Awesome 4.7 -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<!-- End of Font Awesome -->
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<link href='http://fonts.googleapis.com/css?family=Lato:400,700,900,400italic,700italic,900italic' rel='stylesheet' type='text/css'>
<!-- jQuery -->
<script type="text/javascript" src="<?php // echo site_url('js/jquery/jquery.js') ?>"></script>
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