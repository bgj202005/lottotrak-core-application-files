<!--  Main Content -->
<section id="content">
	<div class="content-inner col-centered">
    	<div class="row">
    		<div class="col-xs-12 col-md-8">
    			<div class="row">
    				<article>
    					<h2><?php echo e($article->title);?></h2>
    					<p class="pubdate"><?php echo e($article->pubdate); ?></p>
    					<?php echo $article->body; ?>
    				</article>
    			</div>
    		</div>
	<!--  Sidebar -->
	<div class="col-xs-12 col-md-4 sidebar">
    	<section>
        	<h2>Latest Lottery News</h2>
            	<?php $this->load->view('sidebar'); ?>
    	</section>
		<div class="left-pad top-pad" style = "margin-right: 30px; margin-bottom:15px;">
			<div class="wrapper">
				<a href="#"><img class = "home" src="<?php echo base_url(); ?>images/banner-1.jpg" alt=""></a>
				<a href="#"><img class = "home" src="<?php echo base_url(); ?>images/banner-2.jpg" alt=""></a>
			</div>
			<a class="link" href="#">More propositions</a>
		</div>
	</div>
    <div class="wrapper">
        <div class="content-menu">
        	<?php echo get_footer_menu($footer_menu_inside); ?>
        	
            <div class="clear"></div>
            </div>
	       </div>
        </div>
    </div>
</section>