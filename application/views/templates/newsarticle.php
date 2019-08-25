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
    	    	<section>
    	    		<h2>Recent News</h2>
    				<?php $this->load->view('sidebar'); ?>
    	    	</section>
    	    </div>
    	    <div class="wrapper">
            <div class="content-menu">
            	<?php echo get_footer_menu($footer_menu_inside); ?>
            	<!--  <ul>
                	<li><a href="#">Texas Hold’em</a></li>
                    <li><a href="#">Omaha</a></li>
                    <li><a href="#">Omaha Hi-Lo</a></li>
                    <li><a href="#">Stud</a></li>
                    <li><a href="#">Stud Hi-Lo</a></li>
                    <li><a href="#">Draw</a></li>
                    <li><a href="#">2-7 Triple Draw</a></li>
                    <li><a href="#">2-7 Single Draw</a></li>
                    <li><a href="#">HORSE</a></li>
                    <li><a href="#">Razz</a></li>
                    <li><a href="#">8-Game Mix</a></li>
                    <li class="last"><a href="#">Badugi</a></li>
               </ul> -->
                <div class="clear"></div>
            </div>
        </div>
    </div>
 </div>
</section>