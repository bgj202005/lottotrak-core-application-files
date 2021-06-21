<!--  Main Content -->
<section id="content">
    <div class="content-inner  col-centered">
        <div class="row">
            <div class="col-xs-12 col-md-8">
                <div class="row">
        			<article>
        				<h2><?php echo e($page->title);?></h2>
        				<?php echo $page->body; ?>
        			</article>
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