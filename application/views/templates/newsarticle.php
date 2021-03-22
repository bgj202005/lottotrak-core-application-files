<!--  Main Content -->
<section id="content">
    <div class="content-inner  col-centered">
        <div class = "row">
            <div class="col-xs-12 col-md-8">
        	   <div class="row">
        	       <?php if($pagination): ?>
                        <section><?php echo $pagination; ?></section>
        			<?php endif; ?>
        		</div>
       			<div class = "row">	
        				<?php if (count($articles)): foreach ($articles as $article): ?>
        				    <article><?php echo get_excerpt($article); ?><hr></article>
        				<?php  endforeach; endif;?>
        		</div>
    			<div class="row">
        			<?php if($pagination): ?>
        				<section><?php echo $pagination; ?></section>
        			<?php endif; ?>
    		    </div>
    		</div>
    	    <!--  Sidebar -->
    	    <div class="col-xs-12 col-md-4 sidebar">
				<?php $this->load->view('sidebar'); ?>
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