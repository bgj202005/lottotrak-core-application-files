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
				<div class = "row">
					<?php if(!is_null($article->raw)) echo stripslashes($article->raw); ?> 
				</div>
    		</div>
	<!--  Sidebar -->
	<div class="col-xs-12 col-md-4 sidebar">
		<?php $this->load->view('sidebar'); ?>	
	</div>
    <div class="wrapper">
        <div class="content-menu">
        	<?php echo get_footer_menu($footer_menu_inside, $maintenance); ?>
        	
            <div class="clear"></div>
            </div>
	       </div>
        </div>
    </div>
</section>