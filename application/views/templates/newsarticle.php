<!--  Main Content -->
<section id="content">
    <div class="content-inner  col-centered">
        <div class = "row">
            <div class="col-xs-12 col-md-9">
        	   <div class="row">
        	       <?php if($pagination): ?>
                        <section class="pagination-section"><?php echo $pagination; ?></section>
        			<?php endif; ?>
        		</div>
       			<div class="row article-listing">	
        				<?php if (count($articles)): foreach ($articles as $article): ?>
        				    <article class="lottery-article-preview">
        				    	<header class="article-preview-header">
        				    		<h2 class="article-preview-title">
        				    			<a href="<?php echo site_url('article/'.$article->id.'/'.$article->slug); ?>">
        				    				<?php echo e($article->title); ?>
        				    			</a>
        				    		</h2>
        				    		<p class="article-preview-date">
        				    			<i class="fa fa-calendar"></i>
        				    			<time datetime="<?php echo date('Y-m-d', strtotime($article->pubdate)); ?>">
        				    				<?php echo date('F j, Y', strtotime($article->pubdate)); ?>
        				    			</time>
        				    		</p>
        				    	</header>
        				    	<div class="article-preview-content">
        				    		<?php echo get_excerpt($article); ?>
        				    	</div>
        				    	<footer class="article-preview-footer">
        				    		<a href="<?php echo site_url('article/'.$article->id.'/'.$article->slug); ?>" class="read-more-btn">
        				    			Read Full Article <i class="fa fa-arrow-right"></i>
        				    		</a>
        				    	</footer>
        				    </article>
        				    <hr class="article-divider">
        				<?php  endforeach; endif;?>
        		</div>
    			<div class="row">
        			<?php if($pagination): ?>
        				<section class="pagination-section"><?php echo $pagination; ?></section>
        			<?php endif; ?>
    		    </div>
    		</div>
    	    <!--  Sidebar -->
    	    <div class="col-xs-12 col-md-3 sidebar">
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