<!--  Main Content -->
<section id="content">
	<div class="content-inner col-centered">
    	<div class="row">
    		<div class="col-xs-12 col-md-9">
    			<div class="row">
    				<article class="lottery-article">
    					<header class="article-header">
    						<h1 class="article-title"><?php echo e($article->title);?></h1>
    						<p class="article-pubdate">
    							<i class="fa fa-calendar"></i> 
    							<time datetime="<?php echo date('Y-m-d', strtotime($article->pubdate)); ?>">
    								<?php echo date('F j, Y', strtotime($article->pubdate)); ?>
    							</time>
    						</p>
    					</header>
    					<div class="article-content">
    						<?php echo $article->body; ?>
    					</div>
					</article>
				</div>
				<?php if(!is_null($article->raw)): ?>
				<div class="row">
					<div class="article-raw-content">
						<?php echo stripslashes($article->raw); ?> 
					</div>
				</div>
				<?php endif; ?>
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