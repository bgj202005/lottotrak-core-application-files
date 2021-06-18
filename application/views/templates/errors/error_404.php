<?php
defined('BASEPATH') OR exit('No direct script access allowed');?>

<style type="text/css">

p {
	margin: 12px 15px 12px 15px;
}
</style>
<section id="content">
    <div class="content-inner  col-centered">
        <div class="row">
            <div class="col-xs-12 col-md-8">
                <div class="row">
        			<article>
        				<h2><p>Error 404. The Page <?=$slug?> does not EXIST.</p></h2>
        			</article>
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